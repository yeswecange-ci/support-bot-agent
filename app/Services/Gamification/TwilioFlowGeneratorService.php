<?php

namespace App\Services\Gamification;

use App\Models\Game;

class TwilioFlowGeneratorService
{
    private string $baseUrl;

    public function __construct()
    {
        $this->baseUrl = rtrim(config('app.url'), '/');
    }

    /**
     * Génère le JSON complet d'un Flow Twilio Studio pour un jeu.
     */
    public function generate(Game $game): array
    {
        $slug = $game->slug;
        $questions = $game->questions()->get();
        $webhookBase = "{$this->baseUrl}/gamification/webhook/{$slug}";

        $widgets = [];
        $transitions = [];

        $y = 0;
        $x = 0;
        $step = 200;

        // ── Trigger ──────────────────────────────────────────────
        $widgets[] = [
            'name' => 'Trigger',
            'type' => 'trigger',
            'transitions' => [
                ['event' => 'incomingMessage', 'next' => 'check_participant'],
                ['event' => 'incomingCall'],
                ['event' => 'incomingConversationMessage'],
                ['event' => 'incomingRequest'],
                ['event' => 'incomingParent'],
            ],
            'properties' => ['offset' => ['x' => $x, 'y' => $y]],
        ];

        $y += $step;

        // ── HTTP: check_participant ───────────────────────────────
        $widgets[] = [
            'name' => 'check_participant',
            'type' => 'make-http-request',
            'transitions' => [
                ['event' => 'success', 'next' => 'split_eligible'],
                ['event' => 'failed'],
            ],
            'properties' => [
                'offset'       => ['x' => $x, 'y' => $y],
                'method'       => 'POST',
                'url'          => "{$webhookBase}/check",
                'parameters'   => [
                    ['key' => 'phone', 'value' => '{{contact.channel.address}}'],
                ],
                'content_type' => 'application/x-www-form-urlencoded',
            ],
        ];

        $y += $step;

        // ── Split: eligible? ─────────────────────────────────────
        $widgets[] = [
            'name' => 'split_eligible',
            'type' => 'split-based-on',
            'transitions' => [
                ['event' => 'match', 'conditions' => [['friendly_name' => 'eligible', 'arguments' => ['{{widgets.check_participant.parsed.eligible}}'], 'type' => 'equal_to', 'value' => 'true']], 'next' => 'split_name_known'],
                ['event' => 'noMatch', 'next' => 'send_not_eligible'],
            ],
            'properties' => [
                'offset' => ['x' => $x, 'y' => $y],
                'input'  => '{{widgets.check_participant.parsed.eligible}}',
            ],
        ];

        $y += $step;

        // ── Send: not eligible ────────────────────────────────────
        $widgets[] = [
            'name' => 'send_not_eligible',
            'type' => 'send-message',
            'transitions' => [
                ['event' => 'sent'],
            ],
            'properties' => [
                'offset' => ['x' => $x + 300, 'y' => $y],
                'from'   => '{{flow.channel.address}}',
                'to'     => '{{contact.channel.address}}',
                'body'   => "Désolé, vous n'êtes pas éligible à ce jeu ou avez déjà participé.",
            ],
        ];

        // ── Split: name_known? ───────────────────────────────────
        $nameKnownY = $y;
        $widgets[] = [
            'name' => 'split_name_known',
            'type' => 'split-based-on',
            'transitions' => [
                ['event' => 'match', 'conditions' => [['friendly_name' => 'name_known', 'arguments' => ['{{widgets.check_participant.parsed.name_known}}'], 'type' => 'equal_to', 'value' => 'true']], 'next' => $this->firstQuestionWidget($questions)],
                ['event' => 'noMatch', 'next' => 'ask_name'],
            ],
            'properties' => [
                'offset' => ['x' => $x, 'y' => $nameKnownY],
                'input'  => '{{widgets.check_participant.parsed.name_known}}',
            ],
        ];

        $y += $step;

        // ── SendWait: ask_name ───────────────────────────────────
        $widgets[] = [
            'name' => 'ask_name',
            'type' => 'send-and-wait-for-reply',
            'transitions' => [
                ['event' => 'incomingMessage', 'next' => 'save_name'],
                ['event' => 'timeout'],
                ['event' => 'delivery'],
            ],
            'properties' => [
                'offset'  => ['x' => $x - 300, 'y' => $y],
                'from'    => '{{flow.channel.address}}',
                'to'      => '{{contact.channel.address}}',
                'body'    => "Bienvenue ! Quel est votre nom complet pour participer à \"{{widgets.check_participant.parsed.game_name}}\" ?",
                'timeout' => '3600',
            ],
        ];

        $y += $step;

        // ── HTTP: save_name ──────────────────────────────────────
        $widgets[] = [
            'name' => 'save_name',
            'type' => 'make-http-request',
            'transitions' => [
                ['event' => 'success', 'next' => $this->firstQuestionWidget($questions)],
                ['event' => 'failed'],
            ],
            'properties' => [
                'offset'       => ['x' => $x - 300, 'y' => $y],
                'method'       => 'POST',
                'url'          => "{$webhookBase}/save-name",
                'parameters'   => [
                    ['key' => 'phone', 'value' => '{{contact.channel.address}}'],
                    ['key' => 'name',  'value' => '{{widgets.ask_name.inbound.Body}}'],
                ],
                'content_type' => 'application/x-www-form-urlencoded',
            ],
        ];

        // Reset y to after split_name_known for questions column
        $y = $nameKnownY + $step;

        // ── Questions ────────────────────────────────────────────
        $prevWidget = null;
        foreach ($questions as $index => $question) {
            $qNum = $index + 1;
            $qWidgetName = "q{$qNum}";
            $answerWidgetName = "answer_q{$qNum}";
            $nextWidget = isset($questions[$index + 1]) ? "q" . ($qNum + 1) : 'http_complete';

            // Body de la question
            $body = $question->text;
            if (in_array($question->type, ['mcq', 'vote']) && !empty($question->options)) {
                $body .= "\n";
                foreach ($question->options as $i => $opt) {
                    $body .= "\n" . ($i + 1) . ". {$opt}";
                }
            }

            // SendWait: question
            $widgets[] = [
                'name' => $qWidgetName,
                'type' => 'send-and-wait-for-reply',
                'transitions' => [
                    ['event' => 'incomingMessage', 'next' => $answerWidgetName],
                    ['event' => 'timeout'],
                    ['event' => 'delivery'],
                ],
                'properties' => [
                    'offset'  => ['x' => $x, 'y' => $y],
                    'from'    => '{{flow.channel.address}}',
                    'to'      => '{{contact.channel.address}}',
                    'body'    => $body,
                    'timeout' => '3600',
                ],
            ];

            $y += $step;

            // HTTP: answer
            $widgets[] = [
                'name' => $answerWidgetName,
                'type' => 'make-http-request',
                'transitions' => [
                    ['event' => 'success', 'next' => $nextWidget],
                    ['event' => 'failed'],
                ],
                'properties' => [
                    'offset'       => ['x' => $x, 'y' => $y],
                    'method'       => 'POST',
                    'url'          => "{$webhookBase}/answer",
                    'parameters'   => [
                        ['key' => 'phone',          'value' => '{{contact.channel.address}}'],
                        ['key' => 'question_order', 'value' => (string) $qNum],
                        ['key' => 'answer',         'value' => "{{widgets.{$qWidgetName}.inbound.Body}}"],
                    ],
                    'content_type' => 'application/x-www-form-urlencoded',
                ],
            ];

            $y += $step;
            $prevWidget = $answerWidgetName;
        }

        // ── HTTP: complete ───────────────────────────────────────
        $widgets[] = [
            'name' => 'http_complete',
            'type' => 'make-http-request',
            'transitions' => [
                ['event' => 'success', 'next' => 'send_thank_you'],
                ['event' => 'failed'],
            ],
            'properties' => [
                'offset'       => ['x' => $x, 'y' => $y],
                'method'       => 'POST',
                'url'          => "{$webhookBase}/complete",
                'parameters'   => [
                    ['key' => 'phone', 'value' => '{{contact.channel.address}}'],
                ],
                'content_type' => 'application/x-www-form-urlencoded',
            ],
        ];

        $y += $step;

        // ── Send: thank_you ──────────────────────────────────────
        $thankYou = $game->thank_you_message ?: "Merci pour votre participation ! Bonne chance !";
        $widgets[] = [
            'name' => 'send_thank_you',
            'type' => 'send-message',
            'transitions' => [
                ['event' => 'sent'],
            ],
            'properties' => [
                'offset' => ['x' => $x, 'y' => $y],
                'from'   => '{{flow.channel.address}}',
                'to'     => '{{contact.channel.address}}',
                'body'   => $thankYou,
            ],
        ];

        return [
            'description'    => "Flow généré pour le jeu : {$game->name}",
            'states'         => $widgets,
            'initial_state'  => 'Trigger',
            'flags'          => ['allow_concurrent_calls' => true],
        ];
    }

    private function firstQuestionWidget($questions): string
    {
        if ($questions->isEmpty()) {
            return 'http_complete';
        }
        return 'q1';
    }
}
