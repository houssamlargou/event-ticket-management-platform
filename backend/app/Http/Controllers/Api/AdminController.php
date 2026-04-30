<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\EventResource;
use App\Models\Event;
use App\Models\User;
use App\Services\AdminService;
use Illuminate\Http\JsonResponse;

class AdminController extends Controller
{
    public function __construct(protected AdminService $adminService){}

    public function dashboard(): JsonResponse {
        $user = request()->user('sanctum');

        if(!$user || $user->role !== 'admin') {
            return response()->json([
                'message' => 'Forbidden.',
            ], 403);
        }

        $result = $this->adminService->getDashboard();

        return response()->json([
            'data' => $result['data'],
        ], $result['status']);
    }

    public function events(): JsonResponse
    {
        $events = Event::with('user')
            ->latest()
            ->get();

        return response()->json([
            'message' => 'Admin events retrieved successfully.',
            'data' => EventResource::collection($events)->resolve(),
        ]);
    }

    public function users(): JsonResponse
    {
        $users = User::query()
            ->latest()
            ->get()
            ->map(function (User $user) {
                return [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'role' => $user->role,
                ];
            });

        return response()->json([
            'message' => 'Admin users retrieved successfully.',
            'data' => $users,
        ]);
    }
}
