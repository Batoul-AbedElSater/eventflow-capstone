@extends('layouts.guest')

@section('content')

<!-- Video Background -->
<video autoplay muted loop id="bg-video">
    <source src="{{ asset('videos/background1.mp4') }}" type="video/mp4">
</video>

<div class="container" id="container">
    
    {{-- REGISTER FORM (LEFT SIDE) --}}
    <div class="form-container sign-up-container">
        <form method="POST" action="{{ route('register') }}">
            @csrf
            <div class="form-title">Register</div>
            <h1>Create Account</h1>
            <p>Plan your unforgettable moments</p>
            
            {{-- Name --}}
            <div class="input-wrapper">
                <i class="fas fa-user"></i>
                <input type="text" name="name" placeholder="Full Name" value="{{ old('name') }}" required>
            </div>
            @error('name')<span class="error-msg">{{ $message }}</span>@enderror
            
            {{-- Email --}}
            <div class="input-wrapper">
                <i class="fas fa-envelope"></i>
                <input type="email" name="email" placeholder="Email" value="{{ old('email') }}" required>
            </div>
            @error('email')<span class="error-msg">{{ $message }}</span>@enderror
            
            {{-- Phone --}}
            <div class="input-wrapper">
                <i class="fas fa-phone"></i>
                <input type="text" name="phone" placeholder="Phone (Optional)" value="{{ old('phone') }}">
            </div>
            
            {{-- Password --}}
            <div class="input-wrapper">
                <i class="fas fa-lock"></i>
                <input type="password" id="reg-password" name="password" placeholder="Password" required>
                <i class="fas fa-eye toggle-password" onclick="togglePassword('reg-password', this)"></i>
            </div>
            @error('password')<span class="error-msg">{{ $message }}</span>@enderror
            
            {{-- Confirm Password --}}
            <div class="input-wrapper">
                <i class="fas fa-lock"></i>
                <input type="password" id="reg-password-confirm" name="password_confirmation" placeholder="Confirm Password" required>
                <i class="fas fa-eye toggle-password" onclick="togglePassword('reg-password-confirm', this)"></i>
            </div>
            
            {{-- Role Selection --}}
            <div class="role-wrapper">
                <label>Register as:</label>
                <div style="position: relative; display: inline-block; width: 100%;">
                    <select name="role" id="role-select" required style="width: 100%; padding: 12px 15px; border: 2px solid #ddd; border-radius: 8px; font-size: 14px; background: white; cursor: pointer; appearance: none; background-image: url('data:image/svg+xml;utf8,<svg fill=%22%23667eea%22 height=%2224%22 viewBox=%220 0 24 24%22 width=%2224%22 xmlns=%22http://www.w3.org/2000/svg%22><path d=%22M7 10l5 5 5-5z%22/></svg>'); background-repeat: no-repeat; background-position: right 8px center; background-size: 24px; padding-right: 40px;">
                        <option value="">Choose your role...</option>
                        <option value="client"><i class="fas fa-user-circle"></i> Client</option>
                        <option value="planner"><i class="fas fa-calendar-check"></i> Planner</option>
                        <option value="assistant"><i class="fas fa-person-hiking"></i> Assistant</option>
                    </select>
                </div>
            </div>
            @error('role')<span class="error-msg">{{ $message }}</span>@enderror
            
            <button type="submit" class="auth-btn">Sign Up</button>
        </form>
    </div>
    
    {{-- LOGIN FORM (RIGHT SIDE) --}}
    <div class="form-container sign-in-container">
        <form method="POST" action="{{ route('login') }}">
            @csrf
            <div class="form-title">Login</div>
            <h1>Welcome Back</h1>
            <p>Let's make magic happen</p>
            
            {{-- Email --}}
            <div class="input-wrapper">
                <i class="fas fa-envelope"></i>
                <input type="email" name="email" placeholder="Email" value="{{ old('email') }}" required>
            </div>
            @error('email')<span class="error-msg">{{ $message }}</span>@enderror
            
            {{-- Password --}}
            <div class="input-wrapper">
                <i class="fas fa-lock"></i>
                <input type="password" id="login-password" name="password" placeholder="Password" required>
                <i class="fas fa-eye toggle-password" onclick="togglePassword('login-password', this)"></i>
            </div>
            @error('password')<span class="error-msg">{{ $message }}</span>@enderror
            
            {{-- Remember Me --}}
            <label class="remember-me">
                <input type="checkbox" name="remember">
                <span>Remember me</span>
            </label>
            
            <button type="submit" class="auth-btn">Sign In</button>
        </form>
    </div>
    
    {{-- OVERLAY CONTAINER --}}
    <div class="overlay-container">
        <div class="overlay">
            
            {{-- LEFT PANEL (Shows when on Login side) --}}
            <div class="overlay-panel overlay-left">
                <h1>Welcome Back!</h1>
                @auth
                    <p class="welcome-user">Hello, {{ Auth::user()->name }}! </p>
                @else
                    <p>To keep connected with us please login with your personal info</p>
                @endauth
                <button class="ghost-btn" id="signIn">Sign In</button>
            </div>
            
            {{-- RIGHT PANEL (Shows when on Register side) --}}
            <div class="overlay-panel overlay-right">
                <h1>Hello, Friend!</h1>
                <p>Enter your personal details and start your journey with us</p>
                <button class="ghost-btn" id="signUp">Sign Up</button>
            </div>
            
        </div>
    </div>
    
</div>
@endsection
