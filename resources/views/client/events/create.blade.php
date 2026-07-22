@extends('layouts.client')

@section('title', 'Create Event')

@section('content')
<div class="create-event-container">

    <!-- Header -->
    <div class="page-header">
        <div>
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

            </div>
        </div>

        <!-- Step 3: Choose Planner -->
        <div class="form-section">
            <div class="section-header">
                <span class="step-number">3</span>
                <h3>Choose Event Planner (Optional)</h3>
            </div>

            <p style="margin: 8px 0 16px; color: #475B35; font-size: 14px;">
                Available planners: {{ $planners->count() }}
            </p>

            @if($planners->isEmpty())
                <div class="form-group full-width" style="background: #EFE7DA; border: 1px solid #E19184; border-radius: 10px; padding: 12px 14px; color: #620607; margin-bottom: 16px;">
                    No planner profiles are available right now. You can continue with "No Planner Yet" and assign one later.
                </div>
            @endif

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
                            @php
                                $avatarPath = $planner->avatar_url;
                                $plannerAvatarUrl = $avatarPath
                                    ? (str_starts_with($avatarPath, 'http://') || str_starts_with($avatarPath, 'https://')
                                        ? $avatarPath
                                        : asset('storage/' . ltrim($avatarPath, '/')))
                                    : 'https://ui-avatars.com/api/?name=' . urlencode($planner->name) . '&background=EFE7DA&color=C63E4E&rounded=true&bold=true';
                            @endphp
                            <div class="planner-avatar">
                                <img src="{{ $plannerAvatarUrl }}" alt="{{ $planner->name }} profile image">
                               {{-- -  <img src="{{ $planner->avatar_url ?? 'https://ui-avatars.com/api/?name=' . '&background=FFFFFF&color=C63E4E'}} urlencode($planner->name) }}"
                                     alt="{{ $planner->name }}">--}}
                            </div>
                            <h4>{{ $planner->name }}</h4>
                            <div class="planner-rating">
                                <i class="fas fa-star"></i>
                                <span>{{ $planner->rating_avg ? number_format($planner->rating_avg, 1) : 'N/A' }}</span>
                                <small>/10 ({{ $planner->review_count ?? 0 }} reviews)</small>
                            </div>
                            @if($planner->plannerProfile)
                                @php
                                    $specialties = $planner->plannerProfile->specialties;
                                    $specialtiesText = is_array($specialties)
                                        ? implode(', ', array_filter($specialties))
                                        : (string) $specialties;
                                @endphp
                                <p class="planner-specialties">
                                    {{ Str::limit($specialtiesText ?: 'General event planning', 70) }}
                                </p>
                                @if(!is_null($planner->plannerProfile->years_experience))
                                    <p class="planner-experience">
                                        {{ $planner->plannerProfile->years_experience }} years exp.
                                    </p>
                                @endif
                                @if(!empty($planner->plannerProfile->bio))
                                    <p class="planner-experience">
                                        {{ Str::limit($planner->plannerProfile->bio, 80) }}
                                    </p>
                                @endif
                            @else
                                <p class="planner-specialties">Planner profile coming soon.</p>
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
