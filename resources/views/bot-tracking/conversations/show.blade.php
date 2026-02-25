@extends('layouts.app')

@section('content')
<div class="flex-1 overflow-y-auto bg-gray-50">
    <div class="max-w-5xl mx-auto px-4 sm:px-6 py-6">

        {{-- Back + Header --}}
        <div class="flex items-center gap-3 mb-6">
            <a href="{{ route('bot-tracking.conversations') }}" class="inline-flex items-center gap-1 text-sm text-gray-500 hover:text-gray-700">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                Conversations
            </a>
            <span class="text-gray-300">/</span>
            <span class="text-sm font-medium text-gray-900">{{ $conversation->display_name ?? $conversation->phone_number }}</span>
        </div>

        {{-- Conversation Header Card --}}
        <div class="bg-white rounded-xl border border-gray-200 p-5 mb-6">
            <div class="flex flex-col sm:flex-row sm:items-start sm:justify-between gap-4">
                <div class="flex items-center gap-4">
                    <div class="w-14 h-14 rounded-full bg-indigo-100 flex items-center justify-center flex-shrink-0">
                        <span class="text-xl font-bold text-indigo-700">{{ strtoupper(substr($conversation->display_name ?? '?', 0, 1)) }}</span>
                    </div>
                    <div>
                        <h1 class="text-xl font-bold text-gray-900">{{ $conversation->display_name ?? 'Inconnu' }}</h1>
                        <p class="text-sm text-gray-500">{{ $conversation->phone_number }}</p>
                        @if($conversation->email)
                        <p class="text-sm text-gray-400">{{ $conversation->email }}</p>
                        @endif
                    </div>
                </div>
                <div class="flex flex-wrap items-center gap-2">
                    @php
                        $sm = ['active' => 'bg-green-100 text-green-700', 'completed' => 'bg-blue-100 text-blue-700', 'timeout' => 'bg-amber-100 text-amber-700', 'abandoned' => 'bg-gray-100 text-gray-600'];
                        $sc = $sm[$conversation->status] ?? 'bg-gray-100 text-gray-600';
                    @endphp
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium {{ $sc }}">{{ ucfirst($conversation->status) }}</span>
                    @if($conversation->is_client === true || $conversation->is_client == 1)
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-indigo-100 text-indigo-700">Client Sportcash</span>
                    @elseif($conversation->is_client === false || $conversation->is_client == 0)
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-orange-100 text-orange-700">Non-client</span>
                    @endif
                    @if($conversation->carte_vip)
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-yellow-100 text-yellow-700">VIP</span>
                    @endif
                </div>
            </div>

            {{-- Info Grid --}}
            <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 mt-5 pt-5 border-t border-gray-100">
                <div>
                    <p class="text-xs text-gray-400 uppercase tracking-wide">Session ID</p>
                    <p class="text-sm font-mono text-gray-700 mt-0.5 truncate">{{ $conversation->session_id ?? '&mdash;' }}</p>
                </div>
                <div>
                    <p class="text-xs text-gray-400 uppercase tracking-wide">Menu actuel</p>
                    <p class="text-sm text-gray-700 mt-0.5">{{ $conversation->current_menu ?? '&mdash;' }}</p>
                </div>
                <div>
                    <p class="text-xs text-gray-400 uppercase tracking-wide">VIN</p>
                    <p class="text-sm font-mono text-gray-700 mt-0.5">{{ $conversation->vin ?? '&mdash;' }}</p>
                </div>
                <div>
                    <p class="text-xs text-gray-400 uppercase tracking-wide">Carte VIP</p>
                    <p class="text-sm text-gray-700 mt-0.5">{{ $conversation->carte_vip ?? '&mdash;' }}</p>
                </div>
                <div>
                    <p class="text-xs text-gray-400 uppercase tracking-wide">Debut</p>
                    <p class="text-sm text-gray-700 mt-0.5">@if($conversation->started_at){{ \Carbon\Carbon::parse($conversation->started_at)->format('d/m/Y H:i') }}@else&mdash;@endif</p>
                </div>
                <div>
                    <p class="text-xs text-gray-400 uppercase tracking-wide">Fin</p>
                    <p class="text-sm text-gray-700 mt-0.5">@if($conversation->ended_at){{ \Carbon\Carbon::parse($conversation->ended_at)->format('d/m/Y H:i') }}@else&mdash;@endif</p>
                </div>
                <div>
                    <p class="text-xs text-gray-400 uppercase tracking-wide">Duree</p>
                    <p class="text-sm text-gray-700 mt-0.5">@if($conversation->duration_seconds){{ round($conversation->duration_seconds / 60) }}min@else&mdash;@endif</p>
                </div>
                <div>
                    <p class="text-xs text-gray-400 uppercase tracking-wide">ID Chatwoot</p>
                    <p class="text-sm font-mono text-gray-700 mt-0.5">{{ $conversation->chatwoot_conversation_id ?? '&mdash;' }}</p>
                </div>
            </div>
        </div>

        {{-- Menu Path --}}
        @if(!empty($conversation->menu_path ?? []))
        <div class="bg-white rounded-xl border border-gray-200 p-5 mb-6">
            <h3 class="text-sm font-semibold text-gray-700 mb-3">Parcours menu</h3>
            <div class="flex flex-wrap items-center gap-2">
                @foreach(($conversation->menu_path ?? []) as $step)
                <span class="inline-flex items-center px-2.5 py-1 rounded-lg text-xs font-medium bg-indigo-50 text-indigo-700 border border-indigo-100">{{ $step }}</span>
                @if(!$loop->last)<span class="text-gray-300 text-sm">&rarr;</span>@endif
                @endforeach
            </div>
        </div>
        @endif

        {{-- Events Timeline --}}
        <div class="bg-white rounded-xl border border-gray-200 p-5">
            <h3 class="text-sm font-semibold text-gray-700 mb-6">Historique des evenements ({{ $conversation->events->count() }})</h3>
            @if($conversation->events->isEmpty())
                <p class="text-sm text-gray-400 text-center py-8">Aucun evenement enregistre.</p>
            @else
            <div class="relative pl-6 space-y-6">
                <div class="absolute left-2 top-2 bottom-2 w-px bg-gray-200"></div>
                @foreach($conversation->events as $event)
                @php
                    $typeColors = [
                        'menu_choice'     => 'bg-indigo-500',  'free_input'       => 'bg-purple-500',
                        'message_received' => 'bg-green-500',  'message_sent'      => 'bg-blue-500',
                        'agent_transfer'  => 'bg-amber-500',  'error'             => 'bg-red-500',
                        'invalid_input'   => 'bg-red-400',
                    ];
                    $dot = $typeColors[$event->event_type] ?? 'bg-gray-400';
                    $ts  = $event->event_at ?? $event->created_at;
                @endphp
                <div class="relative">
                    <div class="absolute -left-6 mt-1 w-3 h-3 rounded-full {{ $dot }} ring-2 ring-white"></div>
                    <div class="pl-2">
                        <div class="flex items-center justify-between mb-1">
                            <span class="text-xs font-semibold text-gray-500 uppercase tracking-wide">{{ str_replace([ '_'], 
\, $event->event_type) }}</span>
                            <span class="text-xs text-gray-400">{{ $ts ? \Carbon\Carbon::parse($ts)->format('d/m/Y H:i:s') : '' }}</span>
                        </div>
                        @if($event->user_input)
                        <div class="bg-gray-50 rounded-lg px-3 py-2 mb-1 border-l-2 border-indigo-400">
                            <p class="text-xs text-gray-500 mb-0.5">Saisie utilisateur</p>
                            <p class="text-sm text-gray-800">{{ $event->user_input }}</p>
                        </div>
                        @endif
                        @if($event->bot_message)
                        <div class="bg-blue-50 rounded-lg px-3 py-2 mb-1 border-l-2 border-blue-400">
                            <p class="text-xs text-blue-500 mb-0.5">Message bot</p>
                            <p class="text-sm text-gray-800">{{ $event->bot_message }}</p>
                        </div>
                        @endif
                        @if($event->widget_name)
                        <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-gray-100 text-gray-600">Widget: {{ $event->widget_name }}</span>
                        @endif
                        @if($event->metadata)
                        <details class="mt-1">
                            <summary class="text-xs text-gray-400 cursor-pointer hover:text-gray-600">Metadata</summary>
                            <pre class="text-xs text-gray-600 bg-gray-50 rounded p-2 mt-1 overflow-x-auto">{{ is_array($event->metadata) ? json_encode($event->metadata, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) : $event->metadata }}</pre>
                        </details>
                        @endif
                    </div>
                </div>
                @endforeach
            </div>
            @endif
        </div>

    </div>
</div>
@endsection
