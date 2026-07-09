<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\UserPreference;

class SettingController extends Controller
{
public function index(Request $request)
{
    $user = $request->user();
    $preferences = $user->preferences ?? new UserPreference();

    return response()->json([
        "success" => true,
        "data" => [
            "notifications" => [
                "in_app_alerts" => (bool) ($preferences->in_app_notifications ?? true),
            ],
        ],
    ]);
}
    //update app_alerts

   public function updateNotifications(Request $request)
{
    $validated = $request->validate([
        'in_app_alerts' => ['required', 'boolean'],
    ]);

    $user = $request->user();
    $preferences = $user->preferences ?? new UserPreference();

    $preferences->user_id = $user->id;
    $preferences->in_app_notifications = $validated['in_app_alerts'];
    $preferences->save();

    return response()->json([
        'success' => true,
        'message' => 'Notification settings updated.',
        'data' => [
            'in_app_alerts' => (bool) $preferences->in_app_notifications,
        ],
    ]);
}
// logout user
     public function logout(Request $request)
    {
        // If using Laravel Sanctum
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'success' => true,
            'message' => 'Logged out successfully.',
        ]);
    }

  //delete account
    public function deleteAccount(Request $request)
    {
        $user = $request->user();

        // Revoke tokens first, then delete
        $user->tokens()->delete();
        $user->delete();

        return response()->json([
            'success' => true,
            'message' => 'Account deleted successfully.',
        ]);
    }
}
