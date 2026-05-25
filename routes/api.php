<?php
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\Client\DashboardController;
use App\Http\Controllers\Api\Client\EventController;
use App\Http\Controllers\Api\Client\MessageController;
use App\Http\Controllers\Api\Client\NotificationController;
use App\Http\Controllers\Api\Client\InvitationController;
use App\Http\Controllers\Api\AuthController;
//use App\Http\Controllers\Api\Client\DashboardController as ApiClientDashboardController;
//use App\Http\Controllers\Api\Client\EventController as ApiClientEventController;

/*
|--------------------------------------------------------------------------
| API Routes - RESTful for Mobile App
|--------------------------------------------------------------------------
*/

// Sanctum Authentication
Route::middleware(['auth:sanctum'])->group(function () {
    
    // ============================================
    // CLIENT API ROUTES
    // ============================================
    
    Route::prefix('client')->name('api.client.')->group(function () {
        
        // Events
        Route::get('/events', [App\Http\Controllers\Api\Client\EventController::class, 'index'])->name('events.index');
        Route::post('/events', [App\Http\Controllers\Api\Client\EventController::class, 'store'])->name('events.store');
        Route::get('/events/{event}', [App\Http\Controllers\Api\Client\EventController::class, 'show'])->name('events.show');
        Route::put('/events/{event}', [App\Http\Controllers\Api\Client\EventController::class, 'update'])->name('events.update');
        Route::delete('/events/{event}', [App\Http\Controllers\Api\Client\EventController::class, 'destroy'])->name('events.destroy');
        
        // Event Photo
        Route::post('/events/{event}/photo', [App\Http\Controllers\Api\Client\EventController::class, 'uploadPhoto'])->name('events.photo.upload');
        Route::delete('/events/{event}/photo', [App\Http\Controllers\Api\Client\EventController::class, 'deletePhoto'])->name('events.photo.delete');
        
        // Guests
        Route::get('/events/{event}/guests', [App\Http\Controllers\Api\Client\GuestController::class, 'index'])->name('guests.index');
        Route::post('/events/{event}/guests', [App\Http\Controllers\Api\Client\GuestController::class, 'store'])->name('guests.store');
        Route::get('/guests/{guest}', [App\Http\Controllers\Api\Client\GuestController::class, 'show'])->name('guests.show');
        Route::put('/guests/{guest}', [App\Http\Controllers\Api\Client\GuestController::class, 'update'])->name('guests.update');
        Route::delete('/guests/{guest}', [App\Http\Controllers\Api\Client\GuestController::class, 'destroy'])->name('guests.destroy');
        Route::post('/guests/{guest}/resend', [App\Http\Controllers\Api\Client\GuestController::class, 'resendInvitation'])->name('guests.resend');
        
        // Messages
        Route::get('/events/{event}/messages', [App\Http\Controllers\Api\Client\MessageController::class, 'index'])->name('messages.index');
        Route::post('/events/{event}/messages', [App\Http\Controllers\Api\Client\MessageController::class, 'store'])->name('messages.store');
        Route::delete('/messages/{message}', [App\Http\Controllers\Api\Client\MessageController::class, 'destroy'])->name('messages.destroy');
        
        // Notifications
        Route::get('/notifications', [App\Http\Controllers\Api\Client\NotificationController::class, 'index'])->name('notifications.index');
        Route::post('/notifications/{id}/read', [App\Http\Controllers\Api\Client\NotificationController::class, 'markAsRead'])->name('notifications.read');
        Route::post('/notifications/read-all', [App\Http\Controllers\Api\Client\NotificationController::class, 'markAllAsRead'])->name('notifications.read-all');
        Route::get('/notifications/stats', [App\Http\Controllers\Api\Client\NotificationController::class, 'stats'])->name('notifications.stats');
    });
    
    // ============================================
    // PLANNER API ROUTES
    // ============================================
    
    Route::prefix('planner')->name('api.planner.')->group(function () {
        
        // Events
        Route::get('/events', [App\Http\Controllers\Api\Planner\EventController::class, 'index'])->name('events.index');
        Route::get('/events/{event}', [App\Http\Controllers\Api\Planner\EventController::class, 'show'])->name('events.show');
        Route::put('/events/{event}/status', [App\Http\Controllers\Api\Planner\EventController::class, 'updateStatus'])->name('events.status');
        
        // Messages
        Route::get('/events/{event}/messages', [App\Http\Controllers\Api\Planner\MessageController::class, 'index'])->name('messages.index');
        Route::post('/events/{event}/messages', [App\Http\Controllers\Api\Planner\MessageController::class, 'store'])->name('messages.store');
        
        // Notifications
        Route::get('/notifications', [App\Http\Controllers\Api\Planner\NotificationController::class, 'index'])->name('notifications.index');
    });
});
// Planner API
Route::middleware(['auth:sanctum'])->prefix('planner')->name('api.planner.')->group(function () {
    Route::get('/events', [App\Http\Controllers\Api\Planner\EventController::class, 'index'])->name('events.index');
    Route::put('/events/{event}/status', [App\Http\Controllers\Api\Planner\EventController::class, 'updateStatus'])->name('events.status');
    Route::get('/analytics', [App\Http\Controllers\Api\Planner\EventController::class, 'analytics'])->name('analytics');
});

// Public RSVP API (no auth required)
Route::get('/rsvp/{token}', [App\Http\Controllers\Api\RsvpController::class, 'show'])->name('api.rsvp.show');
Route::post('/rsvp/{token}', [App\Http\Controllers\Api\RsvpController::class, 'update'])->name('api.rsvp.update');