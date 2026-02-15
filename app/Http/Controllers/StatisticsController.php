<?php

namespace App\Http\Controllers;

use App\Services\Chatwoot\ReportService;
use App\Services\Chatwoot\ChatwootClient;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class StatisticsController extends Controller
{
    public function __construct(
        private ReportService $reports,
        private ChatwootClient $chatwoot,
    ) {}

    /**
     * Page statistiques
     * GET /statistics
     */
    public function index(Request $request)
    {
        $period = $request->get('period', 'week');

        $stats = $this->reports->getFullStats($period);
        $agents = $this->chatwoot->listAgents();
        $agentLeaderboard = $this->reports->getAgentLeaderboard($period);

        return view('statistics.index', [
            'stats'            => $stats,
            'agents'           => $agents,
            'agentLeaderboard' => $agentLeaderboard,
            'currentPeriod'    => $period,
        ]);
    }

    /**
     * AJAX — Donnees stats pour changement de periode dynamique
     * GET /ajax/statistics/data
     */
    public function data(Request $request): JsonResponse
    {
        $period = $request->get('period', 'week');

        try {
            $stats = $this->reports->getFullStats($period);
            $agentLeaderboard = $this->reports->getAgentLeaderboard($period);
            $trends = $this->reports->getConversationTrends($period);

            return response()->json([
                'stats'      => $stats,
                'leaderboard' => $agentLeaderboard,
                'trends'     => $trends,
            ]);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
}
