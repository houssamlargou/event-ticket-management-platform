<?php

namespace Database\Seeders;

use App\Models\Event;
use App\Models\Ticket;
use Illuminate\Database\Seeder;

class TicketSeeder extends Seeder
{
    public function run(): void
    {
        $tickets = [
            [
                'name' => 'Standard Ticket',
                'price' => 100,
                'quantity' => 50,
            ],
            [
                'name' => 'VIP Ticket',
                'price' => 250,
                'quantity' => 25,
            ],
            [
                'name' => 'Student Ticket',
                'price' => 60,
                'quantity' => 40,
            ],
            [
                'name' => 'Premium Ticket',
                'price' => 400,
                'quantity' => 10,
            ],
        ];

        Event::all()->each(function (Event $event) use ($tickets) {
            foreach ($tickets as $ticket) {
                Ticket::updateOrCreate(
                    [
                        'event_id' => $event->id,
                        'name' => $ticket['name'],
                    ],
                    [
                        'price' => $ticket['price'],
                        'quantity' => $ticket['quantity'],
                    ]
                );
            }
        });
    }
}
