@extends('layouts.planner')

@section('title', 'Analytics')

@section('content')
<div class="dashboard-container">

    {{-- Page Header --}}
    <div class="analytics-header">
        <div>
            <h1><i class="fas fa-chart-line"></i> Analytics & Insights</h1>
            <p>Track your performance and growth</p>
        </div>
        <div class="header-actions">
            <select class="filter-select">
                <option>Last 12 Months</option>
                <option>Last 6 Months</option>
                <option>This Year</option>
            </select>
            <button class="btn-secondary">
                <i class="fas fa-download"></i> Export Report
            </button>
        </div>
    </div>

    {{-- Key Stats Grid --}}
    <div class="analytics-stats-grid">
        <div class="analytics-stat-card coral">
            <div class="stat-icon">
                <i class="fas fa-calendar-check"></i>
            </div>
            <div class="stat-info">
                <span class="stat-label">Total Events</span>
                <span class="stat-value">{{ $stats['total_events'] }}</span>
                <span class="stat-change positive">
                    <i class="fas fa-arrow-up"></i> +12% this year
                </span>
            </div>
        </div>

        <div class="analytics-stat-card berry">
            <div class="stat-icon">
                <i class="fas fa-dollar-sign"></i>
            </div>
            <div class="stat-info">
                <span class="stat-label">Total Revenue</span>
                <span class="stat-value">${{ number_format($stats['total_revenue'], 0) }}</span>
                <span class="stat-change positive">
                    <i class="fas fa-arrow-up"></i> +18% this month
                </span>
            </div>
        </div>

        <div class="analytics-stat-card green">
            <div class="stat-icon">
                <i class="fas fa-tasks"></i>
            </div>
            <div class="stat-info">
                <span class="stat-label">Task Completion</span>
                <span class="stat-value">{{ $stats['task_completion_rate'] }}%</span>
                <span class="stat-change positive">
                    <i class="fas fa-arrow-up"></i> +5% this week
                </span>
            </div>
        </div>

        <div class="analytics-stat-card hunter">
            <div class="stat-icon">
                <i class="fas fa-star"></i>
            </div>
            <div class="stat-info">
                <span class="stat-label">Avg Satisfaction</span>
                <span class="stat-value">{{ $stats['avg_satisfaction'] }}/10</span>
                <span class="stat-change positive">
                    <i class="fas fa-arrow-up"></i> +0.3 this quarter
                </span>
            </div>
        </div>
    </div>

    {{-- Charts Section --}}
    <div class="charts-grid">
        
        {{-- Events Over Time Chart --}}
        <div class="chart-card">
            <div class="chart-header">
                <h3><i class="fas fa-chart-area"></i> Events Over Time</h3>
                <span class="chart-subtitle">Last 12 months</span>
            </div>
            <div class="chart-body">
                <canvas id="eventsChart"></canvas>
            </div>
        </div>

        {{-- Revenue Chart --}}
        <div class="chart-card">
            <div class="chart-header">
                <h3><i class="fas fa-chart-bar"></i> Revenue Breakdown</h3>
                <span class="chart-subtitle">Monthly earnings</span>
            </div>
            <div class="chart-body">
                <canvas id="revenueChart"></canvas>
            </div>
        </div>

    </div>

    {{-- Event Type Breakdown --}}
    <div class="two-column-charts">
        
        <div class="chart-card">
            <div class="chart-header">
                <h3><i class="fas fa-chart-pie"></i> Event Types</h3>
            </div>
            <div class="chart-body">
                <canvas id="eventTypesChart"></canvas>
            </div>
            <div class="chart-legend">
                @foreach($eventTypeStats as $type)
                    <div class="legend-item">
                        <span class="legend-dot" style="background: {{ $loop->index === 0 ? '#E19184' : ($loop->index === 1 ? '#C63E4E' : '#475B35') }}"></span>
                        <span class="legend-label">{{ $type['name'] }}</span>
                        <span class="legend-value">{{ $type['count'] }} events</span>
                    </div>
                @endforeach
            </div>
        </div>

        {{-- Milestones --}}
        <div class="card">
            <div class="card-header">
                <h3><i class="fas fa-trophy"></i> Milestones Unlocked</h3>
            </div>
            <div class="card-body">
                <div class="milestones-grid">
                    @foreach($milestones as $milestone)
                        <div class="milestone-item {{ $milestone['unlocked'] ? 'unlocked' : 'locked' }}">
                            <div class="milestone-icon">{{ $milestone['icon'] }}</div>
                            <div class="milestone-info">
                                <strong>{{ $milestone['title'] }}</strong>
                                <span>{{ $milestone['date'] }}</span>
                            </div>
                            @if($milestone['unlocked'])
                                <i class="fas fa-check-circle milestone-check"></i>
                            @else
                                <i class="fas fa-lock milestone-lock"></i>
                            @endif
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

    </div>

    {{-- AI Predictions --}}
    <div class="card predictions-card">
        <div class="card-header">
            <h3><i class="fas fa-brain"></i> Predictions & Insights</h3>
            <span class="badge green">Remeber</span>
        </div>
        <div class="card-body">
            <div class="predictions-grid">
                <div class="prediction-item">
                    <i class="fas fa-calendar-plus prediction-icon"></i>
                    <p>{{ $predictions['next_month'] }}</p>
                </div>
                <div class="prediction-item">
                    <i class="fas fa-trophy prediction-icon"></i>
                    <p>{{ $predictions['year_total'] }}</p>
                </div>
                <div class="prediction-item">
                    <i class="fas fa-fire prediction-icon"></i>
                    <p>{{ $predictions['busy_season'] }}</p>
                </div>
            </div>
        </div>
    </div>

</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
// Events Over Time Chart
const eventsCtx = document.getElementById('eventsChart').getContext('2d');
new Chart(eventsCtx, {
    type: 'line',
    data: {
        labels: {!! json_encode(array_column($monthlyData, 'month')) !!},
        datasets: [{
            label: 'Events',
            data: {!! json_encode(array_column($monthlyData, 'count')) !!},
            borderColor: '#E19184',
            backgroundColor: 'rgba(225, 145, 132, 0.1)',
            tension: 0.4,
            fill: true,
            borderWidth: 3
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: { display: false }
        },
        scales: {
            y: { beginAtZero: true }
        }
    }
});

// Revenue Chart
const revenueCtx = document.getElementById('revenueChart').getContext('2d');
new Chart(revenueCtx, {
    type: 'bar',
    data: {
        labels: {!! json_encode(array_column($revenueData, 'month')) !!},
        datasets: [{
            label: 'Revenue ($)',
            data: {!! json_encode(array_column($revenueData, 'revenue')) !!},
            backgroundColor: '#C63E4E',
            borderRadius: 8
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: { display: false }
        },
        scales: {
            y: { beginAtZero: true }
        }
    }
});

// Event Types Pie Chart
const typesCtx = document.getElementById('eventTypesChart').getContext('2d');
new Chart(typesCtx, {
    type: 'doughnut',
    data: {
        labels: {!! json_encode($eventTypeStats->pluck('name')) !!},
        datasets: [{
            data: {!! json_encode($eventTypeStats->pluck('count')) !!},
            backgroundColor: ['#E19184', '#C63E4E', '#475B35', '#620607'],
            borderWidth: 0
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: { display: false }
        }
    }
});
</script>
@endpush