<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\Client\DashboardController as ApiClientDashboardController;

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