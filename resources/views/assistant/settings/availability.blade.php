@extends('assistant.settings.index')

@section('settings-content')
<div class="settings-section">
    <div class="section-header">
        <h2 class="section-title" style="color: #475B35;">Availability & Schedule</h2>
        <p class="section-subtitle">Let planners know when you're available to work</p>
    </div>

    <form id="availabilityForm" onsubmit="updateAvailability(event)">
        <!-- Working Days -->
        <div class="settings-card" style="border-top: 4px solid var(--coral-haze);">
            <h3 class="card-title">📅 Working Days</h3>
            <p class="card-description">Which days do you usually work?</p>
            <div class="days-grid">
                @php
                    $days = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'];
                    $workingDays = $preferences->working_days ?? [];
                @endphp
                @foreach($days as $day)
                    <label class="day-checkbox">
                        <input type="checkbox" name="working_days[]" value="{{ $day }}" {{ in_array($day, $workingDays) ? 'checked' : '' }}>
                        <span>{{ $day }}</span>
                    </label>
                @endforeach
            </div>
        </div>

        <!-- Working Hours -->
        <div class="settings-card" style="border-top: 4px solid var(--garden-green);">
            <h3 class="card-title">⏰ Working Hours</h3>
            <div class="hours-grid">
                <div class="form-group">
                    <label>Start Time</label>
                    <input type="time" name="working_hours_start" value="{{ $preferences->working_hours_start ?? '08:00' }}">
                </div>
                <div class="form-group">
                    <label>End Time</label>
                    <input type="time" name="working_hours_end" value="{{ $preferences->working_hours_end ?? '18:00' }}">
                </div>
            </div>
        </div>

        <!-- Timezone -->
        <div class="settings-card" style="border-top: 4px solid var(--calypso-berry);">
            <h3 class="card-title">🌍 Timezone</h3>
            <select name="timezone" class="timezone-select">
                <option value="">Select your timezone</option>
                <option value="Asia/Beirut" {{ ($preferences->timezone ?? '') == 'Asia/Beirut' ? 'selected' : '' }}>Asia/Beirut (Lebanon)</option>
                <option value="Europe/London" {{ ($preferences->timezone ?? '') == 'Europe/London' ? 'selected' : '' }}>Europe/London</option>
                <option value="America/New_York" {{ ($preferences->timezone ?? '') == 'America/New_York' ? 'selected' : '' }}>America/New_York</option>
                <option value="Asia/Dubai" {{ ($preferences->timezone ?? '') == 'Asia/Dubai' ? 'selected' : '' }}>Asia/Dubai (UAE)</option>
                <option value="Europe/Paris" {{ ($preferences->timezone ?? '') == 'Europe/Paris' ? 'selected' : '' }}>Europe/Paris</option>
                <option value="Asia/Singapore" {{ ($preferences->timezone ?? '') == 'Asia/Singapore' ? 'selected' : '' }}>Asia/Singapore</option>
            </select>
        </div>

        <!-- Work Locations -->
        <div class="settings-card" style="border-top: 4px solid var(--vampire-hunter);">
            <h3 class="card-title">📍 Work Locations</h3>
            <p class="card-description">Where are you willing to work?</p>
            <div class="location-options">
                @php
                    $locations = $preferences->available_locations ?? [];
                    $locList = ['beirut'=>'Beirut', 'mount-lebanon'=>'Mount Lebanon', 'baalbek'=>'Baalbek', 'tyre'=>'Tyre', 'sidon'=>'Sidon', 'online'=>'Online'];
                @endphp
                @foreach($locList as $value => $label)
                    <label class="location-checkbox">
                        <input type="checkbox" name="available_locations[]" value="{{ $value }}" {{ in_array($value, $locations) ? 'checked' : '' }}>
                        <span>{{ $label }}</span>
                    </label>
                @endforeach
            </div>
        </div>

        <!-- Remote Work -->
        <div class="settings-card" style="border-top: 4px solid #2196F3;">
            <h3 class="card-title">💻 Remote Work</h3>
            <div class="remote-toggle">
                <div class="remote-info">
                    <p class="remote-title">Available for remote tasks</p>
                    <p class="remote-description">Can you do coordination and planning work remotely?</p>
                </div>
                <label class="toggle-switch">
                    <input type="checkbox" name="remote_work" {{ ($preferences->remote_work ?? false) ? 'checked' : '' }}>
                    <span class="toggle-slider"></span>
                </label>
            </div>
        </div>

        <button type="submit" class="btn-save" style="background: linear-gradient(135deg, var(--coral-haze) 0%, var(--calypso-berry) 100%); color: white; padding: 14px 40px; border: none; border-radius: 8px; cursor: pointer; font-weight: 600; font-size: 15px; margin-top: 32px;">
            Save Availability
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
        border-radius: 12px; padding: 32px; transition: all 0.3s ease;
    }
    .settings-card:hover { box-shadow: 0 8px 30px rgba(71, 91, 53, 0.1); }
    .card-title { font-size: 18px; font-weight: 700; color: #333; margin-bottom: 16px; }
    .card-description { font-size: 13px; color: #999; margin-bottom: 16px; }
    .days-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(120px, 1fr)); gap: 12px; }
    .day-checkbox { display: flex; align-items: center; padding: 12px 16px; background: white; border: 2px solid #e0e0e0; border-radius: 8px; cursor: pointer; transition: all 0.3s ease; }
    .day-checkbox input { appearance: none; width: 20px; height: 20px; border: 2px solid #e0e0e0; border-radius: 4px; cursor: pointer; margin-right: 12px; transition: all 0.3s ease; }
    .day-checkbox input:checked { background: linear-gradient(135deg, var(--coral-haze) 0%, var(--calypso-berry) 100%); border-color: var(--calypso-berry); }
    .day-checkbox span { font-weight: 600; color: #333; }
    .day-checkbox:hover { border-color: var(--coral-haze); background: rgba(225,145,132,0.05); }
    .hours-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 20px; }
    .form-group label { display: block; font-size: 13px; font-weight: 600; color: #555; margin-bottom: 8px; text-transform: uppercase; letter-spacing: 0.5px; }
    .form-group input { width: 100%; padding: 12px 16px; border: 1.5px solid #e0e0e0; border-radius: 8px; font-family: 'Raleway', sans-serif; font-size: 14px; transition: all 0.3s ease; }
    .form-group input:focus { outline: none; border-color: var(--coral-haze); box-shadow: 0 0 0 3px rgba(225,145,132,0.1); }
    .timezone-select { width: 100%; padding: 12px 16px; border: 1.5px solid #e0e0e0; border-radius: 8px; font-family: 'Raleway', sans-serif; font-size: 14px; cursor: pointer; transition: all 0.3s ease; }
    .timezone-select:focus { outline: none; border-color: var(--coral-haze); box-shadow: 0 0 0 3px rgba(225,145,132,0.1); }
    .location-options { display: grid; grid-template-columns: repeat(auto-fit, minmax(150px, 1fr)); gap: 12px; }
    .location-checkbox { display: flex; align-items: center; padding: 12px 16px; background: white; border: 2px solid #e0e0e0; border-radius: 8px; cursor: pointer; transition: all 0.3s ease; }
    .location-checkbox input { appearance: none; width: 20px; height: 20px; border: 2px solid #e0e0e0; border-radius: 4px; cursor: pointer; margin-right: 12px; transition: all 0.3s ease; }
    .location-checkbox input:checked { background: linear-gradient(135deg, var(--coral-haze) 0%, var(--calypso-berry) 100%); border-color: var(--calypso-berry); }
    .location-checkbox span { font-weight: 600; color: #333; }
    .location-checkbox:hover { border-color: var(--coral-haze); background: rgba(225,145,132,0.05); }
    .remote-toggle { display: flex; justify-content: space-between; align-items: center; padding: 16px; background: white; border-radius: 8px; }
    .remote-title { font-weight: 600; color: #333; margin-bottom: 4px; }
    .remote-description { font-size: 13px; color: #999; margin: 0; }
    .toggle-switch { position: relative; display: inline-block; width: 50px; height: 28px; }
    .toggle-switch input { opacity: 0; width: 0; height: 0; }
    .toggle-slider { position: absolute; cursor: pointer; top: 0; left: 0; right: 0; bottom: 0; background-color: #ccc; transition: 0.4s; border-radius: 28px; }
    .toggle-slider:before { position: absolute; content: ""; height: 22px; width: 22px; left: 3px; bottom: 3px; background-color: white; transition: 0.4s; border-radius: 50%; }
    input:checked + .toggle-slider { background: linear-gradient(135deg, var(--coral-haze) 0%, var(--calypso-berry) 100%); }
    input:checked + .toggle-slider:before { transform: translateX(22px); }
    @media (max-width: 768px) { .hours-grid { grid-template-columns: 1fr; gap: 16px; } .location-options { grid-template-columns: 1fr; } .remote-toggle { flex-direction: column; gap: 16px; align-items: flex-start; } }
</style>

<script>
    function updateAvailability(e) {
        e.preventDefault();
        const formData = new FormData(document.getElementById('availabilityForm'));
        fetch('{{ route("assistant.settings.availability") }}', {
            method: 'POST',
            body: formData,
            headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' }
        })
        .then(r => r.json())
        .then(data => { if (data.success) alert(data.message); });
    }
</script>
@endsection