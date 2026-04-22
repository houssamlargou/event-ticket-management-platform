<?php

namespace App\Services;

use App\Repositories\AuthRepositoryInterface;
use Illuminate\Support\Facades\Hash;

class AuthService 
{
    public function __construct(protected AuthRepositoryInterface $authRepository){}
    public function register(array $data): array
    {
        $user = $this->authRepository->create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
            'role' => 'user'
        ]);

        $token = $user->createToken('auth_token')->plainTextToken;

        return [
            'user' => $user,
            'token' => $token,
        ];
    }

    public function login(array $data): array
    {
        $user = $this->authRepository->findByEmail($data['email']);

        if(!$user || ! Hash::check($data['password'], $user->password)) {
            return [
                'success' => false,
                'message' => 'Invalid credentials.',
            ];
        }

        $token = $user->createToken('auth_token')->plainTextToken;

        return [
            'success' => true,
            'message' => 'Login successful.',
            'user' => $user,
            'token' => $token,
        ];
    }

    public function logout($user): array {
        $this->authRepository->deleteCurrentToken($user);
        return [
            'message' => 'Logout successfully.',
        ];
    }

    public function getAuthenticatedUser($user): array {
        return [
            'user' => $this->authRepository->getAuthenticatedUser($user),
        ];
    }
}