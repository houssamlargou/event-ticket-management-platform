<?php

namespace App\Repositories;

use Illuminate\Support\Facades\DB;
use App\Models\Favorite;

class FavoriteRepository implements FavoriteRepositoryInterface {
    public function exists(int $userId, int $eventId): bool {
        return DB::table('favorites')
                ->where('user_id', $userId)
                ->where('event_id', $eventId)
                ->exists();
    }

    public function add(int $userId, int $eventId): void {
        DB::table('favorites')->insert([
            'user_id' => $userId,
            'event_id' => $eventId,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    public function remove(int $userId, int $eventId): void {
        DB::table('favorites')
            ->where('user_id', $userId)
            ->where('event_id', $eventId)
            ->delete();
    }

    public function getUserFavorites(int $userId){
        return \App\Models\Event::whereHas('favoritedBy', function ($q) use ($userId){
            $q->where('user_id', $userId);
        })->latest()->get();
    }
}