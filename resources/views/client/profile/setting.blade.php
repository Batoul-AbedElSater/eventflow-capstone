@extends('layouts.client')

@section('title', 'Settings')

@section('content')
<div class="settings-container">
    
    <!-- Back to Dashboard -->
    <a href="{{ route('client.dashboard') }}" class="back-link">
        <i class="fas fa-arrow-left"></i> Back to Dashboard
    </a>

    <!-- Page Header -->
    <div class="page-header-section">
        <div class="page-header-content">
            <h1><i class="fas fa-cog"></i> Settings</h1>
            <p class="subtitle">Manage your preferences and notifications</p>
        </div>
    </div>

    <!-- Success Message -->
    @if(session('success'))
        <div class="alert alert-success">
            <i class="fas fa-check-circle"></i>
            {{ session('success') }}
        </div>
    @endif

    @php
        $preferences = json_decode($user->notification_preferences ?? '{}', true);
    @endphp

    <!-- Notification Settings -->
    <div class="settings-section">
        <div class="section-header">
            <h3>
                <i class="fas fa-bell"></i>
                Notification Preferences
            </h3>
        </div>

        <form action="{{ route('client.settings.update') }}" method="POST" class="settings-form">
            @csrf
            @method('PUT')

            <div class="settings-list">
                <!-- Email Notifications -->
                <div class="setting-item">
                    <div class="setting-info">
                        <h4>Email Notifications</h4>
                        <p>Receive updates and alerts via email</p>
                    </div>
                    <label class="toggle-switch">
                        <input 
                            type="checkbox" 
                            name="email_notifications" 
                            value="1"
                            {{ ($preferences['email_notifications'] ?? false) ? 'checked' : '' }}>
                        <span class="toggle-slider"></span>
                    </label>
                </div>

                <!-- SMS Notifications -->
                <div class="setting-item">
                    <div class="setting-info">
                        <h4>SMS Notifications</h4>
                        <p>Get text messages for important updates</p>
                    </div>
                    <label class="toggle-switch">
                        <input 
                            type="checkbox" 
                            name="sms_notifications" 
                            value="1"
                            {{ ($preferences['sms_notifications'] ?? false) ? 'checked' : '' }}>
                        <span class="toggle-slider"></span>
                    </label>
                </div>

                <!-- Task Reminders -->
                <div class="setting-item">
                    <div class="setting-info">
                        <h4>Task Reminders</h4>
                        <p>Get notified about upcoming task deadlines</p>
                    </div>
                    <label class="toggle-switch">
                        <input 
                            type="checkbox" 
                            name="task_reminders" 
                            value="1"
                            {{ ($preferences['task_reminders'] ?? false) ? 'checked' : '' }}>
                        <span class="toggle-slider"></span>
                    </label>
                </div>

                <!-- Budget Alerts -->
                <div class="setting-item">
                    <div class="setting-info">
                        <h4>Budget Alerts</h4>
                        <p>Receive alerts when budget limits are reached</p>
                    </div>
                    <label class="toggle-switch">
                        <input 
                            type="checkbox" 
                            name="budget_alerts" 
                            value="1"
                            {{ ($preferences['budget_alerts'] ?? false) ? 'checked' : '' }}>
                        <span class="toggle-slider"></span>
                    </label>
                </div>
            </div>

            <div class="form-actions">
                <button type="submit" class="btn-primary">
                    <i class="fas fa-save"></i> Save Settings
                </button>
            </div>
        </form>
    </div>

    <!-- Account Information -->
    <div class="settings-section">
        <div class="section-header">
            <h3>
                <i class="fas fa-info-circle"></i>
                Account Information
            </h3>
        </div>

        <div class="info-grid">
            <div class="info-item">
                <span class="info-label">Account Type</span>
                <span class="info-value">
                    <span class="role-badge">
                        <i class="fas fa-user"></i> Client
                    </span>
                </span>
            </div>

            <div class="info-item">
                <span class="info-label">Member Since</span>
                <span class="info-value">{{ $user->created_at->format('F d, Y') }}</span>
            </div>

            <div class="info-item">
                <span class="info-label">Total Events</span>
                <span class="info-value">{{ $user->clientEvents->count() }}</span>
            </div>

            <div class="info-item">
                <span class="info-label">Account Status</span>
                <span class="info-value">
                    <span class="status-badge active">
                        <i class="fas fa-check-circle"></i> Active
                    </span>
                </span>
            </div>
        </div>
    </div>

    <!-- Danger Zone -->
    <div class="settings-section danger-zone">
        <div class="section-header">
            <h3>
                <i class="fas fa-exclamation-triangle"></i>
                Danger Zone
            </h3>
        </div>

        <div class="danger-content">
            <div class="danger-info">
                <h4>Delete Account</h4>
                <p>Permanently delete your account and all associated data. This action cannot be undone.</p>
            </div>
            <button class="btn-danger" onclick="alert('Account deletion feature coming soon')">
                <i class="fas fa-trash"></i> Delete Account
            </button>
        </div>
    </div>

</div>

<link rel="stylesheet" href="{{ asset('css/client-profile.css') }}">
@endsection