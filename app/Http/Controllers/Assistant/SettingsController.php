<?php

namespace App\Http\Controllers\Assistant;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\UserPreference;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class SettingsController extends Controller
{
    // ========== MAIN SETTINGS ==========
    public function index()
    {
        $user = auth()->user();
        $preferences = $user->preferences ?? new UserPreference();
        $stats = method_exists($this, 'getStats') ? $this->getStats($user) : null;
        return view('assistant.settings.index', compact('user', 'preferences', 'stats'));
    }

    // ========== ACCOUNT SETTINGS ==========
    public function account()
    {
        $user = auth()->user();
        $stats = $this->getStats($user);
        return view('assistant.settings.account', compact('user', 'stats'));
    }

    public function updateProfile(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . auth()->id(),
            'phone' => 'nullable|string|max:20',
            'bio' => 'nullable|string|max:500',
        ]);

        auth()->user()->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Profile updated successfully!'
        ]);
    }

    public function updateProfilePhoto(Request $request)
    {
        $request->validate([
            'photo' => 'required|image|mimes:jpeg,png,jpg,gif|max:5120'
        ]);

        $user = auth()->user();

        if ($user->profile_photo_path) {
            Storage::disk('public')->delete($user->profile_photo_path);
        }

        $path = $request->file('photo')->store('profiles', 'public');
$user->update(['profile_photo_path' => $path]);

        return response()->json([
            'success' => true,
            'message' => 'Photo updated!',
            'url' => asset('storage/' . $path)
        ]);
    }

    public function changePassword(Request $request)
    {
        $request->validate([
            'current_password' => 'required|current_password',
            'new_password' => 'required|string|min:8|confirmed',
        ]);

        auth()->user()->update([
            'password' => Hash::make($request->new_password)
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Password changed successfully!'
        ]);
    }

    // ========== SKILLS SETTINGS ==========
    public function skills()
    {
        $user = auth()->user();
        $preferences = $user->preferences ?? new UserPreference();
        $stats = $this->getStats($user);

        return view('assistant.settings.skills', compact('user', 'preferences', 'stats'));
    }

    public function updateSkills(Request $request)
    {
        $validated = $request->validate([
            'specializations' => 'nullable|array',
            'experience_level' => 'nullable|in:beginner,intermediate,expert',
            'certifications' => 'nullable|array',
            'portfolio_link' => 'nullable|url',
        ]);

        $user = auth()->user();
        $preferences = $user->preferences ?? new UserPreference();

        foreach ($validated as $key => $value) {
            $preferences->$key = $value;
        }

        $preferences->user_id = $user->id;
        $preferences->save();

        return response()->json([
            'success' => true,
            'message' => 'Skills updated successfully!'
        ]);
    }

    // ========== AVAILABILITY SETTINGS ==========
    public function availability()
    {
        $user = auth()->user();
        $preferences = $user->preferences ?? new UserPreference();
        $stats = $this->getStats($user);

        return view('assistant.settings.availability', compact('user', 'preferences', 'stats'));
    }

    public function updateAvailability(Request $request)
    {
        $validated = $request->validate([
            'working_days' => 'nullable|array',
            'working_hours_start' => 'nullable|date_format:H:i',
            'working_hours_end' => 'nullable|date_format:H:i',
            'timezone' => 'nullable|timezone',
            'available_locations' => 'nullable|array',
            'remote_work' => 'nullable|boolean',
        ]);

        $user = auth()->user();
        $preferences = $user->preferences ?? new UserPreference();

        foreach ($validated as $key => $value) {
            $preferences->$key = $value;
        }

        $preferences->user_id = $user->id;
        $preferences->save();

        return response()->json([
            'success' => true,
            'message' => 'Availability updated successfully!'
        ]);
    }

    // ========== NOTIFICATION SETTINGS ==========
    public function notifications()
    {
        return $this->index();
    }

    public function updateNotifications(Request $request)
    {
        $validated = $request->validate([
            'in_app_notifications' => 'nullable|boolean',
        ]);

        $user = auth()->user();
        $preferences = $user->preferences ?? new UserPreference();

        $preferences->in_app_notifications = $validated['in_app_notifications'] ?? false;
        $preferences->user_id = $user->id;
        $preferences->save();

        return response()->json([
            'success' => true,
            'message' => 'Notification preference saved!'
        ]);
    }

    // ========== APPEARANCE SETTINGS ==========
    public function appearance()
    {
        $user = auth()->user();
        $preferences = $user->preferences ?? new UserPreference();
        $stats = $this->getStats($user);

        return view('assistant.settings.appearance', compact('user', 'preferences', 'stats'));
    }

    // ========== EXPORT DATA ==========
    public function exportData()
    {
        $user = auth()->user();
        $data = [
            'user' => $user,
            'tasks' => method_exists($user, 'assignedTasks') ? $user->assignedTasks()->get() : [],
            'preferences' => $user->preferences,
        ];

        return response()->json($data)->download('my-data.json');
    }

    public function updateAppearance(Request $request)
    {
        $validated = $request->validate([
            'theme_mode' => 'nullable|in:light,dark,auto',
            'color_scheme' => 'nullable|in:coral,berry,green,mixed',
            'font_size' => 'nullable|in:small,medium,large',
            'animations' => 'nullable|boolean',
            'language' => 'nullable|in:en,ar,fr',
            'hover_effects' => 'nullable|boolean',
            'reduce_motion' => 'nullable|boolean',
            'collapse_sidebar' => 'nullable|boolean',
            'sidebar_icons_only' => 'nullable|boolean',
        ]);

        $user = auth()->user();
        $preferences = $user->preferences ?? new UserPreference();

        foreach ($validated as $key => $value) {
            $preferences->$key = $value;
        }

        $preferences->user_id = $user->id;
        $preferences->save();

        return response()->json([
            'success' => true,
            'message' => 'Appearance settings updated!'
        ]);
    }

    // ========== PRIVATE HELPER ==========
    private function getStats($user)
    {
        return [
            'completed_tasks' => $user->assignedTasks()->where('tasks.status', 'done')->count(),
            'in_progress_tasks' => $user->assignedTasks()->where('tasks.status', 'in_progress')->count(),
            'orders_placed' => $user->vendorOrders()->count(),
            'ratings' => 4.7,
        ];
    }
}