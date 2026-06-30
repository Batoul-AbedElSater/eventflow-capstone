@extends('client.settings.index')

@section('settings-content')
<div class="settings-section">
    <div class="section-header">
        <h2 class="section-title" style="color: #475B35;">Notification Preferences</h2>
        <p class="section-subtitle">Choose how you want to stay updated</p>
    </div>

    <form id="notificationsForm" onsubmit="updateNotifications(event)">
        @csrf
        <div class="settings-card" style="border-top: 4px solid var(--coral-haze);">
            <h3 class="card-title">Email Notifications</h3>

            <div class="notification-group">
                <div class="notification-item">
                    <div class="notification-content">
                        <label class="notification-label">Planner Messages</label>
                        <p class="notification-description">Get notified when your planner sends you a new message</p>
                    </div>
                    <label class="toggle-switch">
                        <input type="checkbox" name="email_planner_updates" {{ ($preferences->email_planner_updates ?? true) ? 'checked' : '' }}>
                        <span class="toggle-slider"></span>
                    </label>
                </div>

                <div class="notification-item">
                    <div class="notification-content">
                        <label class="notification-label">Event Reminders</label>
                        <p class="notification-description">Receive reminders for your upcoming events (today, tomorrow, in 7 days)</p>
                    </div>
                    <label class="toggle-switch">
                        <input type="checkbox" name="email_reminders" {{ ($preferences->email_reminders ?? true) ? 'checked' : '' }}>
                        <span class="toggle-slider"></span>
                    </label>
                </div>

                <div class="notification-item">
                    <div class="notification-content">
                        <label class="notification-label">Event Updates</label>
                        <p class="notification-description">Get notified when event details are changed</p>
                    </div>
                    <label class="toggle-switch">
                        <input type="checkbox" name="email_event_updates" {{ ($preferences->email_event_updates ?? true) ? 'checked' : '' }}>
                        <span class="toggle-slider"></span>
                    </label>
                </div>
            </div>
        </div>

        <button type="submit" class="btn-save" style="background: linear-gradient(135deg, var(--coral-haze) 0%, var(--calypso-berry) 100%); color: white; padding: 14px 40px; border: none; border-radius: 8px; cursor: pointer; font-weight: 600; font-size: 15px; margin-top: 32px;">
            <i class="fas fa-save"></i> Save Preferences
        </button>
    </form>
</div>

<style>
    .settings-section { display: flex; flex-direction: column; gap: 32px; }
    .section-header { margin-bottom: 24px; }
    .section-title { font-family: 'Playfair Display', serif; font-size: 32px; font-weight: 900; margin-bottom: 8px; }
    .section-subtitle { color: #999; font-size: 14px; }

    .settings-card {
        background: linear-gradient(135deg, rgba(245,249,229,0.5) 0%, rgba(239,231,218,0.5) 100%);
        border-radius: 12px;
        padding: 32px;
        transition: all 0.3s ease;
    }
    .settings-card:hover { box-shadow: 0 8px 30px rgba(71, 91, 53, 0.1); }
    .card-title { font-size: 18px; font-weight: 700; color: #333; margin-bottom: 16px; }

    .notification-group { display: flex; flex-direction: column; gap: 20px; }
    .notification-item {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 16px 0;
        border-bottom: 1px solid #f0f0f0;
    }
    .notification-item:last-child { border-bottom: none; }
    .notification-content { flex: 1; }
    .notification-label { display: block; font-weight: 600; color: #333; margin-bottom: 4px; }
    .notification-description { font-size: 13px; color: #999; margin: 0; }

    .toggle-switch {
        position: relative;
        display: inline-block;
        width: 50px;
        height: 28px;
        flex-shrink: 0;
    }
    .toggle-switch input { opacity: 0; width: 0; height: 0; }
    .toggle-slider {
        position: absolute;
        cursor: pointer;
        top: 0; left: 0; right: 0; bottom: 0;
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
    input:checked + .toggle-slider:before { transform: translateX(22px); }

    .btn-save {
        background: linear-gradient(135deg, var(--coral-haze) 0%, var(--calypso-berry) 100%);
        color: white;
        border: none;
        padding: 12px 32px;
        border-radius: 8px;
        cursor: pointer;
        font-weight: 600;
        font-size: 15px;
        transition: all 0.3s ease;
        display: inline-flex;
        align-items: center;
        gap: 8px;
    }
    .btn-save:hover { transform: translateY(-2px); box-shadow: 0 6px 20px rgba(225,145,132,0.4); }
</style>

<script>
    function updateNotifications(e) {
        e.preventDefault();
        const form = document.getElementById('notificationsForm');
        const formData = new FormData(form);

        fetch('{{ route("client.settings.notifications.update") }}', {
            method: 'POST',
            body: formData,
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            }
        })
        .then(response => {
            if (!response.ok) {
                return response.json().then(err => { throw err; });
            }
            return response.json();
        })
        .then(data => {
            if (data.success) {
                alert(data.message);
            } else {
                let msg = data.message || 'Update failed';
                if (data.errors) {
                    msg += '\n' + Object.values(data.errors).flat().join('\n');
                }
                alert(msg);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('An error occurred. Please check the console.');
        });
    }
</script>
@endsection