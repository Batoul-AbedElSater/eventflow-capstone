@extends('layouts.assistant')

@section('content')
<div class="settings-wrapper">
    <nav class="settings-navbar">
        <div class="settings-header">
            <h1 class="settings-title" style="color: white;">Notification Settings</h1>
            <p class="settings-subtitle">Manage how you receive alerts and updates</p>
        </div>
    </nav>

    <div class="settings-container">
        <!-- Left Sidebar Navigation -->
        <div class="settings-sidebar">
            <div class="sidebar-menu">
                <a href="{{ route('assistant.settings.account') }}" class="menu-item">
                    <svg class="menu-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                    </svg>
                    <span>Account</span>
                </a>

                <a href="{{ route('assistant.settings.skills') }}" class="menu-item">
                    <svg class="menu-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4"></path>
                    </svg>
                    <span>Skills</span>
                </a>

                <a href="{{ route('assistant.settings.availability') }}" class="menu-item">
                    <svg class="menu-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                    </svg>
                    <span>Availability</span>
                </a>

                <a href="{{ route('assistant.settings.notifications') }}" class="menu-item active">
                    <svg class="menu-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path>
                    </svg>
                    <span>Notifications</span>
                </a>

                <a href="{{ route('assistant.settings.appearance') }}" class="menu-item">
                    <svg class="menu-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21a4 4 0 01-4-4V5a2 2 0 012-2h4a2 2 0 012 2v12a4 4 0 01-4 4zm0 0h12a2 2 0 002-2v-4a2 2 0 00-2-2h-2.343M11 7.343l1.657-1.657a2 2 0 012.828 0l2.829 2.829a2 2 0 010 2.828l-8.486 8.485M7 17h.01"></path>
                    </svg>
                    <span>Appearance</span>
                </a>
            </div>
        </div>

        <!-- Main Content -->
        <div class="settings-content">
            <form id="notificationsForm" onsubmit="updateNotifications(event)">
                <!-- Task Notifications -->
                <div class="settings-card" style="border-top: 4px solid var(--coral-haze);">
                    <h3 class="card-title">📋 Task Notifications</h3>
                    <div class="notification-group">
                        <div class="notification-item">
                            <div class="notification-content">
                                <label class="notification-label">New Task Assignments</label>
                                <p class="notification-description">Get notified when planner assigns you a new task</p>
                            </div>
                            <label class="toggle-switch">
                                <input type="checkbox" name="email_task_assignments" {{ $preferences->email_task_assignments ? 'checked' : '' }}>
                                <span class="toggle-slider"></span>
                            </label>
                        </div>

                        <div class="notification-item">
                            <div class="notification-content">
                                <label class="notification-label">Task Reminders</label>
                                <p class="notification-description">Reminders for upcoming task deadlines</p>
                            </div>
                            <label class="toggle-switch">
                                <input type="checkbox" name="email_task_reminders" {{ $preferences->email_task_reminders ? 'checked' : '' }}>
                                <span class="toggle-slider"></span>
                            </label>
                        </div>

                        <div class="notification-item">
                            <div class="notification-content">
                                <label class="notification-label">Task Updates</label>
                                <p class="notification-description">Changes to your assigned tasks</p>
                            </div>
                            <label class="toggle-switch">
                                <input type="checkbox" name="task_updates" {{ $preferences->task_updates ?? true ? 'checked' : '' }}>
                                <span class="toggle-slider"></span>
                            </label>
                        </div>

                        <div class="notification-item">
                            <div class="notification-content">
                                <label class="notification-label">Task Completed Confirmation</label>
                                <p class="notification-description">Confirmation when you mark a task as complete</p>
                            </div>
                            <label class="toggle-switch">
                                <input type="checkbox" name="task_completion_confirm" {{ $preferences->task_completion_confirm ?? true ? 'checked' : '' }}>
                                <span class="toggle-slider"></span>
                            </label>
                        </div>
                    </div>
                </div>

                <!-- Planner Communications -->
                <div class="settings-card" style="border-top: 4px solid var(--garden-green);">
                    <h3 class="card-title">👔 Planner Messages</h3>
                    <div class="notification-group">
                        <div class="notification-item">
                            <div class="notification-content">
                                <label class="notification-label">Direct Messages</label>
                                <p class="notification-description">Messages from your planner</p>
                            </div>
                            <label class="toggle-switch">
                                <input type="checkbox" name="email_planner_messages" {{ $preferences->email_planner_messages ? 'checked' : '' }}>
                                <span class="toggle-slider"></span>
                            </label>
                        </div>

                        <div class="notification-item">
                            <div class="notification-content">
                                <label class="notification-label">Planner Requests</label>
                                <p class="notification-description">Special requests or changes from planner</p>
                            </div>
                            <label class="toggle-switch">
                                <input type="checkbox" name="planner_requests" {{ $preferences->planner_requests ?? true ? 'checked' : '' }}>
                                <span class="toggle-slider"></span>
                            </label>
                        </div>

                        <div class="notification-item">
                            <div class="notification-content">
                                <label class="notification-label">Feedback & Reviews</label>
                                <p class="notification-description">When planner leaves feedback on your work</p>
                            </div>
                            <label class="toggle-switch">
                                <input type="checkbox" name="feedback_reviews" {{ $preferences->feedback_reviews ?? true ? 'checked' : '' }}>
                                <span class="toggle-slider"></span>
                            </label>
                        </div>
                    </div>
                </div>

                <!-- Order & Vendor Notifications -->
                <div class="settings-card" style="border-top: 4px solid var(--calypso-berry);">
                    <h3 class="card-title">🛍️ Order & Vendor Notifications</h3>
                    <div class="notification-group">
                        <div class="notification-item">
                            <div class="notification-content">
                                <label class="notification-label">Order Confirmations</label>
                                <p class="notification-description">When your vendor orders are confirmed</p>
                            </div>
                            <label class="toggle-switch">
                                <input type="checkbox" name="order_confirmations" {{ $preferences->order_confirmations ?? true ? 'checked' : '' }}>
                                <span class="toggle-slider"></span>
                            </label>
                        </div>

                        <div class="notification-item">
                            <div class="notification-content">
                                <label class="notification-label">Order Shipping</label>
                                <p class="notification-description">Shipping and delivery updates for orders</p>
                            </div>
                            <label class="toggle-switch">
                                <input type="checkbox" name="order_shipping" {{ $preferences->order_shipping ?? true ? 'checked' : '' }}>
                                <span class="toggle-slider"></span>
                            </label>
                        </div>

                        <div class="notification-item">
                            <div class="notification-content">
                                <label class="notification-label">Vendor Messages</label>
                                <p class="notification-description">Communication from vendors</p>
                            </div>
                            <label class="toggle-switch">
                                <input type="checkbox" name="vendor_messages" {{ $preferences->vendor_messages ?? true ? 'checked' : '' }}>
                                <span class="toggle-slider"></span>
                            </label>
                        </div>
                    </div>
                </div>

                <!-- Notification Channels -->
                <div class="settings-card" style="border-top: 4px solid var(--vampire-hunter);">
                    <h3 class="card-title">📲 Notification Channels</h3>
                    <div class="notification-group">
                        <div class="notification-item">
                            <div class="notification-content">
                                <label class="notification-label">Email Notifications</label>
                                <p class="notification-description">Receive important updates via email</p>
                            </div>
                            <label class="toggle-switch">
                                <input type="checkbox" name="email_notifications" {{ $preferences->email_notifications ?? true ? 'checked' : '' }}>
                                <span class="toggle-slider"></span>
                            </label>
                        </div>

                        <div class="notification-item">
                            <div class="notification-content">
                                <label class="notification-label">Push Notifications</label>
                                <p class="notification-description">Browser and device push notifications</p>
                            </div>
                            <label class="toggle-switch">
                                <input type="checkbox" name="push_notifications" {{ $preferences->push_notifications ? 'checked' : '' }}>
                                <span class="toggle-slider"></span>
                            </label>
                        </div>

                        <div class="notification-item">
                            <div class="notification-content">
                                <label class="notification-label">SMS Notifications</label>
                                <p class="notification-description">Text message alerts for urgent updates</p>
                            </div>
                            <label class="toggle-switch">
                                <input type="checkbox" name="sms_notifications" {{ $preferences->sms_notifications ? 'checked' : '' }}>
                                <span class="toggle-slider"></span>
                            </label>
                        </div>

                        <div class="notification-item">
                            <div class="notification-content">
                                <label class="notification-label">In-App Notifications</label>
                                <p class="notification-description">Notifications within the application</p>
                            </div>
                            <label class="toggle-switch">
                                <input type="checkbox" name="in_app_notifications" {{ $preferences->in_app_notifications ?? true ? 'checked' : '' }}>
                                <span class="toggle-slider"></span>
                            </label>
                        </div>
                    </div>
                </div>

                <!-- Notification Frequency -->
                <div class="settings-card" style="border-top: 4px solid #FF9800;">
                    <h3 class="card-title">⚡ Notification Frequency</h3>
                    <p class="card-description">How often do you want to receive notifications?</p>
                    <div class="frequency-selector">
                        <label class="frequency-option">
                            <input type="radio" name="notification_frequency" value="instant" {{ $preferences->notification_frequency === 'instant' ? 'checked' : '' }}>
                            <span class="frequency-label">
                                <strong>Instant</strong>
                                <p>Get notified immediately</p>
                            </span>
                        </label>
                        <label class="frequency-option">
                            <input type="radio" name="notification_frequency" value="daily" {{ $preferences->notification_frequency === 'daily' ? 'checked' : '' }}>
                            <span class="frequency-label">
                                <strong>Daily Digest</strong>
                                <p>Summary once per day at 9 AM</p>
                            </span>
                        </label>
                        <label class="frequency-option">
                            <input type="radio" name="notification_frequency" value="weekly" {{ $preferences->notification_frequency === 'weekly' ? 'checked' : '' }}>
                            <span class="frequency-label">
                                <strong>Weekly Digest</strong>
                                <p>Summary once per week</p>
                            </span>
                        </label>
                    </div>
                </div>

                <!-- Quiet Hours -->
                <div class="settings-card" style="border-top: 4px solid #2196F3;">
                    <h3 class="card-title">🔇 Quiet Hours</h3>
                    <p class="card-description">Pause notifications during specific times</p>
                    <div class="quiet-hours-section">
                        <div class="quiet-toggle">
                            <label class="toggle-switch">
                                <input type="checkbox" name="enable_quiet_hours" {{ $preferences->enable_quiet_hours ? 'checked' : '' }}>
                                <span class="toggle-slider"></span>
                            </label>
                            <span>Enable Quiet Hours</span>
                        </div>
                        <div class="quiet-times" id="quietTimesSection" style="display: none; margin-top: 20px;">
                            <div class="form-group">
                                <label>From</label>
                                <input type="time" name="quiet_hours_start" value="{{ $preferences->quiet_hours_start ?? '22:00' }}">
                            </div>
                            <div class="form-group">
                                <label>To</label>
                                <input type="time" name="quiet_hours_end" value="{{ $preferences->quiet_hours_end ?? '08:00' }}">
                            </div>
                        </div>
                    </div>
                </div>

                <button type="submit" class="btn-save" style="background: linear-gradient(135deg, var(--coral-haze) 0%, var(--calypso-berry) 100%); color: white; padding: 14px 40px; border: none; border-radius: 8px; cursor: pointer; font-weight: 600; font-size: 15px; margin-top: 32px;">
                    Save Notification Preferences
                </button>
            </form>
        </div>
    </div>
</div>

<style>
    :root {
        --garden-green: #475B35;
        --amnesiac-white: #F5F9E5;
        --coral-haze: #E19184;
        --calypso-berry: #C63E4E;
        --vampire-hunter: #620607;
        --cream: #EFE7DA;
    }

    .settings-wrapper {
        min-height: 100vh;
        background: linear-gradient(135deg, var(--amnesiac-white) 0%, var(--cream) 100%);
    }

    .settings-navbar {
        background: linear-gradient(135deg, var(--garden-green) 0%, var(--calypso-berry) 100%);
        color: white;
        padding: 40px;
        box-shadow: 0 10px 30px rgba(71, 91, 53, 0.15);
    }

    .settings-title {
        font-family: 'Playfair Display', serif;
        font-size: 48px;
        font-weight: 900;
        margin-bottom: 8px;
        letter-spacing: -1px;
    }

    .settings-subtitle {
        font-size: 16px;
        opacity: 0.9;
    }

    .settings-container {
        max-width: 1400px;
        margin: 0 auto;
        padding: 40px;
        display: grid;
        grid-template-columns: 280px 1fr;
        gap: 40px;
    }

    .settings-sidebar {
        position: sticky;
        top: 100px;
        height: fit-content;
    }

    .sidebar-menu {
        background: white;
        border-radius: 16px;
        overflow: hidden;
        box-shadow: 0 4px 20px rgba(71, 91, 53, 0.08);
    }

    .menu-item {
        display: flex;
        align-items: center;
        gap: 12px;
        padding: 16px 20px;
        color: #555;
        text-decoration: none;
        border-left: 4px solid transparent;
        transition: all 0.3s ease;
        cursor: pointer;
        border: none;
        background: none;
        width: 100%;
        font-size: 14px;
        font-weight: 500;
    }

    .menu-item:hover {
        background: var(--amnesiac-white);
        color: var(--calypso-berry);
        border-left-color: var(--coral-haze);
    }

    .menu-item.active {
        background: linear-gradient(90deg, var(--coral-haze) 0%, var(--calypso-berry) 100%);
        color: white;
        border-left-color: white;
    }

    .menu-icon {
        width: 20px;
        height: 20px;
    }

    .settings-content {
        background: white;
        border-radius: 16px;
        padding: 40px;
        box-shadow: 0 4px 20px rgba(71, 91, 53, 0.08);
    }

    .settings-card {
        background: linear-gradient(135deg, rgba(245,249,229,0.5) 0%, rgba(239,231,218,0.5) 100%);
        border-radius: 12px;
        padding: 32px;
        margin-bottom: 24px;
        transition: all 0.3s ease;
    }

    .settings-card:hover {
        box-shadow: 0 8px 30px rgba(71, 91, 53, 0.1);
    }

    .card-title {
        font-size: 18px;
        font-weight: 700;
        color: #333;
        margin-bottom: 24px;
    }

    .card-description {
        font-size: 13px;
        color: #999;
        margin-bottom: 16px;
    }

    .notification-group {
        display: flex;
        flex-direction: column;
        gap: 20px;
    }

    .notification-item {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 16px 0;
        border-bottom: 1px solid #f0f0f0;
    }

    .notification-item:last-child {
        border-bottom: none;
    }

    .notification-content {
        flex: 1;
    }

    .notification-label {
        display: block;
        font-weight: 600;
        color: #333;
        margin-bottom: 4px;
    }

    .notification-description {
        font-size: 13px;
        color: #999;
        margin: 0;
    }

    /* Toggle Switch */
    .toggle-switch {
        position: relative;
        display: inline-block;
        width: 50px;
        height: 28px;
    }

    .toggle-switch input {
        opacity: 0;
        width: 0;
        height: 0;
    }

    .toggle-slider {
        position: absolute;
        cursor: pointer;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background-color: #ccc;
        transition: 0.4s;
        border-radius: 28px;
    }

    .toggle-slider:before {
        position: absolute;
        content: "";
        height: 22px;
        width: 22px;
        left: 3px;
        bottom: 3px;
        background-color: white;
        transition: 0.4s;
        border-radius: 50%;
    }

    input:checked + .toggle-slider {
        background: linear-gradient(135deg, var(--coral-haze) 0%, var(--calypso-berry) 100%);
    }

    input:checked + .toggle-slider:before {
        transform: translateX(22px);
    }

    /* Frequency Selector */
    .frequency-selector {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
        gap: 16px;
    }

    .frequency-option {
        position: relative;
        cursor: pointer;
    }

    .frequency-option input {
        display: none;
    }

    .frequency-label {
        display: block;
        padding: 16px;
        border: 2px solid #e0e0e0;
        border-radius: 8px;
        transition: all 0.3s ease;
    }

    .frequency-label strong {
        display: block;
        color: #333;
        font-size: 15px;
        margin-bottom: 4px;
    }

    .frequency-label p {
        font-size: 12px;
        color: #999;
        margin: 0;
    }

    .frequency-option input:checked + .frequency-label {
        border-color: var(--coral-haze);
        background: rgba(225, 145, 132, 0.1);
    }

    /* Quiet Hours */
    .quiet-hours-section {
        background: white;
        border-radius: 8px;
        padding: 16px;
    }

    .quiet-toggle {
        display: flex;
        align-items: center;
        gap: 12px;
    }

    .quiet-toggle span {
        font-weight: 600;
        color: #333;
    }

    .quiet-times {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 16px;
        margin-top: 16px;
        padding-top: 16px;
        border-top: 1px solid #f0f0f0;
    }

    .form-group {
        margin-bottom: 0;
    }

    .form-group label {
        display: block;
        font-size: 13px;
        font-weight: 600;
        color: #555;
        margin-bottom: 8px;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .form-group input {
        width: 100%;
        padding: 10px 12px;
        border: 1.5px solid #e0e0e0;
        border-radius: 6px;
        font-family: 'Raleway', sans-serif;
        font-size: 14px;
    }

    .form-group input:focus {
        outline: none;
        border-color: var(--coral-haze);
        box-shadow: 0 0 0 3px rgba(225, 145, 132, 0.1);
    }

    @media (max-width: 1024px) {
        .settings-container {
            grid-template-columns: 1fr;
        }

        .settings-sidebar {
            position: static;
        }

        .sidebar-menu {
            display: flex;
            overflow-x: auto;
        }

        .menu-item {
            white-space: nowrap;
            flex: 0 0 auto;
        }
    }

    @media (max-width: 768px) {
        .frequency-selector {
            grid-template-columns: 1fr;
        }

        .quiet-times {
            grid-template-columns: 1fr;
        }
    }
</style>

<script>
    function updateNotifications(e) {
        e.preventDefault();
        const formData = new FormData(document.getElementById('notificationsForm'));
        
        fetch('{{ route("assistant.settings.notifications") }}', {
            method: 'POST',
            body: formData,
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            }
        })
        .then(r => r.json())
        .then(data => {
            if (data.success) {
                alert(data.message);
            }
        });
    }

    // Toggle quiet hours section
    document.querySelector('input[name="enable_quiet_hours"]').addEventListener('change', function(e) {
        const section = document.getElementById('quietTimesSection');
        section.style.display = e.target.checked ? 'grid' : 'none';
    });

    // Initialize quiet hours visibility
    const quietHoursCheckbox = document.querySelector('input[name="enable_quiet_hours"]');
    if (quietHoursCheckbox.checked) {
        document.getElementById('quietTimesSection').style.display = 'grid';
    }
</script>
@endsection