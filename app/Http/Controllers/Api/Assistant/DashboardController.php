<?php

// namespace App\Http\Controllers\Api\Assistant;

//use App\Http\Controllers\Controller;
//use Illuminate\Http\Request;

// class DashboardController extends Controller
// {
//     public function index(Request $request)
//     {
//         $user = $request->user();

//         return response()->json([
//             'success' => true,
//             'data' => [
//                 'welcome_message' => "Welcome, {$user->name}!",
//                 'user_id' => $user->id,
//                 'user_name' => $user->name,
//                 'user_email' => $user->email,
//                 'role' => $user->role,
//                 'features_coming_soon' => [
//                     'task_management' => 'Manage assigned tasks',
//                     'notifications' => 'Get notified about tasks',
//                     'messaging' => 'Communicate with planners',
//                 ],
//             ]
//         ]);
//     }
// } 