<?php

namespace App\Repositories;

interface FavoriteRepositoryInterface {
    public function exists(int $userId, int $eventId): bool;
    public function add(int $userId, int $eventId): void;
    public function remove(int $userId, int $eventId): void;
    public function getUserFavorites(int $userId);
}