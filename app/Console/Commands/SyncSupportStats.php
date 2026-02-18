<?php

namespace App\Console\Commands;

use App\Models\SupportAgentStat;
use App\Models\SupportDailyStat;
use App\Services\Chatwoot\ChatwootClient;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class SyncSupportStats extends Command
{
    protected $signature = 'stats:sync
                            {--period=all : Periode a synchroniser (today|week|month|quarter|all)}';

    protected $description = 'Synchronise les stats support client depuis Chatwoot vers la base de donnees locale';

    private array $periods = ['today', 'week', 'month', 'quarter'];

    public function __construct(private ChatwootClient $client)
    {
        parent::__construct();
    }

    public function handle(): int
    {
        $period = $this->option('period');
        $targets = $period === 'all' ? $this->periods : [$period];

        $this->info('[stats:sync] Demarrage synchronisation (' . implode(', ', $targets) . ')');

        // Recuperer les compteurs live (open / pending / resolved)
        $counts = $this->fetchCounts();
        $this->line("  → Compteurs live : open={$counts['open_count']}, pending={$counts['pending_count']}, resolved={$counts['resolved_count']}");

        // Essayer les resume API (retournent 404 sur certaines instances)
        $reportsAvailable = $this->checkReportsApi();
        if (!$reportsAvailable) {
            $this->warn('  ~ API Reports non disponible — utilisation des compteurs conversation uniquement');
        }

        foreach ($targets as $p) {
            $this->syncPeriod($p, $counts, $reportsAvailable);
        }

        $this->info('[stats:sync] Synchronisation terminee.');
        return self::SUCCESS;
    }

    // ─────────────────────────────────────────────────────

    private function syncPeriod(string $period, array $counts, bool $reportsAvailable): void
    {
        [$since, $until] = $this->resolvePeriod($period);
        $date = Carbon::today()->toDateString();

        $this->line("  → Periode [{$period}]");

        // Valeurs par defaut = compteurs live
        $data = [
            'conversations_count'     => $counts['open_count'] + $counts['pending_count'] + $counts['resolved_count'],
            'resolutions_count'       => $counts['resolved_count'],
            'incoming_messages_count' => 0,
            'outgoing_messages_count' => 0,
            'avg_first_response_time' => 0,
            'avg_resolution_time'     => 0,
            'open_count'              => $counts['open_count'],
            'pending_count'           => $counts['pending_count'],
            'resolved_count'          => $counts['resolved_count'],
            'trend_data'              => null,
        ];

        // Si l'API reports est disponible, enrichir avec les vraies stats
        if ($reportsAvailable) {
            try {
                $summary = $this->client->getAccountSummary($since, $until);
                $data['conversations_count']     = $summary['conversations_count'] ?? $data['conversations_count'];
                $data['resolutions_count']       = $summary['resolutions_count'] ?? $data['resolutions_count'];
                $data['incoming_messages_count'] = $summary['incoming_messages_count'] ?? 0;
                $data['outgoing_messages_count'] = $summary['outgoing_messages_count'] ?? 0;
                $data['avg_first_response_time'] = (int) ($summary['avg_first_response_time'] ?? 0);
                $data['avg_resolution_time']     = (int) ($summary['avg_resolution_time'] ?? 0);

                // Trend data
                $trend = $this->safeFetch(fn() => $this->client->getAccountReport('conversations_count', $since, $until));
                $data['trend_data'] = $trend ?: null;

            } catch (\Exception $e) {
                $this->warn("    ~ Summary API failed: " . $e->getMessage());
            }
        }

        SupportDailyStat::upsertForPeriod($date, $period, $data);
        $this->line("    ✓ Stats compte sauvegardees (conv: {$data['conversations_count']}, resolved: {$data['resolutions_count']})");

        // Agent stats (seulement si API disponible)
        if ($reportsAvailable) {
            try {
                $agentSummary = $this->client->getAgentSummary($since, $until);
                $agents = $this->fetchAgentsMap();
                $this->syncAgentStats($agentSummary, $agents, $date, $period);
            } catch (\Exception $e) {
                $this->warn("    ~ Agent summary non disponible: " . $e->getMessage());
            }
        }
    }

    // ─────────────────────────────────────────────────────

    private function syncAgentStats(array $agentSummary, array $agentsMap, string $date, string $period): void
    {
        $entries = $agentSummary['data'] ?? $agentSummary;
        if (empty($entries)) {
            $this->line("    ~ Aucune donnee agent");
            return;
        }

        $count = 0;
        foreach ($entries as $entry) {
            $agentId = $entry['id'] ?? null;
            if (!$agentId) continue;

            $metric = $entry['metric'] ?? $entry;
            $agentInfo = $agentsMap[$agentId] ?? null;

            SupportAgentStat::upsertForAgent($date, $period, (int) $agentId, [
                'agent_name'              => $agentInfo['name'] ?? ('Agent #' . $agentId),
                'agent_email'             => $agentInfo['email'] ?? null,
                'conversations_count'     => (int) ($metric['conversations_count'] ?? 0),
                'resolutions_count'       => (int) ($metric['resolutions_count'] ?? 0),
                'avg_first_response_time' => (int) ($metric['avg_first_response_time'] ?? 0),
                'avg_resolution_time'     => (int) ($metric['avg_resolution_time'] ?? 0),
            ]);
            $count++;
        }
        $this->line("    ✓ {$count} agent(s) synchronises");
    }

    // ─────────────────────────────────────────────────────

    private function fetchCounts(): array
    {
        try {
            return $this->client->getConversationCounts();
        } catch (\Exception $e) {
            $this->warn('  ~ Impossible de recuperer les compteurs: ' . $e->getMessage());
            return ['open_count' => 0, 'pending_count' => 0, 'resolved_count' => 0, 'all_count' => 0, 'mine_count' => 0, 'unassigned_count' => 0, 'assigned_count' => 0];
        }
    }

    private function checkReportsApi(): bool
    {
        [$since, $until] = $this->resolvePeriod('today');
        try {
            $this->client->getAccountSummary($since, $until);
            return true;
        } catch (\Exception $e) {
            return false;
        }
    }

    private function fetchAgentsMap(): array
    {
        try {
            $agents = $this->client->listAgents();
            return collect($agents)->keyBy('id')->toArray();
        } catch (\Exception $e) {
            return [];
        }
    }

    private function safeFetch(callable $fn, mixed $default = []): mixed
    {
        try {
            return $fn();
        } catch (\Exception $e) {
            return $default;
        }
    }

    private function resolvePeriod(string $period): array
    {
        return match ($period) {
            'today'   => [(string) Carbon::today()->timestamp,                  (string) Carbon::now()->timestamp],
            'week'    => [(string) Carbon::now()->startOfWeek()->timestamp,     (string) Carbon::now()->timestamp],
            'month'   => [(string) Carbon::now()->startOfMonth()->timestamp,    (string) Carbon::now()->timestamp],
            'quarter' => [(string) Carbon::now()->firstOfQuarter()->timestamp,  (string) Carbon::now()->timestamp],
            default   => [(string) Carbon::today()->timestamp,                  (string) Carbon::now()->timestamp],
        };
    }
}
