@extends('planner.settings.index')

@section('settings-content')
<div class="settings-section">
    <div class="section-header">
        <h2 class="section-title" style="color: #475B35;">Notification Preferences</h2>
        <p class="section-subtitle">Control how and when you receive updates about your business</p>
    </div>

    <form id="notificationsForm" onsubmit="updateNotifications(event)">
        <!-- Client & Inquiry Notifications -->
        <div class="settings-card" style="border-top: 4px solid var(--coral-haze);">
            <h3 class="card-title">👥 Client & Inquiry Notifications</h3>
            <div class="notification-group">
                <div class="notification-item">
                    <div class="notification-content">
                        <label class="notification-label">New Inquiries</label>
                        <p class="notification-description">Get notified when clients send new event inquiries</p>
                    </div>
                    <label class="toggle-switch">
                        <input type="checkbox" name="email_new_inquiries" {{ $preferences->email_new_inquiries ? 'checked' : '' }}>
                        <span class="toggle-slider"></span>
                    </label>
                </div>

                <div class="notification-item">
                    <div class="notification-content">
                        <label class="notification-label">Client Messages</label>
                        <p class="notification-description">Direct messages and chat from your clients</p>
                    </div>
                    <label class="toggle-switch">
                        <input type="checkbox" name="email_client_messages" {{ $preferences->email_client_messages ? 'checked' : '' }}>
                        <span class="toggle-slider"></span>
                    </label>
                </div>

                <div class="notification-item">
                    <div class="notification-content">
                        <label class="notification-label">Inquiry Responses</label>
                        <p class="notification-description">When clients reply to your quotes and proposals</p>
                    </div>
                    <label class="toggle-switch">
                        <input type="checkbox" name="client_responses" {{ $preferences->client_responses ?? true ? 'checked' : '' }}>
                        <span class="toggle-slider"></span>
                    </label>
                </div>
            </div>
        </div>

        <!-- Team & Staff Notifications -->
        <div class="settings-card" style="border-top: 4px solid var(--garden-green);">
            <h3 class="card-title">👨‍💼 Team & Staff Updates</h3>
            <div class="notification-group">
                <div class="notification-item">
                    <div class="notification-content">
                        <label class="notification-label">Assistant Updates</label>
                        <p class="notification-description">Task completions and status updates from assistants</p>
                    </div>
                    <label class="toggle-switch">
                        <input type="checkbox" name="email_assistant_updates" {{ $preferences->email_assistant_updates ? 'checked' : '' }}>
                        <span class="toggle-slider"></span>
                    </label>
                </div>

                <div class="notification-item">
                    <div class="notification-content">
                        <label class="notification-label">Team Messages</label>
                        <p class="notification-description">Communication from your team members</p>
                    </div>
                    <label class="toggle-switch">
                        <input type="checkbox" name="team_messages" {{ $preferences->team_messages ?? true ? 'checked' : '' }}>
                        <span class="toggle-slider"></span>
                    </label>
                </div>

                <div class="notification-item">
                    <div class="notification-content">
                        <label class="notification-label">Task Reminders</label>
                        <p class="notification-description">Reminders for upcoming team tasks and deadlines</p>
                    </div>
                    <label class="toggle-switch">
                        <input type="checkbox" name="task_reminders" {{ $preferences->task_reminders ?? true ? 'checked' : '' }}>
                        <span class="toggle-slider"></span>
                    </label>
                </div>
            </div>
        </div>

        <!-- Vendor Notifications -->
        <div class="settings-card" style="border-top: 4px solid var(--calypso-berry);">
            <h3 class="card-title">🏪 Vendor Notifications</h3>
            <div class="notification-group">
                <div class="notification-item">
                    <div class="notification-content">
                        <label class="notification-label">Vendor Responses</label>
                        <p class="notification-description">Vendors replying to your quotes and requests</p>
                    </div>
                    <label class="toggle-switch">
                        <input type="checkbox" name="email_vendor_responses" {{ $preferences->email_vendor_responses ? 'checked' : '' }}>
                        <span class="toggle-slider"></span>
                    </label>
                </div>

                <div class="notification-item">
                    <div class="notification-content">
                        <label class="notification-label">Vendor Messages</label>
                        <p class="notification-description">Direct communication from vendors</p>
                    </div>
                    <label class="toggle-switch">
                        <input type="checkbox" name="vendor_messages" {{ $preferences->vendor_messages ?? true ? 'checked' : '' }}>
                        <span class="toggle-slider"></span>
                    </label>
                </div>

                <div class="notification-item">
                    <div class="notification-content">
                        <label class="notification-label">Vendor Availability</label>
                        <p class="notification-description">When vendors confirm or reject dates</p>
                    </div>
                    <label class="toggle-switch">
                        <input type="checkbox" name="vendor_availability" {{ $preferences->vendor_availability ?? true ? 'checked' : '' }}>
                        <span class="toggle-slider"></span>
                    </label>
                </div>

                <div class="notification-item">
                    <div class="notification-content">
                        <label class="notification-label">Price Updates</label>
                        <p class="notification-description">Notifications when vendor prices change</p>
                    </div>
                    <label class="toggle-switch">
                        <input type="checkbox" name="price_updates" {{ $preferences->price_updates ?? false ? 'checked' : '' }}>
                        <span class="toggle-slider"></span>
                    </label>
                </div>
            </div>
        </div>

        <!-- Event Reminders -->
        <div class="settings-card" style="border-top: 4px solid var(--vampire-hunter);">
            <h3 class="card-title">📅 Event Reminders</h3>
            <div class="notification-group">
                <div class="notification-item">
                    <div class="notification-content">
                        <label class="notification-label">Event Reminders</label>
                        <p class="notification-description">Reminders before upcoming events</p>
                    </div>
                    <label class="toggle-switch">
                        <input type="checkbox" name="email_event_reminders" {{ $preferences->email_event_reminders ? 'checked' : '' }}>
                        <span class="toggle-slider"></span>
                    </label>
                </div>

                <div class="notification-item">
                    <div class="notification-content">
                        <label class="notification-label">Deadline Alerts</label>
                        <p class="notification-description">Important deadline notifications</p>
                    </div>
                    <label class="toggle-switch">
                        <input type="checkbox" name="deadline_alerts" {{ $preferences->deadline_alerts ?? true ? 'checked' : '' }}>
                        <span class="toggle-slider"></span>
                    </label>
                </div>
            </div>
        </div>

        <!-- Notification Channels -->
        <div class="settings-card" style="border-top: 4px solid #FF9800;">
            <h3 class="card-title">📲 Notification Channels</h3>
            <div class="notification-group">
                <div class="notification-item">
                    <div class="notification-content">
                        <label class="notification-label">Push Notifications</label>
                        <p class="notification-description">Browser and device notifications</p>
                    </div>
                    <label class="toggle-switch">
                        <input type="checkbox" name="push_notifications" {{ $preferences->push_notifications ? 'checked' : '' }}>
                        <span class="toggle-slider"></span>
                    </label>
                </div>

                <div class="notification-item">
                    <div class="notification-content">
                        <label class="notification-label">SMS Notifications</label>
                        <p class="notification-description">Text messages for urgent updates</p>
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
        <div class="settings-card" style="border-top: 4px solid #2196F3;">
            <h3 class="card-title">⚡ Notification Frequency</h3>
            <p class="card-description">How often do you want to receive notifications?</p>
            <div class="frequency-selector">
                <label class="frequency-option">
                    <input type="radio" name="notification_frequency" value="instant" {{ $preferences->notification_frequency === 'instant' ? 'checked' : '' }}>
                    <span class="frequency-label">
                        <strong>Instant</strong>
                        <p>Get notified right away</p>
                    </span>
                </label>
                <label class="frequency-option">
                    <input type="radio" name="notification_frequency" value="daily" {{ $preferences->notification_frequency === 'daily' ? 'checked' : '' }}>
                    <span class="frequency-label">
                        <strong>Daily Digest</strong>
                        <p>Summary once per day (9 AM)</p>
                    </span>
                </label>
                <label class="frequency-option">
                    <input type="radio" name="notification_frequency" value="weekly" {{ $preferences->notification_frequency === 'weekly' ? 'checked' : '' }}>
                    <span class="frequency-label">
                        <strong>Weekly Digest</strong>
                        <p>Summary once per week (Monday 9 AM)</p>
                    </span>
                </label>
            </div>
        </div>

        <!-- Quiet Hours -->
        <div class="settings-card" style="border-top: 4px solid #9C27B0;">
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

<style>
    :root {
        --garden-green: #475B35;
        --amnesiac-white: #F5F9E5;
        --coral-haze: #E19184;
        --calypso-berry: #C63E4E;
        --vampire-hunter: #620607;
        --cream: #EFE7DA;
    }

    .settings-section {
        display: flex;
        flex-direction: column;
        gap: 32px;
    }

    .section-header {
        margin-bottom: 24px;
    }

    .section-title {
        font-family: 'Playfair Display', serif;
        font-size: 32px;
        font-weight: 900;
        margin-bottom: 8px;
    }

    .section-subtitle {
        color: #999;
        font-size: 14px;
    }

    .settings-card {
        background: linear-gradient(135deg, rgba(245,249,229,0.5) 0%, rgba(239,231,218,0.5) 100%);
        border-radius: 12px;
        padding: 32px;
        transition: all 0.3s ease;
    }

    .settings-card:hover {
        box-shadow: 0 8px 30px rgba(71, 91, 53, 0.1);
    }

    .card-title {
        font-size: 18px;
        font-weight: 700;
        color: #333;
        margin-bottom: 16px;
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
        
        fetch('{{ route("planner.settings.notifications") }}', {
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