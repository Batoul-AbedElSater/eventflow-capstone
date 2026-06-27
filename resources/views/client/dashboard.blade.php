@extends('layouts.client')

@section('title', 'Dashboard')

@section('content')
<div class="client-dashboard-luxury">
    <!-- NEW HERO SECTION – Elegant, no buttons, palette colors -->
    <div class="hero-elegant">
        <div class="hero-backdrop"></div>
        <div class="hero-container">
            <div class="hero-badge">
                <span class="badge-glow"><i class="fas fa-wand-magic-sparkles"></i> Your Journey</span>
            </div>
            <h1 class="hero-title">Plan
             <span class="italic-text"> Extraordinary </span>  Moments, Effortlessly </h1>
            <p class="hero-desc">
                Because the best memories are the ones
                you didn't have to worry about.<br>

            </p>
            <div class="hero-stats">
                <div class="stat-item">
                    <i class="fas fa-calendar-check"></i>
                    <span>500+ events planned</span>
                </div>
                <div class="stat-item">
                    <i class="fas fa-smile"></i>
                    <span>98% happy clients</span>
                </div>
            </div>
        </div>
        <div class="hero-floating-shapes">
            <div class="shape shape-1"></div>
            <div class="shape shape-2"></div>
            <div class="shape shape-3"></div>
        </div>
    </div>

    {{-- MY EVENTS SECTION - LUXURY CARDS WITH PHOTOS (exactly as you had) --}}
    <div class="my-events-section">
        <div class="section-header-luxury">
            <div class="section-title-wrapper">
                <div>
                    <h2>My Events</h2>
                    <p>Your upcoming celebrations</p>
                </div>
            </div>
            <a href="{{ route('client.events.index') }}" class="btn-view-all">
                View All Events <i class="fas fa-arrow-right"></i>
            </a>
        </div>

        @if($events->count() > 0)
            <div class="events-grid-luxury">
                @foreach($events as $event)
                    @php
                        $displayStatus = match($event->status) {
                            'draft' => 'pending',
                            'pending' => 'pending',
                            'confirmed' => 'confirmed',
                            'in_progress' => 'confirmed',
                            'completed' => 'confirmed',
                            'declined' => 'declined',
                            default => 'pending'
                        };
                    @endphp
              <div class="event-card-luxury {{ $displayStatus === 'pending' ? 'is-pending' : '' }} {{ $displayStatus === 'declined' ? 'is-declined' : '' }}" data-status="{{ $displayStatus }}">
                        {{-- Event Photo --}}
                        <div class="event-photo-container">
                            @if($event->event_photo)
                                <img src="{{ asset('storage/' . $event->event_photo) }}" alt="{{ $event->name }}" class="event-photo">
                            @else
                                <div class="event-photo-placeholder">
                                    <div class="photo-gradient {{ $event->eventType->name }}">
                                        <div class="photo-icon">
                                            @if($event->eventType->name === 'Wedding')
                                                💒
                                            @elseif($event->eventType->name === 'Birthday')
                                                🎂
                                            @elseif($event->eventType->name === 'Corporate')
                                                💼
                                            @elseif($event->eventType->name === 'Anniversary')
                                                💝
                                            @else
                                                🎉
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            @endif

                            {{-- Status Badge on Photo --}}
                        <div class="event-status-badge-photo {{ $displayStatus }}">
                            @if($displayStatus === 'pending')
                                <i class="fas fa-clock"></i> Pending
                            @elseif($displayStatus === 'confirmed')
                                <i class="fas fa-check-circle"></i> Confirmed
                                @if($event->status !== 'confirmed')
                                    <div class="sub-status-badge {{ $event->status }}">
                                        {{ ucfirst(str_replace('_', ' ', $event->status)) }}
                                    </div>
                                @endif
                            @elseif($displayStatus === 'declined')
                                <i class="fas fa-times-circle"></i> Declined
                            @endif
                        </div>
                        </div>

                        {{-- Lock Overlay for Pending --}}
                        @if($displayStatus === 'pending')
                            <div class="event-lock-overlay pending-overlay">
                                <div class="lock-backdrop"></div>
                                <div class="lock-content">
                                    <div class="lock-icon-luxury">
                                        <i class="fas fa-hourglass-half"></i>
                                    </div>
                                    <h4>Awaiting Response</h4>
                                    <p>Planner is reviewing your request</p>
                                    <div class="lock-actions">
                                        <form method="POST" action="{{ route('client.events.destroy', $event->id) }}">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn-lock-delete" onclick="return confirm('Cancel this request?')">
                                                <i class="fas fa-times"></i> Cancel Request
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        @endif

                        {{-- Lock Overlay for Declined --}}
                        @if($displayStatus === 'declined')
                            <div class="event-lock-overlay declined-overlay">
                                <div class="lock-backdrop"></div>
                                <div class="lock-content">
                                    <div class="lock-icon-luxury declined">
                                        <i class="fas fa-ban"></i>
                                    </div>
                                    <h4>Request Declined</h4>
                                    <p>Unfortunately, your request was declined</p>
                                    <div class="lock-actions">
                                        <form method="POST" action="{{ route('client.events.destroy', $event->id) }}">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn-lock-remove" onclick="return confirm('Remove this event?')">
                                                <i class="fas fa-trash"></i> Remove Event
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        @endif

                        {{-- Event Card Content --}}
                        <div class="event-card-body">
                            <div class="event-header-luxury">
                                <div class="event-type-icon">
                                    <i class="fas fa-tag"></i>
                                </div>
                                <span class="event-type-label">{{ $event->eventType->name }}</span>
                            </div>

                            <h3 class="event-title-luxury">{{ $event->name }}</h3>

                            <div class="event-details-luxury">
                                <div class="event-detail-row">
                                    <div class="detail-icon-wrapper">
                                        <i class="fas fa-calendar-day"></i>
                                    </div>
                                    <div class="detail-text">
                                        <span class="detail-label">Date</span>
                                        <strong>{{ $event->start_date->format('M d, Y') }}</strong>
                                    </div>
                                </div>

                                <div class="event-detail-row">
                                    <div class="detail-icon-wrapper">
                                        <i class="fas fa-map-marker-alt"></i>
                                    </div>
                                    <div class="detail-text">
                                        <span class="detail-label">Location</span>
                                        <strong>{{ Str::limit($event->location_text, 30) }}</strong>
                                    </div>
                                </div>

                                <div class="event-detail-row">
                                    <div class="detail-icon-wrapper">
                                        <i class="fas fa-users"></i>
                                    </div>
                                    <div class="detail-text">
                                        <span class="detail-label">Guests</span>
                                        <strong>{{ $event->guest_estimate }} people</strong>
                                    </div>
                                </div>

                                <div class="event-detail-row">
                                    <div class="detail-icon-wrapper">
                                        <i class="fas fa-coins"></i>
                                    </div>
                                    <div class="detail-text">
                                        <span class="detail-label">Budget</span>
                                        <strong>{{ number_format($event->budget_overall, 0) }} Dollars</strong>
                                    </div>
                                </div>
                            </div>

                            {{-- Card Actions --}}
                            @if($displayStatus === 'confirmed')
                                <div class="event-card-actions-luxury">
                                    <a href="{{ route('client.events.show', $event->id) }}" class="btn-card-action primary">
                                        <i class="fas fa-eye"></i> View Details
                                    </a>
                                    <a href="{{ route('client.events.edit', $event->id) }}" class="btn-card-action secondary">
                                        <i class="fas fa-edit"></i> Edit
                                    </a>
                                    <form method="POST" action="{{ route('client.events.destroy', $event->id) }}" style="display: inline;">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn-card-action danger" onclick="return confirm('Delete this event?')">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <div class="empty-state-luxury">
                <div class="empty-icon">
                    <i class="fas fa-calendar-plus"></i>
                </div>
                <h3>No Events Yet</h3>
                <p>Start planning your first event and make it unforgettable!</p>
                <a href="{{ route('client.events.create') }}" class="btn-primary-gradient">
                    <i class="fas fa-plus-circle"></i> Create Your First Event
                </a>
            </div>
        @endif
    </div>

</div>

<style>
/* ===== NEW HERO STYLES ===== */

:root {
     --coral: #E19184;
        --berry: #C63E4E;
        --vampire: #620607;
        --cream: #EFE7DA;
        --white: #FFFFFF;
        --amnesiac: #F5F9E5;
        --green: #475B35;
        --green-dark: #2C3821;
}
 .hero-elegant {
    position: relative;
    background: var(--berry);
    border-radius: 40px;
    padding: 80px 60px;
    margin-bottom: 40px;
    overflow: hidden;
    isolation: isolate;
    box-shadow: 0 15px 35px rgba(225,145,132,0.3);
      display: flex;
    align-items: center;
}

.hero-container {
    max-width:1050px;
    margin: 0 auto;
     text-align: left;
}
.hero-badge {
    margin-bottom: 25px;
}
.badge-glow {
    padding: 8px 24px;
    font-size: 26px;
    font-weight: 600;
    color: var(--cream);
    letter-spacing: 1.8px;
    display: inline-flex;
    align-items: center;
    gap: 8px;
    text-transform: uppercase;
    font-weight: 800;

}
.hero-title {
    font-size: 68px;
    font-weight: 900;
    line-height: 1.2;
    color: white;
    margin-bottom: 25px;
    text-shadow: 0 6px 30px rgba(0,0,0,0.2);
 font-family: 'DM Serif Display', serif;
}
.italic-text { color:white;
    font-style: italic;
 }
.berry-text { color: white; }
.hero-desc {
    font-size: 27px;
    color:var(--cream);
    margin-bottom: 35px;
    line-height: 1.6;
}
.stat-item {
    display: flex;
    gap: 12px;
    background: rgba(255, 255, 255, 0.18);
    backdrop-filter: blur(12px);
    padding: 14px 30px;
    border-radius: 50px;
    color: var(--white);
    font-weight: 600;
    font-size: 15px;

}
.stat-item i {
    font-size: 22px;
    color:var(--white);
}

.hero-stats {
    display: flex;
    gap: 40px;
}

.hero-floating-shapes {
    position: absolute;
    inset: 0;
    pointer-events: none;
}
.shape {
    position: absolute;
    background: rgba(255,255,255,0.15);
    border-radius: 50%;
    filter: blur(60px);
    animation: floatShape 15s infinite alternate;
}
.shape-1 { width: 300px; height: 300px; top: -100px; left: -100px; background: #EFE7DA; opacity: 0.3; }
.shape-2 { width: 200px; height: 200px; bottom: -80px; right: -50px; background: #E19184; opacity: 0.25; }
.shape-3 { width: 250px; height: 250px; top: 30%; right: 20%; background: #C63E4E; opacity: 0.2; }
@keyframes floatShape {
    0% { transform: translate(0, 0) scale(1); }
    100% { transform: translate(30px, -30px) scale(1.2); }
}
@media (max-width: 768px) {
    .hero-title { font-size: 40px; }
    .hero-stats { flex-direction: column; align-items: center; gap: 15px; }
    .hero-elegant { padding: 50px 30px; }
}
/* ===== YOUR ORIGINAL EVENT CARD STYLES (keep everything below – already present) ===== */
.my-events-section {
    margin-top: 40px;
}
.events-grid-luxury {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(380px, 1fr));
    gap: 30px;
}
.event-card-luxury {
    background: white;
    border-radius: 25px;
    overflow: hidden;
    box-shadow: 0 10px 30px rgba(0,0,0,0.08);
    transition: all 0.3s;
    position: relative;
}
.event-card-luxury:hover {
    transform: translateY(-5px);
    box-shadow: 0 15px 40px rgba(0,0,0,0.15);
}
.event-photo-container {
    position: relative;
    height: 240px;
    overflow: hidden;
}
.event-photo {
    width: 100%;
    height: 100%;
    object-fit: cover;
    transition: transform 0.4s;
}
.event-card-luxury:hover .event-photo {
    transform: scale(1.05);
}
.event-photo-placeholder {
    width: 100%;
    height: 100%;
}
.photo-gradient {
    width: 100%;
    height: 100%;
    display: flex;
    align-items: center;
    justify-content: center;
}
.photo-gradient.Wedding { background: linear-gradient(135deg, #E19184, #C63E4E); }
.photo-gradient.Birthday { background: linear-gradient(135deg, #FFB6C1, #E19184); }
.photo-gradient.Corporate { background: linear-gradient(135deg, #475B35, #2C3821); }
.photo-gradient.Anniversary { background: linear-gradient(135deg, #C63E4E, #620607); }
.photo-icon {
    font-size: 80px;
    filter: drop-shadow(0 4px 10px rgba(0,0,0,0.2));
}
.event-status-badge-photo {
    position: absolute;
    top: 15px;
    right: 15px;
    padding: 8px 18px;
    border-radius: 50px;
    font-size: 13px;
    font-weight: 900;
    backdrop-filter: blur(10px);
    color: white;
    z-index: 2;
}
.event-status-badge-photo.pending { background: #F5A623; }
.event-status-badge-photo.confirmed { background: #7ED321; }
.event-status-badge-photo.declined { background: #D0021B; }
.event-status-badge-photo i { margin-right: 6px; }

.event-lock-overlay {
    position: absolute;
    inset: 0;
    background: rgba(0,0,0,0.75);
    backdrop-filter: blur(5px);
    display: flex;
    align-items: center;
    justify-content: center;
    z-index: 10;
}
.pending-overlay .lock-content,
.declined-overlay .lock-content {
    text-align: center;
    padding: 30px;
    color: white;
}
.lock-icon-luxury {
    font-size: 60px;
    margin-bottom: 15px;
}
.lock-icon-luxury.declined { color: #D0021B; }
.lock-icon-luxury i { color: white; }
.lock-content h4 {
    font-size: 24px;
    margin: 10px 0;
}
.lock-content p {
    margin-bottom: 20px;
    opacity: 0.9;
}
.btn-lock-delete, .btn-lock-remove {
    background: #D0021B;
    color: white;
    border: none;
    padding: 12px 24px;
    border-radius: 12px;
    font-weight: 700;
    cursor: pointer;
    transition: all 0.3s;
}
.btn-lock-delete:hover, .btn-lock-remove:hover {
    background: #a00116;
    transform: scale(1.05);
}

.event-card-body {
    padding: 25px;
}
.event-header-luxury {
    display: flex;
    align-items: center;
    gap: 8px;
    margin-bottom: 12px;
}
.event-type-icon {
    width: 32px;
    height: 32px;
    background: #EFE7DA;
    border-radius: 10px;
    display: flex;
    align-items: center;
    justify-content: center;
    color: #C63E4E;
}
.event-type-label {
    font-size: 14px;
    font-weight: 800;
    color: #C63E4E;
    letter-spacing: 0.5px;
}
.event-title-luxury {
    font-size: 22px;
    font-weight: 900;
    color: #475B35;
    margin: 0 0 20px 0;
}
.event-details-luxury {
    display: flex;
    flex-direction: column;
    gap: 12px;
    margin-bottom: 25px;
}
.event-detail-row {
    display: flex;
    align-items: center;
    gap: 15px;
}
.detail-icon-wrapper {
    width: 45px;
    height: 45px;
    background: #EFE7DA;
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    color: #E19184;
    font-size: 18px;
}
.detail-text {
    flex: 1;
}
.detail-label {
    font-size: 11px;
    color: #95A5A6;
    text-transform: uppercase;
    display: block;
    letter-spacing: 0.5px;
}
.detail-text strong {
    font-size: 16px;
    color: #475B35;
}
.event-card-actions-luxury {
    display: flex;
    gap: 12px;
    margin-top: 10px;
}
.btn-card-action {
    padding: 12px 20px;
    border-radius: 14px;
    font-weight: 800;
    text-decoration: none;
    display: inline-flex;
    align-items: center;
    gap: 8px;
    transition: all 0.3s;
    cursor: pointer;
    border: none;
}
.btn-card-action.primary {
    background: linear-gradient(135deg, #E19184, #C63E4E);
    color: white;
    flex: 1;
    justify-content: center;
}
.btn-card-action.primary:hover {
    transform: translateY(-3px);
    box-shadow: 0 8px 20px rgba(225,145,132,0.4);
}
.btn-card-action.secondary {
    background: #EFE7DA;
    color: #475B35;
}
.btn-card-action.secondary:hover {
    background: #E19184;
    color: white;
}
.btn-card-action.danger {
    background: #D0021B;
    color: white;
    border: none;
}
.btn-card-action.danger:hover {
    background: #a00116;
}

.section-header-luxury {
    background: white !important;
    border: 3px solid var(--berry, #C63E4E) !important;
    border-radius: 40px;
    padding: 20px 30px !important;
    margin-bottom: 25px !important;
    display: flex !important;
    justify-content: space-between !important;
    align-items: center !important;
    box-shadow: 0 2px 8px rgba(0,0,0,0.04) !important;
}
.section-title-wrapper {
    display: flex;
    align-items: center;
    gap: 15px;
}

.section-title-wrapper h2 {
    font-size: 28px;
    font-weight: 700;
    color: var(--vampire);
    margin: 0;
}
.section-title-wrapper p {
    font-size: 15px;
    color: var(--green);
    margin: 0;
}
.btn-view-all {
    padding: 12px 24px;
    background:var(--cream);
    color: var(--green);
    border-radius: 12px;
    font-weight: 700;
    text-decoration: none;
    transition: all 0.3s;
    display: flex;
    align-items: center;
    gap: 8px;
}
.btn-view-all:hover {
    background: #E19184;
    color: white;
}
.empty-state-luxury {
    text-align: center;
    padding: 80px 20px;
    background: #F8F9FA;
    border-radius: 25px;
}
.empty-icon {
    width: 100px;
    height: 100px;
    background: linear-gradient(135deg, #EFE7DA, #E19184);
    border-radius: 50%;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    font-size: 48px;
    color: white;
    margin-bottom: 25px;
}
.empty-state-luxury h3 {
    font-size: 28px;
    color: #475B35;
    margin-bottom: 10px;
}
.empty-state-luxury p {
    font-size: 16px;
    color: #7F8C8D;
    margin-bottom: 30px;
}
.btn-primary-gradient {
    padding: 14px 28px;
    background: linear-gradient(135deg, #E19184, #C63E4E);
    color: white;
    border-radius: 15px;
    font-weight: 700;
    text-decoration: none;
    display: inline-flex;
    align-items: center;
    gap: 10px;
    transition: all 0.3s;
}
.btn-primary-gradient:hover {
    transform: translateY(-3px);
    box-shadow: 0 8px 20px rgba(225,145,132,0.4);
}

/* Sub-status badge inside confirmed badge */
.event-status-badge-photo.confirmed {
    display: flex;
    flex-direction: column;
    align-items: center;
    min-width: 110px;
    padding: 8px 18px;
}
.sub-status-badge {
    margin-top: 6px;
    font-size: 10px;
    font-weight: 600;
    padding: 3px 8px;
    border-radius: 20px;
    background: rgba(0,0,0,0.6);
    color: white;
    text-transform: capitalize;
}
.sub-status-badge.in_progress {
    background: #f39c12;
}
.sub-status-badge.completed {
    background: #3498db;
}
.sub-status-badge.confirmed {
    background: #27ae60;
}

/* Hero section from old file – removed, replaced by .hero-elegant above */
</style>
@endsection
