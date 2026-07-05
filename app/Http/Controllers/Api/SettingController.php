<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class SettingController extends Controller
{
     public function index(Request $request){
$user=$request->user();
return response()->json([
    "success"=>true,
    "data"=>[
        "notifications"=>[
'in_app_alerts'=>(bool)$user->in_app_alerts,
        ],
    ],
]);
    }
    //update app_alerts

    public function updateNotifications(Request $request){
         $validated = $request->validate([
            'in_app_alerts' => ['required', 'boolean'],
        ]);

        $user = $request->user();
        $user->update([
            'in_app_alerts' => $validated['in_app_alerts'],
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Notification settings updated.',
            'data' => [
                'in_app_alerts' => (bool) $user->in_app_alerts,
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
