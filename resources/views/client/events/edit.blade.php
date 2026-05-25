@extends('layouts.client')

@section('title', 'Edit Event')

@section('content')
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