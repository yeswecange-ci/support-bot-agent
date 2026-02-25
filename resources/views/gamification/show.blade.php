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
                    Flow Twilio
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

        <div class="grid grid-cols-3 gap-5">

            {{-- Questions --}}
            <div class="bg-white rounded-xl border border-gray-200 p-5">
                <div class="flex items-center justify-between mb-4">
                    <h2 class="text-sm font-semibold text-gray-700">Questions ({{ $game->questions->count() }})</h2>
                    <a href="{{ route('gamification.edit', $game->slug) }}"
                       class="text-xs text-violet-600 hover:underline">Gérer</a>
                </div>

                @forelse($game->questions as $question)
                <div class="py-3 border-b border-gray-100 last:border-0">
                    <div class="flex items-center gap-2 mb-1">
                        <span class="text-xs font-bold text-violet-600">Q{{ $question->order }}</span>
                        <span class="text-xs px-1.5 py-0.5 bg-gray-100 text-gray-500 rounded">{{ $question->type }}</span>
                    </div>
                    <p class="text-sm text-gray-800">{{ $question->text }}</p>
                    @if($question->options)
                    <div class="mt-1 flex flex-wrap gap-1">
                        @foreach($question->options as $opt)
                        <span class="text-xs px-1.5 py-0.5 bg-violet-50 text-violet-600 rounded">{{ $opt }}</span>
                        @endforeach
                    </div>
                    @endif
                </div>
                @empty
                <p class="text-sm text-gray-400 text-center py-6">Aucune question</p>
                @endforelse
            </div>

            {{-- Participants --}}
            <div class="col-span-2 bg-white rounded-xl border border-gray-200 overflow-hidden">
                <div class="px-5 py-4 border-b border-gray-100">
                    <h2 class="text-sm font-semibold text-gray-700">Participants</h2>
                </div>

                @if($game->participations->isEmpty())
                <div class="py-16 text-center">
                    <p class="text-sm text-gray-400">Aucun participant pour l'instant</p>
                </div>
                @else
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead class="bg-gray-50 border-b border-gray-100">
                            <tr>
                                <th class="text-left px-4 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">Nom</th>
                                <th class="text-left px-4 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">Téléphone</th>
                                <th class="text-left px-4 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">Statut</th>
                                <th class="text-left px-4 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">Date</th>
                                <th class="px-4 py-3"></th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @foreach($game->participations as $p)
                            <tr class="hover:bg-gray-50" id="row-{{ $p->id }}">
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
                                <td class="px-4 py-3 text-gray-400 text-xs">
                                    {{ $p->started_at->format('d/m/Y H:i') }}
                                </td>
                                <td class="px-4 py-3">
                                    @if($p->answers->isNotEmpty())
                                    <button onclick="toggleAnswers({{ $p->id }})"
                                            class="text-xs text-violet-600 hover:underline">
                                        Réponses ({{ $p->answers->count() }})
                                    </button>
                                    @endif
                                </td>
                            </tr>
                            @if($p->answers->isNotEmpty())
                            <tr id="answers-{{ $p->id }}" class="hidden bg-violet-50">
                                <td colspan="5" class="px-4 py-3">
                                    <div class="space-y-2">
                                        @foreach($p->answers->sortBy(fn($a) => $a->question?->order) as $answer)
                                        <div class="flex gap-3 text-sm">
                                            <span class="font-medium text-violet-600 flex-shrink-0">
                                                Q{{ $answer->question?->order }} :
                                            </span>
                                            <span class="text-gray-700">{{ $answer->answer_text }}</span>
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
        </div>

    </div>
</div>

@push('scripts')
<script>
function toggleAnswers(id) {
    const row = document.getElementById('answers-' + id);
    row.classList.toggle('hidden');
}
</script>
@endpush
@endsection
