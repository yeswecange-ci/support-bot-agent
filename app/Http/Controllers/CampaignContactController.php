<?php

namespace App\Http\Controllers;

use App\Models\Campaign;
use App\Models\Contact;
use App\Services\Campaign\ContactImportService;
use App\Services\Chatwoot\ChatwootClient;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class CampaignContactController extends Controller
{
    public function __construct(
        private ContactImportService $importService,
        private ChatwootClient $chatwoot,
    ) {}

    // ── Pages ────────────────────────────────────────────

    public function index(Request $request): View
    {
        $query = Contact::with('creator');

        if ($search = $request->get('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('phone_number', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        $contacts = $query->latest()->paginate(10);
        $campaigns = Campaign::orderBy('name')->get(['id', 'name', 'status']);

        return view('campagnes.contacts.index', compact('contacts', 'campaigns'));
    }

    public function importForm(): View
    {
        $campaigns = Campaign::orderBy('name')->get(['id', 'name', 'status']);
        return view('campagnes.contacts.import', compact('campaigns'));
    }

    // ── AJAX Actions ─────────────────────────────────────

    public function search(Request $request): JsonResponse
    {
        $term = $request->get('q', '');
        $all = $request->get('all', false);

        $query = Contact::query();

        if ($term) {
            $query->where(function ($q) use ($term) {
                $q->where('name', 'like', "%{$term}%")
                  ->orWhere('phone_number', 'like', "%{$term}%");
            });
        }

        if (!$all) {
            $query->limit(20);
        }

        $contacts = $query->get(['id', 'name', 'phone_number', 'email']);

        return response()->json($contacts);
    }

    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'name'           => 'required|string|max:255',
            'phone_number'   => 'required|string|max:30|unique:contacts,phone_number',
            'email'          => 'nullable|email|max:255',
            'campaign_ids'   => 'nullable|array',
            'campaign_ids.*' => 'exists:campaigns,id',
        ]);

        $campaignIds = $data['campaign_ids'] ?? [];
        unset($data['campaign_ids']);

        $data['created_by'] = Auth::id();

        $contact = Contact::create($data);

        if (!empty($campaignIds)) {
            foreach ($campaignIds as $campaignId) {
                Campaign::find($campaignId)?->contacts()->syncWithoutDetaching([$contact->id]);
            }
        }

        return response()->json(['success' => true, 'contact' => $contact]);
    }

    public function update(Request $request, Contact $contact): JsonResponse
    {
        $data = $request->validate([
            'name'         => 'required|string|max:255',
            'phone_number' => 'required|string|max:30|unique:contacts,phone_number,' . $contact->id,
            'email'        => 'nullable|email|max:255',
        ]);

        $contact->update($data);

        return response()->json(['success' => true, 'contact' => $contact->fresh()]);
    }

    public function destroy(Contact $contact): JsonResponse
    {
        $contact->campaignMessages()->delete();
        $contact->campaigns()->detach();
        $contact->delete();

        return response()->json(['success' => true]);
    }

    public function importPreview(Request $request): JsonResponse
    {
        $request->validate([
            'file' => 'required|file|mimes:csv,txt|max:5120',
        ]);

        $path = $request->file('file')->store('imports', 'local');
        $fullPath = storage_path('app/private/' . $path);

        $preview = $this->importService->preview($fullPath);
        $preview['file_path'] = $path;

        return response()->json($preview);
    }

    public function importConfirm(Request $request): JsonResponse
    {
        $data = $request->validate([
            'file_path'        => 'required|string',
            'campaign_ids'     => 'nullable|array',
            'campaign_ids.*'   => 'exists:campaigns,id',
        ]);

        $fullPath = storage_path('app/private/' . $data['file_path']);

        if (!file_exists($fullPath)) {
            return response()->json(['success' => false, 'message' => 'Fichier introuvable'], 404);
        }

        $result = $this->importService->import($fullPath);

        // Attacher les contacts importés aux campagnes sélectionnées
        if (!empty($data['campaign_ids']) && !empty($result['contact_ids'])) {
            foreach ($data['campaign_ids'] as $campaignId) {
                Campaign::find($campaignId)?->contacts()->syncWithoutDetaching($result['contact_ids']);
            }
        }

        // Nettoyer le fichier temporaire
        @unlink($fullPath);

        return response()->json([
            'success'  => true,
            'imported' => $result['imported'],
            'skipped'  => $result['skipped'],
            'errors'   => $result['errors'],
        ]);
    }

    /**
     * Importer tous les contacts depuis Chatwoot vers la table locale
     */
    public function importFromChatwoot(): JsonResponse
    {
        $imported = 0;
        $skipped = 0;
        $page = 1;
        $maxPages = 50; // sécurité

        try {
            while ($page <= $maxPages) {
                $data = $this->chatwoot->listContacts($page, '-last_activity_at');
                $contacts = $data['payload'] ?? [];

                if (empty($contacts)) {
                    break;
                }

                foreach ($contacts as $cwContact) {
                    $phone = $cwContact['phone_number'] ?? null;
                    $name = $cwContact['name'] ?? 'Contact';

                    // Skip contacts sans numéro de téléphone
                    if (empty($phone)) {
                        $skipped++;
                        continue;
                    }

                    // Normaliser le téléphone
                    $phone = preg_replace('/[\s\-\(\)]/', '', $phone);
                    if (!str_starts_with($phone, '+')) {
                        $phone = '+' . $phone;
                    }

                    // Vérifier doublon
                    $existing = Contact::where('phone_number', $phone)->first();
                    if ($existing) {
                        // Mettre à jour le chatwoot_contact_id si manquant
                        if (!$existing->chatwoot_contact_id) {
                            $existing->update(['chatwoot_contact_id' => $cwContact['id']]);
                        }
                        $skipped++;
                        continue;
                    }

                    try {
                        Contact::create([
                            'name' => $name,
                            'phone_number' => $phone,
                            'email' => $cwContact['email'] ?? null,
                            'chatwoot_contact_id' => $cwContact['id'],
                            'created_by' => Auth::id(),
                        ]);
                        $imported++;
                    } catch (\Exception $e) {
                        $skipped++;
                    }
                }

                // Vérifier s'il reste des pages
                $meta = $data['meta'] ?? [];
                $total = $meta['count'] ?? $meta['total'] ?? 0;
                $perPage = 15;
                $totalPages = $total > 0 ? (int) ceil($total / $perPage) : 1;

                if ($page >= $totalPages) {
                    break;
                }

                $page++;
            }

            return response()->json([
                'success'  => true,
                'imported' => $imported,
                'skipped'  => $skipped,
                'message'  => "{$imported} contact(s) importe(s), {$skipped} ignore(s)",
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur: ' . $e->getMessage(),
                'imported' => $imported,
                'skipped' => $skipped,
            ], 500);
        }
    }

    public function syncToChatwoot(Contact $contact): JsonResponse
    {
        try {
            // Chercher si le contact existe déjà dans Chatwoot
            $search = $this->chatwoot->searchContacts($contact->phone_number);
            $results = $search['payload'] ?? [];

            if (count($results) > 0) {
                $contact->update(['chatwoot_contact_id' => $results[0]['id']]);
                return response()->json(['success' => true, 'message' => 'Contact deja present sur la plateforme', 'chatwoot_id' => $results[0]['id']]);
            }

            // Créer dans Chatwoot
            $result = $this->chatwoot->createContact(
                name: $contact->name,
                phoneNumber: $contact->phone_number,
                email: $contact->email,
            );

            $chatwootId = $result['payload']['contact']['id'] ?? null;
            if ($chatwootId) {
                $contact->update(['chatwoot_contact_id' => $chatwootId]);
            }

            return response()->json(['success' => true, 'message' => 'Contact synchronise avec la plateforme', 'chatwoot_id' => $chatwootId]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }
}
