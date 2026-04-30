<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\OrganizerNotification;
use Illuminate\Http\JsonResponse;

class NotificationController extends Controller
{
    public function index(): JsonResponse
    {
        $notifications = OrganizerNotification::query()
            ->where('user_id', auth()->id())
            ->latest()
            ->get()
            ->map(function (OrganizerNotification $notification) {
                return [
                    'id' => $notification->id,
                    'type' => $notification->type,
                    'message' => $notification->message,
                    'data' => $notification->data,
                    'read_at' => $notification->read_at,
                    'created_at' => $notification->created_at,
                ];
            });

        return response()->json([
            'message' => 'Notifications retrieved successfully.',
            'data' => $notifications,
        ]);
    }

    public function markAsRead(int $id): JsonResponse
    {
        $notification = OrganizerNotification::query()
            ->where('user_id', auth()->id())
            ->find($id);

        if (! $notification) {
            return response()->json([
                'message' => 'Notification not found.',
            ], 404);
        }

        if (! $notification->read_at) {
            $notification->update([
                'read_at' => now(),
            ]);
        }

        return response()->json([
            'message' => 'Notification marked as read.',
            'data' => [
                'id' => $notification->id,
                'type' => $notification->type,
                'message' => $notification->message,
                'data' => $notification->data,
                'read_at' => $notification->read_at,
                'created_at' => $notification->created_at,
            ],
        ]);
    }
}
