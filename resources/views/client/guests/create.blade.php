@extends('layouts.client')

@section('title', 'Add Guest')

@section('content')
<div class="create-event-container">
    
    <div class="create-header">
        <div class="breadcrumb">
            <a href="{{ route('client.dashboard') }}"><i class="fas fa-home"></i> Dashboard</a>
            <i class="fas fa-chevron-right"></i>
            <a href="{{ route('client.events.show', $event->id) }}">{{ $event->name }}</a>
            <i class="fas fa-chevron-right"></i>
            <span>Add Guest</span>
        </div>
        <h1><i class="fas fa-user-plus"></i> Add Guest</h1>
        <p>Add a guest and send them an invitation</p>
    </div>

    <form method="POST" action="{{ route('client.guests.store', $event->id) }}" class="event-form-luxury">
        @csrf

        <div class="form-sections">
            
            {{-- Guest Information --}}
            <div class="form-section">
                <div class="section-header">
                    <div class="section-icon">
                        <i class="fas fa-user"></i>
                    </div>
                    <div>
                        <h2>Guest Information</h2>
                        <p>Enter the guest's contact details</p>
                    </div>
                </div>

                <div class="form-grid">
                    <div class="form-group full-width">
                        <label for="name">Full Name *</label>
                        <input type="text" 
                               id="name" 
                               name="name" 
                               value="{{ old('name') }}" 
                               placeholder="John Doe" 
                               required>
                        @error('name')
                            <span class="error-message">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="email">Email Address *</label>
                        <input type="email" 
                               id="email" 
                               name="email" 
                               value="{{ old('email') }}" 
                               placeholder="john@example.com" 
                               required>
                        <small>Invitation will be sent to this email</small>
                        @error('email')
                            <span class="error-message">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="phone">Phone Number</label>
                        <input type="text" 
                               id="phone" 
                               name="phone" 
                               value="{{ old('phone') }}" 
                               placeholder="+966 50 123 4567">
                        @error('phone')
                            <span class="error-message">{{ $message }}</span>
                        @enderror
                    </div>
                </div>
            </div>

            {{-- Additional Options --}}
            <div class="form-section">
                <div class="section-header">
                    <div class="section-icon">
                        <i class="fas fa-cog"></i>
                    </div>
                    <div>
                        <h2>Guest Preferences</h2>
                        <p>Optional details about the guest</p>
                    </div>
                </div>

                <div class="form-grid">
                    <div class="form-group full-width">
                        <div class="checkbox-group">
                            <input type="checkbox" 
                                   id="plus_one_allowed" 
                                   name="plus_one_allowed" 
                                   value="1" 
                                   {{ old('plus_one_allowed') ? 'checked' : '' }}>
                            <label for="plus_one_allowed">Allow guest to bring a plus one (+1)</label>
                        </div>
                        @error('plus_one_allowed')
                            <span class="error-message">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="form-group full-width">
                        <label for="dietary_restrictions">Dietary Restrictions</label>
                        <input type="text" 
                               id="dietary_restrictions" 
                               name="dietary_restrictions" 
                               value="{{ old('dietary_restrictions') }}" 
                               placeholder="e.g., Vegetarian, Gluten-free, Halal">
                        @error('dietary_restrictions')
                            <span class="error-message">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="form-group full-width">
                        <label for="notes">Notes</label>
                        <textarea id="notes" 
                                  name="notes" 
                                  rows="3" 
                                  placeholder="Any special notes about this guest...">{{ old('notes') }}</textarea>
                        @error('notes')
                            <span class="error-message">{{ $message }}</span>
                        @enderror
                    </div>
                </div>
            </div>

        </div>

        <div class="form-actions">
            <a href="{{ route('client.events.show', $event->id) }}" class="btn-secondary-gradient">
                <i class="fas fa-times"></i> Cancel
            </a>
            <button type="submit" class="btn-primary-gradient">
                <i class="fas fa-paper-plane"></i> Add Guest & Send Invitation
            </button>
        </div>
    </form>

</div>
@endsection

@push('styles')
<style>
.checkbox-group {
    display: flex;
    align-items: center;
    gap: 12px;
    padding: 15px;
    background: var(--beige-light);
    border-radius: 12px;
    cursor: pointer;
    transition: var(--transition-fast);
}

.checkbox-group:hover {
    background: var(--peach-cream);
}

.checkbox-group input[type="checkbox"] {
    width: 24px;
    height: 24px;
    cursor: pointer;
}

.checkbox-group label {
    margin: 0;
    cursor: pointer;
    font-weight: 600;
    color: var(--gray-dark);
}
</style>
@endpush