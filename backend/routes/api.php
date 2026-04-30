<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\EventController;
use App\Http\Controllers\Api\TicketController;
use App\Http\Controllers\Api\OrderController;
use App\Http\Controllers\Api\AdminController;
use App\Http\Controllers\Api\FavoriteController;

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);
Route::middleware('auth:sanctum')->group(function(){
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/user', [AuthController::class, 'user']);
    Route::put('/profile', [AuthController::class, 'updateProfile']);
    Route::post('/orders', [OrderController::class, 'store']);
    Route::get('/orders', [OrderController::class, 'index']);
    Route::get('/orders/{id}', [OrderController::class, 'show']);
    Route::post('/orders/{id}/pay', [OrderController::class, 'pay']);
    Route::post('/orders/{id}/cancel', [OrderController::class, 'cancel']);
    Route::get('/admin/dashboard', [AdminController::class, 'dashboard']);
    Route::get('/favorites', [FavoriteController::class, 'index']);
    Route::post('/events/{id}/favorites', [FavoriteController::class, 'toggle']);
});

Route::post('/events', [EventController::class, 'store'])->middleware(['role:organizer', 'auth:sanctum']);
Route::get('/events', [EventController::class, 'index']);
Route::get('/events/{eventId}/tickets', [TicketController::class, 'index']);
Route::get('/events/{id}', [EventController::class, 'show']);
Route::middleware(['auth:sanctum', 'role:organizer'])->group(function(){
    Route::put('/events/{id}', [EventController::class, 'update']);
    Route::delete('/events/{id}', [EventController::class, 'destroy']);
    Route::post('/events/{eventId}/tickets', [TicketController::class, 'store']);
});
Route::patch('/events/{id}/status', [EventController::class, 'moderate'])->middleware(['auth:sanctum', 'role:admin']);
