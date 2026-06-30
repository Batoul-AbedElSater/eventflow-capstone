@extends('layouts.client')

@section('title', 'Edit Event')

@section('content')
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
    padding: 10px;
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
    margin-left: 40px;
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
    margin-left: 40px;
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
    flex: 0 0 46px;
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
.form-group select,
.form-group textarea {
    width: 100%;
    padding: 12px 16px;
    border: 2px solid var(--cream) !important;
    border-radius: 10px !important;
    background: var(--cream) !important;
    color: var(--berry) !important;
    font-size: 15px;
    font-family: inherit;
    transition: var(--transition);
}

.form-group select {
    min-height: 49px;
    cursor: pointer;
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
.form-group select:focus,
.form-group textarea:focus {
    outline: none !important;
    border-color: var(--berry) !important;
    background: var(--white) !important;
    box-shadow: none !important;
}

.photo-upload-container {
    display: grid;
    gap: 16px;
}

.current-photo {
    width: 100%;
    max-height: 260px;
    overflow: hidden;
    border: 2px solid var(--cream);
    border-radius: 16px;
    background: var(--cream);
}

.current-photo img {
    display: block;
    width: 100%;
    height: 260px;
    object-fit: cover;
}

.photo-upload-label {
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    min-height: 150px;
    gap: 8px;
    padding: 24px;
    background: var(--cream) !important;
    border: 2px dashed var(--coral) !important;
    border-radius: 16px;
    color: var(--vampire) !important;
    cursor: pointer;
    text-align: center;
    transition: var(--transition);
}

.photo-upload-label:hover {
    background: rgba(225, 145, 132, 0.12) !important;
    border-color: var(--berry) !important;
}

.photo-upload-label i {
    color: var(--berry) !important;
    font-size: 30px;
}

.photo-upload-label span {
    color: var(--vampire) !important;
    font-weight: 700;
}

.photo-upload-label small {
    color: var(--green) !important;
    font-size: 12px;
}

.photo-upload-container input[type="file"] {
    display: none;
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
    width: min(100%, 1120px) !important;
    max-width: 1020px !important;
    margin-left: auto !important;
    margin-right: auto !important;
}

</style>

<div class="create-event-container">
    
    <div class="create-header">
        <div class="breadcrumb">
            <a href="{{ route('client.dashboard') }}"><i class="fas fa-home"></i> Dashboard</a>
            <i class="fas fa-chevron-right"></i>
            <a href="{{ route('client.events.show', $event->id) }}">{{ $event->name }}</a>
            <i class="fas fa-chevron-right"></i>
            <span>Edit</span>
        </div>
        <h1><i class="fas fa-edit"></i> Edit Event</h1>
        <p>Update your event details</p>
    </div>

    <form method="POST" action="{{ route('client.events.update', $event->id) }}" enctype="multipart/form-data" class="event-form-luxury">
        @csrf
        @method('PUT')

        <div class="form-sections">
            
            {{-- Basic Information --}}
            <div class="form-section">
                <div class="section-header">
                    <div class="section-icon">
                        <i class="fas fa-info-circle"></i>
                    </div>
                    <div>
                        <h2>Basic Information</h2>
                        <p>Essential details about your event</p>
                    </div>
                </div>

                <div class="form-grid">
                    <div class="form-group">
                        <label for="name">Event Name *</label>
                        <input type="text" id="name" name="name" value="{{ old('name', $event->name) }}" required>
                        @error('name')
                            <span class="error-message">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="event_type_id">Event Type *</label>
                        <select id="event_type_id" name="event_type_id" required>
                            <option value="">Select type...</option>
                            @foreach($eventTypes as $type)
                                <option value="{{ $type->id }}" {{ $event->event_type_id == $type->id ? 'selected' : '' }}>
                                    {{ $type->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('event_type_id')
                            <span class="error-message">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="form-group full-width">
                        <label for="description">Description</label>
                        <textarea id="description" name="description" rows="4">{{ old('description', $event->description) }}</textarea>
                        @error('description')
                            <span class="error-message">{{ $message }}</span>
                        @enderror
                    </div>
                </div>
            </div>

            {{-- Date & Time --}}
            <div class="form-section">
                <div class="section-header">
                    <div class="section-icon">
                        <i class="fas fa-calendar"></i>
                    </div>
                    <div>
                        <h2>Date & Time</h2>
                        <p>When will your event take place?</p>
                    </div>
                </div>

                <div class="form-grid">
                    <div class="form-group">
                        <label for="start_date">Start Date *</label>
                        <input type="date" id="start_date" name="start_date" value="{{ old('start_date', $event->start_date->format('Y-m-d')) }}" required>
                        @error('start_date')
                            <span class="error-message">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="end_date">End Date</label>
                        <input type="date" id="end_date" name="end_date" value="{{ old('end_date', $event->end_date?->format('Y-m-d')) }}">
                        @error('end_date')
                            <span class="error-message">{{ $message }}</span>
                        @enderror
                    </div>
                </div>
            </div>

            {{-- Location --}}
            <div class="form-section">
                <div class="section-header">
                    <div class="section-icon">
                        <i class="fas fa-map-marker-alt"></i>
                    </div>
                    <div>
                        <h2>Location</h2>
                        <p>Where will the event be held?</p>
                    </div>
                </div>

                <div class="form-grid">
                    <div class="form-group full-width">
                        <label for="location_text">Venue Address *</label>
                        <input type="text" id="location_text" name="location_text" value="{{ old('location_text', $event->location_text) }}" required>
                        @error('location_text')
                            <span class="error-message">{{ $message }}</span>
                        @enderror
                    </div>
                </div>
            </div>

            {{-- Guest & Budget --}}
            <div class="form-section">
                <div class="section-header">
                    <div class="section-icon">
                        <i class="fas fa-users"></i>
                    </div>
                    <div>
                        <h2>Guests & Budget</h2>
                        <p>How many people and what's your budget?</p>
                    </div>
                </div>

                <div class="form-grid">
                    <div class="form-group">
                        <label for="guest_estimate">Expected Guests *</label>
                        <input type="number" id="guest_estimate" name="guest_estimate" value="{{ old('guest_estimate', $event->guest_estimate) }}" min="1" required>
                        @error('guest_estimate')
                            <span class="error-message">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="budget_overall">Total Budget (SAR) *</label>
                        <input type="number" id="budget_overall" name="budget_overall" value="{{ old('budget_overall', $event->budget_overall) }}" min="0" step="0.01" required>
                        @error('budget_overall')
                            <span class="error-message">{{ $message }}</span>
                        @enderror
                    </div>
                </div>
            </div>

            {{-- Event Photo --}}
            <div class="form-section">
                <div class="section-header">
                    <div class="section-icon">
                        <i class="fas fa-image"></i>
                    </div>
                    <div>
                        <h2>Event Photo</h2>
                        <p>Upload a beautiful image for your event</p>
                    </div>
                </div>

                <div class="form-group full-width">
                    <div class="photo-upload-container">
                        @if($event->event_photo)
                            <div class="current-photo">
                                <img src="{{ asset('storage/' . $event->event_photo) }}" alt="Current photo">
                            </div>
                        @endif
                        <label for="event_photo" class="photo-upload-label">
                            <i class="fas fa-cloud-upload-alt"></i>
                            <span>Click to upload new photo</span>
                            <small>JPG, PNG or WEBP (Max 2MB)</small>
                        </label>
                        <input type="file" id="event_photo" name="event_photo" accept="image/*">
                        @error('event_photo')
                            <span class="error-message">{{ $message }}</span>
                        @enderror
                    </div>
                </div>
            </div>

        </div>

        {{-- Form Actions --}}
        <div class="form-actions">
            <a href="{{ route('client.events.show', $event->id) }}" class="btn-secondary-gradient">
                <i class="fas fa-times"></i> Cancel
            </a>
            <button type="submit" class="btn-primary-gradient">
                <i class="fas fa-save"></i> Update Event
            </button>
        </div>
    </form>

</div>
@endsection