@extends('layouts.app')

@section('title', $game ? 'Modifier ' . $game->name : 'Nouveau jeu')

@section('content')
<div class="flex-1 overflow-y-auto bg-gray-50">
    <div class="max-w-3xl mx-auto px-4 sm:px-6 py-6">

        {{-- Header --}}
        <div class="flex items-center gap-3 mb-6">
            <a href="{{ $game ? route('gamification.show', $game->slug) : route('gamification.index') }}"
               class="w-8 h-8 flex items-center justify-center rounded-lg text-gray-400 hover:bg-gray-200 hover:text-gray-600 transition">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                </svg>
            </a>
            <div>
                <h1 class="text-xl font-bold text-gray-900">{{ $game ? 'Modifier le jeu' : 'Nouveau jeu' }}</h1>
                @if($game)
                <p class="text-sm text-gray-500 font-mono">{{ $game->slug }}</p>
                @endif
            </div>
        </div>

        @if(session('success'))
        <div class="mb-4 px-4 py-3 bg-green-50 border border-green-200 text-green-800 text-sm rounded-lg">
            {{ session('success') }}
        </div>
        @endif

        @if($errors->any())
        <div class="mb-4 px-4 py-3 bg-red-50 border border-red-200 text-red-800 text-sm rounded-lg">
            <ul class="list-disc list-inside space-y-1">
                @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
        @endif

        {{-- Formulaire principal --}}
        <form method="POST"
              action="{{ $game ? route('gamification.update', $game->slug) : route('gamification.store') }}"
              class="space-y-5">
            @csrf
            @if($game) @method('PUT') @endif

            {{-- Informations de base --}}
            <div class="bg-white rounded-xl border border-gray-200 p-5 space-y-4">
                <h2 class="text-sm font-semibold text-gray-700">Informations du jeu</h2>

                <div>
                    <label class="block text-xs font-medium text-gray-600 mb-1.5">Nom du jeu *</label>
                    <input type="text" name="name" required
                           value="{{ old('name', $game?->name) }}"
                           placeholder="Ex: Grand Quiz Sportcash Été 2026"
                           class="w-full px-3.5 py-2.5 text-sm border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-violet-500 focus:border-transparent">
                </div>

                <div>
                    <label class="block text-xs font-medium text-gray-600 mb-1.5">Description</label>
                    <textarea name="description" rows="2"
                              placeholder="Description affichée aux participants..."
                              class="w-full px-3.5 py-2.5 text-sm border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-violet-500 focus:border-transparent resize-none">{{ old('description', $game?->description) }}</textarea>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-medium text-gray-600 mb-1.5">Type de jeu *</label>
                        <select name="type" required
                                class="w-full px-3.5 py-2.5 text-sm border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-violet-500 bg-white">
                            <option value="quiz"       {{ old('type', $game?->type) === 'quiz'       ? 'selected' : '' }}>Quiz</option>
                            <option value="free_text"  {{ old('type', $game?->type) === 'free_text'  ? 'selected' : '' }}>Réponse libre</option>
                            <option value="vote"       {{ old('type', $game?->type) === 'vote'       ? 'selected' : '' }}>Vote</option>
                            <option value="prediction" {{ old('type', $game?->type) === 'prediction' ? 'selected' : '' }}>Pronostic</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-600 mb-1.5">Éligibilité *</label>
                        <select name="eligibility" required
                                class="w-full px-3.5 py-2.5 text-sm border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-violet-500 bg-white">
                            <option value="all"          {{ old('eligibility', $game?->eligibility) === 'all'          ? 'selected' : '' }}>Tout le monde</option>
                            <option value="clients_only" {{ old('eligibility', $game?->eligibility) === 'clients_only' ? 'selected' : '' }}>Clients uniquement</option>
                        </select>
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-medium text-gray-600 mb-1.5">Date de début</label>
                        <input type="datetime-local" name="start_date"
                               value="{{ old('start_date', $game?->start_date?->format('Y-m-d\TH:i')) }}"
                               class="w-full px-3.5 py-2.5 text-sm border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-violet-500">
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-600 mb-1.5">Date de fin</label>
                        <input type="datetime-local" name="end_date"
                               value="{{ old('end_date', $game?->end_date?->format('Y-m-d\TH:i')) }}"
                               class="w-full px-3.5 py-2.5 text-sm border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-violet-500">
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-medium text-gray-600 mb-1.5">Limite de participants</label>
                    <input type="number" name="max_participants" min="1"
                           value="{{ old('max_participants', $game?->max_participants) }}"
                           placeholder="Laisser vide pour illimité"
                           class="w-full px-3.5 py-2.5 text-sm border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-violet-500">
                </div>

                <div>
                    <label class="block text-xs font-medium text-gray-600 mb-1.5">Message de fin (merci)</label>
                    <textarea name="thank_you_message" rows="2"
                              placeholder="Merci pour votre participation ! Bonne chance !"
                              class="w-full px-3.5 py-2.5 text-sm border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-violet-500 resize-none">{{ old('thank_you_message', $game?->thank_you_message) }}</textarea>
                </div>
            </div>

            <div class="flex justify-end gap-3">
                <a href="{{ $game ? route('gamification.show', $game->slug) : route('gamification.index') }}"
                   class="px-4 py-2.5 text-sm font-medium text-gray-600 bg-white border border-gray-200 rounded-lg hover:bg-gray-50 transition">
                    Annuler
                </a>
                <button type="submit"
                        class="px-4 py-2.5 text-sm font-medium text-white bg-violet-600 rounded-lg hover:bg-violet-700 transition shadow-sm">
                    {{ $game ? 'Enregistrer les modifications' : 'Créer le jeu' }}
                </button>
            </div>
        </form>

        {{-- Section questions (affichée seulement si le jeu existe déjà) --}}
        @if($game)
        <div class="mt-6 bg-white rounded-xl border border-gray-200 p-5">
            <div class="flex items-center justify-between mb-4">
                <h2 class="text-sm font-semibold text-gray-700">Questions ({{ $game->questions->count() }})</h2>
            </div>

            {{-- Liste des questions existantes --}}
            @forelse($game->questions as $question)
            <div class="flex items-start justify-between py-3 border-b border-gray-100 last:border-0">
                <div class="flex-1 min-w-0 mr-3">
                    <div class="flex items-center gap-2 mb-1">
                        <span class="text-xs font-bold text-violet-600">Q{{ $question->order }}</span>
                        <span class="text-xs px-1.5 py-0.5 bg-gray-100 text-gray-500 rounded font-medium">{{ $question->type }}</span>
                    </div>
                    <p class="text-sm text-gray-800">{{ $question->text }}</p>
                    @if($question->options)
                    <div class="mt-1 flex flex-wrap gap-1">
                        @foreach($question->options as $opt)
                        <span class="text-xs px-2 py-0.5 bg-violet-50 text-violet-600 rounded">{{ $opt }}</span>
                        @endforeach
                    </div>
                    @endif
                    @if($question->correct_answer)
                    <p class="text-xs text-green-600 mt-1">Réponse : {{ $question->correct_answer }}</p>
                    @endif
                </div>
                <form method="POST" action="{{ route('gamification.questions.destroy', [$game->slug, $question->id]) }}">
                    @csrf @method('DELETE')
                    <button type="submit" onclick="return confirm('Supprimer cette question ?')"
                            class="w-7 h-7 flex items-center justify-center text-gray-300 hover:text-red-500 hover:bg-red-50 rounded-md transition flex-shrink-0">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                        </svg>
                    </button>
                </form>
            </div>
            @empty
            <p class="text-sm text-gray-400 text-center py-4">Aucune question pour l'instant</p>
            @endforelse

            {{-- Formulaire d'ajout de question --}}
            <div class="mt-4 pt-4 border-t border-gray-100">
                <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-3">Ajouter une question</p>
                <form method="POST" action="{{ route('gamification.questions.store', $game->slug) }}" class="space-y-3">
                    @csrf

                    <div>
                        <label class="block text-xs font-medium text-gray-600 mb-1">Texte de la question *</label>
                        <textarea name="text" rows="2" required
                                  placeholder="Quelle est la capitale de la Côte d'Ivoire ?"
                                  class="w-full px-3 py-2 text-sm border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-violet-500 resize-none"></textarea>
                    </div>

                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block text-xs font-medium text-gray-600 mb-1">Type *</label>
                            <select name="type" id="q-type" required
                                    onchange="toggleQuestionFields()"
                                    class="w-full px-3 py-2 text-sm border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-violet-500 bg-white">
                                <option value="mcq">QCM</option>
                                <option value="free_text">Réponse libre</option>
                                <option value="vote">Vote</option>
                                <option value="prediction">Pronostic</option>
                            </select>
                        </div>
                        <div id="field-correct" class="">
                            <label class="block text-xs font-medium text-gray-600 mb-1">Bonne réponse</label>
                            <input type="text" name="correct_answer"
                                   placeholder="Ex: Abidjan"
                                   class="w-full px-3 py-2 text-sm border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-violet-500">
                        </div>
                    </div>

                    <div id="field-options" class="">
                        <label class="block text-xs font-medium text-gray-600 mb-1">Options (une par ligne)</label>
                        <textarea name="options" rows="3"
                                  placeholder="Abidjan&#10;Yamoussoukro&#10;Bouaké"
                                  class="w-full px-3 py-2 text-sm border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-violet-500 resize-none font-mono text-xs"></textarea>
                    </div>

                    <button type="submit"
                            class="inline-flex items-center gap-2 px-3 py-2 text-sm font-medium text-violet-600 border border-violet-200 bg-violet-50 rounded-lg hover:bg-violet-100 transition">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                        </svg>
                        Ajouter la question
                    </button>
                </form>
            </div>
        </div>
        @endif

    </div>
</div>

@push('scripts')
<script>
function toggleQuestionFields() {
    const type = document.getElementById('q-type').value;
    const optionsDiv = document.getElementById('field-options');
    const correctDiv = document.getElementById('field-correct');

    if (type === 'mcq' || type === 'vote') {
        optionsDiv.classList.remove('hidden');
    } else {
        optionsDiv.classList.add('hidden');
    }
}
// Init
toggleQuestionFields();
</script>
@endpush
@endsection
