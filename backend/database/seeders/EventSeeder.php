<?php

namespace Database\Seeders;

use App\Models\Event;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Hash;

class EventSeeder extends Seeder
{
    public function run(): void
    {
        $organizers = User::query()
            ->where('role', 'organizer')
            ->get();

        if ($organizers->isEmpty()) {
            $organizer = User::query()->firstOrNew([
                'email' => 'organizer@test.com',
            ]);

            $organizer->name = 'Organizer Test';
            $organizer->password = Hash::make('password');
            $organizer->role = 'organizer';
            $organizer->save();

            $organizers = new Collection([$organizer]);
        }

        $events = [
            [
                'title' => 'Casablanca Summer Beats',
                'description' => 'A seaside music festival with live DJs, food trucks, and sunset performances along the Corniche.',
                'event_date' => '2026-06-12 18:30:00',
                'location' => 'Casablanca Corniche',
            ],
            [
                'title' => 'Marrakech Art Nights',
                'description' => 'An evening celebration of contemporary art, local galleries, immersive installations, and creative talks.',
                'event_date' => '2026-06-20 19:00:00',
                'location' => 'Marrakech Medina',
            ],
            [
                'title' => 'Rabat Tech Summit',
                'description' => 'A one-day conference for founders, developers, and product teams exploring AI, fintech, and digital innovation.',
                'event_date' => '2026-07-03 09:00:00',
                'location' => 'Rabat Business District',
            ],
            [
                'title' => 'Agadir Beach Fitness Fest',
                'description' => 'Morning yoga, cardio challenges, wellness workshops, and healthy food experiences by the ocean.',
                'event_date' => '2026-07-11 08:00:00',
                'location' => 'Agadir Beachfront',
            ],
            [
                'title' => 'Fes Heritage Music Gala',
                'description' => 'A refined cultural evening featuring Andalusian music, storytelling, and traditional Moroccan hospitality.',
                'event_date' => '2026-07-18 20:00:00',
                'location' => 'Fes Old City',
            ],
            [
                'title' => 'Tangier Startup Mixer',
                'description' => 'A networking event for entrepreneurs, creatives, and investors with startup demos and pitch sessions.',
                'event_date' => '2026-08-01 17:30:00',
                'location' => 'Tangier Marina',
            ],
            [
                'title' => 'Chefchaouen Photography Walk',
                'description' => 'A guided photo walk through the blue streets with practical tips on framing, light, and mobile editing.',
                'event_date' => '2026-08-09 10:00:00',
                'location' => 'Chefchaouen Plaza Uta el-Hammam',
            ],
            [
                'title' => 'Essaouira Jazz by the Sea',
                'description' => 'An open-air jazz concert series blending international artists with coastal atmosphere and local crafts.',
                'event_date' => '2026-08-21 19:30:00',
                'location' => 'Essaouira Port',
            ],
            [
                'title' => 'Oujda Family Fun Carnival',
                'description' => 'A family-friendly weekend filled with games, performances, food stalls, and kids entertainment zones.',
                'event_date' => '2026-09-05 15:00:00',
                'location' => 'Oujda City Park',
            ],
            [
                'title' => 'Meknes Food Market Weekend',
                'description' => 'A curated market showcasing street food, artisan products, cooking demos, and live acoustic music.',
                'event_date' => '2026-09-13 12:00:00',
                'location' => 'Meknes Central Square',
            ],
            [
                'title' => 'Tetouan Design Forum',
                'description' => 'Panels and workshops for designers, makers, and architects focused on craft, branding, and urban identity.',
                'event_date' => '2026-09-26 11:00:00',
                'location' => 'Tetouan Cultural Center',
            ],
            [
                'title' => 'El Jadida Open Air Cinema',
                'description' => 'A relaxed outdoor movie night featuring independent films, lounge seating, and coastal evening vibes.',
                'event_date' => '2026-10-10 20:30:00',
                'location' => 'El Jadida Historic Quarter',
            ],
        ];

        foreach ($events as $index => $eventData) {
            $organizer = $organizers[$index % $organizers->count()];

            Event::query()->updateOrCreate(
                ['title' => $eventData['title']],
                [
                    'user_id' => $organizer->id,
                    'description' => $eventData['description'],
                    'event_date' => $eventData['event_date'],
                    'location' => $eventData['location'],
                    'status' => 'approved',
                    'image' => null,
                ]
            );
        }
    }
}
