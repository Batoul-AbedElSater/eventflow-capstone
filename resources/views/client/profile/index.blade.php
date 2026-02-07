@extends('layouts.client')

@section('title', 'My Profile')

@section('content')
<div class="profile-container">
    
    <!-- Header -->
    <div class="page-header">
        <h1>My Profile</h1>
        <p class="subtitle">Manage your personal information</p>
    </div>

    <!-- Success Message -->
    @if(session('success'))
        <div class="alert alert-success">
            <i class="fas fa-check-circle"></i>
            {{ session('success') }}
        </div>
    @endif

    <!-- Profile Card -->
    <div class="profile-card">
        <div class="profile-header">
            <div class="profile-avatar-large">
                {{ strtoupper(substr($user->name, 0, 2)) }}
            </div>
            <div class="profile-info">
                <h2>{{ $user->name }}</h2>
                <p class="role-badge">
                    <i class="fas fa-user"></i> Client
                </p>
                <p class="member-since">
                    Member since {{ $user->created_at->format('F Y') }}
                </p>
            </div>
        </div>
    </div>

    <!-- Profile Information Form -->
    <div class="form-section">
        <div class="section-header">
            <h3>
                <i class="fas fa-user-edit"></i>
                Personal Information
            </h3>
        </div>

        <form action="{{ route('client.profile.update') }}" method="POST" class="profile-form">
            @csrf
            @method('PUT')

            <div class="form-grid">
                <!-- Name -->
                <div class="form-group">
                    <label for="name">
                        Full Name <span class="required">*</span>
                    </label>
                    <input 
                        type="text" 
                        id="name" 
                        name="name" 
                        value="{{ old('name', $user->name) }}" 
                        required>
                    @error('name')
                        <span class="form-error">{{ $message }}</span>
                    @enderror
                </div>

                <!-- Email -->
                <div class="form-group">
                    <label for="email">
                        Email Address <span class="required">*</span>
                    </label>
                    <input 
                        type="email" 
                        id="email" 
                        name="email" 
                        value="{{ old('email', $user->email) }}" 
                        required>
                    @error('email')
                        <span class="form-error">{{ $message }}</span>
                    @enderror
                </div>

                <!-- Phone -->
                <div class="form-group">
                    <label for="phone">Phone Number</label>
                    <input 
                        type="text" 
                        id="phone" 
                        name="phone" 
                        value="{{ old('phone', $user->phone) }}"
                        placeholder="+1 (555) 123-4567">
                    @error('phone')
                        <span class="form-error">{{ $message }}</span>
                    @enderror
                </div>
            </div>

            <div class="form-actions">
                <button type="submit" class="btn-primary">
                    <i class="fas fa-save"></i> Save Changes
                </button>
            </div>
        </form>
    </div>

    <!-- Change Password Section -->
    <div class="form-section">
        <div class="section-header">
            <h3>
                <i class="fas fa-lock"></i>
                Change Password
            </h3>
        </div>

        <form action="{{ route('client.profile.password') }}" method="POST" class="profile-form">
            @csrf
            @method('PUT')

            <div class="form-grid">
                <!-- Current Password -->
                <div class="form-group full-width">
                    <label for="current_password">
                        Current Password <span class="required">*</span>
                    </label>
                    <input 
                        type="password" 
                        id="current_password" 
                        name="current_password" 
                        required>
                    @error('current_password')
                        <span class="form-error">{{ $message }}</span>
                    @enderror
                </div>

                <!-- New Password -->
                <div class="form-group">
                    <label for="new_password">
                        New Password <span class="required">*</span>
                    </label>
                    <input 
                        type="password" 
                        id="new_password" 
                        name="new_password" 
                        required>
                    <small class="form-hint">Minimum 8 characters</small>
                    @error('new_password')
                        <span class="form-error">{{ $message }}</span>
                    @enderror
                </div>

                <!-- Confirm Password -->
                <div class="form-group">
                    <label for="new_password_confirmation">
                        Confirm New Password <span class="required">*</span>
                    </label>
                    <input 
                        type="password" 
                        id="new_password_confirmation" 
                        name="new_password_confirmation" 
                        required>
                </div>
            </div>

            <div class="form-actions">
                <button type="submit" class="btn-primary">
                    <i class="fas fa-key"></i> Update Password
                </button>
            </div>
        </form>
    </div>

</div>

<link rel="stylesheet" href="{{ asset('css/client-profile.css') }}">
@endsection