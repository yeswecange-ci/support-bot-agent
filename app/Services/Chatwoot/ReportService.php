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
     * Stats globales pour le dashboard (enriched)
     */
    public function getDashboardStats(string $period = 'today'): array
    {
        [$since, $until] = $this->resolvePeriod($period);

        return [
            'summary'             => $this->safeAccountSummary($since, $until),
            'conversations_count' => $this->safeReport('conversations_count', $since, $until),
            'incoming_messages'   => $this->safeReport('incoming_messages_count', $since, $until),
            'outgoing_messages'   => $this->safeReport('outgoing_messages_count', $since, $until),
            'resolutions'         => $this->safeReport('resolutions_count', $since, $until),
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
     * Stats completes pour la page statistiques
     */
    public function getFullStats(string $period = 'week'): array
    {
        [$since, $until] = $this->resolvePeriod($period);

        $accountSummary = $this->safeAccountSummary($since, $until);
        $counts = $this->safeCounts();

        $convTrend = $this->safeReport('conversations_count', $since, $until);
        $resolutionTrend = $this->safeReport('resolutions_count', $since, $until);
        $incomingTrend = $this->safeReport('incoming_messages_count', $since, $until);
        $outgoingTrend = $this->safeReport('outgoing_messages_count', $since, $until);

        return [
            'summary'           => $accountSummary,
            'counts'            => $counts,
            'trends'            => $convTrend,
            'resolution_trends' => $resolutionTrend,
            'incoming_trends'   => $incomingTrend,
            'outgoing_trends'   => $outgoingTrend,
            'period'            => ['since' => $since, 'until' => $until],
        ];
    }

    /**
     * Classement des agents avec summary
     */
    public function getAgentLeaderboard(string $period = 'week'): array
    {
        [$since, $until] = $this->resolvePeriod($period);

        try {
            $summary = $this->client->getAgentSummary($since, $until);
            return $summary;
        } catch (\Exception $e) {
            Log::warning('[ReportService] Agent summary unavailable', ['error' => $e->getMessage()]);
            return [];
        }
    }

    /**
     * Tendances conversations par jour
     */
    public function getConversationTrends(string $period = 'week'): array
    {
        [$since, $until] = $this->resolvePeriod($period);

        try {
            $data = $this->client->getAccountReport('conversations_count', $since, $until);
            return $data;
        } catch (\Exception $e) {
            return [];
        }
    }

    /**
     * Tendances messages (incoming + outgoing) par jour
     */
    public function getMessageTrends(string $period = 'week'): array
    {
        [$since, $until] = $this->resolvePeriod($period);

        return [
            'incoming' => $this->safeReport('incoming_messages_count', $since, $until),
            'outgoing' => $this->safeReport('outgoing_messages_count', $since, $until),
        ];
    }

    /**
     * Tendances resolutions par jour
     */
    public function getResolutionTrends(string $period = 'week'): array
    {
        [$since, $until] = $this->resolvePeriod($period);

        return $this->safeReport('resolutions_count', $since, $until);
    }

    private function safeAccountSummary(string $since, string $until): array
    {
        try {
            return $this->client->getAccountSummary($since, $until);
        } catch (\Exception $e) {
            Log::warning('[ReportService] Account summary unavailable', ['error' => $e->getMessage()]);
            return [
                'conversations_count' => 0,
                'incoming_messages_count' => 0,
                'outgoing_messages_count' => 0,
                'avg_first_response_time' => 0,
                'avg_resolution_time' => 0,
                'resolutions_count' => 0,
            ];
        }
    }

    private function safeReport(string $metric, string $since, string $until): array
    {
        try {
            return $this->client->getAccountReport($metric, $since, $until);
        } catch (\Exception $e) {
            Log::warning("[ReportService] Report '{$metric}' unavailable", ['error' => $e->getMessage()]);
            return [];
        }
    }

    private function safeCounts(): array
    {
        try {
            return $this->client->getConversationCounts();
        } catch (\Exception $e) {
            Log::warning('[ReportService] Counts unavailable', ['error' => $e->getMessage()]);
            return ['all_count' => 0, 'mine_count' => 0, 'unassigned_count' => 0, 'assigned_count' => 0];
        }
    }

    public function resolvePeriod(string $period): array
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
            'quarter' => [
                (string) Carbon::now()->firstOfQuarter()->timestamp,
                (string) Carbon::now()->timestamp,
            ],
            default => [
                (string) Carbon::today()->timestamp,
                (string) Carbon::now()->timestamp,
            ],
        };
    }
}
