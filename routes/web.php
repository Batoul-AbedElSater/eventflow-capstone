<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Client\DashboardController as ClientDashboardController;
use App\Http\Controllers\Client\EventController;
use App\Http\Controllers\Client\GuestController;

// Guest routes (not logged in)
Route::middleware('guest')->group(function () {
    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register']);
    Route::get('/login', [AuthController::class, 'showRegister'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
});

// Authenticated routes (must be logged in)
Route::middleware('auth')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
    Route::get('/logout', [AuthController::class, 'logout']); // GET method too
    
    // Client Dashboard
    Route::prefix('client')->name('client.')->group(function () {
    Route::get('/dashboard', [ClientDashboardController::class, 'index'])->name('dashboard');

    
    // Event routes
    Route::get('/events', [EventController::class, 'index'])->name('events.index');
    Route::get('/events/create', [EventController::class, 'create'])->name('events.create');
    Route::post('/events', [EventController::class, 'store'])->name('events.store');
    Route::get('/events/{id}', [EventController::class, 'show'])->name('events.show');

    // Guest routes (AJAX endpoints)
    Route::post('/events/{eventId}/guests', [GuestController::class, 'store'])->name('events.guests.store');
    Route::put('/events/{eventId}/guests/{guestId}', [GuestController::class, 'update'])->name('events.guests.update');
    Route::delete('/events/{eventId}/guests/{guestId}', [GuestController::class, 'destroy'])->name('events.guests.destroy');
    
    // Placeholder routes
    Route::get('/messages', fn() => 'Messages coming soon')->name('messages');
    Route::get('/profile', fn() => 'Profile coming soon')->name('profile');
    Route::get('/settings', fn() => 'Settings coming soon')->name('settings');
});
    
    // Planner Dashboard (placeholder)
    Route::get('/planner/dashboard', function () {
        return 'Planner Dashboard - Coming Soon!';
    })->name('planner.dashboard');
});

// Default redirect
Route::get('/', function () {
    if (Auth::check()) {
        $user = Auth::user();
        return $user->isClient() 
            ? redirect()->route('client.dashboard')
            : redirect()->route('planner.dashboard');
    }
    return redirect()->route('login');
});