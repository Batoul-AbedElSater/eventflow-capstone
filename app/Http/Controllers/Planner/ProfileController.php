<?php

namespace App\Http\Controllers\Planner;

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
        $layout = 'layouts.planner';
        $roleLabel = 'Planner';
        $routePrefix = 'planner';

        return view('profile.index', compact('user', 'layout', 'roleLabel', 'routePrefix'));
    }

    public function updateProfile(Request $request)
    {
        $user = Auth::user();

        $validated = $request->validate([
            'name'   => 'required|string|max:255',
            'email'  => 'required|email|max:255|unique:users,email,' . $user->id,
            'phone'  => 'nullable|string|max:20',
            'avatar' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
        ]);

        if ($request->hasFile('avatar')) {

    if (!empty($user->avatar_url) && Storage::disk('public')->exists($user->avatar_url)) {
        Storage::disk('public')->delete($user->avatar_url);
    }

    $path = $request->file('avatar')->store('avatars', 'public');

    $user->avatar_url = $path;

    logger()->info('Avatar saved at: ' . $path);
}

       $user->name = $validated['name'];
$user->email = $validated['email'];
$user->phone = $validated['phone'] ?? null;

if (!$user->save()) {
    dd('Save failed');
}

$user->refresh();
        return redirect()->route('planner.profile')
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
            'password' => Hash::make($validated['new_password']),
        ]);

        return redirect()->route('planner.profile')
            ->with('success', 'Password changed successfully!');
    }
}
