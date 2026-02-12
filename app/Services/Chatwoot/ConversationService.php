<?php

namespace App\Services\Chatwoot;

use App\DTOs\ConversationDTO;

class ConversationService
{
    public function __construct(
        private ChatwootClient $client
    ) {}

    /**
     * Liste paginée des conversations
     */
    public function list(string $status = 'open', string $assigneeType = 'all', int $page = 1): array
    {
        $response = $this->client->listConversations($status, $assigneeType, $page);

        return [
            'conversations' => collect($response['data']['payload'] ?? [])
                ->map(fn(array $conv) => ConversationDTO::fromArray($conv))
                ->all(),
            'meta' => $response['data']['meta'] ?? [],
        ];
    }

    /**
     * Conversation + messages pour la vue show
     */
    public function getWithMessages(int $conversationId): array
    {
        $conversation = $this->client->getConversation($conversationId);
        $messages     = $this->client->getMessages($conversationId);

        return [
            'conversation' => ConversationDTO::fromArray($conversation),
            'messages'     => $messages['payload'] ?? [],
            'contact'      => $messages['meta']['contact']['payload'][0] ?? null,
            'assignee'     => $messages['meta']['assignee'] ?? null,
        ];
    }

    /**
     * Polling : messages plus récents que lastMessageId
     */
    public function getNewMessages(int $conversationId, int $lastMessageId): array
    {
        $messages = $this->client->getMessages($conversationId);

        return collect($messages['payload'] ?? [])
            ->filter(fn(array $msg) => $msg['id'] > $lastMessageId)
            ->values()
            ->all();
    }

    public function resolve(int $id): array
    {
        return $this->client->toggleStatus($id, 'resolved');
    }

    public function reopen(int $id): array
    {
        return $this->client->toggleStatus($id, 'open');
    }

    public function assign(int $id, int $agentId): array
    {
        return $this->client->assignConversation($id, $agentId);
    }

    public function getCounts(): array
    {
        return $this->client->getConversationCounts();
    }

    public function search(string $query, int $page = 1): array
    {
        $response = $this->client->searchConversations($query, $page);

        return [
            'conversations' => collect($response['data']['payload'] ?? [])
                ->map(fn(array $conv) => ConversationDTO::fromArray($conv))
                ->all(),
            'meta' => $response['data']['meta'] ?? [],
        ];
    }

    /**
     * Filtrage avancé (par label, etc.)
     */
    public function filterByLabel(string $label, string $status = 'open', int $page = 1): array
    {
        $payload = [
            'payload' => [
                [
                    'attribute_key'  => 'labels',
                    'filter_operator' => 'contains',
                    'values'         => [$label],
                    'query_operator'  => null,
                ],
            ],
        ];

        try {
            $response = $this->client->filterConversations($payload, $page);

            return [
                'conversations' => collect($response['payload'] ?? [])
                    ->map(fn(array $conv) => ConversationDTO::fromArray($conv))
                    ->all(),
                'meta' => $response['meta'] ?? [],
            ];
        } catch (\Exception $e) {
            return ['conversations' => [], 'meta' => []];
        }
    }
}
