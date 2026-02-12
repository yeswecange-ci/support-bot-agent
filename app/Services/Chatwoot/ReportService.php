<?php

namespace App\Services\Chatwoot;

use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class ReportService
{
    public function __construct(
        private ChatwootClient $client
    ) {}

    /**
     * Stats globales pour le dashboard
     */
    public function getDashboardStats(string $period = 'today'): array
    {
        [$since, $until] = $this->resolvePeriod($period);

        return [
            'conversations_count' => $this->safeReport('conversations_count', $since, $until),
            'avg_first_response'  => $this->safeReport('avg_first_response_time', $since, $until),
            'avg_resolution_time' => $this->safeReport('avg_resolution_time', $since, $until),
            'counts'              => $this->safeCounts(),
        ];
    }

    /**
     * Performance par agent
     */
    public function getAgentPerformance(string $period = 'week'): array
    {
        [$since, $until] = $this->resolvePeriod($period);

        try {
            return $this->client->getAgentReport('conversations_count', $since, $until);
        } catch (\Exception $e) {
            Log::warning('[ReportService] Agent report unavailable', ['error' => $e->getMessage()]);
            return [];
        }
    }

    /**
     * Appel report avec fallback en cas d'erreur API
     */
    private function safeReport(string $metric, string $since, string $until): array
    {
        try {
            return $this->client->getAccountReport($metric, $since, $until);
        } catch (\Exception $e) {
            Log::warning("[ReportService] Report '{$metric}' unavailable", ['error' => $e->getMessage()]);
            return [];
        }
    }

    /**
     * Compteurs avec fallback
     */
    private function safeCounts(): array
    {
        try {
            return $this->client->getConversationCounts();
        } catch (\Exception $e) {
            Log::warning('[ReportService] Counts unavailable', ['error' => $e->getMessage()]);
            return ['all_count' => 0, 'mine_count' => 0, 'unassigned_count' => 0, 'assigned_count' => 0];
        }
    }

    private function resolvePeriod(string $period): array
    {
        return match ($period) {
            'today' => [
                (string) Carbon::today()->timestamp,
                (string) Carbon::now()->timestamp,
            ],
            'week' => [
                (string) Carbon::now()->startOfWeek()->timestamp,
                (string) Carbon::now()->timestamp,
            ],
            'month' => [
                (string) Carbon::now()->startOfMonth()->timestamp,
                (string) Carbon::now()->timestamp,
            ],
            default => [
                (string) Carbon::today()->timestamp,
                (string) Carbon::now()->timestamp,
            ],
        };
    }
}
