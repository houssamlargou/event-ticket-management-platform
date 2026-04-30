<?php

namespace Database\Seeders;

use App\Models\Comment;
use App\Models\OrganizerNotification;
use Illuminate\Database\Seeder;

class NotificationSeeder extends Seeder
{
    public function run(): void
    {
        Comment::query()
            ->with(['event', 'user'])
            ->orderBy('id')
            ->get()
            ->each(function (Comment $comment) {
                $event = $comment->event;
                $user = $comment->user;

                if (! $event || ! $user || $event->user_id === $user->id) {
                    return;
                }

                $notification = OrganizerNotification::query()
                    ->where('user_id', $event->user_id)
                    ->where('type', 'new_comment')
                    ->where('data->comment_id', $comment->id)
                    ->first();

                $payload = [
                    'message' => $user->name . ' commented on your event.',
                    'data' => [
                        'event_id' => $event->id,
                        'event_title' => $event->title,
                        'comment_id' => $comment->id,
                        'comment_body' => $comment->body,
                        'commenter_name' => $user->name,
                    ],
                ];

                if ($notification) {
                    $notification->update($payload);
                    return;
                }

                OrganizerNotification::query()->create([
                    'user_id' => $event->user_id,
                    'type' => 'new_comment',
                    ...$payload,
                ]);
            });
    }
}
