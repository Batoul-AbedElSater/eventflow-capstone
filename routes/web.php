<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\Assistant\AssistantController;
use App\Http\Controllers\Planner\TaskController;

// ============================================
// PUBLIC ROUTES
// ============================================

Route::get('/', function () {
    if (auth()->check()) {
        $role = auth()->user()->role;
        return match($role) {

            'planner'   => redirect()->route('planner.dashboard'),
            'client'    => redirect()->route('client.dashboard'),
            'assistant' => redirect()->route('assistant.dashboard'),
            default     => redirect()->route('login'),

            'planner' => redirect()->route('planner.dashboard'),
            'client'  => redirect()->route('client.dashboard'),
            'assistant' => redirect()->route('assistant.tasks'),
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


    // Dashboard
    Route::get('/dashboard', [App\Http\Controllers\Planner\DashboardController::class, 'index'])->name('dashboard');

    // Analytics

    Route::get('/dashboard', [App\Http\Controllers\Planner\DashboardController::class, 'index'])->name('dashboard');

    Route::get('/analytics', [App\Http\Controllers\Planner\AnalyticsController::class, 'index'])->name('events.analytics');

    // Event Requests
    Route::get('/requests', [App\Http\Controllers\Planner\EventRequestController::class, 'index'])->name('requests');
    Route::post('/requests/{id}/accept', [App\Http\Controllers\Planner\EventRequestController::class, 'accept'])->name('requests.accept');
    Route::post('/requests/{id}/decline', [App\Http\Controllers\Planner\EventRequestController::class, 'decline'])->name('requests.decline');

    // Vendor Routes
    Route::prefix('events/{event}/vendors')->name('events.vendors.')->group(function () {
        Route::get('/', [App\Http\Controllers\Planner\VendorController::class, 'index'])->name('index');
        Route::get('/favorites', [App\Http\Controllers\Planner\VendorController::class, 'favorites'])->name('favorites');
        Route::get('/{vendor}', [App\Http\Controllers\Planner\VendorController::class, 'show'])->name('show');
        Route::post('/{vendor}/favorite', [App\Http\Controllers\Planner\VendorController::class, 'toggleFavorite'])->name('toggleFavorite');
        Route::post('/{vendor}/unfavorite', [App\Http\Controllers\Planner\VendorController::class, 'removeFavorite'])->name('removeFavorite');
    });

    // Events
    Route::resource('events', App\Http\Controllers\Planner\EventController::class);
    Route::get('/events/analytics', [App\Http\Controllers\Planner\EventController::class, 'analytics'])->name('events.analytics');
    Route::put('/events/{event}/status', [App\Http\Controllers\Planner\EventController::class, 'updateStatus'])->name('events.status');


    // Tasks (standalone)
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

    // Tasks (per event)
    Route::prefix('events/{event}/tasks')->name('events.tasks.')->group(function () {
        Route::get('/', [App\Http\Controllers\Planner\TaskController::class, 'index'])->name('index');
        Route::post('/', [App\Http\Controllers\Planner\TaskController::class, 'store'])->name('store');
        Route::put('/{task}', [App\Http\Controllers\Planner\TaskController::class, 'update'])->name('update');
        Route::delete('/{task}', [App\Http\Controllers\Planner\TaskController::class, 'destroy'])->name('destroy');
        Route::post('/{task}/toggle', [App\Http\Controllers\Planner\TaskController::class, 'toggleStatus'])->name('toggle');
        Route::put('/tasks/{task}/status', [App\Http\Controllers\Planner\TaskController::class, 'updateStatus'])->name('planner.tasks.status');
    });

    // Guests (per event)

    // Tasks
    Route::prefix('tasks')->name('tasks.')->group(function () {
        Route::get('/', [TaskController::class, 'index'])->name('index');
        Route::post('/', [TaskController::class, 'store'])->name('store');
        Route::get('/{task}', [TaskController::class, 'show'])->name('show');
        Route::put('/{task}', [TaskController::class, 'update'])->name('update');
        Route::delete('/{task}', [TaskController::class, 'destroy'])->name('destroy');
        Route::put('/{task}/status', [TaskController::class, 'updateStatus'])->name('status');
        Route::post('/{task}/duplicate', [TaskController::class, 'duplicate'])->name('duplicate');
        Route::post('/{task}/assign', [TaskController::class, 'assignAssistant'])->name('assign');
        Route::delete('/{task}/unassign/{assistant}', [TaskController::class, 'removeAssistant'])->name('unassign');
        Route::get('/{task}/assistants', [TaskController::class, 'getAssignedAssistants'])->name('assistants');
    });

    Route::get('/gamification/stats', [TaskController::class, 'getGamificationStats'])->name('gamification.stats');
    Route::post('/pomodoro/record', [TaskController::class, 'recordPomodoro'])->name('pomodoro.record');

    // Event Tasks
    Route::prefix('events/{event}/tasks')->name('events.tasks.')->group(function () {
        Route::get('/', [TaskController::class, 'index'])->name('index');
        Route::post('/', [TaskController::class, 'store'])->name('store');
        Route::put('/{task}', [TaskController::class, 'update'])->name('update');
        Route::delete('/{task}', [TaskController::class, 'destroy'])->name('destroy');
        Route::post('/{task}/toggle', [TaskController::class, 'toggleStatus'])->name('toggle');
        Route::put('/tasks/{task}/status', [TaskController::class, 'updateStatus'])->name('planner.tasks.status');
    });

    // Guests

    Route::prefix('events/{event}/guests')->name('events.guests.')->group(function () {
        Route::get('/', [App\Http\Controllers\Client\GuestController::class, 'index'])->name('index');
        Route::post('/', [App\Http\Controllers\Client\GuestController::class, 'store'])->name('store');
        Route::put('/{guest}', [App\Http\Controllers\Client\GuestController::class, 'update'])->name('update');
        Route::delete('/{guest}', [App\Http\Controllers\Client\GuestController::class, 'destroy'])->name('destroy');
    });


    // Budget (per event)
    Route::prefix('events/{event}/budget')->name('events.budget.')->group(function () {
        Route::get('/', [App\Http\Controllers\Client\BudgetController::class, 'index'])->name('index');
        Route::post('/items', [App\Http\Controllers\Client\BudgetController::class, 'storeItem'])->name('items.store');
        Route::put('/items/{item}', [App\Http\Controllers\Client\BudgetController::class, 'updateItem'])->name('items.update');
        Route::delete('/items/{item}', [App\Http\Controllers\Client\BudgetController::class, 'destroyItem'])->name('items.destroy');
    });


    // Messages
    Route::get('/messages', [App\Http\Controllers\Planner\MessageController::class, 'showPage'])->name('messages');
    Route::get('/messages/{event}', [App\Http\Controllers\Planner\MessageController::class, 'index'])->name('messages.index');
    Route::post('/messages/{event}', [App\Http\Controllers\Planner\MessageController::class, 'store'])->name('messages.store');
    Route::delete('/messages/{event}/{message}', [App\Http\Controllers\Planner\MessageController::class, 'destroy'])->name('messages.destroy');
    Route::delete('/events/{event}/messages', [App\Http\Controllers\Planner\MessageController::class, 'deleteAll'])->name('planner.events.messages.deleteAll');

    // Notifications
    Route::prefix('notifications')->name('notifications.')->group(function () {
        Route::get('/', [App\Http\Controllers\Planner\NotificationController::class, 'index'])->name('index');
        Route::post('/{id}/read', [App\Http\Controllers\Planner\NotificationController::class, 'markAsRead'])->name('read');
        Route::post('/{id}/archive', [App\Http\Controllers\Planner\NotificationController::class, 'archive'])->name('archive');
        Route::post('/read-all', [App\Http\Controllers\Planner\NotificationController::class, 'markAllAsRead'])->name('read-all');
        Route::get('/stats', [App\Http\Controllers\Planner\NotificationController::class, 'stats'])->name('stats');
    });

    // Profile

    Route::get('/profile', [App\Http\Controllers\Client\ProfileController::class, 'edit'])->name('profile.edit');
    Route::put('/profile', [App\Http\Controllers\Client\ProfileController::class, 'update'])->name('profile.update');
});

// ============================================
// ASSISTANT ROUTES
// ============================================

    Route::get('/profile', [App\Http\Controllers\client\ProfileController::class, 'edit'])->name('profile.edit');
    Route::put('/profile', [App\Http\Controllers\Client\ProfileController::class, 'update'])->name('profile.update');


// Assistant
//
//
//
//


Route::prefix('assistant')->name('assistant.')->middleware(['auth', 'role:assistant'])->group(function () {

    Route::get('/tasks', [AssistantController::class, 'tasks'])->name('tasks');
    Route::patch('/tasks/{task}/complete', [AssistantController::class, 'completeTask'])->name('tasks.complete');

    // Task Vendors
    Route::get('/tasks/{task}/vendors', [AssistantController::class, 'taskVendors'])->name('tasks.vendors');

    // Vendor Details
   Route::get('/vendor/{vendor}', [AssistantController::class, 'vendorShow'])->name('vendor.show');

    // Order Routes
    Route::get('/task/{task}/vendor/{vendor}/order', [AssistantController::class, 'orderForm'])->name('vendor.order');
    Route::post('/task/{task}/vendor/{vendor}/order', [AssistantController::class, 'submitOrder'])->name('vendor.order.submit');

    Route::get('/orders', [AssistantController::class, 'myOrders'])->name('orders');
    Route::delete('/orders/{order}', [AssistantController::class, 'deleteOrder'])->name('orders.delete');

    });

// ============================================
// CLIENT ROUTES
// ============================================

Route::prefix('client')->name('client.')->middleware(['auth', 'role:client'])->group(function () {

    Route::post('/events/{event}/rating', [App\Http\Controllers\Client\EventController::class, 'storeRating'])->name('rating.store');

    // Dashboard
    Route::get('/dashboard', [App\Http\Controllers\Client\DashboardController::class, 'index'])->name('dashboard');
    Route::resource('events', App\Http\Controllers\Client\EventController::class);


    // Events
    Route::resource('events', App\Http\Controllers\Client\EventController::class);


    // Messages
    Route::get('/messages', [App\Http\Controllers\Client\MessageController::class, 'showPage'])->name('messages');
    Route::get('/events/{event}/messages', [App\Http\Controllers\Client\MessageController::class, 'index'])->name('events.messages.index');
    Route::post('/events/{event}/messages', [App\Http\Controllers\Client\MessageController::class, 'store'])->name('events.messages.store');
    Route::delete('/events/{event}/messages/{message}', [App\Http\Controllers\Client\MessageController::class, 'destroy'])->name('events.messages.destroy');
    Route::delete('/events/{event}/messages', [App\Http\Controllers\Client\MessageController::class, 'deleteAll'])->name('client.events.messages.deleteAll');

    // Notifications
    Route::prefix('notifications')->name('notifications.')->group(function () {
        Route::get('/', [App\Http\Controllers\Client\NotificationController::class, 'index'])->name('index');
        Route::post('/{id}/read', [App\Http\Controllers\Client\NotificationController::class, 'markAsRead'])->name('read');
        Route::post('/{id}/archive', [App\Http\Controllers\Client\NotificationController::class, 'archive'])->name('archive');
        Route::post('/read-all', [App\Http\Controllers\Client\NotificationController::class, 'markAllAsRead'])->name('read-all');
        Route::post('/archive-all', [App\Http\Controllers\Client\NotificationController::class, 'archiveAll'])->name('archive-all');
        Route::get('/stats', [App\Http\Controllers\Client\NotificationController::class, 'stats'])->name('stats');
    });

    // Guests
    Route::prefix('guests')->name('guests.')->group(function () {
        Route::get('/', [App\Http\Controllers\Client\GuestController::class, 'index'])->name('index');
        Route::get('/events/{event}/create', [App\Http\Controllers\Client\GuestController::class, 'create'])->name('create');
        Route::post('/events/{event}', [App\Http\Controllers\Client\GuestController::class, 'store'])->name('store');
        Route::get('/{guest}', [App\Http\Controllers\Client\GuestController::class, 'show'])->name('show');
        Route::put('/{guest}', [App\Http\Controllers\Client\GuestController::class, 'update'])->name('update');
        Route::delete('/{guest}', [App\Http\Controllers\Client\GuestController::class, 'destroy'])->name('destroy');
        Route::post('/{guest}/resend', [App\Http\Controllers\Client\GuestController::class, 'resendInvitation'])->name('resend');
    });


    // Profile & Settings

    // Profile

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
// ADMIN ROUTES
// ============================================

Route::prefix('admin')->name('admin.')->middleware(['auth', 'role:admin'])->group(function () {
    Route::get('/dashboard', [App\Http\Controllers\Client\DashboardController::class, 'index'])->name('dashboard');
});


// ============================================
// FALLBACK ROUTE
// ============================================

Route::fallback(function () {
    return response()->view('errors.404', [], 404);

});



