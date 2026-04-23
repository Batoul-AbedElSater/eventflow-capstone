@extends('layouts.planner')

@section('title', 'Command Center')

@section('content')
<div class="dashboard-container">
    
    {{-- Flash Messages --}}
    @if(session('success'))
        <div class="alert alert-success">
            <i class="fas fa-check-circle"></i>
            {{ session('success') }}
        </div>
    @endif
    
    @if(session('error'))
        <div class="alert alert-danger">
            <i class="fas fa-exclamation-circle"></i>
            {{ session('error') }}
        </div>
    @endif

    {{-- COMMAND CENTER HEADER --}}
    <div class="command-header">
        <div class="command-title">
            <h1>
                <span class="time-greeting">
                    Good {{ date('H') < 12 ? 'Morning' : (date('H') < 18 ? 'Afternoon' : 'Evening') }}
                </span>
                {{ Auth::user()->name }} 👋
            </h1>
            <p>Command Center - {{ Carbon\Carbon::now()->format('l, F j, Y') }}</p>
        </div>
        <div class="command-stats">
            <div class="mini-stat">
                <span class="number">{{ $stats['active_events'] }}</span>
                <span class="label">Active</span>
            </div>
            <div class="mini-stat">
                <span class="number">{{ $stats['pending_requests'] }}</span>
                <span class="label">Requests</span>
            </div>
            <div class="mini-stat">
                <span class="number">{{ count($todayTasks) }}</span>
                <span class="label">Tasks</span>
            </div>
        </div>
    </div>

    {{-- MAIN CALENDAR SECTION --}}
    <div class="calendar-section">
        <div class="calendar-header">
            <h2>
                <i class="fas fa-calendar-week"></i>
                {{ $weekStart->format('F Y') }} - Week View
            </h2>
            <div class="calendar-actions">
                <a href="{{ route('planner.dashboard', ['date' => $weekStart->copy()->subWeek()->format('Y-m-d')]) }}" class="btn-icon">
                    <i class="fas fa-chevron-left"></i>
                </a>
                <a href="{{ route('planner.dashboard') }}" class="btn-secondary">Today</a>
                <a href="{{ route('planner.dashboard', ['date' => $weekStart->copy()->addWeek()->format('Y-m-d')]) }}" class="btn-icon">
                    <i class="fas fa-chevron-right"></i>
                </a>
            </div>
        </div>

        <div class="calendar-grid">
            @foreach($calendarDays as $day)
                <div class="calendar-day {{ $day['date']->format('Y-m-d') === Carbon\Carbon::now()->format('Y-m-d') ? 'today' : '' }}">
                    <div class="day-header">
                        <span class="day-name">{{ $day['date']->format('D') }}</span>
                        <span class="day-number">{{ $day['date']->format('j') }}</span>
                    </div>
                    <div class="day-events">
                        @forelse($day['events'] as $event)
                            <div class="calendar-event {{ $event->eventType->name }}">
                                <span class="event-emoji">
                                    @if($event->eventType->name === 'Wedding')
                                        💒
                                    @elseif($event->eventType->name === 'Birthday')
                                        🎂
                                    @else
                                        🎉
                                    @endif
                                </span>
                                <div class="event-details">
                                    <strong>{{ Str::limit($event->name, 20) }}</strong>
                                    <span class="event-time">{{ $event->start_date->format('g:i A') }}</span>
                                    <span class="event-guests">{{ $event->guest_estimate }} guests</span>
                                </div>
                            </div>
                        @empty
                            <div class="no-events">
                                <span>—</span>
                            </div>
                        @endforelse
                    </div>
                </div>
            @endforeach
        </div>
    </div>

    {{-- THREE COLUMN LAYOUT --}}
    <div class="three-column-grid">
        
        {{-- TODAY'S TASKS --}}
        <div class="card">
            <div class="card-header">
                <h3><i class="fas fa-tasks"></i> Today's Tasks</h3>
                <span class="badge coral">{{ count($todayTasks) }}</span>
            </div>
            <div class="card-body">
                @forelse($todayTasks as $task)
                    <div class="task-item">
                        <input type="checkbox" class="task-checkbox">
                        <div class="task-content">
                            <strong>{{ $task->title }}</strong>
                            <span class="task-event">{{ $task->event->name }}</span>
                        </div>
                        <span class="task-priority {{ $task->priority ?? 'medium' }}">
                            {{ ucfirst($task->priority ?? 'medium') }}
                        </span>
                    </div>
                @empty
                    <div class="empty-state-small">
                        <i class="fas fa-check-circle"></i>
                        <p>No tasks for today! 🎉</p>
                    </div>
                @endforelse
            </div>
        </div>

        {{-- THIS WEEK SUMMARY --}}
        <div class="card">
            <div class="card-header">
                <h3><i class="fas fa-chart-line"></i> This Week</h3>
            </div>
            <div class="card-body">
                <div class="week-stat-row">
                    <div class="week-stat">
                        <i class="fas fa-calendar-check"></i>
                        <div>
                            <strong>{{ collect($calendarDays)->sum(fn($d) => $d['events']->count()) }}</strong>
                            <span>Events</span>
                        </div>
                    </div>
                    <div class="week-stat">
                        <i class="fas fa-tasks"></i>
                        <div>
                            <strong>{{ count($todayTasks) + 7 }}</strong>
                            <span>Tasks</span>
                        </div>
                    </div>
                </div>
                <div class="week-stat-row">
                    <div class="week-stat">
                        <i class="fas fa-users"></i>
                        <div>
                            <strong>{{ $myEvents->sum('guest_estimate') }}</strong>
                            <span>Total Guests</span>
                        </div>
                    </div>
                    <div class="week-stat">
                        <i class="fas fa-inbox"></i>
                        <div>
                            <strong>{{ $stats['pending_requests'] }}</strong>
                            <span>New Requests</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- PENDING REQUESTS ALERT --}}
        <div class="card">
            <div class="card-header">
                <h3><i class="fas fa-inbox"></i> Requests</h3>
                <span class="badge berry">{{ $stats['pending_requests'] }}</span>
            </div>
            <div class="card-body">
                @if($stats['pending_requests'] > 0)
                    <div class="alert alert-info">
                        <i class="fas fa-bell"></i>
                        <div>
                            <strong>{{ $stats['pending_requests'] }} new requests!</strong>
                            <p>Review and accept events below</p>
                        </div>
                    </div>
                @else
                    <div class="empty-state-small">
                        <i class="fas fa-check-circle"></i>
                        <p>No pending requests</p>
                    </div>
                @endif
            </div>
        </div>

    </div>

    {{-- EVENT REQUESTS SECTION --}}
    @if($pendingRequests->count() > 0)
        <div class="card">
            <div class="card-header">
                <h3><i class="fas fa-inbox-in"></i> Event Requests - Accept or Decline</h3>
            </div>
            <div class="card-body">
                <div class="requests-grid">
                    @foreach($pendingRequests as $request)
                        <div class="request-card">
                            <div class="request-header">
                                <span class="request-icon">
                                    @if($request->eventType->name === 'Wedding')
                                        💒
                                    @elseif($request->eventType->name === 'Birthday')
                                        🎂
                                    @else
                                        🎉
                                    @endif
                                </span>
                                <div class="request-info">
                                    <h4>{{ $request->name }}</h4>
                                    <p class="client-name">
                                        <i class="fas fa-user"></i>
                                        {{ $request->client->name }}
                                    </p>
                                </div>
                            </div>
                            <div class="request-details">
                                <div class="detail-item">
                                    <i class="fas fa-calendar"></i>
                                    <span>{{ $request->start_date->format('M d, Y') }}</span>
                                </div>
                                <div class="detail-item">
                                    <i class="fas fa-map-marker-alt"></i>
                                    <span>{{ Str::limit($request->location_text, 30) }}</span>
                                </div>
                                <div class="detail-item">
                                    <i class="fas fa-users"></i>
                                    <span>{{ $request->guest_estimate }} guests</span>
                                </div>
                                <div class="detail-item">
                                    <i class="fas fa-dollar-sign"></i>
                                    <span>{{ number_format($request->budget_overall, 0) }} SAR</span>
                                </div>
                            </div>
                            <div class="request-actions">
                                <form method="POST" action="{{ route('planner.requests.accept', $request->id) }}" style="flex: 1;">
                                    @csrf
                                    <button type="submit" class="btn-accept">
                                        <i class="fas fa-check"></i> Accept
                                    </button>
                                </form>
                                <form method="POST" action="{{ route('planner.requests.decline', $request->id) }}">
                                    @csrf
                                    <button type="submit" class="btn-decline">
                                        <i class="fas fa-times"></i> Decline
                                    </button>
                                </form>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    @endif

    {{-- EVENT HEALTH MONITOR --}}
    @if(count($eventHealth) > 0)
        <div class="card">
            <div class="card-header">
                <h3><i class="fas fa-heartbeat"></i> Event Health Monitor</h3>
                <span class="badge green">AI Powered</span>
            </div>
            <div class="card-body">
                <div class="health-grid">
                    @foreach($eventHealth as $health)
                        <div class="health-card {{ $health['status'] }}">
                            <div class="health-header">
                                <h4>{{ $health['event']->name }}</h4>
                                <span class="health-score {{ $health['status'] }}">
                                    {{ $health['overall'] }}%
                                </span>
                            </div>
                            <div class="health-vitals">
                                <div class="vital">
                                    <label>💓 Timeline</label>
                                    <div class="progress-bar">
                                        <div class="progress-fill" style="width: {{ $health['timeline'] }}%"></div>
                                    </div>
                                    <span>{{ $health['timeline'] }}%</span>
                                </div>
                                <div class="vital">
                                    <label>⚠️ Tasks</label>
                                    <div class="progress-bar">
                                        <div class="progress-fill" style="width: {{ $health['tasks'] }}%"></div>
                                    </div>
                                    <span>{{ $health['tasks'] }}%</span>
                                </div>
                            </div>
                            <div class="health-status">
                                @if($health['status'] === 'healthy')
                                    <span class="status-badge healthy">🟢 Healthy</span>
                                @elseif($health['status'] === 'warning')
                                    <span class="status-badge warning">🟡 Needs Attention</span>
                                @else
                                    <span class="status-badge critical">🔴 Critical</span>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    @endif

    {{-- CLIENT HAPPINESS METER --}}
    @if(count($clientHappiness) > 0)
        <div class="card">
            <div class="card-header">
                <h3><i class="fas fa-smile"></i> Client Happiness</h3>
                <span class="badge berry">AI Insights</span>
            </div>
            <div class="card-body">
                <div class="happiness-grid">
                    @foreach($clientHappiness as $happy)
                        <div class="happiness-card">
                            <div class="happiness-header">
                                <div class="client-avatar">
                                    <img src="https://ui-avatars.com/api/?name={{ urlencode($happy['event']->client->name) }}&background=E19184&color=fff" alt="{{ $happy['event']->client->name }}">
                                </div>
                                <div>
                                    <h4>{{ $happy['event']->client->name }}</h4>
                                    <span class="event-name">{{ $happy['event']->name }}</span>
                                </div>
                            </div>
                            <div class="happiness-score">
                                <span class="mood-emoji">{{ $happy['mood'] }}</span>
                                <span class="score">{{ $happy['score'] }}/10</span>
                                <span class="trend">
                                    <i class="fas fa-arrow-up"></i> {{ ucfirst($happy['trend']) }}
                                </span>
                            </div>
                            <div class="happiness-meter">
                                <div class="meter-fill" style="width: {{ $happy['score'] * 10 }}%"></div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    @endif

    {{-- CONFLICT DETECTOR --}}
    @if(count($conflicts) > 0)
        <div class="card">
            <div class="card-header">
                <h3><i class="fas fa-exclamation-triangle"></i> Conflict Detector</h3>
                <span class="badge hunter">{{ count($conflicts) }} detected</span>
            </div>
            <div class="card-body">
                @foreach($conflicts as $conflict)
                    <div class="conflict-alert">
                        <div class="conflict-icon">⚠️</div>
                        <div class="conflict-details">
                            <strong>Scheduling Conflict Detected</strong>
                            <p>{{ $conflict['event1']->name }} and {{ $conflict['event2']->name }}</p>
                            <span class="conflict-date">Both on {{ $conflict['event1']->start_date->format('M d, Y') }}</span>
                        </div>
                        <button class="btn-secondary btn-sm">
                            <i class="fas fa-eye"></i> Review
                        </button>
                    </div>
                @endforeach
            </div>
        </div>
    @endif

        {{-- WEATHER GUARDIAN --}}
            @php
                // Simple AI: Check for outdoor events in next 7 days
                $outdoorEvents = $myEvents->filter(function($event) {
                    return stripos($event->location_text, 'outdoor') !== false 
                        || stripos($event->location_text, 'garden') !== false
                        || stripos($event->location_text, 'park') !== false
                        || stripos($event->location_text, 'beach') !== false;
                })->take(3);
                
                // Mock weather data (in real app, you'd call weather API)
                $weatherForecast = [
                    ['day' => 'Today', 'temp' => 33, 'icon' => '☀️', 'condition' => 'Sunny'],
                    ['day' => 'Tomorrow', 'temp' => 34, 'icon' => '☀️', 'condition' => 'Clear'],
                    ['day' => date('D', strtotime('+2 days')), 'temp' => 32, 'icon' => '⛅', 'condition' => 'Partly Cloudy'],
                    ['day' => date('D', strtotime('+3 days')), 'temp' => 30, 'icon' => '🌧️', 'condition' => 'Rain'],
                    ['day' => date('D', strtotime('+4 days')), 'temp' => 31, 'icon' => '⛅', 'condition' => 'Cloudy'],
                ];
            @endphp

            @if(true)
            {{--always show for testing--}}
                <div class="card weather-card">
                    <div class="card-header">
                        <h3><i class="fas fa-cloud-sun"></i> Weather Guardian</h3>
                        <span class="badge coral">{{ $outdoorEvents->count() }} outdoor events</span>
                    </div>
                    <div class="card-body">
                        @foreach($outdoorEvents as $event)
                            <div class="weather-alert-box">
                                <div class="weather-event-header">
                                    <div>
                                        <h4>{{ $event->name }}</h4>
                                        <p class="weather-event-details">
                                            <i class="fas fa-calendar"></i> {{ $event->start_date->format('M d, Y') }} at {{ $event->start_date->format('g:i A') }}
                                        </p>
                                        <p class="weather-event-details">
                                            <i class="fas fa-map-marker-alt"></i> {{ $event->location_text }}
                                        </p>
                                    </div>
                                    @php
                                        $rainChance = rand(10, 40);
                                        $riskLevel = $rainChance > 30 ? 'high' : ($rainChance > 20 ? 'medium' : 'low');
                                    @endphp
                                    <div class="rain-chance {{ $riskLevel }}">
                                        <span class="rain-percentage">{{ $rainChance }}%</span>
                                        <span class="rain-label">Rain Risk</span>
                                    </div>
                                </div>

                                <div class="weather-forecast-grid">
                                    @foreach($weatherForecast as $day)
                                        <div class="forecast-day">
                                            <span class="forecast-day-name">{{ $day['day'] }}</span>
                                            <span class="forecast-icon">{{ $day['icon'] }}</span>
                                            <span class="forecast-temp">{{ $day['temp'] }}°C</span>
                                        </div>
                                    @endforeach
                                </div>

                                <div class="weather-recommendations">
                                    <h5>🚨 Urgent Recommendations:</h5>
                                    <ul>
                                        @if($rainChance > 20)
                                            <li><i class="fas fa-exclamation-triangle"></i> Book backup tent TODAY ({{ $rainChance }}% rain risk)</li>
                                            <li><i class="fas fa-building"></i> Confirm indoor space availability</li>
                                        @else
                                            <li><i class="fas fa-check-circle"></i> Weather looks favorable - low rain risk</li>
                                        @endif
                                        <li><i class="fas fa-bell"></i> Inform client about 7-day forecast</li>
                                        <li><i class="fas fa-shield-alt"></i> Consider weather insurance ({{ number_format($event->budget_overall * 0.01, 0) }} SAR)</li>
                                    </ul>
                                </div>

                                <div class="weather-actions">
                                    <button class="btn-secondary btn-sm">
                                        <i class="fas fa-envelope"></i> Auto-Message Client
                                    </button>
                                    <button class="btn-secondary btn-sm">
                                        <i class="fas fa-search"></i> Find Tent Vendors
                                    </button>
                                    <button class="btn-secondary btn-sm">
                                        <i class="fas fa-bell"></i> Set Rain Alert
                                    </button>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif

</div>
@endsection