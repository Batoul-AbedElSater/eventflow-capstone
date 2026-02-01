<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\ClientProfile;
use App\Models\PlannerProfile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    // Show register form
    public function showRegister()
    {
        return view('auth.register');
    }

    // Handle registration
    public function register(Request $request)
    {
        // Validate input: name, email, password, role required
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email', // Must be unique
            'password' => 'required|min:8|confirmed', // Must match password_confirmation
            'role' => 'required|in:client,planner', // Only client or planner
            'phone' => 'required|string|max:20',
        ]);

        // Create user in database
        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']), // Hash password for security
            'role' => $validated['role'],
            'phone' => $validated['phone'] ?? null,
        ]);

        // Create profile based on role
        if ($user->role === 'client') {
            ClientProfile::create(['user_id' => $user->id]); // Empty client profile
        } else {
            PlannerProfile::create(['user_id' => $user->id]); // Empty planner profile
        }

        // Log user in automatically
        Auth::login($user);

        // Redirect to dashboard based on role
        return $user->isClient() 
            ? redirect()->route('client.dashboard')
            : redirect()->route('planner.dashboard');
    }

    // Show login form
    public function showLogin()
    {
        return view('auth.login');
    }

    // Handle login
    public function login(Request $request)
    {
        // Validate credentials
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        // Attempt login (checks email + hashed password)
        if (Auth::attempt($credentials, $request->boolean('remember'))) {
            $request->session()->regenerate(); // Security: prevent session fixation

            $user = Auth::user();
            
            // Redirect based on role
            return $user->isClient()
                ? redirect()->route('client.dashboard')
                : redirect()->route('planner.dashboard');
        }

        // Login failed - return with error
        return back()->withErrors([
            'email' => 'Invalid credentials.',
        ])->onlyInput('email');
    }

    // Handle logout
    public function logout(Request $request)
    {
        Auth::logout(); // Clear session
        $request->session()->invalidate(); // Destroy session
        $request->session()->regenerateToken(); // CSRF security

        return redirect()->route('login');
    }
}
