<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\EventController;
use App\Http\Controllers\Api\TicketController;
use App\Http\Controllers\Api\OrderController;

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);
Route::middleware('auth:sanctum')->group(function(){
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/user', [AuthController::class, 'user']);
    Route::post('/orders', [OrderController::class, 'store']);
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
Route::middleware(['auth:sanctum', 'role:organizer'])->group(function(){
    Route::put('/events/{id}', [EventController::class, 'update']);
    Route::delete('/events/{id}', [EventController::class, 'destroy']);
    Route::post('/events/{eventId}/tickets', [TicketController::class, 'store']);
    });
Route::patch('/events/{id}', [EventController::class, 'moderate'])->middleware(['auth:sanctum', 'role:admin']);
Route::get('/events/{eventId}/tickets', [TicketController::class, 'index']);

