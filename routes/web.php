<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ConversationController;
use App\Http\Controllers\AgentController;
use App\Http\Controllers\CannedResponseController;
use App\Http\Controllers\LabelController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\TeamController;
use App\Http\Controllers\InboxController;
use App\Http\Controllers\StatisticsController;
use Illuminate\Support\Facades\Route;

// ── Route principale → login ──────────────────────────
Route::get('/', function () {
    if (!auth()->check()) {
        return redirect()->route('login');
    }
    return auth()->user()->isAdmin()
        ? redirect()->route('dashboard')
        : redirect()->route('conversations.index');
});

// ── App (authentifié) ─────────────────────────────────
Route::middleware('auth')->group(function () {

    // Conversations (accessible a tous)
    Route::get('/conversations', [ConversationController::class, 'index'])
        ->name('conversations.index');
    Route::get('/conversations/{conversationId}', [ConversationController::class, 'show'])
        ->name('conversations.show');

    // Canned Responses (accessible a tous)
    Route::get('/canned-responses', [CannedResponseController::class, 'index'])
        ->name('canned-responses.index');

    // Contacts (accessible a tous)
    Route::get('/contacts', [ContactController::class, 'index'])
        ->name('contacts.index');

    // Admin only pages
    Route::middleware('admin')->group(function () {
        Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
        Route::get('/teams', [TeamController::class, 'index'])->name('teams.index');
        Route::get('/agents', [AgentController::class, 'index'])->name('agents.index');
        Route::get('/statistics', [StatisticsController::class, 'index'])->name('statistics.index');
    });

    // AJAX — Polling & Actions
    Route::prefix('ajax')->group(function () {
        // Conversations (static routes FIRST)
        Route::get('/conversations/counts', [ConversationController::class, 'counts'])
            ->name('ajax.counts');
        Route::get('/conversations/list-update', [ConversationController::class, 'listUpdate'])
            ->name('ajax.listUpdate');
        Route::post('/conversations/filter', [ConversationController::class, 'filter'])
            ->name('ajax.filter');
        // Conversations (dynamic routes)
        Route::get('/conversations/{conversationId}/panel', [ConversationController::class, 'panel'])
            ->name('ajax.panel');
        Route::get('/conversations/{conversationId}/poll', [ConversationController::class, 'poll'])
            ->name('ajax.poll');
        Route::post('/conversations/{conversationId}/messages', [ConversationController::class, 'sendMessage'])
            ->name('ajax.send');
        Route::post('/conversations/{conversationId}/status', [ConversationController::class, 'toggleStatus'])
            ->name('ajax.status');
        Route::post('/conversations/{conversationId}/read', [ConversationController::class, 'markRead'])
            ->name('ajax.read');
        Route::post('/conversations/{conversationId}/typing', [ConversationController::class, 'typing'])
            ->name('ajax.typing');
        Route::get('/conversations/{conversationId}/messages-before', [ConversationController::class, 'messagesBefore'])
            ->name('ajax.messagesBefore');
        Route::post('/conversations/{conversationId}/assign', [ConversationController::class, 'assign'])
            ->name('ajax.assign');
        Route::delete('/conversations/{conversationId}/messages/{messageId}', [ConversationController::class, 'deleteMessage'])
            ->name('ajax.deleteMessage');
        Route::delete('/conversations/{conversationId}', [ConversationController::class, 'destroy'])
            ->name('ajax.deleteConversation');

        // Twilio Templates
        Route::get('/twilio/templates', [ConversationController::class, 'twilioTemplates'])
            ->name('ajax.twilio.templates');
        Route::post('/conversations/{conversationId}/template-message', [ConversationController::class, 'sendTemplateMessage'])
            ->name('ajax.sendTemplate');

        // Labels
        Route::get('/labels', [LabelController::class, 'list'])->name('ajax.labels');
        Route::post('/labels', [LabelController::class, 'store'])->name('ajax.labels.store');
        Route::get('/conversations/{conversationId}/labels', [LabelController::class, 'conversationLabels'])
            ->name('ajax.conversation.labels');
        Route::post('/conversations/{conversationId}/labels', [LabelController::class, 'updateConversationLabels'])
            ->name('ajax.conversation.labels.update');

        // Canned Responses
        Route::get('/canned-responses', [CannedResponseController::class, 'list'])
            ->name('ajax.canned');
        Route::post('/canned-responses', [CannedResponseController::class, 'store'])
            ->name('ajax.canned.store');
        Route::put('/canned-responses/{id}', [CannedResponseController::class, 'update'])
            ->name('ajax.canned.update');
        Route::delete('/canned-responses/{id}', [CannedResponseController::class, 'destroy'])
            ->name('ajax.canned.destroy');

        // Contacts
        Route::get('/contacts/search', [ContactController::class, 'search'])
            ->name('ajax.contact.search');
        Route::post('/contacts', [ContactController::class, 'store'])
            ->name('ajax.contact.store');
        Route::get('/contacts/{contactId}', [ContactController::class, 'show'])
            ->name('ajax.contact');
        Route::put('/contacts/{contactId}', [ContactController::class, 'update'])
            ->name('ajax.contact.update');
        Route::get('/contacts/{contactId}/conversations', [ContactController::class, 'conversations'])
            ->name('ajax.contact.conversations');
        Route::get('/contacts/{contactId}/notes', [ContactController::class, 'notes'])
            ->name('ajax.contact.notes');
        Route::post('/contacts/{contactId}/notes', [ContactController::class, 'storeNote'])
            ->name('ajax.contact.notes.store');
        Route::delete('/contacts/{contactId}/notes/{noteId}', [ContactController::class, 'destroyNote'])
            ->name('ajax.contact.notes.destroy');
        Route::delete('/contacts/{contactId}', [ContactController::class, 'destroy'])
            ->name('ajax.contact.destroy');

        // Notifications
        Route::get('/notifications', [NotificationController::class, 'list'])
            ->name('ajax.notifications');
        Route::post('/notifications/{id}/read', [NotificationController::class, 'markRead'])
            ->name('ajax.notifications.read');
        Route::post('/notifications/read-all', [NotificationController::class, 'markAllRead'])
            ->name('ajax.notifications.readAll');

        // Teams (list + assign accessible a tous, CRUD admin only)
        Route::get('/teams', [TeamController::class, 'list'])->name('ajax.teams');
        Route::post('/conversations/{conversationId}/team', [TeamController::class, 'assignTeam'])
            ->name('ajax.assign.team');

        // Admin-only AJAX routes
        Route::middleware('admin')->group(function () {
            // Teams CRUD
            Route::post('/teams', [TeamController::class, 'store'])->name('ajax.teams.store');
            Route::put('/teams/{teamId}', [TeamController::class, 'update'])->name('ajax.teams.update');
            Route::delete('/teams/{teamId}', [TeamController::class, 'destroy'])->name('ajax.teams.destroy');
            Route::post('/teams/{teamId}/members', [TeamController::class, 'addMembers'])->name('ajax.teams.members.add');
            Route::delete('/teams/{teamId}/members', [TeamController::class, 'removeMembers'])->name('ajax.teams.members.remove');

            // Agents CRUD
            Route::post('/agents', [AgentController::class, 'store'])->name('ajax.agents.store');
            Route::put('/agents/{agentId}', [AgentController::class, 'update'])->name('ajax.agents.update');
            Route::delete('/agents/{agentId}', [AgentController::class, 'destroy'])->name('ajax.agents.destroy');

            // Inboxes settings
            Route::get('/inboxes', [InboxController::class, 'list'])->name('ajax.inboxes');
            Route::get('/inboxes/{inboxId}/settings', [InboxController::class, 'getSettings'])->name('ajax.inboxes.settings');
            Route::post('/inboxes/{inboxId}/auto-assignment', [InboxController::class, 'updateAutoAssignment'])->name('ajax.inboxes.autoAssign');

            // Statistics
            Route::get('/statistics/data', [StatisticsController::class, 'data'])->name('ajax.statistics.data');
        });
    });

    // Profile
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // AJAX — Agent availability
    Route::post('/ajax/profile/availability', [ProfileController::class, 'toggleAvailability'])
        ->name('ajax.availability');
    Route::get('/ajax/profile/availability', [ProfileController::class, 'getAvailability'])
        ->name('ajax.availability.get');
});

require __DIR__.'/auth.php';
