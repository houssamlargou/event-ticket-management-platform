<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;


class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {

        User::factory()->count(10)->create();

        User::factory()->create([
            'name' => 'Test User',
            'email' => 'user@example.com',
            'password' => bcrypt('hello@123456'),
            'role' => 'user'
        ]);

        User::factory()->create([
            'name' => 'Organizer_1 User',
            'email' => 'organizer@example.com',
            'password' => bcrypt('hello@123456'),
            'role' => 'organizer'
        ]);

        User::factory()->create([
            'name' => 'Organizer_2 User',
            'email' => 'organizer2@example.com',
            'password' => bcrypt('hello@123456'),
            'role' => 'organizer'
        ]);

        User::factory()->create([
            'name' => 'admin User',
            'email' => 'admin@example.com',
            'password' => bcrypt('hello@123456'),
            'role' => 'admin'
        ]);

        $this->call(EventTicketSeeder::class);


    }
}
