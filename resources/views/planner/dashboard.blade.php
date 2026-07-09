@extends('layouts.planner')

@section('title', 'Command Center')

@section('content')
<div class="dashboard-container">

    {{-- Flash Messages --}}
    @if(session('success'))
        <div class="alert alert-success"><i class="fas fa-check-circle"></i> {{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger"><i class="fas fa-exclamation-circle"></i> {{ session('error') }}</div>
    @endif

    {{-- COMMAND CENTER HEADER (updated stats) --}}
    <div class="command-header">
        <div class="command-title">
            <h1><span class="time-greeting">Good {{ date('H') < 12 ? 'Morning' : (date('H') < 18 ? 'Afternoon' : 'Evening') }}</span> {{ Auth::user()->name }} </h1>
            <p>Command Center - {{ Carbon\Carbon::now()->format('l, F j, Y') }}</p>
        </div>
        <div class="command-stats">
            <div class="mini-stat">
                <span class="number">{{ $stats['active_events'] ?? 0 }}</span>
                <span class="label">Active Events</span>
            </div>
            <div class="mini-stat">
                <span class="number">{{ $stats['pending_requests'] ?? 0 }}</span>
                <span class="label">New Requests</span>
            </div>
            <div class="mini-stat">
                <span class="number">{{ $todayTasks->count() ?? 0 }}</span>
                <span class="label">Today's Tasks</span>
            </div>
        </div>
    </div>

    <div style="height: 40px;"></div>

    {{-- MAIN CALENDAR SECTION --}}
    <div class="calendar-section">
        <div class="calendar-header">
            <h2><i class="fas fa-calendar-week"></i> {{ $weekStart->format('F Y') }} - Week View</h2>
            <div class="calendar-actions">
                <a href="{{ route('planner.dashboard', ['date' => $weekStart->copy()->subWeek()->format('Y-m-d')]) }}" class="btn-icon"><i class="fas fa-chevron-left"></i></a>
                <a href="{{ route('planner.dashboard') }}" class="btn-secondary">Today</a>
                <a href="{{ route('planner.dashboard', ['date' => $weekStart->copy()->addWeek()->format('Y-m-d')]) }}" class="btn-icon"><i class="fas fa-chevron-right"></i></a>
            </div>
        </div>
        <div class="calendar-grid">
            @foreach($calendarDays as $day)
                <div class="calendar-day {{ $day['date']->isToday() ? 'today' : '' }}">
                    <div class="day-header">
                        <span class="day-name">{{ $day['date']->format('D') }}</span>
                        <span class="day-number">{{ $day['date']->format('j') }}</span>
                    </div>
                    <div class="day-events">
                        @forelse($day['events'] as $event)
                            <div class="calendar-event {{ $event->eventType->name }}">
                                <span class="event-emoji">
                                    @if($event->eventType->name === 'Wedding') 💒
                                    @elseif($event->eventType->name === 'Birthday') 🎂
                                    @else 🎉 @endif
                                </span>
                                <div class="event-details">
                                    <strong>{{ Str::limit($event->name, 20) }}</strong>
                                    <span class="event-time">{{ $event->start_date->format('g:i A') }}</span>
                                    <span class="event-guests">{{ $event->guest_estimate }} guests</span>
                                </div>
                            </div>
                        @empty
                            <div class="no-events">—</div>
                        @endforelse
                    </div>
                </div>
            @endforeach
        </div>
    </div>

    <div style="height: 50px;"></div>

    {{-- TIME MACHINE VIEW --}}
    <div class="card time-machine-card">
        <div class="card-header">
            <h3><i class="fas fa-history"></i> Time Machine - Your Journey</h3>

        </div>
        <div class="card-body">
            <div class="time-machine-timeline">
                @foreach($timeMachineData as $data)
                    <div class="timeline-month {{ $data['is_peak'] ? 'peak-month' : '' }}">
                        <div class="timeline-bar" style="height: {{ $data['count'] > 0 ? ($data['count'] * 20) : 5 }}px">
                            <span class="bar-count">{{ $data['count'] }}</span>
                        </div>
                        <div class="timeline-label">
                            <span class="month-name">{{ $data['month'] }}</span>
                            @if($data['is_peak']) <span class="peak-badge">🎯</span> @endif
                        </div>
                    </div>
                @endforeach
            </div>

            <div class="journey-stats-grid">
                <div class="journey-stat">
                    <i class="fas fa-calendar-check"></i>
                    <div><strong>{{ $journeyInsights['total_journey_events'] }}</strong><span>Total Events Completed</span></div>
                </div>
                <div class="journey-stat">
                    <i class="fas fa-fire"></i>
                    <div><strong>{{ $journeyInsights['best_month'] }}</strong><span>Busiest Month ({{ $journeyInsights['best_month_count'] }} events)</span></div>
                </div>
                <div class="journey-stat">
                    <i class="fas fa-dollar-sign"></i>
                    <div><strong>${{ number_format($journeyInsights['total_journey_revenue'], 0) }}</strong><span>Total Revenue Earned</span></div>
                </div>
                <div class="journey-stat">
                    <i class="fas fa-chart-line"></i>
                    <div><strong>{{ $journeyInsights['avg_monthly_events'] }}</strong><span>Avg Events Per Month</span></div>
                </div>
            </div>

            <div class="journey-insights">
                <h4><i class="fas fa-brain"></i> AI Journey Insights</h4>
                <div class="insights-grid">
                    <div class="insight-card"><i class="fas fa-trending-up"></i><p>Your busiest season is <strong>{{ $journeyInsights['best_month'] }}</strong> – plan ahead for peak months!</p></div>
                    <div class="insight-card"><i class="fas fa-chart-line"></i><p>You're averaging <strong>{{ $journeyInsights['avg_monthly_events'] }} events/month</strong> – on track for {{ $journeyInsights['avg_monthly_events'] * 12 }} events this year.</p></div>
                    <div class="insight-card"><i class="fas fa-lightbulb"></i><p>Most profitable event type: <strong>Weddings</strong> – focus marketing here for maximum revenue.</p></div>
                </div>
            </div>
        </div>
    </div>

    <div style="height: 50px;"></div>

    {{-- RAPID FIRE MODE --}}

<div class="card rapid-fire-card">
    <div class="card-header">
        <h3><i class="fas fa-bolt"></i> Rapid Fire Mode</h3>
    </div>
    <div class="card-body">
        @if($todayTasks->count() > 0)
            <div id="rapidFireContainer" data-tasks='@json($todayTasks->values())' data-csrf="{{ csrf_token() }}">
                <div class="rapid-fire-task">
                    <div class="task-counter">TASK 1 of {{ $todayTasks->count() }}</div>
                    <h4 id="taskTitle">{{ $todayTasks[0]->title }}</h4>
                    <p class="task-event-name"><i class="fas fa-calendar"></i> {{ $todayTasks[0]->event->name }}</p>
                    <div class="rapid-fire-actions">
                        <button class="rapid-btn done" id="doneBtn"><i class="fas fa-check"></i> Done</button>
                        <button class="rapid-btn skip" id="skipBtn"><i class="fas fa-arrow-right"></i> Skip</button>
                        <button class="rapid-btn remind" id="remindBtn"><i class="fas fa-clock"></i> Remind Later</button>
                    </div>
                </div>
                <div class="rapid-fire-progress">
                    <div class="progress-bar-rapid"><div class="progress-fill-rapid" style="width: 0%"></div></div>
                    <span class="progress-text">Progress: 0/{{ $todayTasks->count() }} tasks</span>
                </div>
                <div class="rapid-fire-streak"><i class="fas fa-fire"></i><span>Streak: 0 tasks in a row!</span></div>
            </div>
        @else
            <div class="empty-state-small"><i class="fas fa-check-circle"></i><p>All tasks completed! Enjoy your productive day! 🎉</p></div>
        @endif
    </div>
</div>

    <div style="height: 50px;"></div>

    {{-- CONFLICT DETECTOR --}}
    @if(count($conflicts) > 0)
        <div class="card">
            <div class="card-header"><h3><i class="fas fa-exclamation-triangle"></i> Conflict Detector</h3><span class="badge hunter">{{ count($conflicts) }} detected</span></div>
            <div class="card-body">
                @foreach($conflicts as $conflict)
                    <div class="conflict-alert">
                        <div class="conflict-icon">⚠️</div>
                        <div class="conflict-details">
                            <strong>Scheduling Conflict Detected</strong>
                            <p>{{ $conflict['event1']->name }} and {{ $conflict['event2']->name }}</p>
                            <span class="conflict-date">Both on {{ $conflict['event1']->start_date->format('M d, Y') }}</span>
                        </div>
                        <button class="btn-secondary btn-sm review-conflict" data-event1="{{ $conflict['event1']->id }}" data-event2="{{ $conflict['event2']->id }}"><i class="fas fa-eye"></i> Review</button>
                    </div>
                @endforeach
            </div>
        </div>
        <div style="height: 50px;"></div>
    @endif

    {{-- EVENT HEALTH MONITOR --}}
    @if(count($eventHealth) > 0)
        <div class="card">
            <div class="card-header"><h3><i class="fas fa-heartbeat"></i> Event Health Monitor</h3></div>
            <div class="card-body">
                <div class="health-grid">
                    @foreach($eventHealth as $health)
                        <div class="health-card {{ $health['status'] }}">
                            <div class="health-header">
                                <h4>{{ $health['event']->name }}</h4>
                                <span class="health-score {{ $health['status'] }}">{{ $health['overall'] }}%</span>
                            </div>
                            <div class="health-vitals">
                                <div class="vital"><label>💓 Timeline</label><div class="progress-bar"><div class="progress-fill" style="width: {{ $health['timeline'] }}%"></div></div><span>{{ $health['timeline'] }}%</span></div>
                                <div class="vital"><label>⚠️ Tasks</label><div class="progress-bar"><div class="progress-fill" style="width: {{ $health['tasks'] }}%"></div></div><span>{{ $health['tasks'] }}%</span></div>
                            </div>
                            <div class="health-status">
                                @if($health['status'] === 'healthy') <span class="status-badge healthy">🟢 Healthy</span>
                                @elseif($health['status'] === 'warning') <span class="status-badge warning">🟡 Needs Attention</span>
                                @else <span class="status-badge critical">🔴 Critical</span> @endif
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
        <div style="height: 50px;"></div>
    @endif

    {{-- WEATHER GUARDIAN --}}
    @if($outdoorEvents->count() > 0)
        <div class="card weather-card">
            <div class="card-header"><h3><i class="fas fa-cloud-sun"></i> Weather Guardian</h3><span class="badge coral">{{ $outdoorEvents->count() }} outdoor events</span></div>
            <div class="card-body">
                @foreach($outdoorEvents as $event)
                    @php $rainChance = $weatherForecast[array_rand($weatherForecast)]['rain_chance'] ?? 15; $riskLevel = $rainChance > 30 ? 'high' : ($rainChance > 20 ? 'medium' : 'low'); @endphp
                    <div class="weather-alert-box" data-event-id="{{ $event->id }}">
                        <div class="weather-event-header">
                            <div>
                                <h4>{{ $event->name }}</h4>
                                <p class="weather-event-details"><i class="fas fa-calendar"></i> {{ $event->start_date->format('M d, Y') }} at {{ $event->start_date->format('g:i A') }}</p>
                                <p class="weather-event-details"><i class="fas fa-map-marker-alt"></i> {{ $event->location_text }}</p>
                            </div>
                            <div class="rain-chance {{ $riskLevel }}">
                                <span class="rain-percentage">{{ $rainChance }}%</span>
                                <span class="rain-label">Rain Risk</span>
                            </div>
                        </div>
                        <div class="weather-forecast-title"><h5>📅 14-Day Forecast (2 Weeks)</h5></div>
                        <div class="weather-forecast-grid-scroll">
                            @foreach($weatherForecast as $day)
                                <div class="forecast-day">
                                    <span class="forecast-day-name">{{ $day['day'] }}</span>
                                    <span class="forecast-icon">{{ $day['icon'] }}</span>
                                    <span class="forecast-temp">{{ $day['temp'] }}°C</span>
                                    <span class="forecast-rain">{{ $day['rain_chance'] }}%</span>
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
                                    <li><i class="fas fa-check-circle"></i> Weather looks favorable – low rain risk</li>
                                @endif
                                <li><i class="fas fa-bell"></i> Inform client about 14‑day forecast</li>
                                <li><i class="fas fa-shield-alt"></i> Consider weather insurance ({{ number_format($event->budget_overall * 0.01, 0) }} SAR)</li>
                            </ul>
                        </div>
                        <div class="weather-actions">
                            <button class="btn-secondary btn-sm auto-message" data-event-name="{{ $event->name }}" data-client-email="{{ optional($event->client)->email ?? '' }}"><i class="fas fa-envelope"></i> Auto-Message Client</button>
                            <button class="btn-secondary btn-sm find-vendors" data-location="{{ $event->location_text }}"><i class="fas fa-search"></i> Find Tent Vendors</button>
                            <button class="btn-secondary btn-sm set-rain-alert" data-event-id="{{ $event->id }}" data-rain-chance="{{ $rainChance }}"><i class="fas fa-bell"></i> Set Rain Alert</button>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
        <div style="height: 50px;"></div>
    @endif

    {{-- CLIENT HAPPINESS METER (always visible) --}}
<div class="card">
    <div class="card-header"><h3><i class="fas fa-smile"></i> Client Happiness</h3></div>
    <div class="card-body">
        @if(count($clientHappiness) > 0)
            <div class="happiness-grid">
                @foreach($clientHappiness as $happy)
                    <div class="happiness-card">
                        <div class="happiness-header">
                            <div class="client-avatar"><img src="https://ui-avatars.com/api/?name={{ urlencode($happy['event']->client->name) }}&background=E19184&color=fff" alt="{{ $happy['event']->client->name }}"></div>
                            <div><h4>{{ $happy['event']->client->name }}</h4><span class="event-name">{{ $happy['event']->name }}</span></div>
                        </div>
                        <div class="happiness-score">
                            <span class="mood-emoji">{{ $happy['mood'] }}</span>
                            <span class="score">{{ $happy['score'] }}/10</span>
                            <span class="trend"><i class="fas fa-arrow-up"></i> {{ ucfirst($happy['trend']) }}</span>
                        </div>
                        <div class="happiness-meter"><div class="meter-fill" style="width: {{ $happy['score'] * 10 }}%"></div></div>
                    </div>
                @endforeach
            </div>
        @else
            <div class="empty-state-small">
                <i class="fas fa-star"></i>
                <p>No ratings yet. Once clients rate you, their happiness scores will appear here.</p>
            </div>
        @endif
    </div>
</div>
    {{-- EVENT REQUESTS SECTION --}}
    @if($pendingRequests->count() > 0)
        <div class="card">
            <div class="card-header"><h3><i class="fas fa-inbox-in"></i> Event Requests - Accept or Decline</h3></div>
            <div class="card-body">
                <div class="requests-grid">
                    @foreach($pendingRequests as $request)
                        <div class="request-card">
                            <div class="request-header">
                                <span class="request-icon">@if($request->eventType->name === 'Wedding') 💒 @elseif($request->eventType->name === 'Birthday') 🎂 @else 🎉 @endif</span>
                                <div class="request-info">
                                    <h4>{{ $request->name }}</h4>
                                    <p class="client-name"><i class="fas fa-user"></i> {{ $request->client->name }}</p>
                                </div>
                            </div>
                            <div class="request-details">
                                <div class="detail-item"><i class="fas fa-calendar"></i><span>{{ $request->start_date->format('M d, Y') }}</span></div>
                                <div class="detail-item"><i class="fas fa-map-marker-alt"></i><span>{{ Str::limit($request->location_text, 30) }}</span></div>
                                <div class="detail-item"><i class="fas fa-users"></i><span>{{ $request->guest_estimate }} guests</span></div>
                                <div class="detail-item"><i class="fas fa-dollar-sign"></i><span>{{ number_format($request->budget_overall, 0) }} SAR</span></div>
                            </div>
                            <div class="request-actions">
                                <form method="POST" action="{{ route('planner.requests.accept', $request->id) }}" style="flex:1;">@csrf<button type="submit" class="btn-accept"><i class="fas fa-check"></i> Accept</button></form>
                                <form method="POST" action="{{ route('planner.requests.decline', $request->id) }}">@csrf<button type="submit" class="btn-decline"><i class="fas fa-times"></i> Decline</button></form>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    @endif

</div>
@endsection



@push('scripts')
<script>
   // Rapid Fire Mode (improved)
const rapidContainer = document.getElementById('rapidFireContainer');
if (rapidContainer) {
    let tasks = rapidContainer.dataset.tasks ? JSON.parse(rapidContainer.dataset.tasks) : [];
    let currentIndex = 0;
    let completedCount = 0;
    let streak = 0;
    const progressFill = document.querySelector('.progress-fill-rapid');
    const progressText = document.querySelector('.progress-text');
    const streakSpan = document.querySelector('.rapid-fire-streak span');
    const taskCounter = document.querySelector('.task-counter');
    const taskTitle = document.getElementById('taskTitle');
    const taskEvent = document.querySelector('.task-event-name');
    const doneBtn = document.getElementById('doneBtn');
    const skipBtn = document.getElementById('skipBtn');
    const remindBtn = document.getElementById('remindBtn');

    function updateDisplay() {
        if (currentIndex < tasks.length) {
            const task = tasks[currentIndex];
            taskCounter.textContent = `TASK ${currentIndex+1} of ${tasks.length}`;
            taskTitle.textContent = task.title;
            taskEvent.innerHTML = `<i class="fas fa-calendar"></i> ${task.event.name}`;
            doneBtn.setAttribute('data-task-id', task.id);
        } else {
            const taskDiv = document.querySelector('.rapid-fire-task');
            if (taskDiv) taskDiv.innerHTML = '<div class="completed-all"><i class="fas fa-trophy"></i><h4>All tasks completed!</h4><p>Great job!</p></div>';
        }
        const percent = (completedCount / tasks.length) * 100;
        if (progressFill) progressFill.style.width = percent + '%';
        if (progressText) progressText.textContent = `Progress: ${completedCount}/${tasks.length} tasks`;
        if (streakSpan) streakSpan.textContent = `Streak: ${streak} tasks in a row!`;
    }

  async function completeTask(taskId) {
    try {
        const response = await fetch(`/planner/tasks/${taskId}/status`, {
            method: 'PUT',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': rapidContainer.dataset.csrf },
            body: JSON.stringify({ status: 'done' })
        });
        if (response.ok) {
            completedCount++;
            streak++;
            currentIndex++;
            updateDisplay();
            // Reload the page to refresh all stats, event health, etc.
            setTimeout(() => location.reload(), 500);
        } else {
            alert('Failed to mark task as done');
        }
    } catch (error) {
        console.error('Rapid Fire error:', error);
        alert('Could not update task. Please refresh and try again.');
    }
}

    doneBtn?.addEventListener('click', () => {
        const taskId = doneBtn.getAttribute('data-task-id');
        completeTask(taskId);
    });
    skipBtn?.addEventListener('click', () => {
        if (currentIndex < tasks.length) { currentIndex++; updateDisplay(); }
    });
    remindBtn?.addEventListener('click', () => {
        alert('Reminder set for later. Will reappear tomorrow.');
        if (currentIndex < tasks.length) { currentIndex++; updateDisplay(); }
    });
}
</script>
@endpush
