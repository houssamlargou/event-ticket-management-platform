<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;


class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {

        User::factory()->count(10)->create();

        User::unguarded(function () {
            User::updateOrCreate(
                ['email' => 'admin@test.com'],
                [
                    'name' => 'Admin Test',
                    'password' => Hash::make('hello123456'),
                    'role' => 'admin',
                ]
            );

            User::updateOrCreate(
                ['email' => 'organizer@test.com'],
                [
                    'name' => 'Organizer Test',
                    'password' => Hash::make('hello123456'),
                    'role' => 'organizer',
                ]
            );

            User::updateOrCreate(
                ['email' => 'user@test.com'],
                [
                    'name' => 'User Test',
                    'password' => Hash::make('hello123456'),
                    'role' => 'user',
                ]
            );
        });

        $this->call(EventSeeder::class);
        $this->call(EventTicketSeeder::class);


    }
}
