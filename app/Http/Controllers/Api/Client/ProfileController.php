<?php

namespace App\Http\Controllers\Api\Client;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rules\Password;

class ProfileController extends Controller
{
    public function show(Request $request)
    {
        return response()->json([
            'success' => true,
            'data' => $this->profileData($request->user()),
        ]);
    }

    public function updateProfile(Request $request)
    {
        $user = $request->user();

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email,' . $user->id,
            'phone' => 'nullable|string|max:20',
            'avatar' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
        ]);

        if ($request->hasFile('avatar')) {
            if ($user->avatar_url && Storage::disk('public')->exists($user->avatar_url)) {
                Storage::disk('public')->delete($user->avatar_url);
            }

            $path = $request->file('avatar')->store('avatars', 'public');
            $user->avatar_url = $path;
        }

        $user->name = $validated['name'];
        $user->email = $validated['email'];
        $user->phone = $validated['phone'] ?? null;
        $user->save();

        return response()->json([
            'success' => true,
            'message' => 'Profile updated successfully',
            'data' => $this->profileData($user->fresh()),
        ]);
    }

    public function updatePassword(Request $request)
    {
        $validated = $request->validate([
            'current_password' => 'required',
            'new_password' => ['required', 'confirmed', Password::min(8)],
        ]);

        $user = $request->user();

        if (!Hash::check($validated['current_password'], $user->password)) {
            return response()->json([
                'success' => false,
                'message' => 'Current password is incorrect',
                'errors' => [
                    'current_password' => ['Current password is incorrect'],
                ],
            ], 422);
        }

        $user->update([
            'password' => Hash::make($validated['new_password']),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Password changed successfully',
        ]);
    }

    public function settings(Request $request)
    {
        return response()->json([
            'success' => true,
            'data' => [
                'notification_preferences' => $this->notificationPreferences($request->user()),
            ],
        ]);
    }

    public function updateSettings(Request $request)
    {
        $validated = $request->validate([
            'email_notifications' => 'nullable|boolean',
            'sms_notifications' => 'nullable|boolean',
            'task_reminders' => 'nullable|boolean',
            'budget_alerts' => 'nullable|boolean',
        ]);

        $preferences = [
            'email_notifications' => $validated['email_notifications'] ?? false,
            'sms_notifications' => $validated['sms_notifications'] ?? false,
            'task_reminders' => $validated['task_reminders'] ?? false,
            'budget_alerts' => $validated['budget_alerts'] ?? false,
        ];

        $user = $request->user();
        $user->notification_preferences = json_encode($preferences);
        $user->save();

        return response()->json([
            'success' => true,
            'message' => 'Settings updated successfully',
            'data' => [
                'notification_preferences' => $preferences,
            ],
        ]);
    }

    private function profileData($user)
    {
        return [
            'id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
            'phone' => $user->phone,
            'role' => $user->role,
            'avatar_url' => $user->avatar_url
                ? asset('storage/' . $user->avatar_url)
                : null,
            'created_at' => $user->created_at,
            'notification_preferences' => $this->notificationPreferences($user),
        ];
    }

    private function notificationPreferences($user)
    {
        $preferences = $user->notification_preferences;

        if (is_string($preferences)) {
            $preferences = json_decode($preferences, true) ?? [];
        }

        return array_merge([
            'email_notifications' => true,
            'sms_notifications' => false,
            'task_reminders' => true,
            'budget_alerts' => true,
        ], $preferences ?? []);
    }
}