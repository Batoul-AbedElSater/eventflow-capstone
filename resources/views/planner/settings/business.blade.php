@extends('planner.settings.index')

@section('settings-content')
<div class="settings-section">
    <div class="section-header">
        <h2 class="section-title" style="color: #475B35;">Business Information</h2>
        <p class="section-subtitle">Manage your business profile and details</p>
    </div>

    <form id="businessForm" onsubmit="updateBusiness(event)">
        <!-- Company Details -->
        <div class="settings-card" style="border-top: 4px solid var(--coral-haze);">
            <h3 class="card-title">🏢 Company Details</h3>
            <div class="form-group">
                <label>Company Name</label>
                <input type="text" name="company_name" placeholder="Your company or business name" 
                       value="{{ $preferences->company_name ?? '' }}">
            </div>
            <div class="form-group">
                <label>Business Type</label>
                <select name="business_type" class="form-select">
                    <option value="">Select business type</option>
                    <option value="freelance" {{ $preferences->business_type === 'freelance' ? 'selected' : '' }}>Freelance Planner</option>
                    <option value="small_team" {{ $preferences->business_type === 'small_team' ? 'selected' : '' }}>Small Team (2-5)</option>
                    <option value="agency" {{ $preferences->business_type === 'agency' ? 'selected' : '' }}>Event Planning Agency</option>
                </select>
            </div>
            <div class="form-group">
                <label>Years of Experience</label>
                <input type="number" name="years_experience" min="0" max="70" 
                       placeholder="How many years in event planning?" 
                       value="{{ $preferences->years_experience ?? '' }}">
            </div>
        </div>

        <!-- Specializations -->
        <div class="settings-card" style="border-top: 4px solid var(--garden-green);">
            <h3 class="card-title">⭐ Specializations</h3>
            <p class="card-description">What types of events do you specialize in?</p>
            <div class="specializations-grid">
                @php
                    $specs = ['Wedding', 'Birthday', 'Corporate', 'Henna', 'Engagement', 'Anniversary', 'Graduation', 'Other'];
                    $selected = $preferences->specializations ?? [];
                @endphp
                @foreach($specs as $spec)
                    <label class="spec-checkbox">
                        <input type="checkbox" name="specializations[]" value="{{ strtolower($spec) }}" 
                               {{ in_array(strtolower($spec), $selected) ? 'checked' : '' }}>
                        <span>{{ $spec }}</span>
                    </label>
                @endforeach
            </div>
        </div>

        <!-- Service Areas -->
        <div class="settings-card" style="border-top: 4px solid var(--calypso-berry);">
            <h3 class="card-title">📍 Service Areas</h3>
            <p class="card-description">Which areas do you provide services in?</p>
            <div class="areas-grid">
                @php
                    $areas = ['Beirut', 'Mount Lebanon', 'Baalbek', 'Tyre', 'Sidon', 'North', 'South', 'Bekaa'];
                    $selectedAreas = $preferences->service_areas ?? [];
                @endphp
                @foreach($areas as $area)
                    <label class="area-checkbox">
                        <input type="checkbox" name="service_areas[]" value="{{ $area }}" 
                               {{ in_array($area, $selectedAreas) ? 'checked' : '' }}>
                        <span>{{ $area }}</span>
                    </label>
                @endforeach
            </div>
        </div>

        <!-- Business License & Tax -->
        <div class="settings-card" style="border-top: 4px solid var(--vampire-hunter);">
            <h3 class="card-title">📋 Legal Information</h3>
            <div class="form-group">
                <label>Business License Number</label>
                <input type="text" name="business_license" placeholder="Your business license number" 
                       value="{{ $preferences->business_license ?? '' }}">
            </div>
            <div class="form-group">
                <label>Tax ID / Registration Number</label>
                <input type="text" name="tax_id" placeholder="Your tax identification number" 
                       value="{{ $preferences->tax_id ?? '' }}">
            </div>
        </div>

        <!-- About Your Business -->
        <div class="settings-card" style="border-top: 4px solid #FF9800;">
            <h3 class="card-title">📝 About Your Business</h3>
            <div class="form-group">
                <label>Business Description</label>
                <textarea name="about_business" rows="5" placeholder="Tell clients about your business philosophy, approach, and what makes you unique...">{{ $preferences->about_business ?? '' }}</textarea>
                <p class="form-hint">Maximum 500 characters</p>
            </div>
        </div>

        <!-- Business Stats -->
        <div class="stats-preview" style="background: linear-gradient(135deg, rgba(225, 145, 132, 0.1) 0%, rgba(198, 62, 78, 0.1) 100%); border-radius: 12px; padding: 24px; margin-bottom: 32px;">
            <h3 style="color: #475B35; margin-bottom: 16px;">Business Statistics</h3>
            <div class="stats-grid">
                <div class="stat-item">
                    <p class="stat-label">Total Events Planned</p>
                    <p class="stat-value" style="color: #E19184;">{{ $businessStats['total_events'] }}</p>
                </div>
                <div class="stat-item">
                    <p class="stat-label">Active Clients</p>
                    <p class="stat-value" style="color: #C63E4E;">{{ $businessStats['active_clients'] }}</p>
                </div>
                <div class="stat-item">
                    <p class="stat-label">Team Members</p>
                    <p class="stat-value" style="color: #475B35;">{{ $businessStats['team_members'] }}</p>
                </div>
                <div class="stat-item">
                    <p class="stat-label">Total Revenue</p>
                    <p class="stat-value" style="color: #620607;">${{ number_format($businessStats['total_revenue'], 0) }}</p>
                </div>
            </div>
        </div>

        <button type="submit" class="btn-save" style="background: linear-gradient(135deg, var(--coral-haze) 0%, var(--calypso-berry) 100%); color: white; padding: 14px 40px; border: none; border-radius: 8px; cursor: pointer; font-weight: 600; font-size: 15px;">
            Save Business Information
        </button>
    </form>
</div>

<style>
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

    .form-group {
        margin-bottom: 20px;
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

    .form-group input,
    .form-group textarea,
    .form-select {
        width: 100%;
        padding: 12px 16px;
        border: 1.5px solid #e0e0e0;
        border-radius: 8px;
        font-family: 'Raleway', sans-serif;
        font-size: 14px;
        transition: all 0.3s ease;
    }

    .form-group input:focus,
    .form-group textarea:focus,
    .form-select:focus {
        outline: none;
        border-color: var(--coral-haze);
        box-shadow: 0 0 0 3px rgba(225, 145, 132, 0.1);
    }

    .form-hint {
        font-size: 12px;
        color: #999;
        margin-top: 4px;
    }

    /* Specializations Grid */
    .specializations-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
        gap: 12px;
    }

    .spec-checkbox {
        position: relative;
        display: flex;
        align-items: center;
        padding: 12px 16px;
        background: white;
        border: 2px solid #e0e0e0;
        border-radius: 8px;
        cursor: pointer;
        transition: all 0.3s ease;
    }

    .spec-checkbox input {
        appearance: none;
        width: 20px;
        height: 20px;
        border: 2px solid #e0e0e0;
        border-radius: 4px;
        cursor: pointer;
        margin-right: 12px;
        transition: all 0.3s ease;
    }

    .spec-checkbox input:checked {
        background: linear-gradient(135deg, var(--coral-haze) 0%, var(--calypso-berry) 100%);
        border-color: var(--calypso-berry);
    }

    .spec-checkbox span {
        font-weight: 600;
        color: #333;
    }

    .spec-checkbox:hover {
        border-color: var(--coral-haze);
        background: rgba(225, 145, 132, 0.05);
    }

    /* Service Areas */
    .areas-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(120px, 1fr));
        gap: 12px;
    }

    .area-checkbox {
        position: relative;
        display: flex;
        align-items: center;
        padding: 12px 16px;
        background: white;
        border: 2px solid #e0e0e0;
        border-radius: 8px;
        cursor: pointer;
        transition: all 0.3s ease;
    }

    .area-checkbox input {
        appearance: none;
        width: 20px;
        height: 20px;
        border: 2px solid #e0e0e0;
        border-radius: 4px;
        cursor: pointer;
        margin-right: 12px;
        transition: all 0.3s ease;
    }

    .area-checkbox input:checked {
        background: linear-gradient(135deg, var(--coral-haze) 0%, var(--calypso-berry) 100%);
        border-color: var(--calypso-berry);
    }

    .area-checkbox span {
        font-weight: 600;
        color: #333;
        font-size: 14px;
    }

    .area-checkbox:hover {
        border-color: var(--coral-haze);
        background: rgba(225, 145, 132, 0.05);
    }

    /* Stats */
    .stats-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 20px;
    }

    .stat-item {
        background: white;
        border-radius: 8px;
        padding: 16px;
        text-align: center;
    }

    .stat-label {
        font-size: 12px;
        font-weight: 700;
        color: #999;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        margin-bottom: 8px;
    }

    .stat-value {
        font-family: 'Playfair Display', serif;
        font-size: 28px;
        font-weight: 900;
    }

    @media (max-width: 768px) {
        .specializations-grid {
            grid-template-columns: repeat(2, 1fr);
        }

        .areas-grid {
            grid-template-columns: repeat(2, 1fr);
        }

        .stats-grid {
            grid-template-columns: repeat(2, 1fr);
        }
    }
</style>

<script>
    function updateBusiness(e) {
        e.preventDefault();
        const formData = new FormData(document.getElementById('businessForm'));
        
        fetch('{{ route("planner.settings.business") }}', {
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