<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\EventController;

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

Route::post('/events', [EventController::class, 'store'])->middleware(['role:organizer', 'auth:sanctum']);
Route::get('/events', [EventController::class, 'index']);
Route::get('/events/{id}', [EventController::class, 'show']);
Route::put('/events/{id}', [EventController::class, 'update'])->middleware(['auth:sanctum', 'role:organizer']);