<?php

use App\Http\Controllers\Webhook\TwilioWebhookController;
use App\Http\Controllers\Webhook\ChatwootWebhookController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes — Webhooks
|--------------------------------------------------------------------------
|
| Ces routes ne nécessitent PAS l'auth Laravel.
| Elles sont protégées par la signature Twilio et le secret Chatwoot.
|
*/

Route::prefix('webhooks')->group(function () {

    // Twilio Studio → Handoff vers Chatwoot
    Route::post('/twilio/handoff', [TwilioWebhookController::class, 'handoff'])
        ->middleware(\App\Http\Middleware\ValidateTwilioSignature::class)
        ->name('webhook.twilio.handoff');

    // Chatwoot → Agent message → Twilio WhatsApp
    Route::post('/chatwoot', [ChatwootWebhookController::class, 'handle'])
        ->name('webhook.chatwoot');
});
