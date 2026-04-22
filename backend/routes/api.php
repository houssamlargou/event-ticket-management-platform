<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);
Route::middleware('auth:sanctum')->group(function(){
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/user', [AuthController::class, 'user']);
});

Route::get('/admin-only', function(){
    return response()->json([
        'message' => 'Welcome admin.',
    ]);
})->middleware(['role:admin','auth:sanctum']);

Route::get('/organizer-only', function(){
    return response()->json([
        'message' => 'Welcome organizer.',
    ]);
})->middleware(['role:organizer', 'auth:sanctum']);