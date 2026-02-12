<?php

namespace App\Http\Controllers;

use App\Services\Chatwoot\ChatwootClient;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class ContactController extends Controller
{
    public function __construct(
        private ChatwootClient $chatwoot,
    ) {}

    /**
     * AJAX — Détails complets d'un contact
     * GET /ajax/contacts/{id}
     */
    public function show(int $contactId): JsonResponse
    {
        $contact = $this->chatwoot->getContact($contactId);

        return response()->json($contact);
    }

    /**
     * AJAX — Modifier un contact
     * PUT /ajax/contacts/{id}
     */
    public function update(int $contactId, Request $request): JsonResponse
    {
        $request->validate([
            'name'         => 'sometimes|string|max:255',
            'email'        => 'sometimes|nullable|email|max:255',
            'phone_number' => 'sometimes|nullable|string|max:30',
            'company_name' => 'sometimes|nullable|string|max:255',
        ]);

        $result = $this->chatwoot->updateContact($contactId, $request->only([
            'name', 'email', 'phone_number', 'company_name',
        ]));

        return response()->json($result);
    }

    /**
     * AJAX — Conversations du contact
     * GET /ajax/contacts/{id}/conversations
     */
    public function conversations(int $contactId): JsonResponse
    {
        $data = $this->chatwoot->getContactConversations($contactId);

        return response()->json($data);
    }

    /**
     * AJAX — Notes du contact
     * GET /ajax/contacts/{id}/notes
     */
    public function notes(int $contactId): JsonResponse
    {
        $notes = $this->chatwoot->listContactNotes($contactId);

        return response()->json($notes);
    }

    /**
     * AJAX — Ajouter une note
     * POST /ajax/contacts/{id}/notes
     */
    public function storeNote(int $contactId, Request $request): JsonResponse
    {
        $request->validate(['content' => 'required|string|max:4096']);

        $result = $this->chatwoot->createContactNote($contactId, $request->content);

        return response()->json($result, 201);
    }

    /**
     * AJAX — Supprimer une note
     * DELETE /ajax/contacts/{id}/notes/{noteId}
     */
    public function destroyNote(int $contactId, int $noteId): JsonResponse
    {
        $this->chatwoot->deleteContactNote($contactId, $noteId);

        return response()->json(['success' => true]);
    }
}
