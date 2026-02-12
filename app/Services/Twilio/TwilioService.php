<?php

namespace App\Services\Twilio;

use Twilio\Rest\Client;
use Illuminate\Support\Facades\Log;

class TwilioService
{
    private Client $client;
    private string $from;

    public function __construct()
    {
        $this->client = new Client(
            config('services.twilio.sid'),
            config('services.twilio.auth_token')
        );
        $this->from = config('services.twilio.whatsapp_from');
    }

    /**
     * Envoyer un message WhatsApp via Twilio
     */
    public function sendWhatsApp(string $to, string $body): void
    {
        if (!str_starts_with($to, 'whatsapp:')) {
            $to = "whatsapp:{$to}";
        }

        try {
            $this->client->messages->create($to, [
                'from' => $this->from,
                'body' => $body,
            ]);

            Log::info('WhatsApp envoyé', ['to' => $to, 'body' => mb_substr($body, 0, 50)]);
        } catch (\Exception $e) {
            Log::error('Erreur envoi WhatsApp', [
                'to'    => $to,
                'error' => $e->getMessage(),
            ]);
            throw $e;
        }
    }
}
