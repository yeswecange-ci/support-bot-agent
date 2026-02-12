@extends('layouts.app')
@section('title', 'Dashboard')

@section('content')
<div class="flex-1 overflow-y-auto p-6">

    {{-- Header --}}
    <div class="flex items-center justify-between mb-6">
        <h1 class="text-2xl font-bold text-gray-900">Dashboard</h1>
        <div class="flex gap-2">
            @foreach(['today' => "Aujourd'hui", 'week' => 'Semaine', 'month' => 'Mois'] as $p => $label)
                <a href="{{ route('dashboard', ['period' => $p]) }}"
                   class="px-3 py-1.5 text-sm rounded-lg transition
                          {{ $period === $p ? 'bg-primary-500 text-white' : 'bg-gray-100 text-gray-600 hover:bg-gray-200' }}">
                    {{ $label }}
                </a>
            @endforeach
        </div>
    </div>

    {{-- Compteurs principaux --}}
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-8">

        {{-- Conversations ouvertes --}}
        <div class="bg-white rounded-xl p-5 border shadow-sm">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500">Ouvertes</p>
                    <p class="text-3xl font-bold text-gray-900 mt-1">{{ $counts['mine_count'] ?? 0 }}</p>
                </div>
                <div class="w-12 h-12 bg-green-100 rounded-xl flex items-center justify-center">
                    <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
                    </svg>
                </div>
            </div>
        </div>

        {{-- Non assignées --}}
        <div class="bg-white rounded-xl p-5 border shadow-sm">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500">Non assignées</p>
                    <p class="text-3xl font-bold text-orange-600 mt-1">{{ $counts['unassigned_count'] ?? 0 }}</p>
                </div>
                <div class="w-12 h-12 bg-orange-100 rounded-xl flex items-center justify-center">
                    <svg class="w-6 h-6 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.34 16.5c-.77.833.192 2.5 1.732 2.5z"/>
                    </svg>
                </div>
            </div>
        </div>

        {{-- Assignées --}}
        <div class="bg-white rounded-xl p-5 border shadow-sm">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500">Assignées</p>
                    <p class="text-3xl font-bold text-gray-900 mt-1">{{ $counts['assigned_count'] ?? 0 }}</p>
                </div>
                <div class="w-12 h-12 bg-blue-100 rounded-xl flex items-center justify-center">
                    <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                    </svg>
                </div>
            </div>
        </div>

        {{-- Total --}}
        <div class="bg-white rounded-xl p-5 border shadow-sm">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500">Total</p>
                    <p class="text-3xl font-bold text-gray-900 mt-1">{{ $counts['all_count'] ?? 0 }}</p>
                </div>
                <div class="w-12 h-12 bg-purple-100 rounded-xl flex items-center justify-center">
                    <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                    </svg>
                </div>
            </div>
        </div>
    </div>

    {{-- Lien rapide --}}
    <div class="flex gap-4">
        <a href="{{ route('conversations.index', ['status' => 'open', 'assignee_type' => 'unassigned']) }}"
           class="flex-1 bg-white rounded-xl p-5 border shadow-sm hover:border-primary-300 transition">
            <h3 class="font-semibold text-gray-900">Conversations non assignées</h3>
            <p class="text-sm text-gray-500 mt-1">Voir les conversations en attente d'assignation</p>
        </a>
        <a href="{{ route('conversations.index', ['status' => 'open']) }}"
           class="flex-1 bg-white rounded-xl p-5 border shadow-sm hover:border-primary-300 transition">
            <h3 class="font-semibold text-gray-900">Toutes les conversations ouvertes</h3>
            <p class="text-sm text-gray-500 mt-1">Gérer les conversations en cours</p>
        </a>
    </div>
</div>
@endsection
