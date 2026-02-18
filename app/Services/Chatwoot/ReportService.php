<?php

namespace App\Services\Chatwoot;

use App\Models\SupportAgentStat;
use App\Models\SupportDailyStat;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class ReportService
{
    public function __construct(
        private ChatwootClient $client
    ) {}

    // ═══════════════════════════════════════════════════════
    // PUBLIC — Dashboard & Statistics
    // ═══════════════════════════════════════════════════════

    /**
     * Stats globales pour le dashboard (DB-first, API fallback)
     */
    public function getDashboardStats(string $period = 'today'): array
    {
        $dbRow = SupportDailyStat::where('date', today()->toDateString())
            ->where('period', $period)
            ->first();

        if ($dbRow) {
            return [
                'summary'             => $this->summaryFromRow($dbRow),
                'conversations_count' => $dbRow->trend_data ?? [],
                'incoming_messages'   => [],
                'outgoing_messages'   => [],
                'resolutions'         => [],
                'avg_first_response'  => [],
                'avg_resolution_time' => [],
                'counts'              => [
                    'open_count'       => $dbRow->open_count,
                    'pending_count'    => $dbRow->pending_count,
                    'resolved_count'   => $dbRow->resolved_count,
                    'all_count'        => $dbRow->open_count,
                    'mine_count'       => 0,
                    'assigned_count'   => $dbRow->open_count,
                    'unassigned_count' => 0,
                ],
                'source' => 'db',
            ];
        }

        // Fallback API
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
            'source'              => 'api',
        ];
    }

    /**
     * Stats completes pour la page statistiques (DB-first, API fallback)
     */
    public function getFullStats(string $period = 'week'): array
    {
        $dbRow = SupportDailyStat::where('date', today()->toDateString())
            ->where('period', $period)
            ->first();

        if ($dbRow) {
            $trend = $dbRow->trend_data ?? [];
            return [
                'summary'           => $this->summaryFromRow($dbRow),
                'counts'            => [
                    'open_count'       => $dbRow->open_count,
                    'pending_count'    => $dbRow->pending_count,
                    'resolved_count'   => $dbRow->resolved_count,
                    'all_count'        => $dbRow->open_count,
                    'mine_count'       => 0,
                    'assigned_count'   => $dbRow->open_count,
                    'unassigned_count' => 0,
                ],
                'trends'            => $trend,
                'resolution_trends' => [],
                'incoming_trends'   => [],
                'outgoing_trends'   => [],
                'period'            => $this->resolvePeriodTimestamps($period),
                'source'            => 'db',
            ];
        }

        // Fallback API
        [$since, $until] = $this->resolvePeriod($period);
        $accountSummary = $this->safeAccountSummary($since, $until);
        $counts = $this->safeCounts();

        return [
            'summary'           => $accountSummary,
            'counts'            => $counts,
            'trends'            => $this->safeReport('conversations_count', $since, $until),
            'resolution_trends' => $this->safeReport('resolutions_count', $since, $until),
            'incoming_trends'   => $this->safeReport('incoming_messages_count', $since, $until),
            'outgoing_trends'   => $this->safeReport('outgoing_messages_count', $since, $until),
            'period'            => ['since' => $since, 'until' => $until],
            'source'            => 'api',
        ];
    }

    /**
     * Classement des agents (DB-first, API fallback)
     */
    public function getAgentLeaderboard(string $period = 'week'): array
    {
        $rows = SupportAgentStat::where('date', today()->toDateString())
            ->where('period', $period)
            ->orderByDesc('conversations_count')
            ->get();

        if ($rows->isNotEmpty()) {
            return $rows->map(fn($r) => [
                'id'     => $r->chatwoot_agent_id,
                'name'   => $r->agent_name,
                'email'  => $r->agent_email,
                'metric' => [
                    'conversations_count'     => $r->conversations_count,
                    'resolutions_count'       => $r->resolutions_count,
                    'avg_first_response_time' => $r->avg_first_response_time,
                    'avg_resolution_time'     => $r->avg_resolution_time,
                ],
            ])->values()->toArray();
        }

        // Fallback API
        [$since, $until] = $this->resolvePeriod($period);
        try {
            $summary = $this->client->getAgentSummary($since, $until);
            // Normalise les deux formats possibles de l'API
            $entries = $summary['data'] ?? $summary;
            return collect($entries)->map(fn($e) => [
                'id'     => $e['id'] ?? null,
                'name'   => $e['name'] ?? null,
                'email'  => $e['email'] ?? null,
                'metric' => $e['metric'] ?? [
                    'conversations_count'     => $e['conversations_count'] ?? 0,
                    'resolutions_count'       => $e['resolutions_count'] ?? 0,
                    'avg_first_response_time' => $e['avg_first_response_time'] ?? 0,
                    'avg_resolution_time'     => $e['avg_resolution_time'] ?? 0,
                ],
            ])->values()->toArray();
        } catch (\Exception $e) {
            Log::warning('[ReportService] Agent summary unavailable', ['error' => $e->getMessage()]);
            return [];
        }
    }

    /**
     * Derniere date de synchronisation
     */
    public function lastSyncedAt(): ?\Carbon\Carbon
    {
        return SupportDailyStat::lastSyncedAt();
    }

    // ═══════════════════════════════════════════════════════
    // LEGACY helpers (gardés pour compatibilité)
    // ═══════════════════════════════════════════════════════

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

    public function getConversationTrends(string $period = 'week'): array
    {
        [$since, $until] = $this->resolvePeriod($period);
        try {
            return $this->client->getAccountReport('conversations_count', $since, $until);
        } catch (\Exception $e) {
            return [];
        }
    }

    public function getMessageTrends(string $period = 'week'): array
    {
        [$since, $until] = $this->resolvePeriod($period);
        return [
            'incoming' => $this->safeReport('incoming_messages_count', $since, $until),
            'outgoing' => $this->safeReport('outgoing_messages_count', $since, $until),
        ];
    }

    public function getResolutionTrends(string $period = 'week'): array
    {
        [$since, $until] = $this->resolvePeriod($period);
        return $this->safeReport('resolutions_count', $since, $until);
    }

    // ═══════════════════════════════════════════════════════
    // PRIVATE
    // ═══════════════════════════════════════════════════════

    private function summaryFromRow(SupportDailyStat $row): array
    {
        return [
            'conversations_count'     => $row->conversations_count,
            'resolutions_count'       => $row->resolutions_count,
            'incoming_messages_count' => $row->incoming_messages_count,
            'outgoing_messages_count' => $row->outgoing_messages_count,
            'avg_first_response_time' => $row->avg_first_response_time,
            'avg_resolution_time'     => $row->avg_resolution_time,
        ];
    }

    private function safeAccountSummary(string $since, string $until): array
    {
        try {
            return $this->client->getAccountSummary($since, $until);
        } catch (\Exception $e) {
            Log::warning('[ReportService] Account summary unavailable', ['error' => $e->getMessage()]);
            return [
                'conversations_count'     => 0,
                'incoming_messages_count' => 0,
                'outgoing_messages_count' => 0,
                'avg_first_response_time' => 0,
                'avg_resolution_time'     => 0,
                'resolutions_count'       => 0,
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
            return ['all_count' => 0, 'mine_count' => 0, 'unassigned_count' => 0, 'assigned_count' => 0, 'open_count' => 0, 'pending_count' => 0, 'resolved_count' => 0];
        }
    }

    public function resolvePeriod(string $period): array
    {
        return match ($period) {
            'today'   => [(string) Carbon::today()->timestamp,              (string) Carbon::now()->timestamp],
            'week'    => [(string) Carbon::now()->startOfWeek()->timestamp,  (string) Carbon::now()->timestamp],
            'month'   => [(string) Carbon::now()->startOfMonth()->timestamp, (string) Carbon::now()->timestamp],
            'quarter' => [(string) Carbon::now()->firstOfQuarter()->timestamp, (string) Carbon::now()->timestamp],
            default   => [(string) Carbon::today()->timestamp,              (string) Carbon::now()->timestamp],
        };
    }

    private function resolvePeriodTimestamps(string $period): array
    {
        [$since, $until] = $this->resolvePeriod($period);
        return ['since' => $since, 'until' => $until];
    }
}
