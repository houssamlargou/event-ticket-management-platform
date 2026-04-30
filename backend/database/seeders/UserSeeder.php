<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
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
    }
}
