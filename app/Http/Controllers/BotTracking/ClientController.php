<?php

namespace App\Http\Controllers\BotTracking;

use App\Http\Controllers\Controller;
use App\Models\Client;
use App\Models\Conversation;
use App\Models\ConversationEvent;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class ClientController extends Controller
{
    /**
     * Display a listing of clients
     */
    public function index(Request $request)
    {
        $inboxId = session('active_inbox_id');

        // Si un inbox est sélectionné, restreindre aux numéros ayant des conversations dans cet inbox
        $inboxPhones = null;
        if ($inboxId) {
            $inboxPhones = Conversation::where('inbox_id', $inboxId)
                ->distinct()
                ->pluck('phone_number');
        }

        $query = Client::query();

        if ($inboxPhones !== null) {
            $query->whereIn('phone_number', $inboxPhones);
        }

        // Search filter
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('phone_number', 'like', "%{$search}%")
                  ->orWhere('client_full_name', 'like', "%{$search}%")
                  ->orWhere('whatsapp_profile_name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        // Client type filter
        if ($request->filled('is_client')) {
            if ($request->is_client === 'true') {
                $query->isClient();
            } elseif ($request->is_client === 'false') {
                $query->isNotClient();
            }
        }

        // Date range filter
        if ($request->filled('date_from')) {
            $query->whereDate('first_interaction_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('first_interaction_at', '<=', $request->date_to);
        }

        // Sort — whitelist pour éviter injection SQL
        $allowedSorts = ['last_interaction_at', 'client_full_name', 'interaction_count', 'created_at'];
        $sortBy = in_array($request->input('sort_by'), $allowedSorts) ? $request->input('sort_by') : 'last_interaction_at';
        $sortOrder = $request->input('sort_order') === 'asc' ? 'asc' : 'desc';
        $query->orderBy($sortBy, $sortOrder);

        $clients = $query->paginate(10)->withQueryString();

        // Stats filtrées par inbox
        $baseQuery = Client::query();
        if ($inboxPhones !== null) {
            $baseQuery->whereIn('phone_number', $inboxPhones);
        }

        $stats = [
            'total_clients'      => (clone $baseQuery)->count(),
            'sportcash_clients'  => (clone $baseQuery)->isClient()->count(),
            'non_clients'        => (clone $baseQuery)->isNotClient()->count(),
            'recent_clients'     => (clone $baseQuery)->recent(30)->count(),
            'total_interactions' => (clone $baseQuery)->sum('interaction_count'),
            'total_conversations'=> (clone $baseQuery)->sum('conversation_count'),
        ];

        return view('bot-tracking.clients.index', compact('clients', 'stats'));
    }

    /**
     * Display the specified client
     */
    public function show($id)
    {
        $client = Client::findOrFail($id);
        $inboxId = session('active_inbox_id');

        // Base query conversations — filtrée par inbox si actif
        $convQuery = Conversation::where('phone_number', $client->phone_number);
        if ($inboxId) {
            $convQuery->where('inbox_id', $inboxId);
        }

        // Get all conversations for this client
        $conversations = (clone $convQuery)
            ->with(['events' => function($query) {
                $query->orderBy('event_at', 'desc');
            }, 'agent'])
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        // Get all events for this client across all conversations
        $conversationIds = (clone $convQuery)->pluck('id');

        $allEvents = ConversationEvent::whereIn('conversation_id', $conversationIds)
            ->orderBy('event_at', 'desc')
            ->paginate(20, ['*'], 'events_page');

        $interactionStats = [
            'total_messages' => ConversationEvent::whereIn('conversation_id', $conversationIds)
                ->where('event_type', 'free_input')
                ->count(),

            'menu_choices' => ConversationEvent::whereIn('conversation_id', $conversationIds)
                ->where('event_type', 'menu_choice')
                ->count(),

            'total_duration' => $client->total_duration,

            'avg_duration' => (clone $convQuery)
                ->whereNotNull('duration_seconds')
                ->avg('duration_seconds'),
        ];

        // Get event type breakdown
        $eventBreakdown = ConversationEvent::whereIn('conversation_id', $conversationIds)
            ->selectRaw('event_type, count(*) as count')
            ->groupBy('event_type')
            ->pluck('count', 'event_type')
            ->toArray();

        return view('bot-tracking.clients.show', compact('client', 'conversations', 'interactionStats', 'allEvents', 'eventBreakdown'));
    }

    /**
     * Show the form for editing a client
     */
    public function edit($id)
    {
        $client = Client::findOrFail($id);
        return view('bot-tracking.clients.edit', compact('client'));
    }

    /**
     * Update the specified client
     */
    public function update(Request $request, $id)
    {
        $client = Client::findOrFail($id);

        $validated = $request->validate([
            'client_full_name' => 'nullable|string|max:255',
            'whatsapp_profile_name' => 'nullable|string|max:255',
            'email' => 'nullable|email|max:255',
            'phone_number' => 'required|string|max:50',
            'is_client' => 'nullable|boolean',
            'vin' => 'nullable|string|max:50',
            'carte_vip' => 'nullable|string|max:50',
        ]);

        $oldData = $client->toArray();
        $client->update($validated);

        Log::info('client_updated', [
            'message' => "Client {$client->display_name} ({$client->phone_number}) a été mis à jour",
            'old' => $oldData,
            'new' => $client->fresh()->toArray(),
        ]);

        return redirect()->route('bot-tracking.clients.show', $client->id)
            ->with('success', 'Les informations du client ont été mises à jour avec succès.');
    }

    /**
     * Sync all clients from conversations
     */
    public function sync()
    {
        // Get all unique phone numbers from conversations
        $conversations = Conversation::whereNotNull('phone_number')
            ->orderBy('created_at', 'asc')
            ->get();

        $synced = 0;
        $updated = 0;

        foreach ($conversations as $conversation) {
            $client = Client::findOrCreateByPhone($conversation->phone_number);

            // Update client info from conversation
            $client->updateFromConversation($conversation);

            // Count interactions for this conversation
            $interactionCount = ConversationEvent::where('conversation_id', $conversation->id)
                ->whereIn('event_type', ['free_input', 'menu_choice'])
                ->count();

            if ($interactionCount > 0) {
                $client->increment('interaction_count', $interactionCount);
            }

            $client->increment('conversation_count');

            // Update first interaction date
            if (!$client->first_interaction_at || $conversation->started_at < $client->first_interaction_at) {
                $client->first_interaction_at = $conversation->started_at;
                $client->save();
            }

            // Update last_interaction_at from actual conversation data
            $client->updateLastInteractionAt();

            if ($client->wasRecentlyCreated) {
                $synced++;
            } else {
                $updated++;
            }
        }

        return redirect()->route('bot-tracking.clients.index')
            ->with('success', "Synchronisation terminée : {$synced} nouveaux clients, {$updated} clients mis à jour.");
    }
}
