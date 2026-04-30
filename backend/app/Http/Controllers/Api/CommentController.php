<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Comment\StoreCommentRequest;
use App\Models\Comment;
use App\Models\Event;
use App\Models\OrganizerNotification;
use Illuminate\Http\JsonResponse;

class CommentController extends Controller
{
    public function index(int $id): JsonResponse
    {
        $event = Event::find($id);

        if (! $event) {
            return response()->json([
                'message' => 'Event not found.',
            ], 404);
        }

        $comments = Comment::with('user')
            ->where('event_id', $event->id)
            ->latest()
            ->get()
            ->map(function (Comment $comment) {
                return [
                    'id' => $comment->id,
                    'body' => $comment->body,
                    'created_at' => $comment->created_at,
                    'user' => [
                        'id' => $comment->user->id,
                        'name' => $comment->user->name,
                    ],
                ];
            });

        return response()->json([
            'message' => 'Comments retrieved successfully.',
            'data' => $comments,
        ]);
    }

    public function store(StoreCommentRequest $request, int $id): JsonResponse
    {
        $event = Event::with('user')->find($id);
        $user = $request->user();

        if (! $event) {
            return response()->json([
                'message' => 'Event not found.',
            ], 404);
        }

        if (! in_array($user->role, ['user', 'organizer'], true)) {
            return response()->json([
                'message' => 'Only users and organizers can comment on events.',
            ], 403);
        }

        $comment = Comment::create([
            'user_id' => $user->id,
            'event_id' => $event->id,
            'body' => $request->validated()['body'],
        ])->load('user');

        if ($event->user_id !== $user->id) {
            OrganizerNotification::create([
                'user_id' => $event->user_id,
                'type' => 'new_comment',
                'message' => $user->name . ' commented on your event.',
                'data' => [
                    'event_id' => $event->id,
                    'event_title' => $event->title,
                    'comment_id' => $comment->id,
                    'comment_body' => $comment->body,
                    'commenter_name' => $user->name,
                ],
            ]);
        }

        return response()->json([
            'message' => 'Comment posted successfully.',
            'data' => [
                'id' => $comment->id,
                'body' => $comment->body,
                'created_at' => $comment->created_at,
                'user' => [
                    'id' => $comment->user->id,
                    'name' => $comment->user->name,
                ],
            ],
        ], 201);
    }
}
