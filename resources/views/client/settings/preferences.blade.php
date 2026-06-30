@extends('client.settings.index')

@section('settings-content')
<div class="settings-section">
    <div class="section-header">
        <h2 class="section-title" style="color: #475B35;">Event Preferences</h2>
        <p class="section-subtitle">Tell us about your ideal celebration</p>
    </div>

    <form id="preferencesForm" onsubmit="updatePreferences(event)">
        <!-- Event Type -->
        <div class="settings-card" style="border-top: 4px solid var(--coral-haze);">
            <h3 class="card-title">🎉 Preferred Event Type</h3>
            <div class="preference-group">
                <input type="text" name="preferred_event_type" class="preference-input" 
                       placeholder="e.g., Wedding, Birthday, Henna, Corporate" 
                       value="{{ $preferences->preferred_event_type ?? '' }}">
                <p class="preference-hint">What type of events do you plan most often?</p>
            </div>
        </div>

        <!-- Budget Range -->
        <div class="settings-card" style="border-top: 4px solid var(--calypso-berry);">
            <h3 class="card-title">💰 Budget Range</h3>
            <div class="preference-group">
                <select name="budget_range" class="preference-select">
                    <option value="">Select your typical budget</option>
                    <option value="under-5000" {{ $preferences->budget_range === 'under-5000' ? 'selected' : '' }}>Under $5,000</option>
                    <option value="5000-10000" {{ $preferences->budget_range === '5000-10000' ? 'selected' : '' }}>$5,000 - $10,000</option>
                    <option value="10000-25000" {{ $preferences->budget_range === '10000-25000' ? 'selected' : '' }}>$10,000 - $25,000</option>
                    <option value="25000-50000" {{ $preferences->budget_range === '25000-50000' ? 'selected' : '' }}>$25,000 - $50,000</option>
                    <option value="over-50000" {{ $preferences->budget_range === 'over-50000' ? 'selected' : '' }}>Over $50,000</option>
                </select>
            </div>
        </div>

        <!-- Guest Count -->
        <div class="settings-card" style="border-top: 4px solid var(--garden-green);">
            <h3 class="card-title">👥 Ideal Guest Count</h3>
            <div class="preference-group">
                <input type="number" name="ideal_guest_count" class="preference-input" 
                       placeholder="How many guests do you usually invite?" 
                       value="{{ $preferences->ideal_guest_count ?? '' }}">
            </div>
        </div>

        <!-- Favorite Vendors -->
        <div class="settings-card" style="border-top: 4px solid var(--vampire-hunter);">
            <h3 class="card-title">⭐ Favorite Vendors</h3>
            <div class="preference-group">
                <p class="preference-hint">Add your go-to vendors for faster planning</p>
                <div class="vendor-tags">
                    @if($user->favoriteVendors)
                        @foreach($user->favoriteVendors as $vendor)
                            <span class="vendor-tag">
                                {{ $vendor->name }}
                                <button type="button" class="tag-remove">×</button>
                            </span>
                        @endforeach
                    @endif
                </div>
                <input type="text" class="preference-input" placeholder="Search and add vendors...">
            </div>
        </div>

        <!-- Communication Style -->
        <div class="settings-card" style="border-top: 4px solid #FF9800;">
            <h3 class="card-title">💬 Communication Style</h3>
            <div class="preference-group">
                <label class="checkbox-option">
                    <input type="checkbox" name="communication[]" value="email">
                    <span class="checkbox-label">Email (formal)</span>
                </label>
                <label class="checkbox-option">
                    <input type="checkbox" name="communication[]" value="chat">
                    <span class="checkbox-label">Chat (quick updates)</span>
                </label>
                <label class="checkbox-option">
                    <input type="checkbox" name="communication[]" value="phone">
                    <span class="checkbox-label">Phone (detailed discussions)</span>
                </label>
                <label class="checkbox-option">
                    <input type="checkbox" name="communication[]" value="video">
                    <span class="checkbox-label">Video calls (planning sessions)</span>
                </label>
            </div>
        </div>

        <button type="submit" class="btn-save" style="background: linear-gradient(135deg, var(--coral-haze) 0%, var(--calypso-berry) 100%); color: white; padding: 14px 40px; border: none; border-radius: 8px; cursor: pointer; font-weight: 600; font-size: 15px; margin-top: 32px;">
            Save Preferences
        </button>
    </form>
</div>

<style>
    .preference-group {
        display: flex;
        flex-direction: column;
        gap: 12px;
    }

    .preference-input,
    .preference-select {
        padding: 12px 16px;
        border: 1.5px solid #e0e0e0;
        border-radius: 8px;
        font-family: 'Raleway', sans-serif;
        font-size: 14px;
        transition: all 0.3s ease;
    }

    .preference-input:focus,
    .preference-select:focus {
        outline: none;
        border-color: var(--coral-haze);
        box-shadow: 0 0 0 3px rgba(225, 145, 132, 0.1);
    }

    .preference-hint {
        font-size: 13px;
        color: #999;
        margin: 0;
    }

    /* Vendor Tags */
    .vendor-tags {
        display: flex;
        flex-wrap: wrap;
        gap: 8px;
        margin-bottom: 12px;
    }

    .vendor-tag {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 8px 12px;
        background: linear-gradient(135deg, var(--coral-haze) 0%, var(--calypso-berry) 100%);
        color: white;
        border-radius: 20px;
        font-size: 13px;
        font-weight: 600;
    }

    .tag-remove {
        background: none;
        border: none;
        color: white;
        cursor: pointer;
        font-size: 18px;
        line-height: 1;
    }

    /* Checkbox Option */
    .checkbox-option {
        display: flex;
        align-items: center;
        padding: 12px;
        background: white;
        border-radius: 8px;
        cursor: pointer;
        transition: all 0.3s ease;
    }

    .checkbox-option:hover {
        background: rgba(225, 145, 132, 0.05);
    }

    .checkbox-option input {
        appearance: none;
        width: 20px;
        height: 20px;
        border: 2px solid #e0e0e0;
        border-radius: 4px;
        cursor: pointer;
        margin-right: 12px;
        transition: all 0.3s ease;
    }

    .checkbox-option input:checked {
        background: linear-gradient(135deg, var(--coral-haze) 0%, var(--calypso-berry) 100%);
        border-color: var(--calypso-berry);
    }

    .checkbox-label {
        font-weight: 600;
        color: #333;
    }
</style>

<script>
    function updatePreferences(e) {
        e.preventDefault();
        const formData = new FormData(document.getElementById('preferencesForm'));
        
        fetch('{{ route("client.settings.preferences") }}', {
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