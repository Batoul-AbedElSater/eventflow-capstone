@extends('layouts.planner')

@section('title', 'Dashboard')

@section('content')
<div class="dashboard-container">
    
    {{-- Welcome Section --}}
    <div class="welcome-section">
        <div class="welcome-text">
            <h2>Good {{ date('H') < 12 ? 'Morning' : (date('H') < 18 ? 'Afternoon' : 'Evening') }}, {{ Auth::user()->name }}! 👋</h2>
            <p>You have a productive day ahead</p>
        </div>
        <div class="welcome-actions">
            <button class="btn-primary">
                <i class="fas fa-plus"></i>
                Create Event
            </button>
        </div>
    </div>

    {{-- Quick Stats --}}
    <div class="stats-grid">
        <div class="stat-card coral">
            <div class="stat-icon">
                <i class="fas fa-calendar-check"></i>
            </div>
            <div class="stat-content">
                <h3>{{ $stats['total_events'] }}</h3>
                <p>Total Events</p>
                <span class="stat-trend"><i class="fas fa-arrow-up"></i> +12% from last month</span>
            </div>
        </div>

        <div class="stat-card berry">
            <div class="stat-icon">
                <i class="fas fa-fire"></i>
            </div>
            <div class="stat-content">
                <h3>{{ $stats['active_events'] }}</h3>
                <p>Active Events</p>
                <span class="stat-trend"><i class="fas fa-arrow-up"></i> +5 this week</span>
            </div>
        </div>

        <div class="stat-card green">
            <div class="stat-icon">
                <i class="fas fa-dollar-sign"></i>
            </div>
            <div class="stat-content">
                <h3>${{ number_format($stats['total_revenue'], 0) }}</h3>
                <p>Revenue (Est.)</p>
                <span class="stat-trend"><i class="fas fa-arrow-up"></i> +18% this month</span>
            </div>
        </div>

        <div class="stat-card hunter">
            <div class="stat-icon">
                <i class="fas fa-star"></i>
            </div>
            <div class="stat-content">
                <h3>4.9</h3>
                <p>Average Rating</p>
                <span class="stat-trend"><i class="fas fa-arrow-up"></i> +0.2 this quarter</span>
            </div>
        </div>
    </div>

    {{-- Today's Schedule & Pending Requests --}}
    <div class="two-column-grid">
        {{-- Today's Schedule --}}
        <div class="card">
            <div class="card-header">
                <h3><i class="fas fa-clock"></i> Today's Schedule</h3>
                <span class="badge coral">{{ $todayEvents->count() }} events</span>
            </div>
            <div class="card-body">
                @forelse($todayEvents as $event)
                    <div class="timeline-item">
                        <div class="timeline-time">{{ $event->start_date->format('g:i A') }}</div>
                        <div class="timeline-content">
                            <h4>{{ $event->name }}</h4>
                            <p><i class="fas fa-map-marker-alt"></i> {{ $event->location_text }}</p>
                        </div>
                    </div>
                @empty
                    <div class="empty-state-small">
                        <i class="fas fa-calendar-day"></i>
                        <p>No events scheduled for today</p>
                    </div>
                @endforelse
            </div>
        </div>

        {{-- Pending Requests --}}
        <div class="card">
            <div class="card-header">
                <h3><i class="fas fa-inbox"></i> Pending Requests</h3>
                <span class="badge berry">{{ $pendingRequests }} new</span>
            </div>
            <div class="card-body">
                @if($pendingRequests > 0)
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle"></i>
                        <div>
                            <strong>You have {{ $pendingRequests }} new event requests!</strong>
                            <p>Review and accept events to add them to your schedule.</p>
                        </div>
                    </div>
                    <button class="btn-secondary btn-block">
                        <i class="fas fa-inbox"></i> View Requests
                    </button>
                @else
                    <div class="empty-state-small">
                        <i class="fas fa-check-circle"></i>
                        <p>No pending requests</p>
                    </div>
                @endif
            </div>
        </div>
    </div>

    {{-- Upcoming Events --}}
    <div class="card">
        <div class="card-header">
            <h3><i class="fas fa-calendar-alt"></i> Upcoming Events</h3>
            <select class="filter-select">
                <option>This Week</option>
                <option>This Month</option>
                <option>All Events</option>
            </select>
        </div>
        <div class="card-body">
            <div class="events-grid">
                @forelse($events->take(6) as $event)
                    <div class="event-card">
                        <div class="event-header">
                            <span class="event-icon">{{ $event->eventType->name === 'Wedding' ? '💒' : ($event->eventType->name === 'Birthday' ? '🎂' : '🎉') }}</span>
                            <span class="badge {{ $event->status === 'planned' ? 'coral' : ($event->status === 'in_progress' ? 'berry' : 'green') }}">
                                {{ ucfirst($event->status) }}
                            </span>
                        </div>
                        <div class="event-info">
                            <h4>{{ $event->name }}</h4>
                            <p class="event-date">
                                <i class="fas fa-calendar"></i>
                                {{ $event->start_date->format('M d, Y') }}
                            </p>
                            <p class="event-location">
                                <i class="fas fa-map-marker-alt"></i>
                                {{ $event->location_text }}
                            </p>
                        </div>
                        <div class="event-stats">
                            <div class="stat">
                                <i class="fas fa-users"></i>
                                {{ $event->guest_estimate }} guests
                            </div>
                            <div class="stat">
                                <i class="fas fa-dollar-sign"></i>
                                ${{ number_format($event->budget_overall, 0) }}
                            </div>
                        </div>
                        <div class="event-actions">
                            <button class="btn-secondary">
                                <i class="fas fa-eye"></i> View
                            </button>
                            <button class="btn-icon">
                                <i class="fas fa-comment"></i>
                            </button>
                        </div>
                    </div>
                @empty
                    <div class="empty-state">
                        <i class="fas fa-calendar-times"></i>
                        <h3>No Events Yet</h3>
                        <p>Start accepting event requests to see them here</p>
                        <button class="btn-primary">
                            <i class="fas fa-inbox"></i> View Requests
                        </button>
                    </div>
                @endforelse
            </div>
        </div>
    </div>

</div>
@endsection