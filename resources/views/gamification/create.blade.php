@extends('layouts.app')

@section('title', $game ? 'Modifier ' . $game->name : 'Nouveau jeu')

@section('content')
<div class="flex-1 overflow-y-auto bg-gray-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 py-6">

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

        {{-- Formulaire --}}
        <form method="POST" id="game-form"
              action="{{ $game ? route('gamification.update', $game->slug) : route('gamification.store') }}">
            @csrf
            @if($game) @method('PUT') @endif

            <div class="grid grid-cols-2 gap-5">

                {{-- Colonne gauche : infos du jeu --}}
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

                {{-- Colonne droite : questions --}}
                <div class="bg-white rounded-xl border border-gray-200 p-5 flex flex-col">
                    <div class="flex items-center justify-between mb-4">
                        <h2 class="text-sm font-semibold text-gray-700">
                            Questions (<span id="q-count">0</span>)
                        </h2>
                        <button type="button" onclick="addQuestion()"
                                class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-medium text-violet-600 border border-violet-200 bg-violet-50 rounded-lg hover:bg-violet-100 transition">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                            </svg>
                            Ajouter une question
                        </button>
                    </div>

                    {{-- Container des questions --}}
                    <div id="questions-container" class="space-y-3 flex-1">
                        {{-- Questions injectées par JS --}}
                    </div>

                    <div id="empty-questions" class="flex-1 flex items-center justify-center py-8 text-center">
                        <div>
                            <div class="w-10 h-10 bg-violet-50 rounded-xl flex items-center justify-center mx-auto mb-2">
                                <svg class="w-5 h-5 text-violet-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                            </div>
                            <p class="text-xs text-gray-400">Aucune question pour l'instant</p>
                            <button type="button" onclick="addQuestion()"
                                    class="mt-2 text-xs text-violet-600 hover:underline">
                                Ajouter la première question
                            </button>
                        </div>
                    </div>
                </div>

            </div>

            {{-- Actions --}}
            <div class="flex justify-end gap-3 mt-5">
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

    </div>
</div>

{{-- Template pour une question --}}
<template id="question-template">
    <div class="question-card border border-gray-200 rounded-lg p-4 bg-gray-50" data-index="">
        <div class="flex items-center justify-between mb-3">
            <span class="text-xs font-bold text-violet-600 q-label">Q1</span>
            <button type="button" onclick="removeQuestion(this)"
                    class="w-6 h-6 flex items-center justify-center text-gray-300 hover:text-red-500 hover:bg-red-50 rounded transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>

        <div class="space-y-2.5">
            <div>
                <label class="block text-xs font-medium text-gray-600 mb-1">Texte *</label>
                <textarea class="q-text w-full px-3 py-2 text-sm border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-violet-500 resize-none bg-white" rows="2" placeholder="Quelle est...?" required></textarea>
            </div>

            <div class="grid grid-cols-2 gap-2.5">
                <div>
                    <label class="block text-xs font-medium text-gray-600 mb-1">Type *</label>
                    <select class="q-type w-full px-3 py-2 text-sm border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-violet-500 bg-white" onchange="toggleQuestionType(this)">
                        <option value="mcq">QCM</option>
                        <option value="free_text">Réponse libre</option>
                        <option value="vote">Vote</option>
                        <option value="prediction">Pronostic</option>
                    </select>
                </div>
                <div class="q-correct-wrapper">
                    <label class="block text-xs font-medium text-gray-600 mb-1">Bonne réponse</label>
                    <input type="text" class="q-correct w-full px-3 py-2 text-sm border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-violet-500 bg-white" placeholder="Ex: Abidjan">
                </div>
            </div>

            <div class="q-options-wrapper">
                <label class="block text-xs font-medium text-gray-600 mb-1">Options (une par ligne)</label>
                <textarea class="q-options w-full px-3 py-2 text-sm border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-violet-500 resize-none font-mono text-xs bg-white" rows="3" placeholder="Abidjan&#10;Yamoussoukro&#10;Bouaké"></textarea>
            </div>
        </div>
    </div>
</template>

@php
    $jsQuestions = $game
        ? $game->questions->sortBy('order')->values()->map(fn($q) => [
            'text'           => $q->text,
            'type'           => $q->type,
            'options'        => $q->options ? implode("\n", $q->options) : '',
            'correct_answer' => $q->correct_answer ?? '',
        ])->toArray()
        : [];
@endphp

@push('scripts')
<script>
// Questions existantes (pour le mode édition)
const existingQuestions = @json($jsQuestions);

let questionIndex = 0;

function updateCount() {
    const cards = document.querySelectorAll('#questions-container .question-card');
    document.getElementById('q-count').textContent = cards.length;
    document.getElementById('empty-questions').style.display = cards.length > 0 ? 'none' : 'flex';
}

function relabelQuestions() {
    document.querySelectorAll('#questions-container .question-card').forEach((card, i) => {
        card.dataset.index = i;
        card.querySelector('.q-label').textContent = 'Q' + (i + 1);
        // Renommer les inputs
        card.querySelector('.q-text').name    = `questions[${i}][text]`;
        card.querySelector('.q-type').name    = `questions[${i}][type]`;
        card.querySelector('.q-correct').name = `questions[${i}][correct_answer]`;
        card.querySelector('.q-options').name = `questions[${i}][options]`;
    });
}

function addQuestion(data = null) {
    const template = document.getElementById('question-template');
    const clone = template.content.cloneNode(true);
    const card = clone.querySelector('.question-card');

    const i = document.querySelectorAll('#questions-container .question-card').length;
    card.dataset.index = i;
    card.querySelector('.q-label').textContent = 'Q' + (i + 1);

    // Nommer les champs
    card.querySelector('.q-text').name    = `questions[${i}][text]`;
    card.querySelector('.q-type').name    = `questions[${i}][type]`;
    card.querySelector('.q-correct').name = `questions[${i}][correct_answer]`;
    card.querySelector('.q-options').name = `questions[${i}][options]`;

    // Pré-remplir si données fournies
    if (data) {
        card.querySelector('.q-text').value    = data.text || '';
        card.querySelector('.q-type').value    = data.type || 'mcq';
        card.querySelector('.q-correct').value = data.correct_answer || '';
        card.querySelector('.q-options').value = data.options || '';
    }

    document.getElementById('questions-container').appendChild(card);

    // Appliquer la visibilité des champs selon le type
    const typeSelect = document.getElementById('questions-container')
        .lastElementChild.querySelector('.q-type');
    toggleQuestionType(typeSelect);

    updateCount();
}

function removeQuestion(btn) {
    const card = btn.closest('.question-card');
    card.remove();
    relabelQuestions();
    updateCount();
}

function toggleQuestionType(select) {
    const card = select.closest('.question-card');
    const optionsWrapper = card.querySelector('.q-options-wrapper');
    const type = select.value;

    if (type === 'mcq' || type === 'vote') {
        optionsWrapper.style.display = '';
    } else {
        optionsWrapper.style.display = 'none';
    }
}

// Init : charger les questions existantes
document.addEventListener('DOMContentLoaded', function () {
    if (existingQuestions.length > 0) {
        existingQuestions.forEach(q => addQuestion(q));
    } else {
        updateCount();
    }
});
</script>
@endpush
@endsection
