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

                <a href="{{ route('planner.events.budget', $event->id) }}" class="action-btn-magic budget">
                    <i class="fas fa-robot"></i> Budget Workspace
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

/* ============================================
   PALETTE (matches the rest of the app)
   ============================================ */
.event-show-magic {
    --peach-cream: #EFE7DA;
    --coral-haze: #E19184;
    --calypso-berry: #C63E4E;
    --garden-green: #475B35;
    --vampire-hunter: #620607;
    --white: #FFFFFF;
    --gray: #8B7B72;
}

/* Hero Section */
.event-hero-magic {
    background: linear-gradient(135deg, var(--coral-haze) 0%, #d98476 100%);
    border-radius: 25px;
    padding: 50px;
    margin-bottom: 30px;
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    gap: 40px;
    color: white;
    position: relative;
    overflow: hidden;
}

/* Decorative circles, matching the dashboard's command-header */
.event-hero-magic::before {
    content: '';
    position: absolute;
    top: -80px;
    right: -80px;
    width: 250px;
    height: 250px;
    background: var(--calypso-berry);
    border-radius: 50%;
    opacity: 0.5;
}

.event-hero-magic::after {
    content: '';
    position: absolute;
    bottom: -100px;
    left: -60px;
    width: 200px;
    height: 200px;
    background: var(--peach-cream);
    border-radius: 50%;
    opacity: 0.3;
}

.hero-left {
    flex: 1;
    position: relative;
    z-index: 1;
}

.event-type-badge {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    padding: 8px 16px;
    background: var(--vampire-hunter);
    border-radius: 20px;
    font-size: 12px;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.05em;
    margin-bottom: 20px;
}

.event-title-magic {
    font-family: Georgia, 'Times New Roman', serif;
    font-size: 44px;
    font-weight: 700;
    margin: 0 0 10px 0;
    line-height: 1.2;
}

.event-subtitle {
    font-size: 16px;
    opacity: 0.95;
    margin: 0 0 30px 0;
    font-weight: 500;
}

.quick-actions-magic {
    display: flex;
    gap: 12px;
    flex-wrap: wrap;
}

.action-btn-magic {
    padding: 13px 24px;
    border: none;
    background: var(--white);
    color: var(--vampire-hunter);
    border-radius: 12px;
    font-weight: 700;
    font-size: 14px;
    cursor: pointer;
    transition: all 0.25s ease;
    text-decoration: none;
    display: inline-flex;
    align-items: center;
    gap: 9px;
    box-shadow: 0 4px 14px rgba(98, 6, 7, 0.12);
}

.action-btn-magic:hover {
    background: var(--vampire-hunter);
    color: white;
    transform: translateY(-3px);
    box-shadow: 0 8px 22px rgba(98, 6, 7, 0.28);
}

.hero-right {
    display: flex;
    flex-direction: column;
    gap: 16px;
    position: relative;
    z-index: 1;
}

/* Soft, borderless stat cards - matches the dashboard's mini-stat treatment */
.status-mega-card {
    background: var(--white);
    padding: 20px 30px;
    border-radius: 16px;
    display: flex;
    align-items: center;
    gap: 15px;
    min-width: 230px;
    box-shadow: 0 6px 20px rgba(98, 6, 7, 0.14);
}

.status-icon {
    width: 46px;
    height: 46px;
    background: linear-gradient(135deg, var(--coral-haze), var(--calypso-berry));
    color: white;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 20px;
    flex-shrink: 0;
}

.status-text {
    font-size: 17px;
    font-weight: 700;
    color: var(--vampire-hunter);
}

.budget-mega-card {
    background: var(--white);
    padding: 20px 30px;
    border-radius: 16px;
    text-align: center;
    box-shadow: 0 6px 20px rgba(98, 6, 7, 0.14);
}

.budget-label {
    font-size: 12px;
    text-transform: uppercase;
    letter-spacing: 0.04em;
    color: var(--gray);
    font-weight: 700;
    margin-bottom: 8px;
}

.budget-amount {
    font-family: Georgia, 'Times New Roman', serif;
    font-size: 30px;
    font-weight: 700;
    color: var(--calypso-berry);
}

/* Content Grid */
.event-content-grid {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 30px;
}

.info-card-magic {
    background: white;
    border-radius: 18px;
    overflow: hidden;
    box-shadow: 0 4px 18px rgba(98, 6, 7, 0.06);
    margin-bottom: 30px;
}

.card-header-magic {
    background: rgba(239, 231, 218, 0.35);
    padding: 20px 28px;
    display: flex;
    justify-content: space-between;
    align-items: center;
    border-bottom: 2px solid;
    border-image: linear-gradient(90deg, var(--coral-haze), var(--calypso-berry)) 1;
}

.card-header-magic h3 {
    font-size: 18px;
    font-weight: 700;
    color: var(--vampire-hunter);
    margin: 0;
    display: flex;
    align-items: center;
    gap: 10px;
}

.card-header-magic h3 i {
    color: var(--coral-haze);
}

.view-all-link {
    color: var(--coral-haze);
    text-decoration: none;
    font-weight: 700;
    font-size: 13px;
}

.card-body-magic {
    padding: 28px;
}

/* Client Profile */
.client-profile-section {
    display: flex;
    gap: 20px;
    align-items: center;
}

.client-avatar-large {
    width: 76px;
    height: 76px;
    background: linear-gradient(135deg, var(--coral-haze), var(--calypso-berry));
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 28px;
    font-weight: 700;
    color: white;
    flex-shrink: 0;
}

.client-details-magic h4 {
    font-family: Georgia, 'Times New Roman', serif;
    font-size: 21px;
    font-weight: 700;
    color: var(--vampire-hunter);
    margin: 0 0 8px 0;
}

.client-details-magic p {
    font-size: 14px;
    color: var(--gray);
    margin: 5px 0;
}

.client-rating {
    display: inline-flex;
    align-items: center;
    gap: 5px;
    margin-top: 10px;
    color: #F5A623;
    font-weight: 700;
    font-size: 14px;
}

/* Detail Rows */
.detail-row-magic {
    display: flex;
    justify-content: space-between;
    padding: 14px 0;
    border-bottom: 1px solid var(--peach-cream);
}

.detail-row-magic:last-child {
    border-bottom: none;
}

.detail-label {
    font-weight: 700;
    color: var(--garden-green);
    display: flex;
    align-items: center;
    gap: 8px;
    font-size: 14px;
}

.detail-value {
    color: var(--gray);
    font-weight: 600;
    font-size: 14px;
}

.description-section {
    margin-top: 18px;
    padding-top: 18px;
    border-top: 2px solid var(--peach-cream);
}

.description-section h4 {
    font-size: 15px;
    font-weight: 700;
    color: var(--garden-green);
    margin: 0 0 10px 0;
}

.description-section p {
    color: var(--gray);
    line-height: 1.6;
    font-size: 14px;
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
    background: var(--peach-cream);
}

.timeline-item:last-child::before {
    display: none;
}

.timeline-dot {
    width: 18px;
    height: 18px;
    background: var(--coral-haze);
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
    color: var(--vampire-hunter);
    font-size: 15px;
}

.timeline-content span {
    color: var(--gray);
    font-size: 13px;
}

/* Progress Circle */
.progress-circle-container {
    display: flex;
    justify-content: center;
    margin: 20px 0 30px;
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
    font-family: Georgia, 'Times New Roman', serif;
    font-size: 42px;
    font-weight: 700;
    color: var(--vampire-hunter);
}

.progress-label {
    display: block;
    font-size: 13px;
    color: var(--gray);
    font-weight: 600;
}

.progress-stats-grid {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 16px;
}

.progress-stat {
    text-align: center;
    padding: 14px;
    background: rgba(239, 231, 218, 0.4);
    border-radius: 12px;
}

.stat-number {
    font-family: Georgia, 'Times New Roman', serif;
    font-size: 28px;
    font-weight: 700;
    color: var(--calypso-berry);
    margin-bottom: 4px;
}

.stat-label {
    font-size: 12px;
    color: var(--gray);
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
    background: rgba(239, 231, 218, 0.4);
    border-radius: 12px;
    align-items: flex-start;
}

.task-checkbox {
    font-size: 18px;
    color: #B8ABA0;
}

.task-item-magic.done .task-checkbox {
    color: #7ED321;
}

.task-info {
    flex: 1;
}

.task-title {
    font-weight: 700;
    color: var(--vampire-hunter);
    margin-bottom: 8px;
    font-size: 14px;
}

.task-meta {
    display: flex;
    gap: 10px;
    flex-wrap: wrap;
}

.priority-badge {
    padding: 3px 10px;
    border-radius: 6px;
    font-size: 11px;
    font-weight: 700;
    text-transform: uppercase;
}

.priority-badge.low { background: #95A5A6; color: white; }
.priority-badge.medium { background: #F5A623; color: white; }
.priority-badge.high { background: #E74C3C; color: white; }
.priority-badge.urgent { background: var(--calypso-berry); color: white; }

.due-date {
    font-size: 12px;
    color: var(--gray);
}

/* Financial */
.financial-item {
    display: flex;
    justify-content: space-between;
    padding: 14px 0;
    border-bottom: 1px solid var(--peach-cream);
    font-size: 14px;
}

.financial-item:last-child {
    border-bottom: none;
}

.financial-item.total {
    padding-top: 18px;
    border-top: 2px solid var(--peach-cream);
    font-size: 17px;
}

.financial-item strong {
    color: var(--vampire-hunter);
}

.financial-item strong.success {
    color: #7ED321;
}

.empty-state-small {
    text-align: center;
    padding: 40px;
    color: var(--gray);
}

.empty-state-small i {
    font-size: 42px;
    margin-bottom: 15px;
    color: var(--coral-haze);
    opacity: 0.5;
}

@media (max-width: 1200px) {
    .event-content-grid {
        grid-template-columns: 1fr;
    }

    .event-hero-magic {
        flex-direction: column;
    }

    .hero-right {
        flex-direction: row;
        width: 100%;
    }

    .status-mega-card,
    .budget-mega-card {
        flex: 1;
    }
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