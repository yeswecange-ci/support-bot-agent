@extends('layouts.app')

@section('title', 'Flow Twilio — ' . $game->name)

@section('content')
<div class="flex-1 overflow-y-auto bg-gray-50">
    <div class="max-w-5xl mx-auto px-4 sm:px-6 py-6">

        {{-- Header --}}
        <div class="flex items-center justify-between mb-6">
            <div class="flex items-center gap-3">
                <a href="{{ route('gamification.show', $game->slug) }}"
                   class="w-8 h-8 flex items-center justify-center rounded-lg text-gray-400 hover:bg-gray-200 hover:text-gray-600 transition">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                    </svg>
                </a>
                <div>
                    <h1 class="text-xl font-bold text-gray-900">Flow Twilio Studio</h1>
                    <p class="text-sm text-gray-500">{{ $game->name }}</p>
                </div>
            </div>

            <div class="flex items-center gap-3">
                <a href="{{ route('gamification.flow', $game->slug) }}"
                   class="inline-flex items-center gap-2 px-3 py-2 text-sm font-medium text-gray-600 bg-white border border-gray-200 rounded-lg hover:bg-gray-50 transition">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                    </svg>
                    Regénérer
                </a>
                <button onclick="copyFlow()"
                        class="inline-flex items-center gap-2 px-4 py-2 text-sm font-medium text-white bg-violet-600 rounded-lg hover:bg-violet-700 transition shadow-sm">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"/>
                    </svg>
                    <span id="copy-label">Copier le JSON</span>
                </button>
            </div>
        </div>

        {{-- Instructions --}}
        <div class="bg-indigo-50 border border-indigo-200 rounded-xl p-5 mb-5">
            <h2 class="text-sm font-semibold text-indigo-800 mb-3">Comment importer ce flow dans Twilio Studio</h2>
            <ol class="space-y-2">
                @foreach([
                    'Copiez le JSON ci-dessous avec le bouton "Copier le JSON"',
                    'Ouvrez <strong>Twilio Studio</strong> → <em>Flows</em> → <em>Create new Flow</em>',
                    'Choisissez "Import from JSON" et collez le JSON',
                    'Cliquez sur <em>Next</em> puis <em>Publish</em>',
                    'Associez ce flow à votre numéro WhatsApp Twilio',
                ] as $i => $step)
                <li class="flex items-start gap-2.5 text-sm text-indigo-700">
                    <span class="w-5 h-5 rounded-full bg-indigo-200 text-indigo-700 flex items-center justify-center text-xs font-bold flex-shrink-0 mt-0.5">{{ $i + 1 }}</span>
                    <span>{!! $step !!}</span>
                </li>
                @endforeach
            </ol>
        </div>

        {{-- URLs des webhooks --}}
        <div class="bg-white rounded-xl border border-gray-200 p-5 mb-5">
            <h2 class="text-sm font-semibold text-gray-700 mb-3">URLs des webhooks</h2>
            <div class="space-y-2">
                @foreach([
                    ['POST', '/check', 'Vérification éligibilité'],
                    ['POST', '/save-name', 'Enregistrement du nom'],
                    ['POST', '/answer', 'Soumission des réponses'],
                    ['POST', '/complete', 'Complétion de la participation'],
                ] as $wh)
                <div class="flex items-center gap-3">
                    <span class="text-xs font-mono font-bold text-gray-400 w-10">{{ $wh[0] }}</span>
                    <code class="flex-1 text-xs font-mono bg-gray-50 border border-gray-200 px-3 py-1.5 rounded-lg text-gray-700 truncate">
                        {{ $webhookBase }}{{ $wh[1] }}
                    </code>
                    <span class="text-xs text-gray-400">{{ $wh[2] }}</span>
                </div>
                @endforeach
            </div>
        </div>

        {{-- JSON du flow --}}
        <div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
            <div class="flex items-center justify-between px-5 py-3 border-b border-gray-100 bg-gray-50">
                <span class="text-xs font-semibold text-gray-500 uppercase tracking-wider">Flow JSON</span>
                <span class="text-xs text-gray-400">
                    Généré le {{ now()->format('d/m/Y à H:i') }}
                    @if($game->synced_at)· Dernière sync : {{ $game->synced_at->format('d/m/Y H:i') }}@endif
                </span>
            </div>
            <pre id="flow-json" class="p-5 text-xs font-mono text-gray-700 overflow-x-auto max-h-[60vh] overflow-y-auto leading-relaxed">{{ json_encode($flow, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) }}</pre>
        </div>

    </div>
</div>

@push('scripts')
<script>
function copyFlow() {
    const json = document.getElementById('flow-json').textContent;
    navigator.clipboard.writeText(json).then(() => {
        const label = document.getElementById('copy-label');
        label.textContent = 'Copié !';
        setTimeout(() => label.textContent = 'Copier le JSON', 2000);
    });
}
</script>
@endpush
@endsection
