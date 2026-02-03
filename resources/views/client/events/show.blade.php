@extends('layouts.client')

@section('title', $event->name)

@section('content')
<div class="event-details-container">
    
    <!-- Header -->
    <div class="event-header">
        <div class="header-left">
            <a href="{{ route('client.dashboard') }}" class="back-link">
                <i class="fas fa-arrow-left"></i> Back to Dashboard
            </a>
            <div class="title-section">
                <h2>{{ $event->name }}</h2>
                <div class="event-meta">
                    <span class="meta-item">
                        <i class="fas fa-calendar"></i>
                        {{ $event->start_date->format('F d, Y') }}
                        @if($event->end_date && $event->end_date != $event->start_date)
                            - {{ $event->end_date->format('F d, Y') }}
                        @endif
                    </span>
                    <span class="meta-item">
                        <i class="fas fa-map-marker-alt"></i>
                        {{ Str::limit($event->location_text, 40) }}
                    </span>
                </div>
            </div>
        </div>
        
        <div class="header-right">
            <!-- Status Badge -->
            @if($event->status === 'draft')
                <span class="status-badge draft">
                    <i class="fas fa-file-alt"></i> Draft
                </span>
            @elseif($event->status === 'planned')
                <span class="status-badge planned">
                    <i class="fas fa-check-circle"></i> Planned
                </span>
            @elseif($event->status === 'in_progress')
                <span class="status-badge in-progress">
                    <i class="fas fa-spinner"></i> In Progress
                </span>
            @elseif($event->status === 'completed')
                <span class="status-badge completed">
                    <i class="fas fa-check-double"></i> Completed
                </span>
            @else
                <span class="status-badge cancelled">
                    <i class="fas fa-times-circle"></i> Cancelled
                </span>
            @endif
        </div>
    </div>

    <!-- Success Message -->
    @if(session('success'))
        <div class="alert alert-success">
            <i class="fas fa-check-circle"></i>
            <span>{{ session('success') }}</span>
        </div>
    @endif

    <!-- Quick Stats Bar -->
    <div class="stats-bar">
        <!-- Countdown -->
        <div class="stat-item">
            <div class="stat-icon countdown">
                <i class="fas fa-clock"></i>
            </div>
            <div class="stat-info">
                @php
                    $daysUntil = now()->diffInDays($event->start_date, false);
                @endphp
                @if($daysUntil > 0)
                    <h4>{{ $daysUntil }}</h4>
                    <p>Days Until Event</p>
                @elseif($daysUntil === 0)
                    <h4>Today!</h4>
                    <p>Event is Today</p>
                @else
                    <h4>{{ abs($daysUntil) }}</h4>
                    <p>Days Ago</p>
                @endif
            </div>
        </div>

        <!-- Budget -->
        <div class="stat-item">
            <div class="stat-icon budget">
                <i class="fas fa-dollar-sign"></i>
            </div>
            <div class="stat-info">
                <h4>${{ number_format($event->budget_overall) }}</h4>
                <p>Total Budget</p>
            </div>
        </div>

        <!-- Guests -->
        <div class="stat-item">
            <div class="stat-icon guests">
                <i class="fas fa-users"></i>
            </div>
            <div class="stat-info">
                @php
                    $totalGuests = $event->guests->count();
                    $rsvpCount = $event->guests->whereIn('rsvp_status', ['accepted'])->count();
                @endphp
                <h4>{{ $totalGuests > 0 ? $rsvpCount : $event->guest_estimate }}</h4>
                <p>{{ $totalGuests > 0 ? 'Guests RSVP\'d' : 'Expected Guests' }}</p>
            </div>
        </div>

        <!-- Tasks Progress -->
        <div class="stat-item">
            <div class="stat-icon tasks">
                <i class="fas fa-tasks"></i>
            </div>
            <div class="stat-info">
                @php
                    $totalTasks = $event->tasks->count();
                    $completedTasks = $event->tasks->where('status', 'done')->count();
                    $progress = $totalTasks > 0 ? round(($completedTasks / $totalTasks) * 100) : 0;
                @endphp
                <h4>{{ $progress }}%</h4>
                <p>Tasks Complete</p>
            </div>
        </div>
    </div>

    <!-- Tabs Navigation -->
    <div class="tabs-container">
        <div class="tabs-nav">
            <button class="tab-btn active" data-tab="overview">
                <i class="fas fa-info-circle"></i> Overview
            </button>
            <button class="tab-btn" data-tab="guests">
                <i class="fas fa-users"></i> Guests
                @if($event->guests->count() > 0)
                    <span class="tab-badge">{{ $event->guests->count() }}</span>
                @endif
            </button>
            <button class="tab-btn" data-tab="budget">
                <i class="fas fa-wallet"></i> Budget
            </button>
            <button class="tab-btn" data-tab="timeline">
                <i class="fas fa-stream"></i> Timeline
            </button>
            <button class="tab-btn" data-tab="messages">
                <i class="fas fa-comments"></i> Messages
                @if($event->planner)
                    <span class="tab-badge">0</span>
                @endif
            </button>
        </div>

        <!-- Tab Content -->
        <div class="tabs-content">
            
            <!-- OVERVIEW TAB -->
            <div class="tab-pane active" id="overview">
                <div class="overview-grid">
                    
                    <!-- Event Information Card -->
                    <div class="info-card">
                        <h3><i class="fas fa-info-circle"></i> Event Information</h3>
                        <div class="info-list">
                            <div class="info-item">
                                <label><i class="fas fa-tag"></i> Event Type</label>
                                <span>{{ $event->eventType->name }}</span>
                            </div>
                            <div class="info-item">
                                <label><i class="fas fa-calendar-alt"></i> Start Date</label>
                                <span>{{ $event->start_date->format('F d, Y') }}</span>
                            </div>
                            @if($event->end_date && $event->end_date != $event->start_date)
                                <div class="info-item">
                                    <label><i class="fas fa-calendar-check"></i> End Date</label>
                                    <span>{{ $event->end_date->format('F d, Y') }}</span>
                                </div>
                            @endif
                            <div class="info-item">
                                <label><i class="fas fa-map-marker-alt"></i> Location</label>
                                <span>{{ $event->location_text }}</span>
                            </div>
                            <div class="info-item">
                                <label><i class="fas fa-users"></i> Expected Guests</label>
                                <span>{{ $event->guest_estimate }}</span>
                            </div>
                            @if($event->description)
                                <div class="info-item full-width">
                                    <label><i class="fas fa-align-left"></i> Description</label>
                                    <span class="description">{{ $event->description }}</span>
                                </div>
                            @endif
                        </div>
                    </div>

                    <!-- Planner Information Card -->
                    <div class="info-card">
                        <h3><i class="fas fa-user-tie"></i> Event Planner</h3>
                        @if($event->planner)
                            <div class="planner-info">
                                <div class="planner-avatar">
                                    <img src="{{ $event->planner->avatar_url ?? 'https://ui-avatars.com/api/?name=' . urlencode($event->planner->name) }}" 
                                         alt="{{ $event->planner->name }}">
                                </div>
                                <div class="planner-details">
                                    <h4>{{ $event->planner->name }}</h4>
                                    @if($event->planner->plannerProfile)
                                        <p class="planner-specialty">{{ $event->planner->plannerProfile->specialties }}</p>
                                        <div class="planner-rating">
                                            <i class="fas fa-star"></i>
                                            <span>{{ $event->planner->rating_avg ?? '5.0' }}</span>
                                            <small>({{ $event->planner->review_count ?? 0 }} reviews)</small>
                                        </div>
                                    @endif
                                    <a href="{{ route('client.messages') }}" class="btn-message">
                                        <i class="fas fa-comments"></i> Message Planner
                                    </a>
                                </div>
                            </div>
                        @else
                            <div class="no-planner">
                                <i class="fas fa-user-slash"></i>
                                <p>No planner assigned yet</p>
                                <small>You can assign a planner later</small>
                            </div>
                        @endif
                    </div>

                </div>
            </div>

            <!-- GUESTS TAB -->
           <div class="tab-pane" id="guests">
    <div class="guests-container">
        
        <!-- Guest Stats -->
        <div class="guest-stats-bar">
            <div class="stat-box">
                <i class="fas fa-users"></i>
                <div>
                    <h4 id="total-guests">{{ $event->guests->count() }}</h4>
                    <p>Total Invited</p>
                </div>
            </div>
            <div class="stat-box">
                <i class="fas fa-check-circle"></i>
                <div>
                    <h4 id="accepted-guests">{{ $event->guests->where('rsvp_status', 'accepted')->count() }}</h4>
                    <p>Accepted</p>
                </div>
            </div>
            <div class="stat-box">
                <i class="fas fa-times-circle"></i>
                <div>
                    <h4 id="declined-guests">{{ $event->guests->where('rsvp_status', 'declined')->count() }}</h4>
                    <p>Declined</p>
                </div>
            </div>
            <div class="stat-box">
                <i class="fas fa-clock"></i>
                <div>
                    <h4 id="pending-guests">{{ $event->guests->where('rsvp_status', 'pending')->count() }}</h4>
                    <p>Pending</p>
                </div>
            </div>
        </div>

        <!-- Actions Bar -->
        <div class="guest-actions-bar">
            <button class="btn-primary" id="add-guest-btn">
                <i class="fas fa-plus"></i> Add Guest
            </button>
            <div class="search-box">
                <i class="fas fa-search"></i>
                <input type="text" id="search-guests" placeholder="Search guests...">
            </div>
            <select id="filter-rsvp" class="filter-select">
                <option value="all">All Guests</option>
                <option value="pending">Pending</option>
                <option value="accepted">Accepted</option>
                <option value="declined">Declined</option>
            </select>
        </div>

        <!-- Guest List -->
        @if($event->guests->count() > 0)
            <div class="guest-table-container">
                <table class="guest-table" id="guest-table">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Phone</th>
                            <th>Dietary</th>
                            <th>Plus One</th>
                            <th>RSVP Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($event->guests as $guest)
                            <tr data-guest-id="{{ $guest->id }}" data-rsvp="{{ $guest->rsvp_status }}">
                                <td class="guest-name">
                                    <div class="name-cell">
                                        <div class="avatar">{{ strtoupper(substr($guest->name, 0, 1)) }}</div>
                                        <span>{{ $guest->name }}</span>
                                    </div>
                                </td>
                                <td>{{ $guest->email }}</td>
                                <td>{{ $guest->phone ?? '-' }}</td>
                                <td>{{ $guest->dietary_restrictions ?? 'None' }}</td>
                                <td>
                                    @if($guest->plus_one_allowed)
                                        <span class="plus-one-badge">
                                            <i class="fas fa-check"></i> {{ $guest->plus_one_name ?? 'Yes' }}
                                        </span>
                                    @else
                                        <span class="text-muted">No</span>
                                    @endif
                                </td>
                                <td>
                                    @if($guest->rsvp_status === 'accepted')
                                        <span class="rsvp-badge accepted">
                                            <i class="fas fa-check-circle"></i> Accepted
                                        </span>
                                    @elseif($guest->rsvp_status === 'declined')
                                        <span class="rsvp-badge declined">
                                            <i class="fas fa-times-circle"></i> Declined
                                        </span>
                                    @else
                                        <span class="rsvp-badge pending">
                                            <i class="fas fa-clock"></i> Pending
                                        </span>
                                    @endif
                                </td>
                                <td>
                                    <div class="action-buttons">
                                        <button class="btn-icon edit-guest" 
                                                data-guest-id="{{ $guest->id }}"
                                                data-guest-name="{{ $guest->name }}"
                                                data-guest-email="{{ $guest->email }}"
                                                data-guest-phone="{{ $guest->phone }}"
                                                data-guest-dietary="{{ $guest->dietary_restrictions }}"
                                                data-guest-plus-one="{{ $guest->plus_one_allowed ? 'true' : 'false' }}"
                                                data-guest-plus-one-name="{{ $guest->plus_one_name }}"
                                                data-guest-notes="{{ $guest->notes }}"
                                                title="Edit">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <button class="btn-icon delete-guest" 
                                                data-guest-id="{{ $guest->id }}"
                                                data-guest-name="{{ $guest->name }}"
                                                title="Delete">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <div class="empty-state" id="empty-state">
                <i class="fas fa-user-plus"></i>
                <h3>No Guests Yet</h3>
                <p>Start adding guests to your event</p>
                <button class="btn-primary" id="add-first-guest-btn">
                    <i class="fas fa-plus"></i> Add Your First Guest
                </button>
            </div>
        @endif

    </div>
</div>

            <!-- BUDGET TAB -->
            <div class="tab-pane" id="budget">
                <div class="budget-container">
                    <div class="budget-overview">
                        <div class="budget-summary">
                            <h3>Budget Overview</h3>
                            <div class="budget-amount">
                                <span class="label">Total Budget</span>
                                <span class="amount">${{ number_format($event->budget_overall, 2) }}</span>
                            </div>
                            <p class="note">Your planner will manage budget categories and expenses</p>
                        </div>
                        
                        <div class="coming-soon-notice">
                            <i class="fas fa-chart-pie"></i>
                            <h3>Budget Tracking</h3>
                            <p>Your planner will create budget categories and track expenses here</p>
                            <p class="note">Full budget features coming in next part!</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- TIMELINE TAB -->
            <div class="tab-pane" id="timeline">
                <div class="timeline-container">
                    @if($event->tasks->count() > 0)
                        <div class="coming-soon-notice">
                            <i class="fas fa-stream"></i>
                            <h3>Event Timeline</h3>
                            <p>Your planner has created {{ $event->tasks->count() }} tasks</p>
                            <p class="note">Full timeline view coming in next part!</p>
                        </div>
                    @else
                        <div class="empty-state">
                            <i class="fas fa-tasks"></i>
                            <h3>No Tasks Yet</h3>
                            <p>Your planner will create a timeline with tasks soon</p>
                        </div>
                    @endif
                </div>
            </div>

            <!-- MESSAGES TAB -->
            <div class="tab-pane" id="messages">
                <div class="messages-container">
                    @if($event->planner)
                        <div class="coming-soon-notice">
                            <i class="fas fa-comments"></i>
                            <h3>Messages</h3>
                            <p>Chat with {{ $event->planner->name }} about your event</p>
                            <p class="note">Full messaging feature coming in next part!</p>
                        </div>
                    @else
                        <div class="empty-state">
                            <i class="fas fa-user-slash"></i>
                            <h3>No Planner Assigned</h3>
                            <p>Assign a planner to start messaging</p>
                        </div>
                    @endif
                </div>
            </div>

        </div>
    </div>

</div>

<!-- ADD/EDIT GUEST MODAL -->
<div class="modal" id="guest-modal">
    <div class="modal-overlay"></div>
    <div class="modal-content">
        <div class="modal-header">
            <h3 id="modal-title">Add Guest</h3>
            <button class="modal-close" id="close-modal">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <form id="guest-form">
            <input type="hidden" id="guest-id">
            <input type="hidden" id="form-method" value="POST">
            
            <div class="form-grid">
                <!-- Name -->
                <div class="form-group">
                    <label for="guest-name">
                        Name <span class="required">*</span>
                    </label>
                    <input type="text" id="guest-name" required>
                    <span class="form-error" id="error-name"></span>
                </div>

                <!-- Email -->
                <div class="form-group">
                    <label for="guest-email">
                        Email <span class="required">*</span>
                    </label>
                    <input type="email" id="guest-email" required>
                    <span class="form-error" id="error-email"></span>
                </div>

                <!-- Phone -->
                <div class="form-group">
                    <label for="guest-phone">Phone (Optional)</label>
                    <input type="text" id="guest-phone">
                </div>

                <!-- Dietary Restrictions -->
                <div class="form-group">
                    <label for="guest-dietary">Dietary Restrictions</label>
                    <select id="guest-dietary">
                        <option value="">None</option>
                        <option value="Vegan">Vegan</option>
                        <option value="Vegetarian">Vegetarian</option>
                        <option value="Gluten-Free">Gluten-Free</option>
                        <option value="Halal">Halal</option>
                        <option value="Kosher">Kosher</option>
                        <option value="Allergies">Allergies (specify in notes)</option>
                    </select>
                </div>

                <!-- Plus One Allowed -->
                <div class="form-group">
                    <label class="checkbox-label">
                        <input type="checkbox" id="guest-plus-one">
                        <span>Allow Plus One</span>
                    </label>
                </div>

                <!-- Plus One Name -->
                <div class="form-group" id="plus-one-name-group" style="display: none;">
                    <label for="guest-plus-one-name">Plus One Name</label>
                    <input type="text" id="guest-plus-one-name" placeholder="Guest's plus one">
                </div>

                <!-- Notes -->
                <div class="form-group full-width">
                    <label for="guest-notes">Notes (Optional)</label>
                    <textarea id="guest-notes" rows="3" placeholder="Any special notes about this guest..."></textarea>
                </div>
            </div>

            <div class="modal-actions">
                <button type="button" class="btn-secondary" id="cancel-btn">Cancel</button>
                <button type="submit" class="btn-primary" id="submit-btn">
                    <i class="fas fa-save"></i> Save Guest
                </button>
            </div>
        </form>
    </div>
</div>

<!-- DELETE CONFIRMATION MODAL -->
<div class="modal" id="delete-modal">
    <div class="modal-overlay"></div>
    <div class="modal-content modal-small">
        <div class="modal-header">
            <h3>Delete Guest</h3>
            <button class="modal-close" id="close-delete-modal">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <div class="modal-body">
            <div class="delete-warning">
                <i class="fas fa-exclamation-triangle"></i>
                <p>Are you sure you want to remove <strong id="delete-guest-name"></strong>?</p>
                <p class="note">This action cannot be undone.</p>
            </div>
        </div>
        <div class="modal-actions">
            <button type="button" class="btn-secondary" id="cancel-delete-btn">Cancel</button>
            <button type="button" class="btn-danger" id="confirm-delete-btn">
                <i class="fas fa-trash"></i> Delete Guest
            </button>
        </div>
    </div>
</div>

<script>
    // Pass event ID to JavaScript
    const EVENT_ID = {{ $event->id }};
    const CSRF_TOKEN = '{{ csrf_token() }}';
</script>
@endsection

@push('styles')
<link rel="stylesheet" href="{{ asset('css/event-details.css') }}">
@endpush

@push('scripts')
<script src="{{ asset('js/event-details.js') }}"></script>
@endpush