<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\Client\DashboardController as ApiClientDashboardController;
use App\Http\Controllers\Api\Client\EventController as ApiClientEventController;

// API Client routes (requires Sanctum token)
Route::middleware('auth:sanctum')->prefix('client')->group(function () {
    Route::get('/dashboard', [ApiClientDashboardController::class, 'index']);
});

// Public API routes (no auth required)
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

// Protected API routes (requires token)
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/me', [AuthController::class, 'me']); // Get current user
});

// API Client routes
Route::middleware('auth:sanctum')->prefix('client')->group(function () {
    Route::get('/dashboard', [ApiClientDashboardController::class, 'index']);
    
    // Event API routes
    Route::get('/events', [ApiClientEventController::class, 'index']);
    Route::get('/events/create-data', [ApiClientEventController::class, 'createData']);
    Route::post('/events', [ApiClientEventController::class, 'store']);
    Route::get('/events/{id}', [ApiClientEventController::class, 'show']);
});