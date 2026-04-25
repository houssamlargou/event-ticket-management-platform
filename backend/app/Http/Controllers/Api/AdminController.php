<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
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
}
