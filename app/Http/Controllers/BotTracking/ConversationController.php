<?php

namespace App\Http\Controllers\BotTracking;

use App\Http\Controllers\Controller;
use App\Models\Conversation;
use App\Models\ConversationEvent;
use App\Models\DailyStatistic;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ConversationController extends Controller
{
    /**
     * Inbox configuré dans .env (CHATWOOT_WHATSAPP_INBOX_ID)
     */
    private function inboxId(): ?int
    {
        return config('chatwoot.whatsapp_inbox_id') ?: null;
    }

    /**
     * Base query Conversation filtrée sur l'inbox configuré
     */
    private function inboxQuery(): \Illuminate\Database\Eloquent\Builder
    {
        $q = Conversation::query();
        if ($this->inboxId()) {
            $q->where('inbox_id', $this->inboxId());
        }
        return $q;
    }

    /**
     * Display the main dashboard
     */
    public function index(Request $request)
    {
        $dateFrom = $request->input('date_from', now()->subDays(30)->format('Y-m-d'));
        $dateTo   = $request->input('date_to', now()->format('Y-m-d'));

        $dateFromFull = $dateFrom . ' 00:00:00';
        $dateToFull   = $dateTo   . ' 23:59:59';

        $conversationsInRange = $this->inboxQuery()
            ->whereBetween('started_at', [$dateFromFull, $dateToFull]);

        $inboxId = $this->inboxId();

        $stats = [
            'total_conversations'     => (clone $conversationsInRange)->count(),
            'active_conversations'    => (clone $conversationsInRange)->where('status', 'active')->count(),
            'completed_conversations' => (clone $conversationsInRange)->where('status', 'completed')->count(),
            'total_clients'           => (clone $conversationsInRange)->where('is_client', true)->distinct()->count('phone_number'),
            'total_non_clients'       => (clone $conversationsInRange)->where('is_client', false)->distinct()->count('phone_number'),
            'avg_duration'            => (clone $conversationsInRange)->whereNotNull('ended_at')->avg('duration_seconds'),
            'total_duration'          => (clone $conversationsInRange)->whereNotNull('ended_at')->sum('duration_seconds'),
            'total_events'            => ConversationEvent::whereHas('conversation', function ($q) use ($dateFromFull, $dateToFull, $inboxId) {
                $q->whereBetween('started_at', [$dateFromFull, $dateToFull]);
                if ($inboxId) $q->where('inbox_id', $inboxId);
            })->count(),
            'total_messages'          => ConversationEvent::where('event_type', 'message_received')
                ->whereHas('conversation', function ($q) use ($dateFromFull, $dateToFull, $inboxId) {
                    $q->whereBetween('started_at', [$dateFromFull, $dateToFull]);
                    if ($inboxId) $q->where('inbox_id', $inboxId);
                })->count(),
            'total_menu_choices'      => ConversationEvent::where('event_type', 'menu_choice')
                ->whereHas('conversation', function ($q) use ($dateFromFull, $dateToFull, $inboxId) {
                    $q->whereBetween('started_at', [$dateFromFull, $dateToFull]);
                    if ($inboxId) $q->where('inbox_id', $inboxId);
                })->count(),
            'total_free_inputs'       => ConversationEvent::where('event_type', 'free_input')
                ->whereHas('conversation', function ($q) use ($dateFromFull, $dateToFull, $inboxId) {
                    $q->whereBetween('started_at', [$dateFromFull, $dateToFull]);
                    if ($inboxId) $q->where('inbox_id', $inboxId);
                })->count(),
            'unique_clients'          => (clone $conversationsInRange)->distinct()->count('phone_number'),
            'new_clients'             => \App\Models\Client::whereBetween('created_at', [$dateFromFull, $dateToFull])->count(),
        ];

        $dailyStats = DailyStatistic::whereBetween('date', [$dateFrom, $dateTo])
            ->orderBy('date', 'asc')
            ->get();

        $menuStats = [
            'informations' => DailyStatistic::whereBetween('date', [$dateFrom, $dateTo])->sum('menu_informations'),
            'demandes'     => DailyStatistic::whereBetween('date', [$dateFrom, $dateTo])->sum('menu_demandes'),
            'paris'        => DailyStatistic::whereBetween('date', [$dateFrom, $dateTo])->sum('menu_paris'),
            'encaissement' => DailyStatistic::whereBetween('date', [$dateFrom, $dateTo])->sum('menu_encaissement'),
            'reclamations' => DailyStatistic::whereBetween('date', [$dateFrom, $dateTo])->sum('menu_reclamations'),
            'plaintes'     => DailyStatistic::whereBetween('date', [$dateFrom, $dateTo])->sum('menu_plaintes'),
            'conseiller'   => DailyStatistic::whereBetween('date', [$dateFrom, $dateTo])->sum('menu_conseiller'),
            'faq'          => DailyStatistic::whereBetween('date', [$dateFrom, $dateTo])->sum('menu_faq'),
        ];

        $recentConversations = $this->inboxQuery()
            ->with('events')
            ->whereBetween('started_at', [$dateFromFull, $dateToFull])
            ->orderBy('started_at', 'desc')
            ->limit(10)
            ->get();

        return view('bot-tracking.conversations.index', compact('stats', 'dailyStats', 'menuStats', 'recentConversations', 'dateFrom', 'dateTo'));
    }

    /**
     * Display active conversations
     */
    public function active()
    {
        $activeConversations = $this->inboxQuery()
            ->where('status', 'active')
            ->with('events')
            ->orderBy('last_activity_at', 'desc')
            ->get();

        return view('bot-tracking.conversations.active', compact('activeConversations'));
    }

    /**
     * Display all conversations list
     */
    public function conversations(Request $request)
    {
        $query = $this->inboxQuery()->with('events');

        $dateFrom = $request->input('date_from', now()->subDays(30)->format('Y-m-d'));
        $dateTo   = $request->input('date_to',   now()->format('Y-m-d'));

        $dateFromFull = $dateFrom . ' 00:00:00';
        $dateToFull   = $dateTo   . ' 23:59:59';
        $query->whereBetween('started_at', [$dateFromFull, $dateToFull]);

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('is_client')) {
            $query->where('is_client', $request->is_client);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('phone_number', 'like', "%{$search}%")
                  ->orWhere('client_full_name', 'like', "%{$search}%")
                  ->orWhere('whatsapp_profile_name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        $conversations = $query->orderBy('started_at', 'desc')
            ->paginate(10)
            ->withQueryString();

        // Stats query — même filtres inbox + date
        $baseStatsQuery = $this->inboxQuery()
            ->whereBetween('started_at', [$dateFromFull, $dateToFull]);

        if ($request->filled('is_client')) {
            $baseStatsQuery->where('is_client', $request->is_client);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $baseStatsQuery->where(function ($q) use ($search) {
                $q->where('phone_number', 'like', "%{$search}%")
                  ->orWhere('client_full_name', 'like', "%{$search}%")
                  ->orWhere('whatsapp_profile_name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        $totalStats = [
            'total'     => $conversations->total(),
            'active'    => (clone $baseStatsQuery)->where('status', 'active')->count(),
            'completed' => (clone $baseStatsQuery)->where('status', 'completed')->count(),
        ];

        return view('bot-tracking.conversations.list', compact('conversations', 'totalStats', 'dateFrom', 'dateTo'));
    }

    /**
     * Display conversation detail
     */
    public function show($id)
    {
        $conversation = $this->inboxQuery()
            ->with(['events' => function ($query) {
                $query->orderBy('event_at', 'asc')->orderBy('created_at', 'asc');
            }])
            ->findOrFail($id);

        // Autres sessions du même numéro — filtrées sur l'inbox
        $phoneConversations = $this->inboxQuery()
            ->where('phone_number', $conversation->phone_number)
            ->orderBy('started_at', 'desc')
            ->get();

        $allEvents = ConversationEvent::whereIn('conversation_id', $phoneConversations->pluck('id'))
            ->with('conversation')
            ->orderBy('event_at', 'asc')
            ->orderBy('created_at', 'asc')
            ->get();

        return view('bot-tracking.conversations.show', compact('conversation', 'phoneConversations', 'allEvents'));
    }

    /**
     * Display statistics page
     */
    public function statistics(Request $request)
    {
        $dateFrom = $request->input('date_from', now()->subDays(30)->format('Y-m-d'));
        $dateTo   = $request->input('date_to', now()->format('Y-m-d'));

        $dateFromFull = $dateFrom . ' 00:00:00';
        $dateToFull   = $dateTo   . ' 23:59:59';

        $inboxId = $this->inboxId();

        $conversationsInRange = $this->inboxQuery()
            ->whereBetween('started_at', [$dateFromFull, $dateToFull]);

        $stats = [
            'total_conversations'     => (clone $conversationsInRange)->count(),
            'active_conversations'    => (clone $conversationsInRange)->where('status', 'active')->count(),
            'completed_conversations' => (clone $conversationsInRange)->where('status', 'completed')->count(),
            'total_clients'           => (clone $conversationsInRange)->where('is_client', true)->distinct()->count('phone_number'),
            'total_non_clients'       => (clone $conversationsInRange)->where('is_client', false)->distinct()->count('phone_number'),
            'avg_duration'            => (clone $conversationsInRange)->whereNotNull('ended_at')->avg('duration_seconds'),
            'total_duration'          => (clone $conversationsInRange)->whereNotNull('ended_at')->sum('duration_seconds'),
            'total_events'            => ConversationEvent::whereHas('conversation', function ($q) use ($dateFromFull, $dateToFull, $inboxId) {
                $q->whereBetween('started_at', [$dateFromFull, $dateToFull]);
                if ($inboxId) $q->where('inbox_id', $inboxId);
            })->count(),
            'total_messages'          => ConversationEvent::where('event_type', 'message_received')
                ->whereHas('conversation', function ($q) use ($dateFromFull, $dateToFull, $inboxId) {
                    $q->whereBetween('started_at', [$dateFromFull, $dateToFull]);
                    if ($inboxId) $q->where('inbox_id', $inboxId);
                })->count(),
            'total_menu_choices'      => ConversationEvent::where('event_type', 'menu_choice')
                ->whereHas('conversation', function ($q) use ($dateFromFull, $dateToFull, $inboxId) {
                    $q->whereBetween('started_at', [$dateFromFull, $dateToFull]);
                    if ($inboxId) $q->where('inbox_id', $inboxId);
                })->count(),
            'total_free_inputs'       => ConversationEvent::where('event_type', 'free_input')
                ->whereHas('conversation', function ($q) use ($dateFromFull, $dateToFull, $inboxId) {
                    $q->whereBetween('started_at', [$dateFromFull, $dateToFull]);
                    if ($inboxId) $q->where('inbox_id', $inboxId);
                })->count(),
            'unique_clients'          => (clone $conversationsInRange)->distinct()->count('phone_number'),
            'new_clients'             => \App\Models\Client::whereBetween('created_at', [$dateFromFull, $dateToFull])->count(),
        ];

        $dailyStats = DailyStatistic::whereBetween('date', [$dateFrom, $dateTo])
            ->orderBy('date', 'asc')
            ->get();

        $menuStats = [
            'informations' => DailyStatistic::whereBetween('date', [$dateFrom, $dateTo])->sum('menu_informations'),
            'demandes'     => DailyStatistic::whereBetween('date', [$dateFrom, $dateTo])->sum('menu_demandes'),
            'paris'        => DailyStatistic::whereBetween('date', [$dateFrom, $dateTo])->sum('menu_paris'),
            'encaissement' => DailyStatistic::whereBetween('date', [$dateFrom, $dateTo])->sum('menu_encaissement'),
            'reclamations' => DailyStatistic::whereBetween('date', [$dateFrom, $dateTo])->sum('menu_reclamations'),
            'plaintes'     => DailyStatistic::whereBetween('date', [$dateFrom, $dateTo])->sum('menu_plaintes'),
            'conseiller'   => DailyStatistic::whereBetween('date', [$dateFrom, $dateTo])->sum('menu_conseiller'),
            'faq'          => DailyStatistic::whereBetween('date', [$dateFrom, $dateTo])->sum('menu_faq'),
        ];

        $statusStats = $this->inboxQuery()
            ->whereBetween('started_at', [$dateFromFull, $dateToFull])
            ->select('status', DB::raw('count(*) as count'))
            ->groupBy('status')
            ->pluck('count', 'status')
            ->toArray();

        $popularPaths = $this->inboxQuery()
            ->whereBetween('started_at', [$dateFromFull, $dateToFull])
            ->whereNotNull('menu_path')
            ->select('menu_path', DB::raw('count(*) as count'))
            ->groupBy('menu_path')
            ->orderBy('count', 'desc')
            ->limit(10)
            ->get();

        $peakHours = $this->inboxQuery()
            ->whereBetween('started_at', [$dateFromFull, $dateToFull])
            ->select(DB::raw('HOUR(started_at) as hour'), DB::raw('count(*) as count'))
            ->groupBy('hour')
            ->orderBy('hour', 'asc')
            ->get();

        $eventStats = ConversationEvent::whereHas('conversation', function ($q) use ($dateFromFull, $dateToFull, $inboxId) {
                $q->whereBetween('started_at', [$dateFromFull, $dateToFull]);
                if ($inboxId) $q->where('inbox_id', $inboxId);
            })
            ->select('event_type', DB::raw('count(*) as count'))
            ->groupBy('event_type')
            ->orderBy('count', 'desc')
            ->get();

        $widgetStats = ConversationEvent::where('event_type', 'free_input')
            ->whereHas('conversation', function ($q) use ($dateFromFull, $dateToFull, $inboxId) {
                $q->whereBetween('started_at', [$dateFromFull, $dateToFull]);
                if ($inboxId) $q->where('inbox_id', $inboxId);
            })
            ->whereNotNull('widget_name')
            ->select('widget_name', DB::raw('count(*) as count'))
            ->groupBy('widget_name')
            ->orderBy('count', 'desc')
            ->get();

        return view('bot-tracking.analytics.index', compact('stats', 'dailyStats', 'menuStats', 'statusStats', 'popularPaths', 'peakHours', 'eventStats', 'widgetStats', 'dateFrom', 'dateTo'));
    }

    /**
     * Display search page for free inputs
     */
    public function search(Request $request)
    {
        $inboxId = $this->inboxId();

        $query = ConversationEvent::with('conversation')
            ->where('event_type', 'free_input')
            ->whereHas('conversation', function ($q) use ($inboxId) {
                if ($inboxId) $q->where('inbox_id', $inboxId);
            });

        if ($request->filled('search')) {
            $query->where('user_input', 'like', '%' . $request->search . '%');
        }

        if ($request->filled('date_from')) {
            $query->whereDate('event_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('event_at', '<=', $request->date_to);
        }

        $freeInputs = $query->orderBy('event_at', 'desc')
            ->paginate(20)
            ->withQueryString();

        return view('bot-tracking.analytics.search', compact('freeInputs'));
    }
}
