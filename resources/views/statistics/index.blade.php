@extends('layouts.app')
@section('title', 'Statistiques')

@section('content')
<div class="flex flex-col h-full overflow-y-auto bg-gray-50">

    {{-- Header --}}
    <div class="flex-shrink-0 bg-white border-b border-gray-200 px-6 py-4">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-xl font-bold text-gray-900">Statistiques</h1>
                <p class="text-sm text-gray-500 mt-0.5">Vue d'ensemble des performances</p>
            </div>
            <div class="flex gap-1 bg-gray-100 rounded-lg p-0.5">
                @foreach(['today' => 'Aujourd\'hui', 'week' => 'Semaine', 'month' => 'Mois', 'quarter' => 'Trimestre'] as $val => $label)
                    <a href="{{ route('statistics.index', ['period' => $val]) }}"
                       class="px-3 py-1.5 text-xs font-medium rounded-md transition {{ $currentPeriod === $val ? 'bg-white shadow-sm text-primary-700' : 'text-gray-500 hover:text-gray-700' }}">
                        {{ $label }}
                    </a>
                @endforeach
            </div>
        </div>
    </div>

    <div class="p-6 space-y-6">

        {{-- Section 1 : Vue d'ensemble --}}
        @php
            $summary = $stats['summary'] ?? [];
            $counts = $stats['counts'] ?? [];

            $convCount = $summary['conversations_count'] ?? 0;
            $resolutions = $summary['resolutions_count'] ?? 0;
            $avgFirstResp = $summary['avg_first_response_time'] ?? 0;
            $avgResolution = $summary['avg_resolution_time'] ?? 0;
            $incomingMsgs = $summary['incoming_messages_count'] ?? 0;
            $outgoingMsgs = $summary['outgoing_messages_count'] ?? 0;
            $resolutionRate = $convCount > 0 ? round(($resolutions / $convCount) * 100) : 0;

            // Format seconds to human readable
            $fmtTime = function($seconds) {
                if (!$seconds || $seconds == 0) return '-';
                $s = (int) $seconds;
                if ($s < 60) return $s . 's';
                if ($s < 3600) return round($s / 60) . 'min';
                return round($s / 3600, 1) . 'h';
            };
        @endphp

        <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-4">
            <div class="bg-white rounded-xl border border-gray-200 p-4">
                <p class="text-[10px] font-semibold text-gray-400 uppercase tracking-wider">Conversations</p>
                <p class="text-2xl font-bold text-gray-900 mt-1">{{ $convCount }}</p>
            </div>
            <div class="bg-white rounded-xl border border-gray-200 p-4">
                <p class="text-[10px] font-semibold text-gray-400 uppercase tracking-wider">Resolues</p>
                <p class="text-2xl font-bold text-green-600 mt-1">{{ $resolutions }}</p>
            </div>
            <div class="bg-white rounded-xl border border-gray-200 p-4">
                <p class="text-[10px] font-semibold text-gray-400 uppercase tracking-wider">1ere reponse</p>
                <p class="text-2xl font-bold text-blue-600 mt-1">{{ $fmtTime($avgFirstResp) }}</p>
            </div>
            <div class="bg-white rounded-xl border border-gray-200 p-4">
                <p class="text-[10px] font-semibold text-gray-400 uppercase tracking-wider">Resolution moy.</p>
                <p class="text-2xl font-bold text-purple-600 mt-1">{{ $fmtTime($avgResolution) }}</p>
            </div>
            <div class="bg-white rounded-xl border border-gray-200 p-4">
                <p class="text-[10px] font-semibold text-gray-400 uppercase tracking-wider">Taux resolution</p>
                <p class="text-2xl font-bold text-amber-600 mt-1">{{ $resolutionRate }}%</p>
            </div>
            <div class="bg-white rounded-xl border border-gray-200 p-4">
                <p class="text-[10px] font-semibold text-gray-400 uppercase tracking-wider">Messages</p>
                <p class="text-2xl font-bold text-gray-900 mt-1">{{ $incomingMsgs + $outgoingMsgs }}</p>
                <p class="text-[10px] text-gray-400 mt-0.5">{{ $incomingMsgs }} entrants / {{ $outgoingMsgs }} sortants</p>
            </div>
        </div>

        {{-- Section 2 : Repartition par statut --}}
        @php
            $openCount = $counts['mine_count'] ?? 0;
            $unassigned = $counts['unassigned_count'] ?? 0;
            $assigned = $counts['assigned_count'] ?? 0;
            $allCount = $counts['all_count'] ?? 1;
        @endphp
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <div class="bg-white rounded-xl border border-gray-200 p-5">
                <h3 class="text-sm font-semibold text-gray-900 mb-4">Repartition des conversations</h3>
                <div class="space-y-3">
                    @php
                        $statusData = [
                            ['label' => 'Mes conversations', 'count' => $openCount, 'color' => 'bg-primary-500'],
                            ['label' => 'Non assignees', 'count' => $unassigned, 'color' => 'bg-orange-500'],
                            ['label' => 'Assignees', 'count' => $assigned, 'color' => 'bg-blue-500'],
                        ];
                    @endphp
                    @foreach($statusData as $item)
                        @php $pct = $allCount > 0 ? round(($item['count'] / $allCount) * 100) : 0; @endphp
                        <div>
                            <div class="flex items-center justify-between mb-1">
                                <span class="text-xs text-gray-600">{{ $item['label'] }}</span>
                                <span class="text-xs font-semibold text-gray-900">{{ $item['count'] }} <span class="text-gray-400 font-normal">({{ $pct }}%)</span></span>
                            </div>
                            <div class="w-full bg-gray-100 rounded-full h-2">
                                <div class="{{ $item['color'] }} h-2 rounded-full transition-all" style="width: {{ $pct }}%"></div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            {{-- Tendances (barres CSS) --}}
            <div class="bg-white rounded-xl border border-gray-200 p-5">
                <h3 class="text-sm font-semibold text-gray-900 mb-4">Tendance des conversations</h3>
                <div id="trend-chart" class="flex items-end gap-1 h-40">
                    @php
                        $trends = $stats['trends'] ?? [];
                        $maxVal = 1;
                        if (is_array($trends)) {
                            foreach ($trends as $t) {
                                $v = $t['value'] ?? 0;
                                if ($v > $maxVal) $maxVal = $v;
                            }
                        }
                    @endphp
                    @if(is_array($trends) && count($trends) > 0)
                        @foreach($trends as $t)
                            @php
                                $val = $t['value'] ?? 0;
                                $pctH = $maxVal > 0 ? round(($val / $maxVal) * 100) : 0;
                                $date = isset($t['timestamp']) ? \Carbon\Carbon::createFromTimestamp($t['timestamp'])->format('d/m') : '';
                            @endphp
                            <div class="flex-1 flex flex-col items-center gap-1 group relative">
                                <div class="w-full bg-primary-400 rounded-t transition-all hover:bg-primary-500" style="height: {{ max($pctH, 2) }}%"></div>
                                <span class="text-[8px] text-gray-400 truncate">{{ $date }}</span>
                                <div class="hidden group-hover:block absolute -top-6 bg-gray-800 text-white text-[10px] px-1.5 py-0.5 rounded">{{ $val }}</div>
                            </div>
                        @endforeach
                    @else
                        <p class="text-xs text-gray-400 mx-auto self-center">Pas de donnees disponibles</p>
                    @endif
                </div>
            </div>
        </div>

        {{-- Section 3 : Performance agents --}}
        <div class="bg-white rounded-xl border border-gray-200 p-5">
            <h3 class="text-sm font-semibold text-gray-900 mb-4">Performance des agents</h3>
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead>
                        <tr class="border-b border-gray-100">
                            <th class="text-left py-2 px-3 text-[10px] font-semibold text-gray-400 uppercase">#</th>
                            <th class="text-left py-2 px-3 text-[10px] font-semibold text-gray-400 uppercase">Agent</th>
                            <th class="text-right py-2 px-3 text-[10px] font-semibold text-gray-400 uppercase">Conversations</th>
                            <th class="text-right py-2 px-3 text-[10px] font-semibold text-gray-400 uppercase">Resolues</th>
                            <th class="text-right py-2 px-3 text-[10px] font-semibold text-gray-400 uppercase">1ere reponse</th>
                            <th class="text-right py-2 px-3 text-[10px] font-semibold text-gray-400 uppercase">Resolution moy.</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50">
                        @php
                            $leaderboard = is_array($agentLeaderboard) ? $agentLeaderboard : [];
                            // Map agent IDs to names
                            $agentMap = collect($agents)->keyBy('id');
                        @endphp
                        @forelse($leaderboard as $i => $entry)
                            @php
                                $agentId = $entry['id'] ?? null;
                                $agentInfo = $agentId ? ($agentMap[$agentId] ?? null) : null;
                                $agentName = $agentInfo['name'] ?? ('Agent #' . $agentId);
                                $convs = $entry['metric']['conversations_count'] ?? 0;
                                $resolved = $entry['metric']['resolutions_count'] ?? 0;
                                $firstResp = $entry['metric']['avg_first_response_time'] ?? 0;
                                $resMoy = $entry['metric']['avg_resolution_time'] ?? 0;
                            @endphp
                            <tr class="hover:bg-gray-50">
                                <td class="py-2.5 px-3 text-xs text-gray-400 font-mono">{{ $i + 1 }}</td>
                                <td class="py-2.5 px-3">
                                    <div class="flex items-center gap-2">
                                        <div class="w-7 h-7 rounded-full bg-primary-100 text-primary-600 flex items-center justify-center font-semibold text-[10px]">
                                            {{ mb_substr($agentName, 0, 1) }}
                                        </div>
                                        <span class="text-sm font-medium text-gray-900">{{ $agentName }}</span>
                                    </div>
                                </td>
                                <td class="py-2.5 px-3 text-right text-sm font-semibold text-gray-900">{{ $convs }}</td>
                                <td class="py-2.5 px-3 text-right text-sm text-green-600 font-medium">{{ $resolved }}</td>
                                <td class="py-2.5 px-3 text-right text-sm text-gray-600">{{ $fmtTime($firstResp) }}</td>
                                <td class="py-2.5 px-3 text-right text-sm text-gray-600">{{ $fmtTime($resMoy) }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="py-8 text-center text-sm text-gray-400">Aucune donnee disponible pour cette periode</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        {{-- Section 4 : Compteurs rapides --}}
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
            <div class="bg-white rounded-xl border border-gray-200 p-4 text-center">
                <p class="text-3xl font-bold text-gray-900">{{ count($agents) }}</p>
                <p class="text-[10px] text-gray-400 uppercase font-semibold mt-1">Agents actifs</p>
            </div>
            <div class="bg-white rounded-xl border border-gray-200 p-4 text-center">
                <p class="text-3xl font-bold text-primary-600">{{ $counts['all_count'] ?? 0 }}</p>
                <p class="text-[10px] text-gray-400 uppercase font-semibold mt-1">Conv. ouvertes</p>
            </div>
            <div class="bg-white rounded-xl border border-gray-200 p-4 text-center">
                <p class="text-3xl font-bold text-orange-500">{{ $counts['unassigned_count'] ?? 0 }}</p>
                <p class="text-[10px] text-gray-400 uppercase font-semibold mt-1">Non assignees</p>
            </div>
            <div class="bg-white rounded-xl border border-gray-200 p-4 text-center">
                <p class="text-3xl font-bold text-green-600">{{ $incomingMsgs }}</p>
                <p class="text-[10px] text-gray-400 uppercase font-semibold mt-1">Msgs entrants</p>
            </div>
        </div>

    </div>
</div>
@endsection
