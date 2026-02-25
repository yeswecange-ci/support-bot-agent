@extends('layouts.app')

@section('title', 'Gamification — Dashboard')

@section('content')
<div class="flex-1 overflow-y-auto bg-gray-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 py-6">

        {{-- Header --}}
        <div class="flex items-center justify-between mb-6">
            <div>
                <h1 class="text-xl font-bold text-gray-900">Gamification</h1>
                <p class="text-sm text-gray-500 mt-0.5">Gérez vos jeux et concours WhatsApp</p>
            </div>
            <a href="{{ route('gamification.create') }}"
               class="inline-flex items-center gap-2 px-4 py-2.5 bg-violet-600 text-white text-sm font-medium rounded-lg hover:bg-violet-700 transition shadow-sm">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
                Nouveau jeu
            </a>
        </div>

        @if(session('success'))
        <div class="mb-4 px-4 py-3 bg-green-50 border border-green-200 text-green-800 text-sm rounded-lg">
            {{ session('success') }}
        </div>
        @endif

        {{-- Stats globales --}}
        <div class="grid grid-cols-4 gap-4 mb-6">
            <div class="bg-white rounded-xl border border-gray-200 p-5">
                <div class="flex items-center justify-between mb-2">
                    <p class="text-xs font-medium text-gray-500">Jeux actifs</p>
                    <div class="w-8 h-8 bg-green-50 rounded-lg flex items-center justify-center">
                        <span class="w-2 h-2 rounded-full bg-green-500"></span>
                    </div>
                </div>
                <p class="text-2xl font-bold text-gray-900">{{ $globalStats['active_count'] }}</p>
            </div>
            <div class="bg-white rounded-xl border border-gray-200 p-5">
                <div class="flex items-center justify-between mb-2">
                    <p class="text-xs font-medium text-gray-500">Total participants</p>
                    <div class="w-8 h-8 bg-violet-50 rounded-lg flex items-center justify-center">
                        <svg class="w-4 h-4 text-violet-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0"/>
                        </svg>
                    </div>
                </div>
                <p class="text-2xl font-bold text-gray-900">{{ $globalStats['total_participants'] }}</p>
            </div>
            <div class="bg-white rounded-xl border border-gray-200 p-5">
                <div class="flex items-center justify-between mb-2">
                    <p class="text-xs font-medium text-gray-500">Taux complétion</p>
                    <div class="w-8 h-8 bg-blue-50 rounded-lg flex items-center justify-center">
                        <svg class="w-4 h-4 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                        </svg>
                    </div>
                </div>
                @php
                    $globalRate = $globalStats['total_participants'] > 0
                        ? round($globalStats['total_completed'] / $globalStats['total_participants'] * 100)
                        : 0;
                @endphp
                <p class="text-2xl font-bold text-gray-900">{{ $globalRate }}%</p>
                <p class="text-xs text-gray-400 mt-0.5">{{ $globalStats['total_completed'] }} complétés</p>
            </div>
            <div class="bg-white rounded-xl border border-gray-200 p-5">
                <div class="flex items-center justify-between mb-2">
                    <p class="text-xs font-medium text-gray-500">Total jeux</p>
                    <div class="w-8 h-8 bg-amber-50 rounded-lg flex items-center justify-center">
                        <svg class="w-4 h-4 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.828 14.828a4 4 0 01-5.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                </div>
                <p class="text-2xl font-bold text-gray-900">{{ $globalStats['total_games'] }}</p>
            </div>
        </div>

        {{-- Tableau des jeux --}}
        <div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
            <div class="px-5 py-4 border-b border-gray-100">
                <h2 class="text-sm font-semibold text-gray-700">Tous les jeux</h2>
            </div>

            @if($games->isEmpty())
            <div class="py-16 text-center">
                <div class="w-12 h-12 bg-violet-50 rounded-xl flex items-center justify-center mx-auto mb-3">
                    <svg class="w-6 h-6 text-violet-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M14.828 14.828a4 4 0 01-5.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
                <p class="text-gray-500 text-sm">Aucun jeu pour l'instant</p>
                <a href="{{ route('gamification.create') }}" class="mt-3 inline-block text-violet-600 text-sm font-medium hover:underline">
                    Créer votre premier jeu
                </a>
            </div>
            @else
            <table class="w-full text-sm">
                <thead class="bg-gray-50 border-b border-gray-200">
                    <tr>
                        <th class="text-left px-4 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">Nom</th>
                        <th class="text-left px-4 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">Type</th>
                        <th class="text-left px-4 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">Statut</th>
                        <th class="text-right px-4 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">Participants</th>
                        <th class="text-right px-4 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">Complétés</th>
                        <th class="text-left px-4 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">Période</th>
                        <th class="text-right px-4 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @foreach($games as $game)
                    @php
                        $typeLabels = ['quiz' => 'Quiz', 'free_text' => 'Réponse libre', 'vote' => 'Vote', 'prediction' => 'Pronostic'];
                        $completionPct = $game->participations_count > 0
                            ? round($game->completed_count / $game->participations_count * 100)
                            : 0;
                    @endphp
                    <tr class="hover:bg-gray-50 transition">
                        <td class="px-4 py-3">
                            <a href="{{ route('gamification.show', $game->slug) }}"
                               class="font-medium text-gray-900 hover:text-violet-600">{{ $game->name }}</a>
                            <p class="text-xs text-gray-400 font-mono mt-0.5">{{ $game->slug }}</p>
                        </td>
                        <td class="px-4 py-3">
                            <span class="text-gray-600">{{ $typeLabels[$game->type] ?? $game->type }}</span>
                        </td>
                        <td class="px-4 py-3">
                            @if($game->status === 'active')
                                <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-700">
                                    <span class="w-1.5 h-1.5 rounded-full bg-green-500"></span> Actif
                                </span>
                            @elseif($game->status === 'closed')
                                <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-700">
                                    <span class="w-1.5 h-1.5 rounded-full bg-red-500"></span> Clôturé
                                </span>
                            @else
                                <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-600">
                                    <span class="w-1.5 h-1.5 rounded-full bg-gray-400"></span> Brouillon
                                </span>
                            @endif
                        </td>
                        <td class="px-4 py-3 text-right font-medium text-gray-900">
                            {{ $game->participations_count }}
                        </td>
                        <td class="px-4 py-3 text-right">
                            <span class="font-medium text-gray-900">{{ $game->completed_count }}</span>
                            @if($game->participations_count > 0)
                            <span class="text-xs text-gray-400 ml-1">({{ $completionPct }}%)</span>
                            @endif
                        </td>
                        <td class="px-4 py-3 text-gray-500 text-xs">
                            @if($game->start_date || $game->end_date)
                                {{ $game->start_date?->format('d/m/Y') ?? '—' }}
                                →
                                {{ $game->end_date?->format('d/m/Y') ?? '∞' }}
                            @else
                                <span class="text-gray-300">Non définie</span>
                            @endif
                        </td>
                        <td class="px-4 py-3">
                            <div class="flex items-center justify-end gap-2">
                                <a href="{{ route('gamification.show', $game->slug) }}"
                                   class="px-2.5 py-1 text-xs font-medium text-violet-600 hover:bg-violet-50 rounded-md transition">
                                    Voir
                                </a>
                                <a href="{{ route('gamification.statistics', $game->slug) }}"
                                   class="px-2.5 py-1 text-xs font-medium text-blue-600 hover:bg-blue-50 rounded-md transition">
                                    Stats
                                </a>
                                <a href="{{ route('gamification.edit', $game->slug) }}"
                                   class="px-2.5 py-1 text-xs font-medium text-gray-600 hover:bg-gray-100 rounded-md transition">
                                    Éditer
                                </a>
                                @if($game->status !== 'active')
                                <form method="POST" action="{{ route('gamification.activate', $game->slug) }}" class="inline">
                                    @csrf
                                    <button type="submit" class="px-2.5 py-1 text-xs font-medium text-green-600 hover:bg-green-50 rounded-md transition">
                                        Activer
                                    </button>
                                </form>
                                @else
                                <form method="POST" action="{{ route('gamification.close', $game->slug) }}" class="inline">
                                    @csrf
                                    <button type="submit" class="px-2.5 py-1 text-xs font-medium text-red-600 hover:bg-red-50 rounded-md transition">
                                        Clôturer
                                    </button>
                                </form>
                                @endif
                                <a href="{{ route('gamification.export', $game->slug) }}"
                                   class="px-2.5 py-1 text-xs font-medium text-gray-500 hover:bg-gray-100 rounded-md transition">
                                    CSV
                                </a>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
            @endif
        </div>

    </div>
</div>
@endsection
