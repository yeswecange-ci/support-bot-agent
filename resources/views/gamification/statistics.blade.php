@extends('layouts.app')

@section('title', 'Statistiques — ' . $game->name)

@section('content')
<div class="flex-1 overflow-y-auto bg-gray-50">
    <div class="max-w-5xl mx-auto px-4 sm:px-6 py-6">

        {{-- Header --}}
        <div class="flex items-center gap-3 mb-6">
            <a href="{{ route('gamification.show', $game->slug) }}"
               class="w-8 h-8 flex items-center justify-center rounded-lg text-gray-400 hover:bg-gray-200 hover:text-gray-600 transition">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                </svg>
            </a>
            <div>
                <h1 class="text-xl font-bold text-gray-900">Statistiques</h1>
                <p class="text-sm text-gray-500 mt-0.5">{{ $game->name }}</p>
            </div>
        </div>

        {{-- KPIs --}}
        <div class="grid grid-cols-3 gap-4 mb-6">
            <div class="bg-white rounded-xl border border-gray-200 p-5">
                <p class="text-xs font-medium text-gray-500 mb-2">Taux de complétion</p>
                <p class="text-3xl font-bold text-violet-600">{{ $completionRate }}%</p>
                <div class="w-full bg-gray-100 rounded-full h-1.5 mt-2">
                    <div class="bg-violet-600 h-1.5 rounded-full" style="width: {{ $completionRate }}%"></div>
                </div>
                <p class="text-xs text-gray-400 mt-1.5">{{ $completed }} / {{ $total }} participants</p>
            </div>

            <div class="bg-white rounded-xl border border-gray-200 p-5">
                <p class="text-xs font-medium text-gray-500 mb-2">Score moyen (complétés)</p>
                @if($avgScore !== null)
                    <p class="text-3xl font-bold text-green-600">{{ $avgScore }}%</p>
                    <p class="text-xs text-gray-400 mt-2">de bonnes réponses en moyenne</p>
                @else
                    <p class="text-2xl font-bold text-gray-300">—</p>
                    <p class="text-xs text-gray-400 mt-2">pas assez de données</p>
                @endif
            </div>

            <div class="bg-white rounded-xl border border-gray-200 p-5">
                <p class="text-xs font-medium text-gray-500 mb-2">Total participants</p>
                <p class="text-3xl font-bold text-gray-900">{{ $total }}</p>
                <div class="flex gap-3 mt-2 text-xs">
                    <span class="text-green-600">{{ $completed }} complétés</span>
                    <span class="text-amber-500">{{ $started }} en cours</span>
                    <span class="text-red-500">{{ $abandoned }} abandonnés</span>
                </div>
            </div>
        </div>

        {{-- Funnel --}}
        <div class="bg-white rounded-xl border border-gray-200 p-5 mb-6">
            <h2 class="text-sm font-semibold text-gray-700 mb-4">Entonnoir de participation</h2>
            <div class="flex items-center gap-0">
                @php
                    $funnelItems = [
                        ['label' => 'Démarré', 'value' => $funnel['started'], 'color' => 'bg-violet-600'],
                        ['label' => 'Complété', 'value' => $funnel['completed'], 'color' => 'bg-green-500'],
                        ['label' => 'Au moins 1 bonne réponse', 'value' => $funnel['at_least_1_correct'], 'color' => 'bg-blue-500'],
                    ];
                    $maxVal = max(array_column($funnelItems, 'value')) ?: 1;
                @endphp
                @foreach($funnelItems as $i => $item)
                <div class="flex-1 {{ $i > 0 ? 'ml-1' : '' }}">
                    <div class="text-xs text-gray-500 mb-1.5">{{ $item['label'] }}</div>
                    <div class="relative h-12 bg-gray-100 rounded-lg overflow-hidden">
                        <div class="{{ $item['color'] }} h-full rounded-lg transition-all"
                             style="width: {{ $maxVal > 0 ? round($item['value'] / $maxVal * 100) : 0 }}%"></div>
                    </div>
                    <div class="mt-1.5 text-lg font-bold text-gray-900">{{ $item['value'] }}</div>
                    @if($i > 0 && $funnel['started'] > 0)
                    <div class="text-xs text-gray-400">{{ round($item['value'] / $funnel['started'] * 100) }}% du total</div>
                    @endif
                </div>
                @if($i < count($funnelItems) - 1)
                <div class="px-2 text-gray-300 mt-6">→</div>
                @endif
                @endforeach
            </div>
        </div>

        {{-- Analyse par question --}}
        @if(!empty($questionStats))
        <div class="bg-white rounded-xl border border-gray-200 p-5">
            <h2 class="text-sm font-semibold text-gray-700 mb-5">Analyse par question</h2>

            <div class="space-y-6">
                @foreach($questionStats as $qs)
                @php $question = $qs['question']; @endphp
                <div class="pb-6 border-b border-gray-100 last:border-0 last:pb-0">

                    {{-- Titre question --}}
                    <div class="flex items-start justify-between gap-3 mb-3">
                        <div>
                            <span class="text-xs font-bold text-violet-600">Q{{ $question->order }}</span>
                            <span class="ml-1.5 text-xs px-1.5 py-0.5 bg-gray-100 text-gray-500 rounded">{{ $question->type }}</span>
                            <p class="text-sm text-gray-800 mt-1">{{ $question->text }}</p>
                        </div>
                        @if($qs['total'] > 0)
                        <div class="text-right flex-shrink-0">
                            @if($qs['rate'] !== null)
                                <p class="text-xl font-bold {{ $qs['rate'] >= 50 ? 'text-green-600' : 'text-red-600' }}">
                                    {{ $qs['rate'] }}%
                                </p>
                                <p class="text-xs text-gray-400">corrects</p>
                            @else
                                <p class="text-xl font-bold text-gray-700">{{ $qs['total'] }}</p>
                                <p class="text-xs text-gray-400">réponse(s)</p>
                            @endif
                        </div>
                        @endif
                    </div>

                    @if($qs['total'] === 0)
                    <p class="text-xs text-gray-400">Aucune réponse pour cette question.</p>
                    @else

                    {{-- Barre correctes/incorrectes --}}
                    @if($qs['rate'] !== null)
                    <div class="mb-3">
                        <div class="flex gap-1 h-3 rounded-full overflow-hidden bg-gray-100">
                            @if($qs['correct'] > 0)
                            <div class="bg-green-500 h-full" style="width: {{ round($qs['correct'] / $qs['total'] * 100) }}%" title="{{ $qs['correct'] }} corrects"></div>
                            @endif
                            @if($qs['wrong'] > 0)
                            <div class="bg-red-400 h-full" style="width: {{ round($qs['wrong'] / $qs['total'] * 100) }}%" title="{{ $qs['wrong'] }} incorrects"></div>
                            @endif
                            @if($qs['null_count'] > 0)
                            <div class="bg-gray-200 h-full" style="width: {{ round($qs['null_count'] / $qs['total'] * 100) }}%" title="{{ $qs['null_count'] }} sans évaluation"></div>
                            @endif
                        </div>
                        <div class="flex gap-4 mt-1.5 text-xs">
                            <span class="flex items-center gap-1 text-green-600">
                                <span class="w-2 h-2 rounded-full bg-green-500 inline-block"></span>
                                {{ $qs['correct'] }} corrects
                            </span>
                            <span class="flex items-center gap-1 text-red-600">
                                <span class="w-2 h-2 rounded-full bg-red-400 inline-block"></span>
                                {{ $qs['wrong'] }} incorrects
                            </span>
                            @if($qs['null_count'] > 0)
                            <span class="flex items-center gap-1 text-gray-400">
                                <span class="w-2 h-2 rounded-full bg-gray-300 inline-block"></span>
                                {{ $qs['null_count'] }} non évalués
                            </span>
                            @endif
                        </div>
                    </div>
                    @endif

                    {{-- Distribution des réponses --}}
                    @if(!empty($qs['distribution']))
                    <div>
                        <p class="text-xs font-medium text-gray-500 mb-2">Distribution des réponses</p>
                        <div class="space-y-1.5">
                            @php $maxDist = max(array_column($qs['distribution'], 'count')) ?: 1; @endphp
                            @foreach(array_slice($qs['distribution'], 0, 8) as $dist)
                            <div class="flex items-center gap-2">
                                <div class="flex-1 min-w-0">
                                    <div class="flex items-center justify-between mb-0.5">
                                        <span class="text-xs text-gray-600 truncate">{{ $dist['text'] }}</span>
                                        <span class="text-xs font-medium text-gray-700 ml-2 flex-shrink-0">{{ $dist['count'] }}</span>
                                    </div>
                                    <div class="w-full bg-gray-100 rounded h-1.5">
                                        <div class="bg-violet-400 h-1.5 rounded" style="width: {{ round($dist['count'] / $maxDist * 100) }}%"></div>
                                    </div>
                                </div>
                            </div>
                            @endforeach
                            @if(count($qs['distribution']) > 8)
                            <p class="text-xs text-gray-400">+ {{ count($qs['distribution']) - 8 }} autres réponses</p>
                            @endif
                        </div>
                    </div>
                    @endif

                    @endif {{-- end if total > 0 --}}
                </div>
                @endforeach
            </div>
        </div>
        @else
        <div class="bg-white rounded-xl border border-gray-200 p-5 text-center py-12">
            <p class="text-sm text-gray-400">Ce jeu n'a pas encore de questions.</p>
            <a href="{{ route('gamification.edit', $game->slug) }}"
               class="mt-2 inline-block text-xs text-violet-600 hover:underline">
                Ajouter des questions
            </a>
        </div>
        @endif

    </div>
</div>
@endsection
