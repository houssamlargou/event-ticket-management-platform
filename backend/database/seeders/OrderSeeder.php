<?php

namespace Database\Seeders;

use App\Models\Order;
use App\Models\Ticket;
use App\Models\User;
use Illuminate\Database\Seeder;

class OrderSeeder extends Seeder
{
    public function run(): void
    {
        $user = User::query()
            ->where('email', 'user@test.com')
            ->first();

        if (! $user) {
            return;
        }

        $tickets = Ticket::query()
            ->whereIn('name', ['Standard Ticket', 'VIP Ticket'])
            ->orderBy('event_id')
            ->orderBy('id')
            ->get();

        $faker = fake();
        $faker->seed(20260430);

        foreach ($tickets as $index => $ticket) {
            $quantity = $faker->numberBetween(1, 4);
            $paymentStatus = $index % 2 === 0 ? 'paid' : 'pending';

            Order::query()->updateOrCreate(
                [
                    'user_id' => $user->id,
                    'ticket_id' => $ticket->id,
                ],
                [
                    'quantity' => $quantity,
                    'total_price' => round($ticket->price * $quantity, 2),
                    'payment_status' => $paymentStatus,
                ]
            );
        }
    }
}
