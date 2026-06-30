@extends('layouts.planner')

@section('title', 'My Events')

@section('content')
<div class="planner-events-page">
    
    {{-- Ultra Luxury Header with Analytics --}}
    <div class="events-header-supreme">
        <div class="header-content-left">
            <div class="header-icon-cosmic">
                <i class="fas fa-calendar-star"></i>
            </div>
            <div class="header-text">
                <h1>Event Command Center</h1>
                <p>Manage your events with style and precision</p>
            </div>
        </div>
        
        <div class="header-stats-mini">
            <div class="stat-mini-card confirmed">
                <div class="stat-mini-icon"><i class="fas fa-check-circle"></i></div>
                <div class="stat-mini-content">
                    <span class="stat-mini-number">{{ $stats['confirmed'] }}</span>
                    <span class="stat-mini-label">Confirmed</span>
                </div>
            </div>

            <div class="stat-mini-card in-progress">
                <div class="stat-mini-icon"><i class="fas fa-tasks"></i></div>
                <div class="stat-mini-content">
                    <span class="stat-mini-number">{{ $stats['in_progress'] }}</span>
                    <span class="stat-mini-label">In Progress</span>
                </div>
            </div>
            
            <div class="stat-mini-card revenue">
                <div class="stat-mini-icon"><i class="fas fa-dollar-sign"></i></div>
                <div class="stat-mini-content">
                    <span class="stat-mini-number">${{ number_format($stats['total_revenue'], 0) }}</span>
                    <span class="stat-mini-label">Revenue</span>
                </div>
            </div>
        </div>
    </div>

    {{-- View Toggle & Filters --}}
    <div class="view-controls-supreme">
        <div class="view-toggle-buttons">
            <button class="view-toggle-btn active" data-view="kanban">
                <i class="fas fa-columns"></i> Kanban
            </button>
            <button class="view-toggle-btn" data-view="calendar">
                <i class="fas fa-calendar"></i> Calendar
            </button>
            <button class="view-toggle-btn" data-view="analytics">
                <i class="fas fa-chart-line"></i> Analytics
            </button>
        </div>
        
        <div class="filter-controls">
            <div class="search-box-luxury">
                <i class="fas fa-search"></i>
                <input type="text" id="eventSearch" placeholder="Search events...">
            </div>
            
            <select class="filter-select-luxury" id="filterType">
                <option value="">All Types</option>
                <option value="wedding">Wedding</option>
                <option value="birthday">Birthday</option>
                <option value="corporate">Corporate</option>
                <option value="conference">Conference</option>
            </select>
            
            <button class="btn-filter-advanced" id="advancedFiltersBtn">
                <i class="fas fa-sliders-h"></i> Advanced
            </button>
        </div>
    </div>

    {{-- KANBAN BOARD VIEW --}}
    <div class="view-container" id="kanbanView">
        <div class="kanban-board-supreme">

            {{-- Column: Confirmed --}}
            <div class="kanban-column" data-status="confirmed">
                <div class="kanban-column-header confirmed">
                    <div class="column-header-left">
                        <i class="fas fa-check-circle"></i>
                        <h3>Confirmed</h3>
                    </div>
                    <span class="column-count">{{ $events->where('status', 'confirmed')->count() }}</span>
                </div>
                <div class="kanban-cards-container" id="confirmed-container">
                    @foreach($events->where('status', 'confirmed') as $event)
                        @include('planner.events.partials.kanban-card', ['event' => $event])
                    @endforeach
                </div>
            </div>

            {{-- Column: In Progress --}}
            <div class="kanban-column" data-status="in_progress">
                <div class="kanban-column-header in-progress">
                    <div class="column-header-left">
                        <i class="fas fa-tasks"></i>
                        <h3>In Progress</h3>
                    </div>
                    <span class="column-count">{{ $events->where('status', 'in_progress')->count() }}</span>
                </div>
                <div class="kanban-cards-container" id="in_progress-container">
                    @foreach($events->where('status', 'in_progress') as $event)
                        @include('planner.events.partials.kanban-card', ['event' => $event])
                    @endforeach
                </div>
            </div>

            {{-- Column: Completed --}}
            <div class="kanban-column" data-status="completed">
                <div class="kanban-column-header completed">
                    <div class="column-header-left">
                        <i class="fas fa-trophy"></i>
                        <h3>Completed</h3>
                    </div>
                    <span class="column-count">{{ $events->where('status', 'completed')->count() }}</span>
                </div>
                <div class="kanban-cards-container" id="completed-container">
                    @foreach($events->where('status', 'completed') as $event)
                        @include('planner.events.partials.kanban-card', ['event' => $event])
                    @endforeach
                </div>
            </div>

        </div>
    </div>

    {{-- CALENDAR HEAT MAP VIEW --}}
    <div class="view-container" id="calendarView" style="display: none;">
        <div class="calendar-heatmap-container">
            <div class="calendar-header-luxury">
                <button class="calendar-nav-btn" id="prevMonth">
                    <i class="fas fa-chevron-left"></i>
                </button>
                <h2 id="currentMonth">Loading...</h2>
                <button class="calendar-nav-btn" id="nextMonth">
                    <i class="fas fa-chevron-right"></i>
                </button>
            </div>
            <div id="calendarGrid" class="calendar-grid-luxury"></div>
            <div class="calendar-legend">
                <span>Less</span>
                <div class="legend-boxes">
                    <div class="legend-box level-0"></div>
                    <div class="legend-box level-1"></div>
                    <div class="legend-box level-2"></div>
                    <div class="legend-box level-3"></div>
                    <div class="legend-box level-4"></div>
                </div>
                <span>More</span>
            </div>
        </div>
    </div>

    {{-- ANALYTICS DASHBOARD VIEW --}}
    <div class="view-container" id="analyticsView" style="display: none;">
        <div class="analytics-dashboard-supreme">
            
            <div class="analytics-card revenue-chart-card">
                <div class="analytics-card-header">
                    <h3><i class="fas fa-chart-line"></i> Revenue Trends</h3>
                    <select class="period-select" id="revenuePeriod">
                        <option value="week">This Week</option>
                        <option value="month" selected>This Month</option>
                        <option value="year">This Year</option>
                    </select>
                </div>
                <canvas id="revenueChart"></canvas>
            </div>

            <div class="analytics-card event-distribution-card">
                <div class="analytics-card-header">
                    <h3><i class="fas fa-chart-pie"></i> Event Types</h3>
                </div>
                <canvas id="eventTypeChart"></canvas>
            </div>

            <div class="analytics-card top-clients-card">
                <div class="analytics-card-header">
                    <h3><i class="fas fa-star"></i> Top Clients</h3>
                </div>
                <div class="top-clients-list">
                    @foreach($topClients as $client)
                        <div class="top-client-item">
                            <div class="client-avatar">{{ strtoupper(substr($client->name, 0, 1)) }}</div>
                            <div class="client-info">
                                <strong>{{ $client->name }}</strong>
                                <span>{{ $client->events_count }} events</span>
                            </div>
                            <div class="client-revenue">${{ number_format($client->total_revenue, 0) }}</div>
                        </div>
                    @endforeach
                </div>
            </div>

            <div class="analytics-card performance-metrics-card">
                <div class="analytics-card-header">
                    <h3><i class="fas fa-tachometer-alt"></i> Performance</h3>
                </div>
                <div class="metrics-grid">
                    <div class="metric-item">
                        <div class="metric-icon"><i class="fas fa-percentage"></i></div>
                        <div class="metric-content">
                            <span class="metric-value">{{ $metrics['acceptance_rate'] }}%</span>
                            <span class="metric-label">Acceptance Rate</span>
                        </div>
                    </div>
                    <div class="metric-item">
                        <div class="metric-icon"><i class="fas fa-clock"></i></div>
                        <div class="metric-content">
                            <span class="metric-value">{{ $metrics['avg_response_time'] }}h</span>
                            <span class="metric-label">Avg Response</span>
                        </div>
                    </div>
                    <div class="metric-item">
                        <div class="metric-icon"><i class="fas fa-smile"></i></div>
                        <div class="metric-content">
                            <span class="metric-value">{{ $metrics['satisfaction_score'] }}/5</span>
                            <span class="metric-label">Client Rating</span>
                        </div>
                    </div>
                    <div class="metric-item">
                        <div class="metric-icon"><i class="fas fa-calendar-check"></i></div>
                        <div class="metric-content">
                            <span class="metric-value">{{ $metrics['completion_rate'] }}%</span>
                            <span class="metric-label">Completion Rate</span>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>

</div>

{{-- Quick Actions FAB --}}
<div class="quick-actions-fab" id="quickActionsFab">
    <button class="fab-main-btn">
        <i class="fas fa-bolt"></i>
    </button>
    <div class="fab-menu">
        <button class="fab-action-btn" data-action="export">
            <i class="fas fa-file-export"></i>
            <span>Export Events</span>
        </button>
        <button class="fab-action-btn" data-action="analytics">
            <i class="fas fa-chart-bar"></i>
            <span>View Analytics</span>
        </button>
        <button class="fab-action-btn" data-action="messages">
            <i class="fas fa-envelope"></i>
            <span>Messages</span>
        </button>
    </div>
</div>

@endsection

@push('styles')
<link rel="stylesheet" href="{{ asset('css/planner-events.css') }}">
@endpush

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.0/Sortable.min.js"></script>
<script>
    // Initialize drag-and-drop only on the 3 active columns
    document.addEventListener('DOMContentLoaded', function () {
        const containers = ['confirmed-container', 'in_progress-container', 'completed-container'];

        containers.forEach(id => {
            const el = document.getElementById(id);
            if (!el) return;

            Sortable.create(el, {
                group: 'events',        // shared group allows cross-column dragging
                animation: 150,
                ghostClass: 'kanban-card-ghost',
                chosenClass: 'kanban-card-chosen',
                onEnd: function (evt) {
                    const eventId = evt.item.dataset.eventId;
                    const newStatus = evt.to.closest('.kanban-column').dataset.status;

                    // Update column counts
                    document.querySelectorAll('.kanban-column').forEach(col => {
                        col.querySelector('.column-count').textContent =
                            col.querySelector('.kanban-cards-container').children.length;
                    });

                    // Persist to server
                    fetch(`/planner/events/${eventId}/status`, {
                        method: 'PUT',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                        },
                        body: JSON.stringify({ status: newStatus })
                    })
                    .then(res => res.json())
                    .then(data => {
                        if (!data.success) {
                            console.error('Status update failed:', data.message);
                        }
                    })
                    .catch(err => console.error('Request failed:', err));
                }
            });
        });
    });
</script>
<script src="{{ asset('js/planner-events.js') }}"></script>
@endpush