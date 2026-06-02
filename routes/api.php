
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
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

Route::middleware(['auth:sanctum'])->group(function () {

    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/me', [AuthController::class, 'me']);
    Route::put('/profile', [AuthController::class, 'updateProfile']);
    Route::put('/profile/password', [AuthController::class, 'updatePassword']);


    // ============================================
    // CLIENT API ROUTES
    // ============================================

    Route::prefix('client')->name('api.client.')->group(function () {

        // Events

      // Static routes FIRST
Route::get('/events/create-data', [EventController::class, 'createData'])->name('events.create-data');

// Dynamic routes AFTER
      Route::get('/events', [EventController::class, 'index'])->name('events.index');
      Route::post('/events', [EventController::class, 'store'])->name('events.store');
      Route::get('/events/{event}', [EventController::class, 'show'])->name('events.show');
      Route::put('/events/{event}', [EventController::class, 'update'])->name('events.update');
      Route::delete('/events/{event}', [EventController::class, 'destroy'])->name('events.destroy');

        // Event Photo
        Route::post('/events/{event}/photo', [App\Http\Controllers\Api\Client\EventController::class, 'uploadPhoto'])->name('events.photo.upload');
        Route::delete('/events/{event}/photo', [App\Http\Controllers\Api\Client\EventController::class, 'deletePhoto'])->name('events.photo.delete');

        // Messages
        Route::get('/events/{event}/messages', [App\Http\Controllers\Api\Client\MessageController::class, 'index'])->name('messages.index');
        Route::post('/events/{event}/messages', [App\Http\Controllers\Api\Client\MessageController::class, 'store'])->name('messages.store');
        Route::delete('/messages/{message}', [App\Http\Controllers\Api\Client\MessageController::class, 'destroy'])->name('messages.destroy');

    });
});
