<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use Illuminate\Support\Facades\Storage;

class ProfileController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $layout = 'layouts.client';
        $roleLabel = 'Client';
        $routePrefix = 'client';

        return view('profile.index', compact('user', 'layout', 'roleLabel', 'routePrefix'));
    }

    public function updateProfile(Request $request)
    {
        $user = Auth::user();

        $validated = $request->validate([
            'name'   => 'required|string|max:255',
            'email'  => 'required|email|max:255|unique:users,email,' . $user->id,
            'phone'  => 'nullable|string|max:20',
            'avatar' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
        ]);

        // avatar upload (SAME AS ASSISTANT)
        if ($request->hasFile('avatar')) {

            if ($user->avatar_url && Storage::disk('public')->exists($user->avatar_url)) {
                Storage::disk('public')->delete($user->avatar_url);
            }

            $path = $request->file('avatar')->store('avatars', 'public');
            $user->avatar_url = $path;
        }

        $user->name  = $validated['name'];
        $user->email = $validated['email'];
        $user->phone = $validated['phone'] ?? null;

        $user->save();

        return redirect()->route('client.profile')
            ->with('success', 'Profile updated successfully!');
    }

    public function updatePassword(Request $request)
    {
        $validated = $request->validate([
            'current_password' => 'required',
            'new_password' => ['required', 'confirmed', Password::min(8)],
        ]);

        $user = Auth::user();

        if (!Hash::check($validated['current_password'], $user->password)) {
            return back()->withErrors(['current_password' => 'Current password is incorrect']);
        }

        $user->update([
            'password' => Hash::make($validated['new_password'])
        ]);

        return redirect()->route('client.profile')
            ->with('success', 'Password changed successfully!');
    }

    public function settings()
    {
        $user = Auth::user();
        return view('client.profile.setting', compact('user'));
    }

    public function updateSettings(Request $request)
    {
        $user = Auth::user();

        $validated = $request->validate([
            'email_notifications' => 'nullable|boolean',
            'sms_notifications'   => 'nullable|boolean',
            'task_reminders'      => 'nullable|boolean',
            'budget_alerts'       => 'nullable|boolean',
        ]);

        $user->update([
            'notification_preferences' => json_encode([
                'email_notifications' => $validated['email_notifications'] ?? false,
                'sms_notifications'   => $validated['sms_notifications'] ?? false,
                'task_reminders'      => $validated['task_reminders'] ?? false,
                'budget_alerts'       => $validated['budget_alerts'] ?? false,
            ])
        ]);

        return redirect()->route('client.settings.index')
            ->with('success', 'Settings updated successfully!');
    }
}