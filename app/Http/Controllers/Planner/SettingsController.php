<?php

namespace App\Http\Controllers\Planner;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\UserPreference;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class SettingsController extends Controller
{
    // ========== MAIN SETTINGS (redirect) ==========
    public function index()
    {
        return redirect()->route('planner.settings.account');
    }

    // ========== ACCOUNT SETTINGS ==========
    public function account()
    {
        $user = auth()->user();
        $businessStats = $this->getBusinessStats($user);
        return view('planner.settings.account', compact('user', 'businessStats'));
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

    // ========== BUSINESS SETTINGS ==========
    public function business()
    {
        $user = auth()->user();
        $preferences = $user->preferences ?? new UserPreference();
        $businessStats = $this->getBusinessStats($user);

        return view('planner.settings.business', compact('user', 'preferences', 'businessStats'));
    }

    public function updateBusiness(Request $request)
    {
        $validated = $request->validate([
            'company_name' => 'nullable|string|max:255',
            'business_type' => 'nullable|in:freelance,small_team,agency',
            'years_experience' => 'nullable|integer',
            'specializations' => 'nullable|array',
            'service_areas' => 'nullable|array',
            'business_license' => 'nullable|string',
            'tax_id' => 'nullable|string',
            'about_business' => 'nullable|string|max:500',
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
            'message' => 'Business settings updated!'
        ]);
    }

    // ========== TEAM MANAGEMENT ==========
    public function team()
    {
        $user = auth()->user();
        $assistants = $user->assistants()->get();
        $businessStats = $this->getBusinessStats($user);

        return view('planner.settings.team', compact('user', 'assistants', 'businessStats'));
    }

    public function addTeamMember(Request $request)
    {
        $validated = $request->validate([
            'email' => 'required|email|exists:users,id',
            'role' => 'required|in:assistant,vendor_manager,coordinator',
        ]);

        // Add team member logic
        return response()->json([
            'success' => true,
            'message' => 'Team member added!'
        ]);
    }

    // ========== VENDOR SETTINGS ==========
    public function vendors()
    {
        $user = auth()->user();
        $preferences = $user->preferences ?? new UserPreference();
        $favoriteVendors = $user->favoriteVendors()->get();
        $businessStats = $this->getBusinessStats($user);

        return view('planner.settings.vendors', compact('user', 'preferences', 'favoriteVendors', 'businessStats'));
    }

    public function updateVendorPreferences(Request $request)
    {
        $validated = $request->validate([
            'vendor_rating_threshold' => 'nullable|numeric|min:1|max:5',
            'vendor_auto_messages' => 'nullable|boolean',
            'vendor_quote_requests' => 'nullable|boolean',
            'vendor_price_alerts' => 'nullable|boolean',
            'vendor_new_services' => 'nullable|boolean',
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
            'message' => 'Vendor preferences updated!'
        ]);
    }

    // ========== ANALYTICS ==========
    public function analytics()
    {
        $user = auth()->user();
        $analytics = [
            'total_events_this_month' => $user->plannerEvents()
                ->whereMonth('start_date', now()->month)
                ->count(),
            'client_satisfaction' => 4.8,
            'repeat_client_rate' => 68,
            'average_event_budget' => $user->plannerEvents()->avg('budget_overall'),
        ];
        $businessStats = $this->getBusinessStats($user);

        return view('planner.settings.analytics', compact('user', 'analytics', 'businessStats'));
    }

    // ========== NOTIFICATION SETTINGS ==========
    public function notifications()
    {
        $user = auth()->user();
        $preferences = $user->preferences ?? new UserPreference();
        $businessStats = $this->getBusinessStats($user);

        return view('planner.settings.notifications', compact('user', 'preferences', 'businessStats'));
    }

    public function updateNotifications(Request $request)
    {
        $validated = $request->validate([
            'email_new_inquiries' => 'nullable|boolean',
            'email_client_messages' => 'nullable|boolean',
            'email_assistant_updates' => 'nullable|boolean',
            'email_vendor_responses' => 'nullable|boolean',
            'email_event_reminders' => 'nullable|boolean',
            'push_notifications' => 'nullable|boolean',
            'sms_notifications' => 'nullable|boolean',
            'in_app_notifications' => 'nullable|boolean',
            'notification_frequency' => 'nullable|in:instant,daily,weekly',
            'enable_quiet_hours' => 'nullable|boolean',
            'quiet_hours_start' => 'nullable|string',
            'quiet_hours_end' => 'nullable|string',
            'client_responses' => 'nullable|boolean',
            'team_messages' => 'nullable|boolean',
            'task_reminders' => 'nullable|boolean',
            'vendor_messages' => 'nullable|boolean',
            'vendor_availability' => 'nullable|boolean',
            'price_updates' => 'nullable|boolean',
            'deadline_alerts' => 'nullable|boolean',
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
            'message' => 'Notification settings updated!'
        ]);
    }

    // ========== APPEARANCE SETTINGS ==========
    public function appearance()
    {
        $user = auth()->user();
        $preferences = $user->preferences ?? new UserPreference();
        $businessStats = $this->getBusinessStats($user);

        return view('planner.settings.appearance', compact('user', 'preferences', 'businessStats'));
    }

    public function updateAppearance(Request $request)
    {
        $validated = $request->validate([
            'theme_mode' => 'nullable|in:light,dark,auto',
            'color_scheme' => 'nullable|in:coral,berry,green,mixed',
            'dashboard_layout' => 'nullable|in:grid,list,compact',
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
            'message' => 'Appearance updated!'
        ]);
    }

    // ========== PRIVATE HELPER ==========
    private function getBusinessStats($user)
    {
        return [
            'total_events' => $user->plannerEvents()->count(),
            'active_clients' => $user->plannerEvents()->distinct('client_id')->count(),
            'team_members' => $user->assistants()->count(),
            'total_revenue' => $user->plannerEvents()->sum('budget_overall'),
        ];
    }
}