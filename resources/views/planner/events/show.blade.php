@extends('layouts.planner')

@section('title', $event->name)

@section('content')
<div class="event-show-magic">

    <!-- Epic Hero Section -->
    <div class="event-hero-magic">
        <div class="hero-left">
            <div class="event-type-badge">
                <i class="fas fa-calendar-star"></i>
                {{ $event->eventType->name ?? 'Event' }}
            </div>
            <h1 class="event-title-magic">{{ $event->name }}</h1>
            <p class="event-subtitle">{{ \Carbon\Carbon::parse($event->start_date)->format('l, F d, Y') }} at {{ \Carbon\Carbon::parse($event->start_time)->format('g:i A') }}</p>

            <div class="quick-actions-magic">
               
                <a href="{{ route('planner.messages') }}" class="action-btn-magic message">
                    <i class="fas fa-comments"></i> Message Client
                </a>
                <button class="action-btn-magic status" onclick="openStatusModal()">
                    <i class="fas fa-sync"></i> Update Status
                </button>
                <a href="/planner/events/{{ $event->id }}/vendors" class="action-btn-magic vendors">
    <i class="fas fa-store"></i> Vendors
</a>
            </div>
        </div>

        <div class="hero-right">
            <div class="status-mega-card {{ $event->status }}">
                <div class="status-icon">
                    @if($event->status === 'completed')
                        <i class="fas fa-check-circle"></i>
                    @elseif($event->status === 'in_progress')
                        <i class="fas fa-spinner fa-spin"></i>
                    @elseif($event->status === 'confirmed')
                        <i class="fas fa-calendar-check"></i>
                    @else
                        <i class="fas fa-clock"></i>
                    @endif
                </div>
                <span class="status-text">{{ ucfirst(str_replace('_', ' ', $event->status)) }}</span>
            </div>

            <div class="budget-mega-card">
                <div class="budget-label">Total Budget</div>
                <div class="budget-amount">${{ number_format($event->budget_overall ?? 0, 2) }}</div>
            </div>
        </div>
    </div>

    <!-- Main Content Grid -->
    <div class="event-content-grid">

        <!-- Left Column -->
        <div class="left-column">

            <!-- Client Information Card -->
            <div class="info-card-magic">
                <div class="card-header-magic">
                    <h3><i class="fas fa-user-circle"></i> Client Information</h3>
                </div>
                <div class="card-body-magic">
                    <div class="client-profile-section">
                        <div class="client-avatar-large">{{ substr($event->client->name ?? 'C', 0, 2) }}</div>
                        <div class="client-details-magic">
                            <h4>{{ $event->client->name ?? 'Client' }}</h4>
                            <p><i class="fas fa-envelope"></i> {{ $event->client->email ?? 'N/A' }}</p>
                            <p><i class="fas fa-phone"></i> {{ $event->client->phone ?? 'N/A' }}</p>
                            @if($event->client->rating_avg)
                            <div class="client-rating">
                                <i class="fas fa-star"></i>
                                <span>{{ number_format($event->client->rating_avg, 1) }} / 5.0</span>
                            </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <!-- Event Details Card -->
            <div class="info-card-magic">
                <div class="card-header-magic">
                    <h3><i class="fas fa-info-circle"></i> Event Details</h3>
                </div>
                <div class="card-body-magic">
                    <div class="detail-row-magic">
                        <div class="detail-label"><i class="fas fa-calendar"></i> Date</div>
                        <div class="detail-value">{{ \Carbon\Carbon::parse($event->start_date)->format('F d, Y') }}</div>
                    </div>
                    <div class="detail-row-magic">
                        <div class="detail-label"><i class="fas fa-clock"></i> Time</div>
                        <div class="detail-value">{{ \Carbon\Carbon::parse($event->start_time)->format('g:i A') }} - {{ \Carbon\Carbon::parse($event->end_time)->format('g:i A') }}</div>
                    </div>
                    <div class="detail-row-magic">
                        <div class="detail-label"><i class="fas fa-map-marker-alt"></i> Location</div>
                        <div class="detail-value">{{ $event->location_text ?? 'TBD' }}</div>
                    </div>
                    <div class="detail-row-magic">
                        <div class="detail-label"><i class="fas fa-users"></i> Guests</div>
                        <div class="detail-value">{{ $event->guest_estimate ?? 'TBD' }}</div>
                    </div>

                    @if($event->description)
                    <div class="description-section">
                        <h4>Description</h4>
                        <p>{{ $event->description }}</p>
                    </div>
                    @endif
                </div>
            </div>

            <!-- Timeline Card -->
            <div class="info-card-magic">
                <div class="card-header-magic">
                    <h3><i class="fas fa-history"></i> Timeline</h3>
                </div>
                <div class="card-body-magic">
                    <div class="timeline-magic">
                        <div class="timeline-item">
                            <div class="timeline-dot"></div>
                            <div class="timeline-content">
                                <strong>Event Created</strong>
                                <span>{{ $event->created_at->format('M d, Y g:i A') }}</span>
                            </div>
                        </div>
                        @if($event->completed_at)
                        <div class="timeline-item">
                            <div class="timeline-dot completed"></div>
                            <div class="timeline-content">
                                <strong>Event Completed</strong>
                                <span>{{ \Carbon\Carbon::parse($event->completed_at)->format('M d, Y g:i A') }}</span>
                            </div>
                        </div>
                        @endif
                    </div>
                </div>
            </div>

        </div>

        <!-- Right Column -->
        <div class="right-column">

            <!-- Progress Overview -->
            <div class="info-card-magic">
                <div class="card-header-magic">
                    <h3><i class="fas fa-chart-pie"></i> Progress Overview</h3>
                </div>
                <div class="card-body-magic">
                    @php
                        $tasks = $event->tasks ?? collect();
                        $totalTasks = $tasks->count();
                        $completedTasks = $tasks->where('status', 'done')->count();
                        $progressPercent = $totalTasks > 0 ? round(($completedTasks / $totalTasks) * 100) : 0;
                    @endphp

                    <div class="progress-circle-container">
                        <div class="progress-circle" data-progress="{{ $progressPercent }}">
                            <svg width="200" height="200">
                                <circle cx="100" cy="100" r="90" fill="none" stroke="#EFE7DA" stroke-width="12"/>
                                <circle cx="100" cy="100" r="90" fill="none" stroke="#E19184" stroke-width="12"
                                        stroke-dasharray="565.48"
                                        stroke-dashoffset="{{ 565.48 - (565.48 * $progressPercent / 100) }}"
                                        transform="rotate(-90 100 100)"/>
                            </svg>
                            <div class="progress-text">
                                <span class="progress-number">{{ $progressPercent }}%</span>
                                <span class="progress-label">Complete</span>
                            </div>
                        </div>
                    </div>

                    <div class="progress-stats-grid">
                        <div class="progress-stat">
                            <div class="stat-number">{{ $totalTasks }}</div>
                            <div class="stat-label">Total Tasks</div>
                        </div>
                        <div class="progress-stat">
                            <div class="stat-number">{{ $completedTasks }}</div>
                            <div class="stat-label">Completed</div>
                        </div>
                        <div class="progress-stat">
                            <div class="stat-number">{{ $totalTasks - $completedTasks }}</div>
                            <div class="stat-label">Remaining</div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Tasks List -->
            <div class="info-card-magic">
                <div class="card-header-magic">
                    <h3><i class="fas fa-tasks"></i> Event Tasks</h3>
                    <a href="{{ route('planner.tasks.index') }}" class="view-all-link">View All →</a>
                </div>
                <div class="card-body-magic">
                    @if($tasks->count() > 0)
                        <div class="tasks-list-magic">
                            @foreach($tasks->take(5) as $task)
                            <div class="task-item-magic {{ $task->status }}">
                                <div class="task-checkbox">
                                    <i class="fas fa-{{ $task->status === 'done' ? 'check-circle' : 'circle' }}"></i>
                                </div>
                                <div class="task-info">
                                    <div class="task-title">{{ $task->title }}</div>
                                    <div class="task-meta">
                                        <span class="priority-badge {{ $task->priority ?? 'medium' }}">
                                            {{ ucfirst($task->priority ?? 'medium') }}
                                        </span>
                                        @if($task->due_date)
                                        <span class="due-date">
                                            <i class="fas fa-clock"></i>
                                            {{ \Carbon\Carbon::parse($task->due_date)->format('M d') }}
                                        </span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                            @endforeach
                        </div>
                    @else
                        <div class="empty-state-small">
                            <i class="fas fa-tasks"></i>
                            <p>No tasks yet</p>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Financial Breakdown -->
            <div class="info-card-magic">
                <div class="card-header-magic">
                    <h3><i class="fas fa-dollar-sign"></i> Financial Breakdown</h3>
                </div>
                <div class="card-body-magic">
                    <div class="financial-item">
                        <span>Total Budget</span>
                        <strong>${{ number_format($event->budget_overall ?? 0, 2) }}</strong>
                    </div>
                    <div class="financial-item">
                        <span>Deposit Paid</span>
                        <strong class="success">$0.00</strong>
                    </div>
                    <div class="financial-item total">
                        <span>Balance Due</span>
                        <strong>${{ number_format($event->budget_overall ?? 0, 2) }}</strong>
                    </div>
                </div>
            </div>

        </div>

    </div>

</div>

<style>
.event-show-magic {
    padding: 30px;
}

/* Hero Section */
.event-hero-magic {
    background: linear-gradient(135deg, #475B35, #2C3821);
    border-radius: 25px;
    padding: 50px;
    margin-bottom: 30px;
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    gap: 40px;
    color: white;
}

.hero-left {
    flex: 1;
}

.event-type-badge {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    padding: 8px 16px;
    background: rgba(255, 255, 255, 0.2);
    border-radius: 20px;
    font-size: 14px;
    font-weight: 700;
    margin-bottom: 20px;
}

.event-title-magic {
    font-size: 48px;
    font-weight: 900;
    margin: 0 0 10px 0;
    line-height: 1.2;
}

.event-subtitle {
    font-size: 18px;
    opacity: 0.9;
    margin: 0 0 30px 0;
}

.quick-actions-magic {
    display: flex;
    gap: 15px;
    flex-wrap: wrap;
}

.action-btn-magic {
    padding: 14px 28px;
    border: 2px solid rgba(255, 255, 255, 0.3);
    background: rgba(255, 255, 255, 0.1);
    color: white;
    border-radius: 12px;
    font-weight: 700;
    cursor: pointer;
    transition: all 0.3s;
    text-decoration: none;
    display: inline-flex;
    align-items: center;
    gap: 10px;
}

.action-btn-magic:hover {
    background: white;
    color: #475B35;
    border-color: white;
    transform: translateY(-3px);
}

.hero-right {
    display: flex;
    flex-direction: column;
    gap: 20px;
}

.status-mega-card {
    background: rgba(255, 255, 255, 0.15);
    backdrop-filter: blur(10px);
    padding: 25px 35px;
    border-radius: 20px;
    display: flex;
    align-items: center;
    gap: 15px;
    min-width: 250px;
}

.status-icon {
    width: 50px;
    height: 50px;
    background: rgba(255, 255, 255, 0.2);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 24px;
}

.status-text {
    font-size: 20px;
    font-weight: 800;
}

.budget-mega-card {
    background: linear-gradient(135deg, #E19184, #C63E4E);
    padding: 25px 35px;
    border-radius: 20px;
    text-align: center;
}

.budget-label {
    font-size: 14px;
    opacity: 0.9;
    margin-bottom: 10px;
}

.budget-amount {
    font-size: 36px;
    font-weight: 900;
}

/* Content Grid */
.event-content-grid {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 30px;
}

.info-card-magic {
    background: white;
    border-radius: 20px;
    overflow: hidden;
    box-shadow: 0 5px 20px rgba(0, 0, 0, 0.08);
    margin-bottom: 30px;
}

.card-header-magic {
    background: linear-gradient(135deg, #EFE7DA, #F8F9FA);
    padding: 20px 30px;
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.card-header-magic h3 {
    font-size: 20px;
    font-weight: 900;
    color: #475B35;
    margin: 0;
    display: flex;
    align-items: center;
    gap: 10px;
}

.view-all-link {
    color: #E19184;
    text-decoration: none;
    font-weight: 700;
    font-size: 14px;
}

.card-body-magic {
    padding: 30px;
}

/* Client Profile */
.client-profile-section {
    display: flex;
    gap: 20px;
    align-items: center;
}

.client-avatar-large {
    width: 80px;
    height: 80px;
    background: linear-gradient(135deg, #475B35, #2C3821);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 32px;
    font-weight: 900;
    color: white;
    flex-shrink: 0;
}

.client-details-magic h4 {
    font-size: 24px;
    font-weight: 900;
    color: #475B35;
    margin: 0 0 10px 0;
}

.client-details-magic p {
    font-size: 14px;
    color: #7F8C8D;
    margin: 5px 0;
}

.client-rating {
    display: inline-flex;
    align-items: center;
    gap: 5px;
    margin-top: 10px;
    color: #F5A623;
    font-weight: 700;
}

/* Detail Rows */
.detail-row-magic {
    display: flex;
    justify-content: space-between;
    padding: 15px 0;
    border-bottom: 1px solid #EFE7DA;
}

.detail-row-magic:last-child {
    border-bottom: none;
}

.detail-label {
    font-weight: 700;
    color: #475B35;
    display: flex;
    align-items: center;
    gap: 8px;
}

.detail-value {
    color: #7F8C8D;
    font-weight: 600;
}

.description-section {
    margin-top: 20px;
    padding-top: 20px;
    border-top: 2px solid #EFE7DA;
}

.description-section h4 {
    font-size: 16px;
    font-weight: 800;
    color: #475B35;
    margin: 0 0 10px 0;
}

.description-section p {
    color: #7F8C8D;
    line-height: 1.6;
}

/* Timeline */
.timeline-magic {
    position: relative;
}

.timeline-item {
    display: flex;
    gap: 20px;
    margin-bottom: 25px;
    position: relative;
}

.timeline-item::before {
    content: '';
    position: absolute;
    left: 9px;
    top: 30px;
    bottom: -25px;
    width: 2px;
    background: #EFE7DA;
}

.timeline-item:last-child::before {
    display: none;
}

.timeline-dot {
    width: 20px;
    height: 20px;
    background: #E19184;
    border-radius: 50%;
    flex-shrink: 0;
    margin-top: 5px;
}

.timeline-dot.completed {
    background: #7ED321;
}

.timeline-content {
    display: flex;
    flex-direction: column;
    gap: 5px;
}

.timeline-content strong {
    color: #475B35;
    font-size: 15px;
}

.timeline-content span {
    color: #7F8C8D;
    font-size: 13px;
}

/* Progress Circle */
.progress-circle-container {
    display: flex;
    justify-content: center;
    margin: 30px 0;
}

.progress-circle {
    position: relative;
}

.progress-text {
    position: absolute;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    text-align: center;
}

.progress-number {
    display: block;
    font-size: 48px;
    font-weight: 900;
    color: #475B35;
}

.progress-label {
    display: block;
    font-size: 14px;
    color: #7F8C8D;
    font-weight: 600;
}

.progress-stats-grid {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 20px;
    margin-top: 30px;
}

.progress-stat {
    text-align: center;
    padding: 15px;
    background: #F8F9FA;
    border-radius: 12px;
}

.stat-number {
    font-size: 32px;
    font-weight: 900;
    color: #E19184;
    margin-bottom: 5px;
}

.stat-label {
    font-size: 13px;
    color: #7F8C8D;
    font-weight: 600;
}

/* Tasks List */
.tasks-list-magic {
    display: flex;
    flex-direction: column;
    gap: 12px;
}

.task-item-magic {
    display: flex;
    gap: 15px;
    padding: 15px;
    background: #F8F9FA;
    border-radius: 12px;
    align-items: flex-start;
}

.task-checkbox {
    font-size: 20px;
    color: #95A5A6;
}

.task-item-magic.done .task-checkbox {
    color: #7ED321;
}

.task-info {
    flex: 1;
}

.task-title {
    font-weight: 700;
    color: #475B35;
    margin-bottom: 8px;
}

.task-meta {
    display: flex;
    gap: 10px;
    flex-wrap: wrap;
}

.priority-badge {
    padding: 4px 10px;
    border-radius: 6px;
    font-size: 11px;
    font-weight: 700;
    text-transform: uppercase;
}

.priority-badge.low { background: #95A5A6; color: white; }
.priority-badge.medium { background: #F5A623; color: white; }
.priority-badge.high { background: #E74C3C; color: white; }
.priority-badge.urgent { background: #C63E4E; color: white; }

.due-date {
    font-size: 12px;
    color: #7F8C8D;
}

/* Financial */
.financial-item {
    display: flex;
    justify-content: space-between;
    padding: 15px 0;
    border-bottom: 1px solid #EFE7DA;
}

.financial-item:last-child {
    border-bottom: none;
}

.financial-item.total {
    padding-top: 20px;
    border-top: 2px solid #EFE7DA;
    font-size: 18px;
}

.financial-item strong {
    color: #475B35;
}

.financial-item strong.success {
    color: #7ED321;
}

.empty-state-small {
    text-align: center;
    padding: 40px;
    color: #95A5A6;
}

.empty-state-small i {
    font-size: 48px;
    margin-bottom: 15px;
}

@media (max-width: 1200px) {
    .event-content-grid {
        grid-template-columns: 1fr;
    }
}
// vendor button just for now
.action-btn-magic.vendors {
    background: rgba(225, 145, 132, 0.25);
    border-color: #E19184;
    color: white;
}

.action-btn-magic.vendors:hover {
    background: #E19184;
    border-color: #E19184;
    color: white;
}
</style>


<!-- Status Update Modal -->
<div id="statusModal" style="display: none; position: fixed; inset: 0; background: rgba(0,0,0,0.7); z-index: 10000; align-items: center; justify-content: center;">
    <div style="background: white; border-radius: 20px; padding: 40px; max-width: 500px; width: 90%;">
        <h3 style="font-size: 24px; font-weight: 900; color: #475B35; margin: 0 0 20px 0;">Update Event Status</h3>

        <form id="statusForm">
            <div style="margin-bottom: 20px;">
                <label style="display: block; font-weight: 700; color: #475B35; margin-bottom: 10px;">Select Status</label>
                <select id="statusSelect" style="width: 100%; padding: 14px; border: 2px solid #EFE7DA; border-radius: 12px; font-size: 15px;">
                    <option value="confirmed" {{ $event->status === 'confirmed' ? 'selected' : '' }}>Confirmed</option>
                    <option value="in_progress" {{ $event->status === 'in_progress' ? 'selected' : '' }}>In Progress</option>
                    <option value="completed" {{ $event->status === 'completed' ? 'selected' : '' }}>Completed</option>
                    <option value="cancelled" {{ $event->status === 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                </select>
            </div>

            <div style="display: flex; gap: 15px; justify-content: flex-end;">
                <button type="button" onclick="closeStatusModal()" style="padding: 14px 28px; background: #EFE7DA; color: #475B35; border: none; border-radius: 12px; font-weight: 700; cursor: pointer;">
                    Cancel
                </button>
                <button type="submit" style="padding: 14px 28px; background: linear-gradient(135deg, #475B35, #2C3821); color: white; border: none; border-radius: 12px; font-weight: 700; cursor: pointer;">
                    Update Status
                </button>
            </div>
        </form>
    </div>
</div>

<script>
function openStatusModal() {
    document.getElementById('statusModal').style.display = 'flex';
}

function closeStatusModal() {
    document.getElementById('statusModal').style.display = 'none';
}

document.getElementById('statusForm').addEventListener('submit', async function(e) {
    e.preventDefault();

    const status = document.getElementById('statusSelect').value;

    try {
        const response = await fetch('/planner/events/{{ $event->id }}/status', {
            method: 'PUT',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            },
            body: JSON.stringify({ status })
        });

        if (response.ok) {
            alert('Status updated successfully!');
            location.reload();
        } else {
            alert('Failed to update status');
        }
    } catch (error) {
        console.error('Error:', error);
        alert('Failed to update status');
    }
});
</script>
@endsection
