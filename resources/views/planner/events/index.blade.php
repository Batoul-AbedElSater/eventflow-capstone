@extends('layouts.planner')

@section('title', 'My Events')

@section('content')
<div class="planner-events-page">

    <div class="events-header-supreme">
        <div class="header-content-left">
            <div class="header-text">
                <h1>Event Command Center</h1>
            </div>
        </div>

        <div class="header-stats-mini">
            <div class="stat-mini-card confirmed">
                <div class="stat-mini-content">
                    <span class="stat-mini-number">{{ $stats['confirmed'] }}</span>
                    <span class="stat-mini-label">Confirmed</span>
                </div>
            </div>

            <div class="stat-mini-card in-progress">
                <div class="stat-mini-content">
                    <span class="stat-mini-number">{{ $stats['in_progress'] }}</span>
                    <span class="stat-mini-label">In Progress</span>
                </div>
            </div>

            <div class="stat-mini-card revenue">
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
                <i class="fas fa-columns"></i> Events
            </button>
            <button class="view-toggle-btn" data-view="calendar">
                <i class="fas fa-calendar"></i> Calendar
            </button>
        </div>

        <div class="filter-controls">
            <div class="search-box-luxury">
                <i class="fas fa-search"></i>
                <input type="text" id="eventSearch" placeholder="Search events...">
            </div>

            {{--
                FIX: filter by event_type_id (a real column on events) instead of
                a derived slug/name string. This is immune to typos, casing, and
                renamed event types — the id never changes. The kanban-card
                partial must render data-type="{{ $event->event_type_id }}" to match.
            --}}
            <select class="filter-select-luxury" id="filterType">
                <option value="">All Types</option>
                @foreach($events->pluck('eventType')->filter()->unique('id')->sortBy('name') as $type)
                    <option value="{{ $type->id }}">{{ $type->name }}</option>
                @endforeach
            </select>
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

</div>
@endsection

@push('styles')
<link rel="stylesheet" href="{{ asset('css/planner-events.css') }}">
@endpush

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.0/Sortable.min.js"></script>
<script src="{{ asset('js/planner-events.js') }}"></script>

{{-- your custom functional calendar script goes AFTER planner-events.js --}}
<script>
document.addEventListener('DOMContentLoaded', function () {
    const calendarGrid = document.getElementById('calendarGrid');
    const currentMonthTitle = document.getElementById('currentMonth');
    const prevMonthBtn = document.getElementById('prevMonth');
    const nextMonthBtn = document.getElementById('nextMonth');
    const calendarButton = document.querySelector('[data-view="calendar"]');

    if (!calendarGrid || !currentMonthTitle || !prevMonthBtn || !nextMonthBtn) return;

    const calendarUrl = @json(route('planner.monthly-calendar.index'));
    let activeDate = new Date();

    function monthParam(date) {
        return `${date.getFullYear()}-${String(date.getMonth() + 1).padStart(2, '0')}`;
    }

    function statusClass(status) {
        return String(status || 'default').toLowerCase().replaceAll('-', '_').replaceAll(' ', '_');
    }

    async function loadCalendar() {
        calendarGrid.innerHTML = '<div class="calendar-loading">Loading calendar...</div>';

        try {
            const response = await fetch(`${calendarUrl}?month=${monthParam(activeDate)}`, {
                headers: {
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                }
            });

            const result = await response.json();

            if (!response.ok || !result.success) {
                throw new Error(result.message || 'Calendar could not load');
            }

            renderCalendar(result.data);
        } catch (error) {
            console.error(error);
            calendarGrid.innerHTML = '<div class="calendar-error">Calendar could not load</div>';
        }
    }

    function renderCalendar(data) {
        currentMonthTitle.textContent = `${data.month_name} ${data.year}`;

        const weekDays = ['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'];

        calendarGrid.innerHTML = `
            ${weekDays.map(day => `<div class="calendar-weekday">${day}</div>`).join('')}
            ${data.calendar_days.map(day => {
                const level = Math.min(day.events_count, 4);

                const events = (day.events || []).map(event => `
                    <a class="calendar-event-chip ${statusClass(event.status)}"
                       href="${event.url}"
                       title="${event.full_name}"
                       style="--event-color:${event.color}">
                        <span class="calendar-event-chip-dot"></span>
                        <span class="calendar-event-chip-text">${event.name}</span>
                    </a>
                `).join('');

                return `
                    <div class="calendar-day-luxury level-${level}
                                ${day.is_today ? 'is-today' : ''}
                                ${day.is_current_month ? '' : 'is-outside-month'}">
                        <div class="calendar-day-number">${Number(day.day_number)}</div>

                        <div class="calendar-day-events-list">
                            ${events}
                            ${day.more_count > 0 ? `<span class="calendar-more-count">+${day.more_count} more</span>` : ''}
                        </div>
                    </div>
                `;
            }).join('')}
        `;
    }

    prevMonthBtn.addEventListener('click', function () {
        activeDate.setMonth(activeDate.getMonth() - 1);
        loadCalendar();
    });

    nextMonthBtn.addEventListener('click', function () {
        activeDate.setMonth(activeDate.getMonth() + 1);
        loadCalendar();
    });

    calendarButton?.addEventListener('click', function () {
        setTimeout(loadCalendar, 50);
    });

    setTimeout(loadCalendar, 100);
});
</script>
@endpush
