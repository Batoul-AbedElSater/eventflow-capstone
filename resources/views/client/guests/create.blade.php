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
:root {
    --coral: #E19184;
    --berry: #C63E4E;
    --vampire: #620607;
    --cream: #EFE7DA;
    --white: #FFFFFF;
    --green: #475B35;
    --green-dark: #2C3821;
    --danger: #D0021B;
    --transition: all 0.3s ease;
}

body:has(.create-event-container) {
    background: var(--cream) !important;
   
}

.create-event-container {
    max-width: 1000px;
    margin: 0 auto;
    padding: 30px;
    background: var(--cream) !important;
    color: var(--green-dark);
}

.create-header {
    margin-bottom: 30px;
    padding: 0 !important;
    background: transparent !important;
    border: 0 !important;
    border-radius: 0 !important;
    box-shadow: none !important;
}

.create-header .breadcrumb {
    display: none !important;
}

.create-header h1 {
    margin: 0 0 5px 0;
    color: var(--vampire) !important;
    font-size: 35px;
    font-weight: 900;
    line-height: 1.1;
}

.create-header h1 i {
    display: none !important;
}

.create-header p {
    margin: 0;
    color: var(--green) !important;
    font-size: 18px;
}

.event-form-luxury,
.form-sections {
    background: transparent !important;
}

.form-section {
    background: var(--white) !important;
    border: 2px solid var(--cream) !important;
    border-radius: 16px !important;
    padding: 30px !important;
    margin-bottom: 25px;
    box-shadow: none !important;
}

.section-header {
    display: flex;
    align-items: center;
    gap: 14px;
    margin-bottom: 25px;
    padding: 0 0 15px 0 !important;
    border-bottom: 2px solid var(--cream) !important;
}

.section-icon {
    width: 46px;
    height: 46px;
    flex: 0 0 60px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    background: var(--berry) !important;
    color: var(--white) !important;
    border-radius: 12px;
    box-shadow: none !important;
}

.section-icon i {
    color: var(--white) !important;
    font-size: 20px;
}

.section-header h2 {
    margin: 0;
    color: var(--vampire) !important;
    font-size: 20px;
    font-weight: 800;
    line-height: 1.2;
}

.section-header p {
    margin: 5px 0 0 0;
    color: var(--green) !important;
    font-size: 13px;
}

.form-grid {
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: 20px;
    margin-bottom: 25px;
}

.form-group {
    display: flex;
    flex-direction: column;
    gap: 8px;
    min-width: 0;
}

.form-group.full-width {
    grid-column: 1 / -1;
}

.form-group label {
    color: var(--vampire) !important;
    font-size: 14px;
    font-weight: 600;
}

.form-group input,
.form-group textarea {
    width: 100%;
    padding: 12px 16px;
    border: 2px solid var(--cream) !important;
    border-radius: 10px !important;
    background: var(--cream) !important;
    color: var(--berry) !important;
    font-size: 15px;
    transition: var(--transition);
}

.form-group textarea {
    min-height: 105px;
    resize: vertical;
}

.form-group input::placeholder,
.form-group textarea::placeholder {
    color: rgba(71, 91, 53, 0.55) !important;
}

.form-group input:focus,
.form-group textarea:focus {
    outline: none !important;
    border-color: var(--berry) !important;
    background: var(--white) !important;
    box-shadow: none !important;
}

.form-group small {
    color: var(--berry) !important;
    font-size: 12px;
    font-style: italic;
}

.checkbox-group {
    display: flex;
    align-items: center;
    gap: 12px;
    padding: 15px;
    background: var(--cream) !important;
    border: 2px solid var(--cream);
    border-radius: 12px !important;
    cursor: pointer;
    transition: var(--transition);
}

.checkbox-group:hover {
    border-color: var(--coral);
    background: rgba(225, 145, 132, 0.12) !important;
}

.checkbox-group input[type="checkbox"] {
    width: 24px;
    height: 24px;
    margin: 0;
    cursor: pointer;
    accent-color: var(--berry);
}

.checkbox-group label {
    margin: 0;
    cursor: pointer;
    color: var(--vampire) !important;
    font-weight: 600;
}

.form-actions {
    display: flex;
    justify-content: flex-end;
    gap: 12px;
    margin-top: 25px;
    background: transparent !important;
}

.btn-primary-gradient,
.btn-secondary-gradient {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 10px;
    padding: 12px 30px;
    border: none !important;
    border-radius: 30px !important;
    color: var(--white) !important;
    font-size: 15px;
    font-weight: 600;
    text-decoration: none;
    cursor: pointer;
    transition: var(--transition);
}

.btn-primary-gradient {
    background: var(--berry) !important;
}

.btn-secondary-gradient {
    background: var(--green) !important;
}

.btn-primary-gradient:hover,
.btn-secondary-gradient:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 20px rgba(88, 96, 65, 0.3);
    color: var(--white) !important;
}

.btn-primary-gradient:hover {
    background: var(--vampire) !important;
}

.btn-secondary-gradient:hover {
    background: var(--green-dark) !important;
}

.error-message {
    color: var(--danger) !important;
    font-size: 12px;
    display: block;
}


@media (max-width: 768px) {
    .create-event-container {
        padding: 20px;
    }

    .form-grid {
        grid-template-columns: 1fr;
    }

    .section-header {
        align-items: flex-start;
    }

    .form-actions {
        flex-direction: column-reverse;
    }

    .btn-primary-gradient,
    .btn-secondary-gradient {
        width: 100%;
    }
}



.create-event-container {
    max-width: 1220px !important;
    width: min(100%, 1220px) !important;
}

.create-header,
.event-form-luxury {
    width: min(100%, 1020px) !important;
    max-width: 1120px !important;
    margin-left: auto !important;
    margin-right: auto !important;
}
.create-header {
    margin-left: 110px !important;
  
}
</style>

@endpush