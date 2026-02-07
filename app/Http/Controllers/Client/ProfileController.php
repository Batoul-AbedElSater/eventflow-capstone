<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class ProfileController extends Controller
{
    /**
     * Show profile page
     */
    public function index()
    {
        $user = Auth::user();
        return view('client.profile.index', compact('user'));
    }

    /**
     * Update profile information
     */
    public function updateProfile(Request $request)
    {
        $user = Auth::user();
        
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email,' . $user->id,
            'phone' => 'nullable|string|max:20',
        ]);
        
        $user->update([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'phone' => $validated['phone'] ?? null,
        ]);
        
        return redirect()->route('client.profile')
            ->with('success', 'Profile updated successfully!');
    }

    /**
     * Update password
     */
    public function updatePassword(Request $request)
    {
        $validated = $request->validate([
            'current_password' => 'required',
            'new_password' => ['required', 'confirmed', Password::min(8)],
        ]);
        
        $user = Auth::user();
        
        // Check current password
        if (!Hash::check($validated['current_password'], $user->password)) {
            return back()->withErrors(['current_password' => 'Current password is incorrect']);
        }
        
        // Update password
        $user->update([
            'password' => Hash::make($validated['new_password'])
        ]);
        
        return redirect()->route('client.profile')
            ->with('success', 'Password changed successfully!');
    }

    /**
     * Show settings page
     */
    public function settings()
    {
        $user = Auth::user();
        return view('client.profile.settings', compact('user'));
    }

    /**
     * Update notification preferences
     */
    public function updateSettings(Request $request)
    {
        $user = Auth::user();
        
        $validated = $request->validate([
            'email_notifications' => 'nullable|boolean',
            'sms_notifications' => 'nullable|boolean',
            'task_reminders' => 'nullable|boolean',
            'budget_alerts' => 'nullable|boolean',
        ]);
        
        // Store preferences in user settings (you can add a settings column or separate table)
        $user->update([
            'notification_preferences' => json_encode([
                'email_notifications' => $validated['email_notifications'] ?? false,
                'sms_notifications' => $validated['sms_notifications'] ?? false,
                'task_reminders' => $validated['task_reminders'] ?? false,
                'budget_alerts' => $validated['budget_alerts'] ?? false,
            ])
        ]);
        
        return redirect()->route('client.settings')
            ->with('success', 'Settings updated successfully!');
    }
}
