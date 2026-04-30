<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use App\Services\FavoriteService;

class FavoriteController extends Controller
{
    public function __construct(protected FavoriteService $favoriteService){}
    public function toggle(int $eventId){
        $user = auth()->user();

        if(!$user){
            return response()->json([
                'message' => 'Unauthenticated',
            ], 401);
        }

        if($user->role !== 'user'){
            return response()->json([
                'message' => 'Only users can favorite events.'
            ], 403);
        }

        $result = $this->favoriteService->toggle($eventId, $user);

        return response()->json([
            'message' => $result['message'],
            'favorited' => $result['favorited'],
        ]);
    }

    public function index() {
        $user = auth()->user();

        if(!$user){
            return response()->json([
                'message' => 'Unauthenticated'
            ], 401);
        }

        if($user->role !== 'user'){
            return response()->json([
                'message' => 'Only users can access favorites'
            ], 403);
        }

        $favorites = $this->favoriteService->getUserFavorites($user);

        return \App\Http\Resources\EventResource::collection($favorites);
    }
}
