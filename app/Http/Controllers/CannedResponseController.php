<?php

namespace App\Http\Controllers;

use App\Services\Chatwoot\ChatwootClient;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class CannedResponseController extends Controller
{
    public function __construct(
        private ChatwootClient $chatwoot,
    ) {}

    /**
     * Page de gestion des réponses rapides
     * GET /canned-responses
     */
    public function index()
    {
        $responses = $this->chatwoot->listCannedResponses();

        return view('canned-responses.index', [
            'responses' => $responses,
        ]);
    }

    /**
     * AJAX — Lister (pour autocomplétion)
     * GET /ajax/canned-responses
     */
    public function list(Request $request): JsonResponse
    {
        $search = $request->get('search');
        $responses = $this->chatwoot->listCannedResponses($search);

        return response()->json($responses);
    }

    /**
     * AJAX — Créer
     * POST /ajax/canned-responses
     */
    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'short_code' => 'required|string|max:50',
            'content'    => 'required|string|max:4096',
        ]);

        $result = $this->chatwoot->createCannedResponse(
            $request->short_code,
            $request->content,
        );

        return response()->json($result, 201);
    }

    /**
     * AJAX — Modifier
     * PUT /ajax/canned-responses/{id}
     */
    public function update(int $id, Request $request): JsonResponse
    {
        $request->validate([
            'short_code' => 'required|string|max:50',
            'content'    => 'required|string|max:4096',
        ]);

        $result = $this->chatwoot->updateCannedResponse(
            $id,
            $request->short_code,
            $request->content,
        );

        return response()->json($result);
    }

    /**
     * AJAX — Supprimer
     * DELETE /ajax/canned-responses/{id}
     */
    public function destroy(int $id): JsonResponse
    {
        $this->chatwoot->deleteCannedResponse($id);

        return response()->json(['success' => true]);
    }
}
