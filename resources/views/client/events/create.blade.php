@extends('layouts.client')

@section('title', 'Create Event')

@section('content')
<div class="create-event-container">
    
    <!-- Header -->
    <div class="page-header">
        <div>
            <a href="{{ route('client.dashboard') }}" class="back-link">
                <i class="fas fa-arrow-left"></i> Back to Dashboard
            </a>
            <h2>Create New Event</h2>
            <p>Fill in the details to start planning your event</p>
        </div>
    </div>

    <!-- Form -->
    <form method="POST" action="{{ route('client.events.store') }}" class="event-form">
        @csrf

        <!-- Step 1: Event Basics -->
        <div class="form-section">
            <div class="section-header">
                <span class="step-number">1</span>
                <h3>Event Basics</h3>
            </div>

            <div class="form-grid">
                <!-- Event Type -->
                <div class="form-group full-width">
                    <label for="event_type_id">
                        Event Type <span class="required">*</span>
                    </label>
                    <select name="event_type_id" id="event_type_id" required>
                        <option value="">Select event type...</option>
                        @foreach($eventTypes as $type)
                            <option value="{{ $type->id }}" {{ old('event_type_id') == $type->id ? 'selected' : '' }}>
                                {{ $type->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('event_type_id')
                        <span class="error">{{ $message }}</span>
                    @enderror
                </div>

                <!-- Event Name -->
                <div class="form-group full-width">
                    <label for="name">
                        Event Name <span class="required">*</span>
                    </label>
                    <input type="text" 
                           name="name" 
                           id="name" 
                           placeholder="e.g., Sarah's Beach Wedding" 
                           value="{{ old('name') }}" 
                           required>
                    @error('name')
                        <span class="error">{{ $message }}</span>
                    @enderror
                </div>

                <!-- Start Date -->
                <div class="form-group">
                    <label for="start_date">
                        Event Date <span class="required">*</span>
                    </label>
                    <input type="date" 
                           name="start_date" 
                           id="start_date" 
                           value="{{ old('start_date') }}" 
                           min="{{ date('Y-m-d', strtotime('+1 day')) }}"
                           required>
                    @error('start_date')
                        <span class="error">{{ $message }}</span>
                    @enderror
                </div>

                <!-- End Date -->
                <div class="form-group">
                    <label for="end_date">
                        End Date (Optional)
                    </label>
                    <input type="date" 
                           name="end_date" 
                           id="end_date" 
                           value="{{ old('end_date') }}">
                    <small>Leave blank if single-day event</small>
                    @error('end_date')
                        <span class="error">{{ $message }}</span>
                    @enderror
                </div>

                <!-- Location -->
                <div class="form-group full-width">
                    <label for="location_text">
                        Location <span class="required">*</span>
                    </label>
                    <input type="text" 
                           name="location_text" 
                           id="location_text" 
                           placeholder="e.g., Sunset Beach, Malibu, CA" 
                           value="{{ old('location_text') }}" 
                           required>
                    @error('location_text')
                        <span class="error">{{ $message }}</span>
                    @enderror
                </div>

                <!-- Description -->
                <div class="form-group full-width">
                    <label for="description">
                        Description (Optional)
                    </label>
                    <textarea name="description" 
                              id="description" 
                              rows="4" 
                              placeholder="Tell us about your vision for this event...">{{ old('description') }}</textarea>
                    @error('description')
                        <span class="error">{{ $message }}</span>
                    @enderror
                </div>
            </div>
        </div>

        <!-- Step 2: Budget & Guests -->
        <div class="form-section">
            <div class="section-header">
                <span class="step-number">2</span>
                <h3>Budget & Guest Information</h3>
            </div>

            <div class="form-grid">
                <!-- Total Budget -->
                <div class="form-group">
                    <label for="budget_overall">
                        Total Budget <span class="required">*</span>
                    </label>
                    <div class="input-with-icon">
                        <span class="icon">$</span>
                        <input type="number" 
                               name="budget_overall" 
                               id="budget_overall" 
                               placeholder="20000" 
                               value="{{ old('budget_overall') }}" 
                               min="0" 
                               step="0.01" 
                               required>
                    </div>
                    @error('budget_overall')
                        <span class="error">{{ $message }}</span>
                    @enderror
                </div>

                <!-- Guest Estimate -->
                <div class="form-group">
                    <label for="guest_estimate">
                        Estimated Guests <span class="required">*</span>
                    </label>
                    <input type="number" 
                           name="guest_estimate" 
                           id="guest_estimate" 
                           placeholder="150" 
                           value="{{ old('guest_estimate') }}" 
                           min="1" 
                           required>
                    @error('guest_estimate')
                        <span class="error">{{ $message }}</span>
                    @enderror
                </div>

                <!-- Guest List Lock Date -->
                <div class="form-group">
                    <label for="guest_list_lock">
                        Guest List Lock Date <span class="required">*</span>
                    </label>
                    <input type="date" 
                           name="guest_list_lock" 
                           id="guest_list_lock" 
                           value="{{ old('guest_list_lock') }}" 
                           required>
                    <small>After this date, you cannot add/remove guests</small>
                    @error('guest_list_lock')
                        <span class="error">{{ $message }}</span>
                    @enderror
                </div>

                <!-- RSVP Deadline -->
                <div class="form-group">
                    <label for="rsvp_deadline">
                        RSVP Deadline <span class="required">*</span>
                    </label>
                    <input type="date" 
                           name="rsvp_deadline" 
                           id="rsvp_deadline" 
                           value="{{ old('rsvp_deadline') }}" 
                           required>
                    <small>Guests must respond by this date</small>
                    @error('rsvp_deadline')
                        <span class="error">{{ $message }}</span>
                    @enderror
                </div>
            </div>
        </div>

        <!-- Step 3: Choose Planner -->
        <div class="form-section">
            <div class="section-header">
                <span class="step-number">3</span>
                <h3>Choose Event Planner (Optional)</h3>
            </div>

            <div class="planners-grid">
                <div class="planner-card">
                    <input type="radio" name="planner_id" value="" id="no-planner" {{ old('planner_id') == '' ? 'checked' : '' }}>
                    <label for="no-planner">
                        <div class="planner-icon">
                            <i class="fas fa-user-slash"></i>
                        </div>
                        <h4>No Planner Yet</h4>
                        <p>I'll choose a planner later</p>
                    </label>
                </div>

                @foreach($planners as $planner)
                    <div class="planner-card">
                        <input type="radio" 
                               name="planner_id" 
                               value="{{ $planner->id }}" 
                               id="planner-{{ $planner->id }}"
                               {{ old('planner_id') == $planner->id ? 'checked' : '' }}>
                        <label for="planner-{{ $planner->id }}">
                            <div class="planner-avatar">
                                <img src="{{ $planner->avatar_url ?? 'https://ui-avatars.com/api/?name=' . urlencode($planner->name) }}" 
                                     alt="{{ $planner->name }}">
                            </div>
                            <h4>{{ $planner->name }}</h4>
                            <div class="planner-rating">
                                <i class="fas fa-star"></i>
                                <span>{{ $planner->rating_avg ?? '5.0' }}</span>
                                <small>({{ $planner->review_count ?? 0 }} reviews)</small>
                            </div>
                            @if($planner->plannerProfile)
                                <p class="planner-specialties">
                                    {{ Str::limit($planner->plannerProfile->specialties, 50) }}
                                </p>
                                <p class="planner-experience">
                                    {{ $planner->plannerProfile->years_experience }} years exp.
                                </p>
                            @endif
                        </label>
                    </div>
                @endforeach
            </div>
            @error('planner_id')
                <span class="error">{{ $message }}</span>
            @enderror
        </div>

        <!-- Submit -->
        <div class="form-actions">
            <a href="{{ route('client.dashboard') }}" class="btn-secondary">
                <i class="fas fa-times"></i> Cancel
            </a>
            <button type="submit" class="btn-primary">
                <i class="fas fa-check"></i> Create Event
            </button>
        </div>
    </form>

</div>
@endsection

@push('styles')
<link rel="stylesheet" href="{{ asset('css/create-event.css') }}">
@endpush

@push('scripts')
<script src="{{ asset('js/create-event.js') }}"></script>
@endpush