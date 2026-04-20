<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Client\DashboardController as ClientDashboardController;
use App\Http\Controllers\Client\EventController;
use App\Http\Controllers\Client\GuestController;
use App\Http\Controllers\Client\InvitationController;
use App\Models\EventType;

/* Route::get('/setup-db', function() {
    \App\Models\EventType::firstOrCreate(['name' => 'Wedding'], ['description' => 'Wedding ceremony']);
    \App\Models\EventType::firstOrCreate(['name' => 'Birthday'], ['description' => 'Birthday celebration']);
    \App\Models\EventType::firstOrCreate(['name' => 'Corporate'], ['description' => 'Business events']);
    return "Success! Dropdowns restored.";
}); */

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
    // Budget route (view-only for client)
    Route::get('/events/{eventId}/budget', [App\Http\Controllers\Client\BudgetController::class, 'show'])
    ->name('events.budget.show'); 
    // Task routes (view-only for client)
    Route::get('/events/{eventId}/tasks', [App\Http\Controllers\Client\TaskController::class, 'index'])
    ->name('events.tasks.index');

    
    // Event routes
    Route::get('/events', [EventController::class, 'index'])->name('events.index');
    Route::get('/events/create', [EventController::class, 'create'])->name('events.create');
    Route::post('/events', [EventController::class, 'store'])->name('events.store');
    Route::get('/events/{id}', [EventController::class, 'show'])->name('events.show');

    // Guest routes (AJAX endpoints)
    Route::post('/events/{eventId}/guests', [GuestController::class, 'store'])->name('events.guests.store');
    Route::put('/events/{eventId}/guests/{guestId}', [GuestController::class, 'update'])->name('events.guests.update');
    Route::delete('/events/{eventId}/guests/{guestId}', [GuestController::class, 'destroy'])->name('events.guests.destroy');
    
    // Invitation routes
    Route::post('/events/{eventId}/invitations/send', [InvitationController::class, 'send'])
        ->name('events.invitations.send');

    // Message routes
    Route::get('/messages', [App\Http\Controllers\Client\MessageController::class, 'index'])
        ->name('messages');
    Route::get('/messages/{threadId}', [App\Http\Controllers\Client\MessageController::class, 'show'])
        ->name('messages.show');
    Route::post('/messages/{threadId}', [App\Http\Controllers\Client\MessageController::class, 'store'])
        ->name('messages.store');
    Route::post('/events/{eventId}/messages/create', [App\Http\Controllers\Client\MessageController::class, 'createThread'])
        ->name('events.messages.create');
    // Profile routes
    Route::get('/profile', [App\Http\Controllers\Client\ProfileController::class, 'index'])
        ->name('profile');
    Route::put('/profile', [App\Http\Controllers\Client\ProfileController::class, 'updateProfile'])
        ->name('profile.update');
    Route::put('/profile/password', [App\Http\Controllers\Client\ProfileController::class, 'updatePassword'])
        ->name('profile.password');

    // Settings routes
    Route::get('/settings', [App\Http\Controllers\Client\ProfileController::class, 'settings'])
        ->name('settings');
    Route::put('/settings', [App\Http\Controllers\Client\ProfileController::class, 'updateSettings'])
        ->name('settings.update');
    
    
  });
    
    // Planner Dashboard
    Route::prefix('planner')->name('planner.')->middleware('auth')->group(function () {
        Route::get('/dashboard', [App\Http\Controllers\Planner\DashboardController::class, 'index'])->name('dashboard');
    });
});

// Public RSVP routes (no authentication required)
Route::get('/rsvp/{token}', [InvitationController::class, 'showRsvp'])->name('rsvp.show');
Route::post('/rsvp/{token}', [InvitationController::class, 'submitRsvp'])->name('rsvp.submit');

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
//------------------------------------------------------------------------------------------






