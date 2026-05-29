<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\HomeController;

// ============================================
// PUBLIC ROUTES
// ============================================

Route::get('/', function () {
    if (auth()->check()) {
        $role = auth()->user()->role;
        return match($role) {
            'planner' => redirect()->route('planner.dashboard'),
            'client'  => redirect()->route('client.dashboard'),
            default   => redirect()->route('login'),
        };
    }
    return redirect()->route('login');
})->name('home');


// ============================================
// AUTHENTICATION ROUTES
// ============================================

Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

Route::get('/register', [AuthController::class, 'showRegistrationForm'])->name('register');
Route::post('/register', [AuthController::class, 'register']);

// ============================================
// PLANNER ROUTES
// ============================================

Route::prefix('planner')->name('planner.')->middleware(['auth', 'role:planner'])->group(function () {

//vendor routes
Route::get('/events/{event}/vendors', function($event) {
    return view('planner.events.vendor.vendor', ['event' => \App\Models\Event::findOrFail($event)]);
});

    // Dashboard
    Route::get('/dashboard', [App\Http\Controllers\Planner\DashboardController::class, 'index'])->name('dashboard');

    // Analytics
     Route::get('/analytics', [App\Http\Controllers\Planner\AnalyticsController::class, 'index'])
    ->name('events.analytics');



    // Event Requests
    Route::get('/requests', [App\Http\Controllers\Planner\EventRequestController::class, 'index'])->name('requests');
    Route::post('/requests/{id}/accept', [App\Http\Controllers\Planner\EventRequestController::class, 'accept'])->name('requests.accept');
    Route::post('/requests/{id}/decline', [App\Http\Controllers\Planner\EventRequestController::class, 'decline'])->name('requests.decline');

    // Events
    Route::resource('events', App\Http\Controllers\Planner\EventController::class);
    Route::get('/events/analytics', [App\Http\Controllers\Planner\EventController::class, 'analytics'])->name('events.analytics');
    Route::put('/events/{event}/status', [App\Http\Controllers\Planner\EventController::class, 'updateStatus'])->name('events.status');

    // TASKS - ADD THESE ROUTES
    Route::prefix('tasks')->name('tasks.')->group(function () {
        Route::get('/', [App\Http\Controllers\Planner\TaskController::class, 'index'])->name('index');
        Route::post('/', [App\Http\Controllers\Planner\TaskController::class, 'store'])->name('store');
        Route::get('/{task}', [App\Http\Controllers\Planner\TaskController::class, 'show'])->name('show');
        Route::put('/{task}', [App\Http\Controllers\Planner\TaskController::class, 'update'])->name('update');
        Route::delete('/{task}', [App\Http\Controllers\Planner\TaskController::class, 'destroy'])->name('destroy');
        Route::put('/{task}/status', [App\Http\Controllers\Planner\TaskController::class, 'updateStatus'])->name('status');
        Route::post('/{task}/duplicate', [App\Http\Controllers\Planner\TaskController::class, 'duplicate'])->name('duplicate');
    });

    // Gamification & Pomodoro
    Route::get('/gamification/stats', [App\Http\Controllers\Planner\TaskController::class, 'getGamificationStats'])->name('gamification.stats');
    Route::post('/pomodoro/record', [App\Http\Controllers\Planner\TaskController::class, 'recordPomodoro'])->name('pomodoro.record');

    // Tasks
    Route::prefix('events/{event}/tasks')->name('events.tasks.')->group(function () {
        Route::get('/', [App\Http\Controllers\Planner\TaskController::class, 'index'])->name('index');
        Route::post('/', [App\Http\Controllers\Planner\TaskController::class, 'store'])->name('store');
        Route::put('/{task}', [App\Http\Controllers\Planner\TaskController::class, 'update'])->name('update');
        Route::delete('/{task}', [App\Http\Controllers\Planner\TaskController::class, 'destroy'])->name('destroy');
        Route::post('/{task}/toggle', [App\Http\Controllers\Planner\TaskController::class, 'toggleStatus'])->name('toggle');
        Route::put('/tasks/{task}/status', [App\Http\Controllers\Planner\TaskController::class, 'updateStatus'])->name('planner.tasks.status');
    });

    // Budget
    Route::prefix('events/{event}/budget')->name('events.budget.')->group(function () {
        Route::get('/', [App\Http\Controllers\Planner\BudgetController::class, 'index'])->name('index');
        Route::post('/items', [App\Http\Controllers\Planner\BudgetController::class, 'storeItem'])->name('items.store');
        Route::put('/items/{item}', [App\Http\Controllers\Planner\BudgetController::class, 'updateItem'])->name('items.update');
        Route::delete('/items/{item}', [App\Http\Controllers\Planner\BudgetController::class, 'destroyItem'])->name('items.destroy');
    });

    // Guests
    Route::prefix('events/{event}/guests')->name('events.guests.')->group(function () {
        Route::get('/', [App\Http\Controllers\Planner\GuestController::class, 'index'])->name('index');
        Route::post('/', [App\Http\Controllers\Planner\GuestController::class, 'store'])->name('store');
        Route::put('/{guest}', [App\Http\Controllers\Planner\GuestController::class, 'update'])->name('update');
        Route::delete('/{guest}', [App\Http\Controllers\Planner\GuestController::class, 'destroy'])->name('destroy');
    });


    // MESSAGES
    // Messages Routes - ADD THESE
    Route::get('/messages', [App\Http\Controllers\Planner\MessageController::class, 'showPage'])->name('messages');
    Route::get('/messages/{event}', [App\Http\Controllers\Planner\MessageController::class, 'index'])->name('messages.index');
    Route::post('/messages/{event}', [App\Http\Controllers\Planner\MessageController::class, 'store'])->name('messages.store');
    Route::delete('/messages/{event}/{message}', [App\Http\Controllers\Planner\MessageController::class, 'destroy'])->name('messages.destroy');
    Route::delete('/events/{event}/messages', [App\Http\Controllers\Planner\MessageController::class, 'deleteAll'])
    ->name('planner.events.messages.deleteAll');

    // Notifications
    Route::prefix('notifications')->name('notifications.')->group(function () {
        Route::get('/', [App\Http\Controllers\Planner\NotificationController::class, 'index'])->name('index');
        Route::post('/{id}/read', [App\Http\Controllers\Planner\NotificationController::class, 'markAsRead'])->name('read');
        Route::post('/{id}/archive', [App\Http\Controllers\Planner\NotificationController::class, 'archive'])->name('archive');
        Route::post('/read-all', [App\Http\Controllers\Planner\NotificationController::class, 'markAllAsRead'])->name('read-all');
        Route::get('/stats', [App\Http\Controllers\Planner\NotificationController::class, 'stats'])->name('stats');
    });

    // Profile & Settings
    Route::get('/profile', [App\Http\Controllers\Planner\ProfileController::class, 'edit'])->name('profile.edit');
    Route::put('/profile', [App\Http\Controllers\Planner\ProfileController::class, 'update'])->name('profile.update');
    Route::get('/settings', [App\Http\Controllers\Planner\SettingsController::class, 'index'])->name('settings.index');
    Route::put('/settings', [App\Http\Controllers\Planner\SettingsController::class, 'update'])->name('settings.update');


});

// ============================================
// CLIENT ROUTES
// ============================================

Route::prefix('client')->name('client.')->middleware(['auth', 'role:client'])->group(function () {

Route::post('/events/{event}/rating', [App\Http\Controllers\Client\EventController::class, 'storeRating'])->name('rating.store');

    // Dashboard
    Route::get('/dashboard', [App\Http\Controllers\Client\DashboardController::class, 'index'])->name('dashboard');

    // Events - Full Resource Routes
     Route::resource('events', App\Http\Controllers\Client\EventController::class);





    // Messages Page (Main view with all events)
    Route::get('/messages', [App\Http\Controllers\Client\MessageController::class, 'showPage'])->name('messages');

    // Messages for specific event (API endpoints)
    Route::get('/events/{event}/messages', [App\Http\Controllers\Client\MessageController::class, 'index'])->name('events.messages.index');
    Route::post('/events/{event}/messages', [App\Http\Controllers\Client\MessageController::class, 'store'])->name('events.messages.store');
    Route::delete('/events/{event}/messages/{message}', [App\Http\Controllers\Client\MessageController::class, 'destroy'])->name('events.messages.destroy');
    Route::delete('/events/{event}/messages', [App\Http\Controllers\Client\MessageController::class, 'deleteAll'])
    ->name('client.events.messages.deleteAll');


    // Client Messages Routes - ADD THESE
    // Route::get('/events/{event}/messages', [App\Http\Controllers\Client\MessageController::class, 'index'])->name('events.messages.index');
    // Route::post('/events/{event}/messages', [App\Http\Controllers\Client\MessageController::class, 'store'])->name('events.messages.store');
    // Route::delete('/events/{event}/messages/{message}', [App\Http\Controllers\Client\MessageController::class, 'destroy'])->name('events.messages.destroy');





    // Notifications
    Route::prefix('notifications')->name('notifications.')->group(function () {
        Route::get('/', [App\Http\Controllers\Client\NotificationController::class, 'index'])->name('index');
        Route::post('/{id}/read', [App\Http\Controllers\Client\NotificationController::class, 'markAsRead'])->name('read');
        Route::post('/{id}/archive', [App\Http\Controllers\Client\NotificationController::class, 'archive'])->name('archive');
        Route::post('/read-all', [App\Http\Controllers\Client\NotificationController::class, 'markAllAsRead'])->name('read-all');
        Route::post('/archive-all', [App\Http\Controllers\Client\NotificationController::class, 'archiveAll'])->name('archive-all');
        Route::get('/stats', [App\Http\Controllers\Client\NotificationController::class, 'stats'])->name('stats');
    });

    // GUESTS MANAGEMENT (REPLACE INVITATION ROUTES)
    Route::prefix('guests')->name('guests.')->group(function () {
        Route::get('/', [App\Http\Controllers\Client\GuestController::class, 'index'])->name('index');
        Route::get('/events/{event}/create', [App\Http\Controllers\Client\GuestController::class, 'create'])->name('create');
        Route::post('/events/{event}', [App\Http\Controllers\Client\GuestController::class, 'store'])->name('store');
        Route::get('/{guest}', [App\Http\Controllers\Client\GuestController::class, 'show'])->name('show');
        Route::put('/{guest}', [App\Http\Controllers\Client\GuestController::class, 'update'])->name('update');
        Route::delete('/{guest}', [App\Http\Controllers\Client\GuestController::class, 'destroy'])->name('destroy');
        Route::post('/{guest}/resend', [App\Http\Controllers\Client\GuestController::class, 'resendInvitation'])->name('resend'); // ADD THIS
    });

    // Guest Management (Keep existing)
    Route::prefix('guests')->name('guests.')->group(function () {
        Route::get('/', [App\Http\Controllers\Client\GuestController::class, 'index'])->name('index');
        Route::get('/{guest}', [App\Http\Controllers\Client\GuestController::class, 'show'])->name('show');
        Route::put('/{guest}', [App\Http\Controllers\Client\GuestController::class, 'update'])->name('update');
    });

    // Profile & Settings (Keep existing functionality - only colors updated in CSS)
    Route::get('/profile', [App\Http\Controllers\Client\ProfileController::class, 'index'])->name('profile');
    Route::put('/profile', [App\Http\Controllers\Client\ProfileController::class, 'updateProfile'])->name('profile.update');
    Route::put('/profile/password', [App\Http\Controllers\Client\ProfileController::class, 'updatePassword'])->name('profile.password');
    Route::get('/settings', [App\Http\Controllers\Client\ProfileController::class, 'settings'])->name('settings');
    Route::put('/settings', [App\Http\Controllers\Client\ProfileController::class, 'updateSettings'])->name('settings.update');
});

// ============================================
// PUBLIC GUEST RSVP ROUTES
// ============================================

Route::prefix('rsvp')->name('rsvp.')->group(function () {
    Route::get('/{token}', [App\Http\Controllers\RsvpController::class, 'show'])->name('show');
    Route::post('/{token}', [App\Http\Controllers\RsvpController::class, 'update'])->name('update');
});

// ============================================
// ADMIN ROUTES (If you have admin panel)
// ============================================

Route::prefix('admin')->name('admin.')->middleware(['auth', 'role:admin'])->group(function () {
    Route::get('/dashboard', [App\Http\Controllers\Admin\DashboardController::class, 'index'])->name('dashboard');
    // Add more admin routes as needed
});

// ============================================
// FALLBACK ROUTE
// ============================================

Route::fallback(function () {
    return response()->view('errors.404', [], 404);
});




