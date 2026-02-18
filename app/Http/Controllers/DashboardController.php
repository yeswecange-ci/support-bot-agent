<?php

namespace App\Http\Controllers;

use App\Services\Chatwoot\ReportService;
use App\Services\Chatwoot\ConversationService;
use App\Services\Chatwoot\ChatwootClient;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;

class DashboardController extends Controller
{
    public function __construct(
        private ReportService $reports,
        private ConversationService $conversations,
        private ChatwootClient $chatwoot,
    ) {}

    public function index(Request $request)
    {
        $period = $request->get('period', 'week');

        try {
            $counts = $this->conversations->getCounts();
        } catch (\Exception $e) {
            Log::warning('[Dashboard] Counts unavailable', ['error' => $e->getMessage()]);
            $counts = ['all_count' => 0, 'mine_count' => 0, 'unassigned_count' => 0, 'assigned_count' => 0, 'open_count' => 0, 'pending_count' => 0, 'resolved_count' => 0];
        }

        try {
            $agents = $this->chatwoot->listAgents();
        } catch (\Exception $e) {
            Log::warning('[Dashboard] Agents unavailable', ['error' => $e->getMessage()]);
            $agents = [];
        }

        return view('dashboard.index', [
            'stats'      => $this->reports->getDashboardStats($period),
            'agents'     => $agents,
            'agentStats' => $this->reports->getAgentLeaderboard($period),
            'counts'     => $counts,
            'period'     => $period,
        ]);
    }

    /**
     * AJAX — Dashboard data for period changes
     */
    public function data(Request $request): JsonResponse
    {
        $period = $request->get('period', 'week');

        try {
            $stats = $this->reports->getDashboardStats($period);
            $agentStats = $this->reports->getAgentLeaderboard($period);
            $counts = $this->conversations->getCounts();

            return response()->json([
                'stats'      => $stats,
                'agentStats' => $agentStats,
                'counts'     => $counts,
            ]);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
}
