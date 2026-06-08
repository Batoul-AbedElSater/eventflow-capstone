<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\Client\DashboardController;
use App\Http\Controllers\Api\Client\EventController;
use App\Http\Controllers\Api\Client\GuestController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\RsvpController; 
use App\Http\Controllers\Api\Client\MessageController;
use App\Http\Controllers\Api\Client\NotificationController;
use App\Http\Controllers\Api\Planner\DashboardController as PlannerDashboardController;
use App\Http\Controllers\Api\Planner\EventRequestController;


/*
|--------------------------------------------------------------------------
| API Routes - RESTful for Mobile App
|--------------------------------------------------------------------------
*/

// Sanctum Authentication
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

// Public RSVP routes (no authentication required)
Route::get('/rsvp/{token}', [RsvpController::class, 'show']);
Route::post('/rsvp/{token}', [RsvpController::class, 'submit']);

Route::middleware(['auth:sanctum'])->group(function () {

    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/me', [AuthController::class, 'me']);
    Route::put('/profile', [AuthController::class, 'updateProfile']);
    Route::put('/profile/password', [AuthController::class, 'updatePassword']);

    // ============================================
    // CLIENT API ROUTES
    // ============================================

    Route::prefix('client')->name('api.client.')->group(function () {

        // Dashboard
        Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

        // Events
        Route::get('/events/create-data', [EventController::class, 'createData'])->name('events.create-data');
        Route::get('/events', [EventController::class, 'index'])->name('events.index');
        Route::post('/events', [EventController::class, 'store'])->name('events.store');
        Route::get('/events/{event}', [EventController::class, 'show'])->name('events.show');
        Route::put('/events/{event}', [EventController::class, 'update'])->name('events.update');
        Route::delete('/events/{event}', [EventController::class, 'destroy'])->name('events.destroy');

        // Event Photo
        Route::post('/events/{event}/photo', [EventController::class, 'uploadPhoto'])->name('events.photo.upload');
        Route::delete('/events/{event}/photo', [EventController::class, 'deletePhoto'])->name('events.photo.delete');

        // Messages
        Route::get('/messages/events', [MessageController::class, 'eventsWithMessages'])->name('messages.events');
        Route::get('/events/{event}/messages', [MessageController::class, 'index'])->name('messages.index');
        Route::post('/events/{event}/messages', [MessageController::class, 'store'])->name('messages.store');
        Route::delete('/messages/{message}', [MessageController::class, 'destroy'])->name('messages.destroy');
        Route::delete('/events/{event}/messages', [MessageController::class, 'deleteAll'])->name('messages.delete-all');

        // Guests
        Route::get('/guests', [GuestController::class, 'index'])->name('guests.index');
        Route::get('/events/{event}/guests', [GuestController::class, 'byEvent'])->name('events.guests.index');
        Route::post('/events/{event}/guests', [GuestController::class, 'store'])->name('events.guests.store');
        Route::get('/guests/{guest}', [GuestController::class, 'show'])->name('guests.show');
        Route::put('/guests/{guest}', [GuestController::class, 'update'])->name('guests.update');
        Route::patch('/guests/{guest}', [GuestController::class, 'update'])->name('guests.patch');
        Route::delete('/guests/{guest}', [GuestController::class, 'destroy'])->name('guests.destroy');
        Route::post('/guests/{guest}/resend', [GuestController::class, 'resendInvitation'])->name('guests.resend');
        Route::post('/guests/{guest}/check-in', [GuestController::class, 'checkIn'])->name('guests.check-in');
        Route::delete('/guests/{guest}/check-in', [GuestController::class, 'undoCheckIn'])->name('guests.check-in.undo');

        // Notifications
        Route::prefix('notifications')->name('notifications.')->group(function () {
            Route::get('/', [NotificationController::class, 'index'])->name('index');
            Route::get('/stats', [NotificationController::class, 'stats'])->name('stats');
            Route::get('/unread-count', [NotificationController::class, 'unreadCount'])->name('unread-count');
            Route::post('/{id}/read', [NotificationController::class, 'markAsRead'])->name('read');
            Route::post('/{id}/archive', [NotificationController::class, 'archive'])->name('archive');
            Route::post('/read-all', [NotificationController::class, 'markAllAsRead'])->name('read-all');
            Route::post('/archive-all', [NotificationController::class, 'archiveAll'])->name('archive-all');
        });
    });

  
    // PLANNER API ROUTES - CORRECTED
   

    Route::prefix('planner')->name('api.planner.')->group(function () {
        
        // Dashboard - Weekly Calendar
        Route::get('/dashboard', [PlannerDashboardController::class, 'index'])->name('dashboard');
        Route::get('/dashboard/events/{date}', [PlannerDashboardController::class, 'getDayEvents'])->name('dashboard.events');
        
        // Event Requests
        Route::get('/requests', [EventRequestController::class, 'index'])->name('requests.index');
        Route::get('/requests/stats', [EventRequestController::class, 'stats'])->name('requests.stats');
        Route::post('/requests/{event}/accept', [EventRequestController::class, 'accept'])->name('requests.accept');
        Route::post('/requests/{event}/decline', [EventRequestController::class, 'decline'])->name('requests.decline');

       
    });
});