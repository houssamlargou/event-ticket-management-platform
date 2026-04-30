<?php

namespace App\Repositories;
use App\Models\User;

interface AuthRepositoryInterface {
    public function findByEmail(string $email): ?User;
    public function create(array $data):User;
    public function deleteCurrentToken($user): void;
    public function getAuthenticatedUser($user);
}