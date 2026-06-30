@extends('planner.settings.index')

@section('settings-content')
<div class="settings-section">
    <div class="section-header">
        <h2 class="section-title" style="color: #475B35;">Vendor Management</h2>
        <p class="section-subtitle">Manage your favorite vendors and preferences</p>
    </div>

    <!-- Rating Threshold -->
    <div class="settings-card" style="border-top: 4px solid var(--coral-haze);">
        <h3 class="card-title">⭐ Vendor Rating Threshold</h3>
        <p class="card-description">Only show vendors with ratings above this threshold</p>
        <div class="rating-slider-container">
            <input type="range" id="ratingThreshold" name="vendor_rating_threshold" min="1" max="5" step="0.5" 
                   value="{{ $user->preferences->vendor_rating_threshold ?? 3.5 }}" class="rating-slider">
            <div class="rating-display">
                <span id="ratingValue" style="font-family: 'Playfair Display', serif; font-size: 28px; font-weight: 900; color: var(--coral-haze);">{{ $user->preferences->vendor_rating_threshold ?? 3.5 }}</span>
                <span style="color: #999; font-size: 13px; margin-left: 8px;">stars and above</span>
            </div>
        </div>
    </div>

    <!-- Favorite Vendors -->
    <div class="settings-card" style="border-top: 4px solid var(--garden-green);">
        <h3 class="card-title">❤️ Favorite Vendors</h3>
        <p class="card-description">Quick access to your most trusted vendors</p>
        
        @if($user->favoriteVendors && count($user->favoriteVendors) > 0)
            <div class="vendors-list">
                @foreach($user->favoriteVendors as $vendor)
                    <div class="vendor-item">
                        <div class="vendor-info">
                            <h4 class="vendor-name">{{ $vendor->name }}</h4>
                            <p class="vendor-category">{{ $vendor->category ?? 'General' }}</p>
                            <p class="vendor-rating">⭐ {{ $vendor->rating ?? 'N/A' }} ({{ $vendor->reviews_count ?? 0 }} reviews)</p>
                        </div>
                        <button type="button" class="btn-remove-vendor" onclick="removeFavoriteVendor({{ $vendor->id }})" style="background: #ff6b6b; color: white; border: none; padding: 8px 16px; border-radius: 6px; cursor: pointer; font-weight: 600; font-size: 12px;">
                            Remove
                        </button>
                    </div>
                @endforeach
            </div>
        @else
            <p style="color: #999; text-align: center; padding: 32px 0;">No favorite vendors yet. Add vendors from your events to favorites!</p>
        @endif
    </div>

    <!-- Blocked Vendors -->
    <div class="settings-card" style="border-top: 4px solid var(--calypso-berry);">
        <h3 class="card-title">🚫 Blocked Vendors</h3>
        <p class="card-description">Vendors you don't want to work with</p>
        
        <div class="blocked-vendors-list">
            <div class="blocked-vendor-item">
                <span>No blocked vendors</span>
                <button type="button" class="btn-add-block" style="background: var(--coral-haze); color: white; border: none; padding: 6px 12px; border-radius: 6px; cursor: pointer; font-weight: 600; font-size: 12px;">
                    + Add Blocked Vendor
                </button>
            </div>
        </div>
    </div>

    <!-- Vendor Communication Preferences -->
    <div class="settings-card" style="border-top: 4px solid var(--vampire-hunter);">
        <h3 class="card-title">💬 Communication Preferences</h3>
        <form id="vendorCommForm" onsubmit="updateVendorPreferences(event)">
            <div class="communication-options">
                <label class="comm-option">
                    <input type="checkbox" name="vendor_auto_messages" checked>
                    <span>
                        <strong>Auto-message Best Matches</strong>
                        <p>Automatically message top-rated vendors for availability</p>
                    </span>
                </label>
                <label class="comm-option">
                    <input type="checkbox" name="vendor_quote_requests" checked>
                    <span>
                        <strong>Request Quotes Automatically</strong>
                        <p>Ask for quotes from favorite vendors for new events</p>
                    </span>
                </label>
                <label class="comm-option">
                    <input type="checkbox" name="vendor_price_alerts">
                    <span>
                        <strong>Price Change Alerts</strong>
                        <p>Notify me when vendor prices change</p>
                    </span>
                </label>
                <label class="comm-option">
                    <input type="checkbox" name="vendor_new_services" checked>
                    <span>
                        <strong>New Services Notification</strong>
                        <p>Get notified when vendors add new services</p>
                    </span>
                </label>
            </div>
            <button type="submit" class="btn-save" style="background: linear-gradient(135deg, var(--coral-haze) 0%, var(--calypso-berry) 100%); color: white; padding: 12px 32px; border: none; border-radius: 8px; cursor: pointer; font-weight: 600; margin-top: 20px;">
                Save Preferences
            </button>
        </form>
    </div>

    <!-- Vendor Discounts -->
    <div class="settings-card" style="border-top: 4px solid #FF9800;">
        <h3 class="card-title">🎁 Vendor Discounts & Offers</h3>
        <div class="discounts-list">
            <div class="discount-item">
                <div class="discount-info">
                    <h4>Premium Florist</h4>
                    <p class="discount-code">Code: HAFLET20</p>
                    <p class="discount-desc">20% off on bulk orders</p>
                </div>
                <span class="discount-badge">Active</span>
            </div>
            <div class="discount-item">
                <div class="discount-info">
                    <h4>Elite Catering</h4>
                    <p class="discount-code">Code: SPRING25</p>
                    <p class="discount-desc">25% off spring events</p>
                </div>
                <span class="discount-badge">Expired</span>
            </div>
        </div>
    </div>
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

    /* Rating Slider */
    .rating-slider-container {
        display: flex;
        align-items: center;
        gap: 20px;
        padding: 20px;
        background: white;
        border-radius: 8px;
    }

    .rating-slider {
        flex: 1;
        height: 6px;
        border-radius: 3px;
        background: linear-gradient(90deg, #e0e0e0 0%, var(--coral-haze) 100%);
        outline: none;
        -webkit-appearance: none;
        appearance: none;
    }

    .rating-slider::-webkit-slider-thumb {
        -webkit-appearance: none;
        appearance: none;
        width: 24px;
        height: 24px;
        border-radius: 50%;
        background: linear-gradient(135deg, var(--coral-haze) 0%, var(--calypso-berry) 100%);
        cursor: pointer;
        box-shadow: 0 2px 8px rgba(198, 62, 78, 0.4);
    }

    .rating-slider::-moz-range-thumb {
        width: 24px;
        height: 24px;
        border-radius: 50%;
        background: linear-gradient(135deg, var(--coral-haze) 0%, var(--calypso-berry) 100%);
        cursor: pointer;
        border: none;
        box-shadow: 0 2px 8px rgba(198, 62, 78, 0.4);
    }

    .rating-display {
        display: flex;
        align-items: center;
        white-space: nowrap;
    }

    /* Vendors List */
    .vendors-list {
        display: flex;
        flex-direction: column;
        gap: 12px;
    }

    .vendor-item {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 16px;
        background: white;
        border-radius: 8px;
    }

    .vendor-info {
        flex: 1;
    }

    .vendor-name {
        font-weight: 700;
        color: #333;
        margin: 0 0 4px 0;
    }

    .vendor-category {
        font-size: 12px;
        color: #999;
        margin: 0 0 4px 0;
    }

    .vendor-rating {
        font-size: 13px;
        color: #666;
        margin: 0;
    }

    /* Communication Options -->
    .communication-options {
        display: flex;
        flex-direction: column;
        gap: 12px;
    }

    .comm-option {
        display: flex;
        align-items: flex-start;
        gap: 12px;
        padding: 12px;
        background: white;
        border-radius: 8px;
        cursor: pointer;
    }

    .comm-option input {
        appearance: none;
        width: 20px;
        height: 20px;
        border: 2px solid #e0e0e0;
        border-radius: 4px;
        cursor: pointer;
        margin-top: 2px;
        flex-shrink: 0;
        transition: all 0.3s ease;
    }

    .comm-option input:checked {
        background: linear-gradient(135deg, var(--coral-haze) 0%, var(--calypso-berry) 100%);
        border-color: var(--calypso-berry);
    }

    .comm-option strong {
        display: block;
        color: #333;
        margin-bottom: 2px;
    }

    .comm-option p {
        font-size: 12px;
        color: #999;
        margin: 0;
    }

    /* Discounts */
    .discounts-list {
        display: flex;
        flex-direction: column;
        gap: 12px;
    }

    .discount-item {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 16px;
        background: white;
        border-radius: 8px;
        border-left: 4px solid var(--coral-haze);
    }

    .discount-info h4 {
        font-weight: 700;
        color: #333;
        margin: 0 0 4px 0;
    }

    .discount-code {
        font-family: monospace;
        font-weight: 700;
        color: var(--coral-haze);
        font-size: 13px;
        margin: 0 0 4px 0;
    }

    .discount-desc {
        font-size: 12px;
        color: #999;
        margin: 0;
    }

    .discount-badge {
        background: linear-gradient(135deg, var(--coral-haze) 0%, var(--calypso-berry) 100%);
        color: white;
        padding: 6px 12px;
        border-radius: 12px;
        font-size: 12px;
        font-weight: 600;
    }

    @media (max-width: 768px) {
        .rating-slider-container {
            flex-direction: column;
        }

        .vendor-item {
            flex-direction: column;
            gap: 12px;
        }

        .discount-item {
            flex-direction: column;
            gap: 12px;
        }
    }
</style>

<script>
    document.getElementById('ratingThreshold').addEventListener('input', function(e) {
        document.getElementById('ratingValue').textContent = e.target.value;
    });

    function updateVendorPreferences(e) {
        e.preventDefault();
        const formData = new FormData(document.getElementById('vendorCommForm'));
        
        fetch('{{ route("planner.settings.vendors") }}', {
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

    function removeFavoriteVendor(vendorId) {
        if (confirm('Remove this vendor from favorites?')) {
            // Add remove logic
        }
    }
</script>
@endsection