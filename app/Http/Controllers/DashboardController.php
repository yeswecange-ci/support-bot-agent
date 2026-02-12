<?php

namespace App\Http\Controllers;

use App\Services\Chatwoot\ReportService;
use App\Services\Chatwoot\ConversationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class DashboardController extends Controller
{
    public function __construct(
        private ReportService $reports,
        private ConversationService $conversations,
    ) {}

    public function index(Request $request)
    {
        $period = $request->get('period', 'today');

        try {
            $counts = $this->conversations->getCounts();
        } catch (\Exception $e) {
            Log::warning('[Dashboard] Counts unavailable', ['error' => $e->getMessage()]);
            $counts = ['all_count' => 0, 'mine_count' => 0, 'unassigned_count' => 0, 'assigned_count' => 0];
        }

        return view('dashboard.index', [
            'stats'  => $this->reports->getDashboardStats($period),
            'agents' => $this->reports->getAgentPerformance($period),
            'counts' => $counts,
            'period' => $period,
        ]);
    }
}
