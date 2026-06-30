@extends('client.settings.index')

@section('settings-content')
<div class="settings-section">
    <div class="section-header">
        <h2 class="section-title" style="color: #475B35;">Privacy & Safety</h2>
        <p class="section-subtitle">Control who can see your information and how it's used</p>
    </div>

    <form id="privacyForm" onsubmit="updatePrivacy(event)">
        <!-- Profile Visibility -->
        <div class="settings-card" style="border-top: 4px solid var(--garden-green);">
            <h3 class="card-title">👁️ Profile Visibility</h3>
            <div class="privacy-group">
                <p class="group-description">Who can see your profile information?</p>
                <label class="radio-option">
                    <input type="radio" name="profile_visibility" value="public" {{ $preferences->profile_visibility === 'public' ? 'checked' : '' }}>
                    <span class="radio-label">
                        <strong>Public</strong>
                        <p>Anyone can view your profile</p>
                    </span>
                </label>
                <label class="radio-option">
                    <input type="radio" name="profile_visibility" value="friends" {{ $preferences->profile_visibility === 'friends' ? 'checked' : '' }}>
                    <span class="radio-label">
                        <strong>Friends Only</strong>
                        <p>Only people you've worked with</p>
                    </span>
                </label>
                <label class="radio-option">
                    <input type="radio" name="profile_visibility" value="private" {{ $preferences->profile_visibility === 'private' ? 'checked' : '' }}>
                    <span class="radio-label">
                        <strong>Private</strong>
                        <p>Nobody can view your profile</p>
                    </span>
                </label>
            </div>
        </div>

        <!-- Contact Information -->
        <div class="settings-card" style="border-top: 4px solid var(--coral-haze);">
            <h3 class="card-title">📧 Contact Information Visibility</h3>
            <div class="privacy-group">
                <div class="privacy-toggle-item">
                    <div class="privacy-content">
                        <label class="privacy-label">Show Email Address</label>
                        <p class="privacy-description">Allow vendors and planners to see your email</p>
                    </div>
                    <label class="toggle-switch">
                        <input type="checkbox" name="show_email" {{ $preferences->show_email ? 'checked' : '' }}>
                        <span class="toggle-slider"></span>
                    </label>
                </div>

                <div class="privacy-toggle-item">
                    <div class="privacy-content">
                        <label class="privacy-label">Show Phone Number</label>
                        <p class="privacy-description">Allow vendors and planners to see your phone</p>
                    </div>
                    <label class="toggle-switch">
                        <input type="checkbox" name="show_phone" {{ $preferences->show_phone ? 'checked' : '' }}>
                        <span class="toggle-slider"></span>
                    </label>
                </div>
            </div>
        </div>

        <!-- Communication Preferences -->
        <div class="settings-card" style="border-top: 4px solid var(--calypso-berry);">
            <h3 class="card-title">💬 Communication Preferences</h3>
            <div class="privacy-group">
                <div class="privacy-toggle-item">
                    <div class="privacy-content">
                        <label class="privacy-label">Allow Vendor Contact</label>
                        <p class="privacy-description">Vendors can reach out to you about services</p>
                    </div>
                    <label class="toggle-switch">
                        <input type="checkbox" name="allow_vendor_contact" {{ $preferences->allow_vendor_contact ? 'checked' : '' }}>
                        <span class="toggle-slider"></span>
                    </label>
                </div>

                <div class="privacy-toggle-item">
                    <div class="privacy-content">
                        <label class="privacy-label">Allow Planner Suggestions</label>
                        <p class="privacy-description">Planners can suggest their services to you</p>
                    </div>
                    <label class="toggle-switch">
                        <input type="checkbox" name="allow_planner_suggestions" {{ $preferences->allow_planner_suggestions ? 'checked' : '' }}>
                        <span class="toggle-slider"></span>
                    </label>
                </div>
            </div>
        </div>

        <!-- Data & Analytics -->
        <div class="settings-card" style="border-top: 4px solid var(--vampire-hunter);">
            <h3 class="card-title">📊 Data & Analytics</h3>
            <div class="privacy-group">
                <div class="privacy-toggle-item">
                    <div class="privacy-content">
                        <label class="privacy-label">Allow Data Collection</label>
                        <p class="privacy-description">Help us improve by sharing usage analytics</p>
                    </div>
                    <label class="toggle-switch">
                        <input type="checkbox" name="data_collection" {{ $preferences->data_collection ? 'checked' : '' }}>
                        <span class="toggle-slider"></span>
                    </label>
                </div>
            </div>
        </div>

        <!-- Blocked Users -->
        <div class="settings-card" style="border-top: 4px solid #ff6b6b;">
            <h3 class="card-title">🚫 Blocked Users & Vendors</h3>
            <p class="group-description">You haven't blocked anyone yet</p>
            <button type="button" class="btn-secondary" style="border: 2px solid var(--coral-haze); color: var(--coral-haze); padding: 10px 20px; border-radius: 8px; background: white; cursor: pointer; font-weight: 600; margin-top: 16px;">
                View Blocked List
            </button>
        </div>

        <!-- Security Alert -->
        <div class="settings-card security-alert">
            <div class="alert-content">
                <h4>🔒 Your account is secure</h4>
                <p>Last login: Today at 10:30 AM from Chrome on macOS</p>
                <button type="button" class="btn-text">View all active sessions</button>
            </div>
        </div>

        <button type="submit" class="btn-save" style="background: linear-gradient(135deg, var(--coral-haze) 0%, var(--calypso-berry) 100%); color: white; padding: 14px 40px; border: none; border-radius: 8px; cursor: pointer; font-weight: 600; font-size: 15px; margin-top: 32px;">
            Save Privacy Settings
        </button>
    </form>
</div>

<style>
    .privacy-group {
        display: flex;
        flex-direction: column;
        gap: 16px;
    }

    .group-description {
        font-size: 14px;
        color: #666;
        margin-bottom: 8px;
    }

    .radio-option {
        position: relative;
        display: flex;
        align-items: flex-start;
        padding: 16px;
        border: 1.5px solid #e0e0e0;
        border-radius: 8px;
        cursor: pointer;
        transition: all 0.3s ease;
    }

    .radio-option input {
        appearance: none;
        width: 20px;
        height: 20px;
        border: 2px solid var(--coral-haze);
        border-radius: 50%;
        cursor: pointer;
        flex-shrink: 0;
        margin-top: 2px;
        margin-right: 16px;
        transition: all 0.3s ease;
    }

    .radio-option input:checked {
        background: linear-gradient(135deg, var(--coral-haze) 0%, var(--calypso-berry) 100%);
        border-color: var(--calypso-berry);
    }

    .radio-label {
        flex: 1;
    }

    .radio-label strong {
        display: block;
        color: #333;
        margin-bottom: 4px;
    }

    .radio-label p {
        font-size: 13px;
        color: #999;
        margin: 0;
    }

    .radio-option:hover {
        border-color: var(--coral-haze);
        background: rgba(225, 145, 132, 0.05);
    }

    .privacy-toggle-item {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 16px 0;
        border-bottom: 1px solid #f0f0f0;
    }

    .privacy-toggle-item:last-child {
        border-bottom: none;
    }

    .privacy-label {
        font-weight: 600;
        color: #333;
        display: block;
        margin-bottom: 4px;
    }

    .privacy-description {
        font-size: 13px;
        color: #999;
        margin: 0;
    }

    .security-alert {
        background: linear-gradient(135deg, rgba(71, 91, 53, 0.05) 0%, rgba(225, 145, 132, 0.05) 100%);
        border-top: 4px solid var(--garden-green);
    }

    .alert-content h4 {
        color: var(--garden-green);
        margin-bottom: 8px;
    }

    .alert-content p {
        font-size: 13px;
        color: #666;
        margin-bottom: 12px;
    }

    .btn-text {
        background: none;
        border: none;
        color: var(--coral-haze);
        cursor: pointer;
        font-weight: 600;
        text-decoration: underline;
    }
</style>

<script>
    function updatePrivacy(e) {
        e.preventDefault();
        const formData = new FormData(document.getElementById('privacyForm'));
        
        fetch('{{ route("client.settings.privacy") }}', {
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
</script>
@endsection