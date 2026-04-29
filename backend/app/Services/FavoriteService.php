<?php

namespace App\Services;

use App\Repositories\FavoriteRepositoryInterface;

class FavoriteService {
    public function __construct(protected FavoriteRepositoryInterface $favoriteRepository){}
    public function toggle(int $eventId, $user): array {
        if($this->favoriteRepository->exists($user->id, $eventId)){
            $this->favoriteRepository->remove($user->id, $eventId);
            return [
                'success' => true,
                'message' => 'Remove from favorites',
                'favorited' => false,
            ];
        }

        $this->favoriteRepository->add($user->id, $eventId);

        return [
            'success' => true,
            'message' => 'Added to favorites',
            'favorited' => true,
        ];
    }

    public function getUserFavorites($user){
        return $this->favoriteRepository->getUserFavorites($user->id);
    }
}