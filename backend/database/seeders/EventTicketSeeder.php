<?php

namespace Database\Seeders;

use App\Models\Event;
use App\Models\Ticket;
use App\Models\User;
use Illuminate\Database\Seeder;

class EventTicketSeeder extends Seeder
{
    public function run(): void
    {
        $organizer = User::where('email', 'organizer@example.com')->first();
        if(!$organizer) {
            return;
        }

        $event = Event::firstOrCreate(
            ['title' => 'Test Music Festival'],
            [
                'user_id' => $organizer->id,
                'description' => 'Seeded event for API testing',
                'event_date' => '2026-08-15 18:00:00',
                'location' => 'Casablanca',
                'status' => 'approved'
            ]
        );

        $event = Event::firstOrCreate(
            ['title' => 'Jazz Festival'],
            [
                'user_id' => $organizer->id,
                'description' => 'Jazz for API testing',
                'event_date' => '2026-08-15 18:00:00',
                'location' => 'Casablanca',
                'status' => 'approved'
            ]
        );

        Ticket::firstOrCreate(
            ['event_id' => $event->id,
             'name' => 'VIP',
            ],
            ['price' => 250, 'quantity' => 100]
        );

        Ticket::firstOrCreate(
            ['event_id' => $event->id,
             'name' => 'Standard',
            ],
            ['price' => 100, 'quantity' => 200]
        );

    }
}