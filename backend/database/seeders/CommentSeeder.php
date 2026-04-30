<?php

namespace Database\Seeders;

use App\Models\Comment;
use App\Models\Event;
use App\Models\User;
use Illuminate\Database\Seeder;

class CommentSeeder extends Seeder
{
    public function run(): void
    {
        $user = User::query()
            ->where('email', 'user@test.com')
            ->first();

        if (! $user) {
            return;
        }

        $commentTemplates = [
            'Looking forward to this event. The lineup and concept sound great.',
            'This looks well organized and the location is perfect for a weekend plan.',
            'I am interested in attending and would love to bring a couple of friends too.',
        ];

        Event::query()
            ->orderBy('id')
            ->get()
            ->each(function (Event $event) use ($user, $commentTemplates) {
                foreach ($commentTemplates as $template) {
                    Comment::query()->updateOrCreate(
                        [
                            'user_id' => $user->id,
                            'event_id' => $event->id,
                            'body' => $template,
                        ],
                        []
                    );
                }
            });
    }
}
