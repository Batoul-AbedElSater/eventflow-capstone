<?php

namespace App\Http\Controllers\Client;

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
        return redirect()->route('client.settings.account');
    }

    // ========== ACCOUNT SETTINGS ==========
    public function account()
    {
        $user = auth()->user();
        return view('client.settings.account', compact('user'));
    }

    public function updateProfile(Request $request)
    {
        try {
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
        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            \Log::error('Profile update error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Server error: ' . $e->getMessage()
            ], 500);
        }
    }

    public function updateProfilePhoto(Request $request)
    {
        try {
            $request->validate([
                'photo' => 'required|image|mimes:jpeg,png,jpg,gif|max:5120'
            ]);

            $user = auth()->user();
            if ($user->profile_photo_path) {
                Storage::disk('public')->delete($user->profile_photo_path);
            }

            $path = $request->file('photo')->store('clients/profile-photos', 'public');
            $user->update(['profile_photo_path' => $path]);

            return response()->json([
                'success' => true,
                'message' => 'Photo updated!',
                'url' => asset('storage/' . $path)
            ]);
        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            \Log::error('Photo update error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Server error: ' . $e->getMessage()
            ], 500);
        }
    }

    public function changePassword(Request $request)
    {
        try {
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
        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            \Log::error('Password change error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Server error: ' . $e->getMessage()
            ], 500);
        }
    }

    // ========== NOTIFICATIONS ==========
    public function notifications()
    {
        $user = auth()->user();
        $preferences = $user->preferences ?? new UserPreference();
        return view('client.settings.notifications', compact('user', 'preferences'));
    }

   public function updateNotifications(Request $request)
{
    try {
        $validated = $request->validate([
            'email_planner_updates' => 'nullable|boolean',
            'email_reminders' => 'nullable|boolean',
            'email_event_updates' => 'nullable|boolean',
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
    } catch (ValidationException $e) {
        return response()->json([
            'success' => false,
            'message' => 'Validation failed',
            'errors' => $e->errors()
        ], 422);
    } catch (\Exception $e) {
        \Log::error('Notification update error: ' . $e->getMessage());
        return response()->json([
            'success' => false,
            'message' => 'Server error: ' . $e->getMessage()
        ], 500);
    }
}

    // ========== PRIVACY ==========
    public function privacy()
    {
        $user = auth()->user();
        $preferences = $user->preferences ?? new UserPreference();
        return view('client.settings.privacy', compact('user', 'preferences'));
    }

    public function updatePrivacy(Request $request)
    {
        try {
            $validated = $request->validate([
                'profile_visibility' => 'nullable|in:public,private,friends',
                'show_email' => 'nullable|boolean',
                'show_phone' => 'nullable|boolean',
                'allow_vendor_contact' => 'nullable|boolean',
                'allow_planner_suggestions' => 'nullable|boolean',
                'data_collection' => 'nullable|boolean',
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
                'message' => 'Privacy settings updated!'
            ]);
        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            \Log::error('Privacy update error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Server error: ' . $e->getMessage()
            ], 500);
        }
    }

    // ========== APPEARANCE ==========
    public function appearance()
    {
        $user = auth()->user();
        $preferences = $user->preferences ?? new UserPreference();
        return view('client.settings.appearance', compact('user', 'preferences'));
    }

    public function updateAppearance(Request $request)
    {
        try {
            $validated = $request->validate([
                'theme_mode' => 'nullable|in:light,dark,auto',
                'color_scheme' => 'nullable|in:coral,berry,green,mixed',
                'font_size' => 'nullable|in:small,medium,large',
                'animations' => 'nullable|boolean',
                'language' => 'nullable|in:en,ar,fr',
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
        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            \Log::error('Appearance update error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Server error: ' . $e->getMessage()
            ], 500);
        }
    }

    // ========== PREFERENCES ==========
    public function preferences()
    {
        $user = auth()->user();
        $preferences = $user->preferences ?? new UserPreference();
        return view('client.settings.preferences', compact('user', 'preferences'));
    }

    public function updatePreferences(Request $request)
    {
        try {
            $validated = $request->validate([
                'preferred_event_type' => 'nullable|string',
                'budget_range' => 'nullable|string',
                'ideal_guest_count' => 'nullable|integer',
                'favorite_vendors' => 'nullable|array',
                'favorite_planners' => 'nullable|array',
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
                'message' => 'Preferences saved!'
            ]);
        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            \Log::error('Preferences update error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Server error: ' . $e->getMessage()
            ], 500);
        }
    }

    // ========== EXPORT DATA ==========
    public function exportData()
    {
        $user = auth()->user();
        $data = [
            'user' => $user,
            'events' => $user->clientEvents,
            'preferences' => $user->preferences,
        ];

        return response()->json($data)->download('my-data.json');
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
}