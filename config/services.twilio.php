<?php

/**
 * AJOUTER CE BLOC dans votre config/services.php existant
 * (Ne pas remplacer le fichier, juste ajouter dans le tableau)
 */

return [
    // ... autres services existants ...

    'twilio' => [
        'sid'            => env('TWILIO_SID'),
        'auth_token'     => env('TWILIO_AUTH_TOKEN'),
        'whatsapp_from'  => env('TWILIO_WHATSAPP_FROM', 'whatsapp:+14155238886'),
    ],
];
