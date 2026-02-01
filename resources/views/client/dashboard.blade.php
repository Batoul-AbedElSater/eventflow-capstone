@extends('layouts.client')

@section('title', 'Dashboard')

@section('content')
<div class="dashboard-container">
    
    <!-- Welcome Section -->
    <div class="welcome-section">
        <div class="welcome-text">
            <h2>Welcome back, {{ Auth::user()->name }}! 👋</h2>
            <p>Here's what's happening with your events</p>
        </div>
        <a href="{{ route('client.events.create') }}" class="btn-primary">
            <i class="fas fa-plus"></i> Create New Event
        </a>
    </div>

    <!-- Quick Stats Cards -->
    <div class="stats-grid">
        <!-- Card 1: Total Events -->
        <div class="stat-card blue">
            <div class="stat-icon">
                <i class="fas fa-calendar-alt"></i>
            </div>
            <div class="stat-content">
                <h3>{{ $stats['total_events'] }}</h3>
                <p>Total Events</p>
                <span class="stat-trend">
                    <i class="fas fa-arrow-up"></i> {{ $stats['active_events'] }} active
                </span>
            </div>
        </div>

        <!-- Card 2: Total Guests -->
        <div class="stat-card green">
            <div class="stat-icon">
                <i class="fas fa-users"></i>
            </div>
            <div class="stat-content">
                <h3>{{ $stats['total_guests'] }}</h3>
                <p>Total Guests</p>
                <span class="stat-trend">
                    <i class="fas fa-check-circle"></i> {{ $stats['total_rsvp'] }} RSVP'd
                </span>
            </div>
        </div>

        <!-- Card 3: Budget Status -->
        <div class="stat-card yellow">
            <div class="stat-icon">
                <i class="fas fa-dollar-sign"></i>
            </div>
            <div class="stat-content">
                @php
                    $totalBudget = $events->sum('budget_overall');
                    $totalSpent = $events->sum(fn($e) => $e->getTotalSpent());
                    $budgetPercent = $totalBudget > 0 ? round(($totalSpent / $totalBudget) * 100) : 0;
                @endphp
                <h3>{{ $budgetPercent }}%</h3>
                <p>Budget Used</p>
                <span class="stat-trend">
                    ${{ number_format($totalSpent) }} / ${{ number_format($totalBudget) }}
                </span>
            </div>
        </div>

        <!-- Card 4: Next Event -->
        <div class="stat-card purple">
            <div class="stat-icon">
                <i class="fas fa-clock"></i>
            </div>
            <div class="stat-content">
                @if($upcomingEvent)
                    <h3>{{ $daysUntil }}</h3>
                    <p>Days Until</p>
                    <span class="stat-trend">
                        {{ $upcomingEvent->name }}
                    </span>
                @else
                    <h3>--</h3>
                    <p>No Upcoming</p>
                    <span class="stat-trend">Create your first event!</span>
                @endif
            </div>
        </div>
    </div>

    <!-- Events Section -->
    <div class="events-section">
        <div class="section-header">
            <h3>My Events</h3>
            <div class="section-actions">
                <select class="filter-select">
                    <option value="all">All Events</option>
                    <option value="upcoming">Upcoming</option>
                    <option value="past">Past</option>
                    <option value="cancelled">Cancelled</option>
                </select>
            </div>
        </div>

        <!-- Events Grid -->
        <div class="events-grid">
            @forelse($events as $event)
                <div class="event-card {{ $event->status }}">
                    <!-- Event Header -->
                    <div class="event-header">
                        <div class="event-icon">
                            {{ $event->eventType->name === 'Wedding' ? '💒' : '🎉' }}
                        </div>
                        <div class="event-status">
                            @if($event->status === 'draft')
                                <span class="badge blue">Draft</span>
                            @elseif($event->status === 'planned')
                                <span class="badge green">Planned</span>
                            @elseif($event->status === 'in_progress')
                                <span class="badge gray">In Progress</span>
                            @elseif($event->status === 'completed')
                                <span class="badge purple">Completed</span>
                            @else
                                <span class="badge red">Cancelled</span>
                            @endif
                        </div>
                    </div>

                    <!-- Event Info -->
                    <div class="event-info">
                        <h4>{{ $event->name }}</h4>
                        <p class="event-date">
                            <i class="fas fa-calendar"></i> 
                            {{ $event->start_date->format('M d, Y') }}
                        </p>
                        <p class="event-location">
                            <i class="fas fa-map-marker-alt"></i> 
                            {{ Str::limit($event->location_text, 30) }}
                        </p>
                    </div>

                    <!-- Event Progress -->
                    <div class="event-progress">
                        @php
                            $totalTasks = $event->tasks->count();
                            $completedTasks = $event->tasks->where('status', 'done')->count();
                            $progress = $totalTasks > 0 ? round(($completedTasks / $totalTasks) * 100) : 0;
                        @endphp
                        <div class="progress-bar">
                            <div class="progress-fill" style="width: {{ $progress }}%"></div>
                        </div>
                        <span class="progress-text">{{ $progress }}% Complete</span>
                    </div>

                    <!-- Event Stats -->
                    <div class="event-stats">
                        <div class="stat">
                            <i class="fas fa-tasks"></i>
                            <span>{{ $completedTasks }}/{{ $totalTasks }}</span>
                        </div>
                        <div class="stat">
                            <i class="fas fa-dollar-sign"></i>
                            @php
                                $spent = $event->getTotalSpent();
                                $budget = $event->budget_overall;
                                $budgetPercent = $budget > 0 ? round(($spent / $budget) * 100) : 0;
                            @endphp
                            <span>{{ $budgetPercent }}%</span>
                        </div>
                        <div class="stat">
                            <i class="fas fa-users"></i>
                            @php
                                $rsvpCount = $event->guests->whereIn('rsvp_status', ['accepted'])->count();
                            @endphp
                            <span>{{ $rsvpCount }} <i class="fas fa-arrow-up small"></i></span>
                        </div>
                    </div>

                    <!-- Event Actions -->
                    <div class="event-actions">
                        <a href="{{ route('client.events.show', $event->id) }}" class="btn-secondary">
                            View Details
                        </a>
                        @if($event->planner)
                            <a href="{{ route('client.messages', ['event' => $event->id]) }}" class="btn-icon">
                                <i class="fas fa-comments"></i>
                            </a>
                        @endif
                    </div>
                </div>
            @empty
                <!-- Empty State -->
                <div class="empty-state">
                    <i class="fas fa-calendar-plus"></i>
                    <h3>No Events Yet</h3>
                    <p>Create your first event to get started!</p>
                    <a href="{{ route('client.events.create') }}" class="btn-primary">
                        <i class="fas fa-plus"></i> Create Event
                    </a>
                </div>
            @endforelse
        </div>
    </div>

</div>
@endsection