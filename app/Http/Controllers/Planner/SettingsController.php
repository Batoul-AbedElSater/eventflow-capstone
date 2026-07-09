<?php

namespace App\Http\Controllers\Planner;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\UserPreference;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;

class SettingsController extends Controller
{
    // ========== MAIN SETTINGS ==========
    public function index()
    {
        $user = auth()->user();
        $preferences = $user->preferences ?? new UserPreference();
        $businessStats = method_exists($this, 'getBusinessStats') ? $this->getBusinessStats($user) : null;
        return view('planner.settings.index', compact('user', 'preferences', 'businessStats'));
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
        // show unified settings page
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
        $businessStats = $this->getBusinessStats($user);

        return view('planner.settings.appearance', compact('user', 'preferences', 'businessStats'));
    }

    // ========== EXPORT DATA ==========
    public function exportData()
    {
        $user = auth()->user();
        $data = [
            'user' => $user,
            'events' => $user->plannerEvents,
            'preferences' => $user->preferences,
        ];

        return response()->json($data)->download('my-data.json');
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

    // ========== DELETE ACCOUNT ==========
    public function deleteAccount(Request $request)
    {
        try {
            $request->validate([
                'confirmation' => 'required|accepted',
                'password' => 'required|current_password',
            ]);

            $user = auth()->user();

            if ($user->profile_photo_path) {
                Storage::disk('public')->delete($user->profile_photo_path);
            }

            auth()->logout();
            $user->delete();

            return response()->json([
                'success' => true,
                'message' => 'Account deleted permanently!'
            ]);
        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            \Log::error('Account deletion error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Server error: ' . $e->getMessage()
            ], 500);
        }
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