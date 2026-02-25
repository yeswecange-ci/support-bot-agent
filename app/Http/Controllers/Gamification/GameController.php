<?php

namespace App\Http\Controllers\Gamification;

use App\Http\Controllers\Controller;
use App\Models\Game;
use App\Models\GameAnswer;
use App\Models\GameParticipation;
use App\Models\GameQuestion;
use App\Services\Gamification\TwilioFlowGeneratorService;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Symfony\Component\HttpFoundation\StreamedResponse;

class GameController extends Controller
{
    public function __construct(private TwilioFlowGeneratorService $flowGenerator) {}

    // ── Liste ─────────────────────────────────────────────────────────────────

    public function index()
    {
        $games = Game::withCount('participations')
            ->orderByDesc('created_at')
            ->paginate(20);

        return view('gamification.index', compact('games'));
    }

    // ── Création ──────────────────────────────────────────────────────────────

    public function create()
    {
        return view('gamification.create', ['game' => null]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'              => 'required|string|max:255',
            'description'       => 'nullable|string',
            'type'              => 'required|in:quiz,free_text,vote,prediction',
            'eligibility'       => 'required|in:all,clients_only',
            'start_date'        => 'nullable|date',
            'end_date'          => 'nullable|date|after_or_equal:start_date',
            'max_participants'  => 'nullable|integer|min:1',
            'thank_you_message' => 'nullable|string',
        ]);

        $validated['slug']   = Game::generateSlug($validated['name']);
        $validated['status'] = 'draft';

        $game = Game::create($validated);

        return redirect()->route('gamification.show', $game->slug)
            ->with('success', 'Jeu créé avec succès.');
    }

    // ── Affichage ─────────────────────────────────────────────────────────────

    public function show(string $slug)
    {
        $game = Game::where('slug', $slug)
            ->with(['questions', 'participations.answers.question'])
            ->firstOrFail();

        $stats = [
            'total'     => $game->participations->count(),
            'completed' => $game->participations->where('status', 'completed')->count(),
            'abandoned' => $game->participations->where('status', 'abandoned')->count(),
            'started'   => $game->participations->where('status', 'started')->count(),
        ];

        return view('gamification.show', compact('game', 'stats'));
    }

    // ── Édition ───────────────────────────────────────────────────────────────

    public function edit(string $slug)
    {
        $game = Game::where('slug', $slug)->with('questions')->firstOrFail();
        return view('gamification.create', compact('game'));
    }

    public function update(Request $request, string $slug)
    {
        $game = Game::where('slug', $slug)->firstOrFail();

        $validated = $request->validate([
            'name'              => 'required|string|max:255',
            'description'       => 'nullable|string',
            'type'              => 'required|in:quiz,free_text,vote,prediction',
            'eligibility'       => 'required|in:all,clients_only',
            'start_date'        => 'nullable|date',
            'end_date'          => 'nullable|date|after_or_equal:start_date',
            'max_participants'  => 'nullable|integer|min:1',
            'thank_you_message' => 'nullable|string',
        ]);

        $game->update($validated);

        return redirect()->route('gamification.show', $game->slug)
            ->with('success', 'Jeu mis à jour.');
    }

    public function destroy(string $slug)
    {
        $game = Game::where('slug', $slug)->firstOrFail();
        $game->delete();

        return redirect()->route('gamification.index')
            ->with('success', 'Jeu supprimé.');
    }

    // ── Questions ─────────────────────────────────────────────────────────────

    public function storeQuestion(Request $request, string $slug)
    {
        $game = Game::where('slug', $slug)->firstOrFail();

        $validated = $request->validate([
            'text'           => 'required|string',
            'type'           => 'required|in:mcq,free_text,vote,prediction',
            'options'        => 'nullable|string',
            'correct_answer' => 'nullable|string|max:255',
        ]);

        $maxOrder = $game->questions()->max('order') ?? 0;

        $options = null;
        if (!empty($validated['options'])) {
            $options = array_filter(array_map('trim', explode("\n", $validated['options'])));
            $options = array_values($options);
        }

        GameQuestion::create([
            'game_id'        => $game->id,
            'order'          => $maxOrder + 1,
            'text'           => $validated['text'],
            'type'           => $validated['type'],
            'options'        => $options ?: null,
            'correct_answer' => $validated['correct_answer'] ?? null,
        ]);

        return redirect()->route('gamification.show', $slug)
            ->with('success', 'Question ajoutée.');
    }

    public function destroyQuestion(string $slug, int $id)
    {
        $game = Game::where('slug', $slug)->firstOrFail();
        GameQuestion::where('game_id', $game->id)->where('id', $id)->delete();

        // Renuméroter les ordres
        $game->questions()->orderBy('order')->each(function ($q, $i) {
            $q->update(['order' => $i + 1]);
        });

        return redirect()->route('gamification.show', $slug)
            ->with('success', 'Question supprimée.');
    }

    // ── Statuts ───────────────────────────────────────────────────────────────

    public function activate(string $slug)
    {
        $game = Game::where('slug', $slug)->firstOrFail();
        $game->update(['status' => 'active']);

        return back()->with('success', 'Jeu activé.');
    }

    public function close(string $slug)
    {
        $game = Game::where('slug', $slug)->firstOrFail();
        $game->update(['status' => 'closed']);

        return back()->with('success', 'Jeu clôturé.');
    }

    // ── Flow Twilio ───────────────────────────────────────────────────────────

    public function showFlow(string $slug)
    {
        $game = Game::where('slug', $slug)->with('questions')->firstOrFail();
        $flow = $this->flowGenerator->generate($game);

        $game->update(['synced_at' => now()]);

        $webhookBase = rtrim(config('app.url'), '/') . "/gamification/webhook/{$slug}";

        return view('gamification.flow', compact('game', 'flow', 'webhookBase'));
    }

    // ── Export CSV ────────────────────────────────────────────────────────────

    public function export(string $slug): StreamedResponse
    {
        $game = Game::where('slug', $slug)
            ->with(['questions', 'participations.answers.question'])
            ->firstOrFail();

        $filename = "participations-{$slug}-" . now()->format('Ymd-His') . '.csv';

        return response()->streamDownload(function () use ($game) {
            $handle = fopen('php://output', 'w');

            // En-tête
            $headers = ['Nom', 'Téléphone', 'Statut', 'Début', 'Fin'];
            foreach ($game->questions as $question) {
                $headers[] = "Q{$question->order}: " . mb_substr($question->text, 0, 50);
            }
            fputcsv($handle, $headers, ';');

            // Lignes
            foreach ($game->participations as $participation) {
                $row = [
                    $participation->participant_name ?? '',
                    $participation->phone_number,
                    $participation->status,
                    $participation->started_at?->format('d/m/Y H:i'),
                    $participation->completed_at?->format('d/m/Y H:i') ?? '',
                ];

                foreach ($game->questions as $question) {
                    $answer = $participation->answers
                        ->firstWhere('question_id', $question->id);
                    $row[] = $answer?->answer_text ?? '';
                }

                fputcsv($handle, $row, ';');
            }

            fclose($handle);
        }, $filename, [
            'Content-Type' => 'text/csv; charset=UTF-8',
        ]);
    }
}
