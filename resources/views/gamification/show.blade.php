@extends('layouts.app')

@section('title', $game->name)

@section('content')
<div class="flex-1 overflow-y-auto bg-gray-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 py-6">

        {{-- Header --}}
        <div class="flex items-start justify-between mb-6">
            <div class="flex items-center gap-3">
                <a href="{{ route('gamification.index') }}"
                   class="w-8 h-8 flex items-center justify-center rounded-lg text-gray-400 hover:bg-gray-200 hover:text-gray-600 transition">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                    </svg>
                </a>
                <div>
                    <div class="flex items-center gap-3">
                        <h1 class="text-xl font-bold text-gray-900">{{ $game->name }}</h1>
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
                    </div>
                    <p class="text-sm text-gray-500 mt-0.5">
                        @php $typeLabels = ['quiz' => 'Quiz', 'free_text' => 'Réponse libre', 'vote' => 'Vote', 'prediction' => 'Pronostic']; @endphp
                        {{ $typeLabels[$game->type] ?? $game->type }}
                        @if($game->start_date || $game->end_date)
                         · {{ $game->start_date?->format('d/m/Y') ?? '—' }} → {{ $game->end_date?->format('d/m/Y') ?? '∞' }}
                        @endif
                    </p>
                </div>
            </div>

            <div class="flex items-center gap-2">
                @if($game->status !== 'active')
                <form method="POST" action="{{ route('gamification.activate', $game->slug) }}">
                    @csrf
                    <button type="submit"
                            class="px-3 py-2 text-sm font-medium text-green-600 bg-green-50 border border-green-200 rounded-lg hover:bg-green-100 transition">
                        Activer
                    </button>
                </form>
                @else
                <form method="POST" action="{{ route('gamification.close', $game->slug) }}">
                    @csrf
                    <button type="submit"
                            class="px-3 py-2 text-sm font-medium text-red-600 bg-red-50 border border-red-200 rounded-lg hover:bg-red-100 transition">
                        Clôturer
                    </button>
                </form>
                @endif

                <a href="{{ route('gamification.edit', $game->slug) }}"
                   class="px-3 py-2 text-sm font-medium text-gray-600 bg-white border border-gray-200 rounded-lg hover:bg-gray-50 transition">
                    Éditer
                </a>
                <a href="{{ route('gamification.flow', $game->slug) }}"
                   class="px-3 py-2 text-sm font-medium text-indigo-600 bg-indigo-50 border border-indigo-200 rounded-lg hover:bg-indigo-100 transition">
                    Flow Studio
                </a>
                <a href="{{ route('gamification.export', $game->slug) }}"
                   class="inline-flex items-center gap-2 px-3 py-2 text-sm font-medium text-white bg-violet-600 rounded-lg hover:bg-violet-700 transition shadow-sm">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                    </svg>
                    Export CSV
                </a>
            </div>
        </div>

        @if(session('success'))
        <div class="mb-4 px-4 py-3 bg-green-50 border border-green-200 text-green-800 text-sm rounded-lg">
            {{ session('success') }}
        </div>
        @endif

        {{-- Stats rapides --}}
        <div class="grid grid-cols-4 gap-4 mb-6">
            <div class="bg-white rounded-xl border border-gray-200 p-5 text-center">
                <p class="text-2xl font-bold text-gray-900">{{ $stats['total'] }}</p>
                <p class="text-xs text-gray-500 mt-1">Participants</p>
            </div>
            <div class="bg-white rounded-xl border border-gray-200 p-5 text-center">
                <p class="text-2xl font-bold text-green-600">{{ $stats['completed'] }}</p>
                <p class="text-xs text-gray-500 mt-1">Complétés</p>
            </div>
            <div class="bg-white rounded-xl border border-gray-200 p-5 text-center">
                <p class="text-2xl font-bold text-amber-500">{{ $stats['started'] }}</p>
                <p class="text-xs text-gray-500 mt-1">En cours</p>
            </div>
            <div class="bg-white rounded-xl border border-gray-200 p-5 text-center">
                <p class="text-2xl font-bold text-red-500">{{ $stats['abandoned'] }}</p>
                <p class="text-xs text-gray-500 mt-1">Abandonnés</p>
            </div>
        </div>

        {{-- Onglets --}}
        <div class="bg-white rounded-xl border border-gray-200 overflow-hidden">

            {{-- Barre d'onglets --}}
            <div class="border-b border-gray-200">
                <nav class="flex" id="tab-nav">
                    <button type="button" onclick="switchTab('apercu')" id="tab-apercu"
                            class="tab-btn px-5 py-3.5 text-sm font-medium text-violet-600 border-b-2 border-violet-600 transition">
                        Aperçu
                    </button>
                    <button type="button" onclick="switchTab('participants')" id="tab-participants"
                            class="tab-btn px-5 py-3.5 text-sm font-medium text-gray-500 hover:text-gray-700 border-b-2 border-transparent transition">
                        Participants &amp; Réponses
                        @if($stats['total'] > 0)
                        <span class="ml-1.5 px-1.5 py-0.5 text-xs bg-gray-100 text-gray-500 rounded-full">{{ $stats['total'] }}</span>
                        @endif
                    </button>
                    <button type="button" onclick="switchTab('stats')" id="tab-stats"
                            class="tab-btn px-5 py-3.5 text-sm font-medium text-gray-500 hover:text-gray-700 border-b-2 border-transparent transition">
                        Statistiques
                    </button>
                </nav>
            </div>

            {{-- Onglet Aperçu --}}
            <div id="pane-apercu" class="p-5">
                <div class="grid grid-cols-2 gap-5">

                    {{-- Questions --}}
                    <div>
                        <div class="flex items-center justify-between mb-4">
                            <h3 class="text-sm font-semibold text-gray-700">Questions ({{ $game->questions->count() }})</h3>
                            <a href="{{ route('gamification.edit', $game->slug) }}"
                               class="inline-flex items-center gap-1 text-xs text-violet-600 hover:underline">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                </svg>
                                Gérer les questions
                            </a>
                        </div>

                        @forelse($game->questions->sortBy('order') as $question)
                        <div class="py-3.5 border-b border-gray-100 last:border-0">
                            <div class="flex items-center gap-2 mb-1.5">
                                <span class="text-xs font-bold text-violet-600">Q{{ $question->order }}</span>
                                <span class="text-xs px-1.5 py-0.5 bg-gray-100 text-gray-500 rounded font-medium">{{ $question->type }}</span>
                            </div>
                            <p class="text-sm text-gray-800">{{ $question->text }}</p>
                            @if($question->options)
                            <div class="mt-1.5 flex flex-wrap gap-1">
                                @foreach($question->options as $opt)
                                <span class="text-xs px-2 py-0.5 bg-violet-50 text-violet-600 rounded">{{ $opt }}</span>
                                @endforeach
                            </div>
                            @endif
                            @if($question->correct_answer)
                            <p class="text-xs text-green-600 mt-1.5 flex items-center gap-1">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                </svg>
                                {{ $question->correct_answer }}
                            </p>
                            @endif
                        </div>
                        @empty
                        <div class="py-8 text-center">
                            <p class="text-sm text-gray-400">Aucune question</p>
                            <a href="{{ route('gamification.edit', $game->slug) }}"
                               class="mt-2 inline-block text-xs text-violet-600 hover:underline">
                                Ajouter des questions
                            </a>
                        </div>
                        @endforelse
                    </div>

                    {{-- Infos du jeu --}}
                    <div class="border-l border-gray-100 pl-5">
                        <h3 class="text-sm font-semibold text-gray-700 mb-4">Informations</h3>
                        <dl class="space-y-3">
                            @if($game->description)
                            <div>
                                <dt class="text-xs font-medium text-gray-500">Description</dt>
                                <dd class="mt-0.5 text-sm text-gray-700">{{ $game->description }}</dd>
                            </div>
                            @endif
                            <div>
                                <dt class="text-xs font-medium text-gray-500">Éligibilité</dt>
                                <dd class="mt-0.5 text-sm text-gray-700">
                                    {{ $game->eligibility === 'all' ? 'Tout le monde' : 'Clients uniquement' }}
                                </dd>
                            </div>
                            @if($game->start_date || $game->end_date)
                            <div>
                                <dt class="text-xs font-medium text-gray-500">Période</dt>
                                <dd class="mt-0.5 text-sm text-gray-700">
                                    {{ $game->start_date?->format('d/m/Y H:i') ?? '—' }}
                                    →
                                    {{ $game->end_date?->format('d/m/Y H:i') ?? '∞' }}
                                </dd>
                            </div>
                            @endif
                            @if($game->max_participants)
                            <div>
                                <dt class="text-xs font-medium text-gray-500">Limite</dt>
                                <dd class="mt-0.5 text-sm text-gray-700">{{ $game->max_participants }} participants</dd>
                            </div>
                            @endif
                            @if($game->thank_you_message)
                            <div>
                                <dt class="text-xs font-medium text-gray-500">Message de fin</dt>
                                <dd class="mt-0.5 text-sm text-gray-600 italic">{{ $game->thank_you_message }}</dd>
                            </div>
                            @endif
                        </dl>
                    </div>

                </div>
            </div>

            {{-- Onglet Participants --}}
            <div id="pane-participants" class="hidden">
                {{-- Recherche --}}
                <div class="px-5 py-3 border-b border-gray-100 bg-gray-50">
                    <input type="text" id="participant-search" placeholder="Rechercher par nom ou téléphone..."
                           oninput="filterParticipants(this.value)"
                           class="w-full max-w-sm px-3.5 py-2 text-sm border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-violet-500 bg-white">
                </div>

                @if($game->participations->isEmpty())
                <div class="py-16 text-center">
                    <p class="text-sm text-gray-400">Aucun participant pour l'instant</p>
                </div>
                @else
                <div class="overflow-x-auto">
                    <table class="w-full text-sm" id="participants-table">
                        <thead class="bg-gray-50 border-b border-gray-100">
                            <tr>
                                <th class="text-left px-4 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">Nom</th>
                                <th class="text-left px-4 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">Téléphone</th>
                                <th class="text-left px-4 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">Statut</th>
                                <th class="text-left px-4 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">Score</th>
                                <th class="text-left px-4 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">Début</th>
                                <th class="px-4 py-3"></th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100" id="participants-body">
                            @php $totalQuestions = $game->questions->count(); @endphp
                            @foreach($game->participations as $p)
                            @php
                                $correctCount = $p->answers->where('is_correct', true)->count();
                                $hasScore = $p->answers->whereNotNull('is_correct')->count() > 0;
                            @endphp
                            <tr class="hover:bg-gray-50 participant-row" data-search="{{ strtolower($p->participant_name . ' ' . $p->phone_number) }}">
                                <td class="px-4 py-3 font-medium text-gray-900">
                                    {{ $p->participant_name ?? '—' }}
                                </td>
                                <td class="px-4 py-3 text-gray-500 font-mono text-xs">{{ $p->phone_number }}</td>
                                <td class="px-4 py-3">
                                    @if($p->status === 'completed')
                                        <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-700">Complété</span>
                                    @elseif($p->status === 'abandoned')
                                        <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-700">Abandonné</span>
                                    @else
                                        <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-xs font-medium bg-amber-100 text-amber-700">En cours</span>
                                    @endif
                                </td>
                                <td class="px-4 py-3">
                                    @if($hasScore && $totalQuestions > 0)
                                        <span class="text-sm font-medium {{ $correctCount === $totalQuestions ? 'text-green-600' : 'text-gray-700' }}">
                                            {{ $correctCount }}/{{ $totalQuestions }}
                                        </span>
                                    @else
                                        <span class="text-gray-300 text-xs">—</span>
                                    @endif
                                </td>
                                <td class="px-4 py-3 text-gray-400 text-xs">
                                    {{ $p->started_at->format('d/m/Y H:i') }}
                                </td>
                                <td class="px-4 py-3">
                                    @if($p->answers->isNotEmpty())
                                    <button onclick="toggleAnswers({{ $p->id }})"
                                            class="text-xs text-violet-600 hover:underline whitespace-nowrap">
                                        Voir réponses
                                    </button>
                                    @endif
                                </td>
                            </tr>
                            @if($p->answers->isNotEmpty())
                            <tr id="answers-{{ $p->id }}" class="hidden bg-violet-50">
                                <td colspan="6" class="px-4 py-3">
                                    <div class="space-y-1.5">
                                        @foreach($p->answers->sortBy(fn($a) => $a->question?->order) as $answer)
                                        <div class="flex items-center gap-3 text-sm">
                                            <span class="font-medium text-violet-600 flex-shrink-0 w-10">
                                                Q{{ $answer->question?->order }} :
                                            </span>
                                            <span class="text-gray-700 flex-1">{{ $answer->answer_text }}</span>
                                            @if($answer->is_correct === true)
                                                <span class="inline-flex items-center gap-0.5 px-1.5 py-0.5 rounded text-xs font-medium bg-green-100 text-green-700">
                                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                                    Correct
                                                </span>
                                            @elseif($answer->is_correct === false)
                                                <span class="inline-flex items-center gap-0.5 px-1.5 py-0.5 rounded text-xs font-medium bg-red-100 text-red-700">
                                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                                                    Incorrect
                                                </span>
                                            @else
                                                <span class="px-1.5 py-0.5 rounded text-xs text-gray-400">—</span>
                                            @endif
                                        </div>
                                        @endforeach
                                    </div>
                                </td>
                            </tr>
                            @endif
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @endif
            </div>

            {{-- Onglet Statistiques (résumé) --}}
            <div id="pane-stats" class="hidden p-5">

                {{-- Taux de complétion --}}
                <div class="mb-6">
                    <div class="flex items-center justify-between mb-2">
                        <span class="text-sm font-medium text-gray-700">Taux de complétion</span>
                        <span class="text-sm font-bold text-gray-900">{{ $stats['completion_rate'] }}%</span>
                    </div>
                    <div class="w-full bg-gray-100 rounded-full h-2.5">
                        <div class="bg-violet-600 h-2.5 rounded-full transition-all"
                             style="width: {{ $stats['completion_rate'] }}%"></div>
                    </div>
                    <p class="text-xs text-gray-400 mt-1">{{ $stats['completed'] }} complétés sur {{ $stats['total'] }} participants</p>
                </div>

                {{-- Score par question --}}
                @if($game->questions->isNotEmpty())
                <div class="mb-6">
                    <h3 class="text-sm font-semibold text-gray-700 mb-4">Score par question</h3>
                    <div class="space-y-4">
                        @foreach($game->questions->sortBy('order') as $question)
                        @php $qs = $questionStats[$question->id] ?? null; @endphp
                        <div>
                            <div class="flex items-center justify-between mb-1.5">
                                <span class="text-sm text-gray-700">
                                    <span class="font-medium text-violet-600">Q{{ $question->order }}</span>
                                    — {{ mb_substr($question->text, 0, 60) }}{{ strlen($question->text) > 60 ? '...' : '' }}
                                </span>
                                @if($qs && $qs['total'] > 0 && $qs['rate'] !== null)
                                    <span class="text-sm font-bold text-gray-900 ml-2 flex-shrink-0">
                                        {{ $qs['correct'] }}/{{ $qs['total'] }}
                                        <span class="text-xs font-normal text-gray-400">({{ $qs['rate'] }}%)</span>
                                    </span>
                                @elseif($qs && $qs['total'] > 0)
                                    <span class="text-xs text-gray-400 ml-2">{{ $qs['total'] }} réponse(s)</span>
                                @else
                                    <span class="text-xs text-gray-300 ml-2">Aucune réponse</span>
                                @endif
                            </div>
                            @if($qs && $qs['total'] > 0 && $qs['rate'] !== null)
                            <div class="w-full bg-gray-100 rounded-full h-2">
                                <div class="bg-green-500 h-2 rounded-full" style="width: {{ $qs['rate'] }}%"></div>
                            </div>
                            @endif
                        </div>
                        @endforeach
                    </div>
                </div>
                @endif

                {{-- Lien stats complètes --}}
                <div class="pt-4 border-t border-gray-100">
                    <a href="{{ route('gamification.statistics', $game->slug) }}"
                       class="inline-flex items-center gap-2 px-4 py-2.5 text-sm font-medium text-violet-600 border border-violet-200 bg-violet-50 rounded-lg hover:bg-violet-100 transition">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                        </svg>
                        Voir les statistiques complètes
                    </a>
                </div>

            </div>

        </div>{{-- /tabs --}}

    </div>
</div>

@push('scripts')
<script>
function switchTab(name) {
    // Masquer tous les panneaux
    ['apercu', 'participants', 'stats'].forEach(t => {
        document.getElementById('pane-' + t).classList.add('hidden');
        const btn = document.getElementById('tab-' + t);
        btn.classList.remove('text-violet-600', 'border-violet-600');
        btn.classList.add('text-gray-500', 'border-transparent');
    });

    // Afficher le panneau actif
    document.getElementById('pane-' + name).classList.remove('hidden');
    const activeBtn = document.getElementById('tab-' + name);
    activeBtn.classList.remove('text-gray-500', 'border-transparent');
    activeBtn.classList.add('text-violet-600', 'border-violet-600');
}

function toggleAnswers(id) {
    const row = document.getElementById('answers-' + id);
    row.classList.toggle('hidden');
}

function filterParticipants(q) {
    const lower = q.toLowerCase();
    document.querySelectorAll('.participant-row').forEach(row => {
        const search = row.dataset.search || '';
        row.style.display = search.includes(lower) ? '' : 'none';

        // Masquer aussi la ligne réponses si la ligne principale est masquée
        const pid = row.querySelector('[onclick]')?.getAttribute('onclick')?.match(/\d+/)?.[0];
        if (pid) {
            const answerRow = document.getElementById('answers-' + pid);
            if (answerRow) {
                answerRow.style.display = search.includes(lower) ? '' : 'none';
            }
        }
    });
}
</script>
@endpush
@endsection
