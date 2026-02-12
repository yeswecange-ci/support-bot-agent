<?php

namespace App\Http\Controllers;

use App\Services\Chatwoot\ChatwootClient;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class NotificationController extends Controller
{
    public function __construct(
        private ChatwootClient $chatwoot,
    ) {}

    /**
     * AJAX — Lister les notifications
     * GET /ajax/notifications
     */
    public function list(Request $request): JsonResponse
    {
        $page = (int) $request->get('page', 1);

        try {
            $data = $this->chatwoot->listNotifications($page);

            return response()->json($data);
        } catch (\Exception $e) {
            return response()->json([
                'data'    => ['payload' => []],
                'meta'    => ['unread_count' => 0, 'count' => 0],
            ]);
        }
    }

    /**
     * AJAX — Marquer une notification comme lue
     * POST /ajax/notifications/{id}/read
     */
    public function markRead(int $id): JsonResponse
    {
        try {
            $this->chatwoot->markNotificationRead($id);
        } catch (\Exception $e) {
            // Silently ignore
        }

        return response()->json(['success' => true]);
    }

    /**
     * AJAX — Tout marquer comme lu
     * POST /ajax/notifications/read-all
     */
    public function markAllRead(): JsonResponse
    {
        try {
            $this->chatwoot->markAllNotificationsRead();
        } catch (\Exception $e) {
            // Silently ignore
        }

        return response()->json(['success' => true]);
    }
}
