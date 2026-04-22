<?php 

namespace App\Repositories;
use App\Models\User;

class AuthRepository implements AuthRepositoryInterface {
    public function findByEmail(string $email): ?User {
        return User::where('email', $email)->first();
    }

    public function create(array $data): User {
        return User::create($data);
    }

    public function deleteCurrentToken($user): void {
        $user->currentAccessToken()->delete();
    }

    public function getAuthenticatedUser($user) {
        return $user;
    }
}