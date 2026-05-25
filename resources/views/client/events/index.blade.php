@extends('layouts.client')

@section('title', 'My Events')

@section('content')
<div class="events-paradise">
    
    {{-- Ultra Creative Header --}}
    <div class="paradise-header">
        <div class="header-art">
            <div class="art-blob blob-1"></div>
            <div class="art-blob blob-2"></div>
        </div>
        <div class="header-content">
            <div class="header-badge">
                <i class="fas fa-sparkles"></i>
                <span>Your Celebration Journey</span>
            </div>
            <h1 class="paradise-title">
                My <span class="coral-text">Dream</span> Events
            </h1>
            <p class="paradise-subtitle">Every moment, perfectly planned</p>
        </div>
        <a href="{{ route('client.events.create') }}" class="btn-create-magic">
            <i class="fas fa-wand-magic-sparkles"></i>
            <span>Create New Event</span>
        </a>
    </div>

    {{-- Creative Filter Pills --}}
    <div class="filter-paradise">
        <button class="filter-pill active" data-status="all">
            <i class="fas fa-infinity"></i> All Events
        </button>
        <button class="filter-pill" data-status="confirmed">
            <i class="fas fa-circle-check"></i> Confirmed
        </button>
        <button class="filter-pill" data-status="pending">
            <i class="fas fa-hourglass-half"></i> Awaiting
        </button>
        <button class="filter-pill" data-status="declined">
            <i class="fas fa-ban"></i> Declined
        </button>
    </div>

    {{-- Revolutionary Event Cards --}}
    @if($events->count() > 0)
        <div class="events-masonry" id="eventsGrid">
            @foreach($events as $event)
                @php
                    // Map planner statuses to client display statuses
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
                <div class="event-masterpiece" data-status="{{ $displayStatus }}">
                    
                    {{-- Photo Section with Overlay --}}
                    <div class="event-visual">
                        @if($event->event_photo)
                            <img src="{{ asset('storage/' . $event->event_photo) }}" alt="{{ $event->name }}" class="event-image">
                        @else
                            <div class="event-placeholder">
                                <div class="placeholder-gradient {{ $event->eventType->name }}">
                                    <div class="placeholder-pattern"></div>
                                    <div class="placeholder-icon">
                                        @if($event->eventType->name === 'Wedding')
                                            <i class="fas fa-rings-wedding"></i>
                                        @elseif($event->eventType->name === 'Birthday')
                                            <i class="fas fa-cake-candles"></i>
                                        @elseif($event->eventType->name === 'Corporate')
                                            <i class="fas fa-building"></i>
                                        @elseif($event->eventType->name === 'Anniversary')
                                            <i class="fas fa-heart"></i>
                                        @else
                                            <i class="fas fa-party-horn"></i>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @endif
                        
                        {{-- Floating Status Badge (uses displayStatus) --}}
                        <div class="status-float {{ $displayStatus }}">
                            @if($displayStatus === 'pending')
                                <i class="fas fa-clock"></i>
                                <span>Awaiting</span>
                            @elseif($displayStatus === 'declined')
                                <i class="fas fa-times-circle"></i>
                                <span>Declined</span>
                            @elseif($displayStatus === 'confirmed')
                                <i class="fas fa-check-circle"></i>
                                <span>Confirmed</span>
                                {{-- Sub‑badge for real planner status --}}
                                <div class="sub-status-badge {{ $event->status }}">
                                    @if($event->status === 'confirmed')
                                        Confirmed
                                    @elseif($event->status === 'in_progress')
                                        In Progress
                                    @elseif($event->status === 'completed')
                                        Completed
                                    @endif
                                </div>
                            @endif
                        </div>

                        {{-- Event Type Tag --}}
                        <div class="event-type-tag">
                            <i class="fas fa-tag"></i>
                            {{ $event->eventType->name }}
                        </div>
                    </div>

                    {{-- Content Section --}}
                    <div class="event-essence">
                        <h3 class="event-name">{{ $event->name }}</h3>
                        
                        <div class="event-meta">
                            <div class="meta-item">
                                <div class="meta-icon">
                                    <i class="fas fa-calendar-days"></i>
                                </div>
                                <div class="meta-info">
                                    <span class="meta-label">Date</span>
                                    <strong>{{ $event->start_date->format('M d, Y') }}</strong>
                                </div>
                            </div>

                            <div class="meta-item">
                                <div class="meta-icon">
                                    <i class="fas fa-location-dot"></i>
                                </div>
                                <div class="meta-info">
                                    <span class="meta-label">Venue</span>
                                    <strong>{{ Str::limit($event->location_text, 25) }}</strong>
                                </div>
                            </div>

                            <div class="meta-grid">
                                <div class="meta-small">
                                    <i class="fas fa-users"></i>
                                    <span>{{ $event->guest_estimate }} Guests</span>
                                </div>
                                <div class="meta-small">
                                    <i class="fas fa-wallet"></i>
                                    <span>{{ number_format($event->budget_overall, 0) }} SAR</span>
                                </div>
                            </div>
                        </div>

                        {{-- Action Buttons Based on Display Status --}}
                        @if($displayStatus === 'pending')
                            <div class="event-locked">
                                <div class="lock-message">
                                    <i class="fas fa-hourglass-half"></i>
                                    <p>Planner is reviewing your request...</p>
                                </div>
                                <form method="POST" action="{{ route('client.events.destroy', $event->id) }}">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn-cancel" onclick="return confirm('Cancel this request?')">
                                        <i class="fas fa-xmark"></i> Cancel Request
                                    </button>
                                </form>
                            </div>
                        @elseif($displayStatus === 'declined')
                            <div class="event-declined">
                                <div class="declined-message">
                                    <i class="fas fa-ban"></i>
                                    <p>Request was declined</p>
                                </div>
                                <form method="POST" action="{{ route('client.events.destroy', $event->id) }}">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn-remove" onclick="return confirm('Remove this event?')">
                                        <i class="fas fa-trash"></i> Remove
                                    </button>
                                </form>
                            </div>
                        @else
                            {{-- For confirmed events (including in_progress and completed) --}}
                            <div class="event-actions">
                                <a href="{{ route('client.events.show', $event->id) }}" class="btn-enter">
                                    <i class="fas fa-arrow-right-to-bracket"></i>
                                    <span>Enter Event</span>
                                </a>
                                <div class="action-more">
                                    <a href="{{ route('client.events.edit', $event->id) }}" class="btn-icon" title="Edit">
                                        <i class="fas fa-pen"></i>
                                    </a>
                                    <form method="POST" action="{{ route('client.events.destroy', $event->id) }}" style="display: inline;">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn-icon danger" onclick="return confirm('Delete event?')" title="Delete">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>
    @else
        <div class="paradise-empty">
            <div class="empty-art">
                <div class="empty-circle"></div>
                <i class="fas fa-calendar-plus"></i>
            </div>
            <h3>Your Canvas Awaits</h3>
            <p>Begin your journey of unforgettable celebrations</p>
            <a href="{{ route('client.events.create') }}" class="btn-create-magic">
                <i class="fas fa-wand-magic-sparkles"></i>
                <span>Create Your First Event</span>
            </a>
        </div>
    @endif

</div>

<style>
/* ... your existing CSS (keep exactly as before) ... */
:root {
    --coral: #E19184;
    --berry: #C63E4E;
    --hunter: #620607;
    --cream: #EFE7DA;
    --green: #475B35;
}

.events-paradise {
    padding: 40px;
    background: linear-gradient(135deg, #FFF5F5 0%, #FFF 50%, #FFF5F0 100%);
    min-height: 100vh;
}

/* ========== HEADER ========== */
.paradise-header {
    position: relative;
    background: linear-gradient(135deg, var(--coral) 0%, var(--berry) 100%);
    border-radius: 30px;
    padding: 50px 60px;
    margin-bottom: 40px;
    overflow: hidden;
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.header-art {
    position: absolute;
    inset: 0;
}

.art-blob {
    position: absolute;
    border-radius: 50%;
    filter: blur(80px);
    opacity: 0.3;
}

.blob-1 {
    width: 400px;
    height: 400px;
    background: white;
    top: -100px;
    right: -100px;
    animation: blobFloat 20s infinite ease-in-out;
}

.blob-2 {
    width: 300px;
    height: 300px;
    background: var(--hunter);
    bottom: -80px;
    left: 10%;
    animation: blobFloat 25s infinite ease-in-out reverse;
}

@keyframes blobFloat {
    0%, 100% { transform: translate(0, 0) scale(1); }
    50% { transform: translate(50px, -50px) scale(1.1); }
}

.header-content {
    position: relative;
    z-index: 2;
}

.header-badge {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    background: rgba(255, 255, 255, 0.25);
    backdrop-filter: blur(10px);
    padding: 10px 20px;
    border-radius: 50px;
    color: white;
    font-weight: 700;
    margin-bottom: 20px;
    font-size: 14px;
}

.paradise-title {
    font-size: 56px;
    font-weight: 900;
    color: white;
    margin: 0 0 10px 0;
    line-height: 1;
    text-shadow: 0 4px 20px rgba(0, 0, 0, 0.2);
}



.coral-text {
    color: var(--cream);
    text-shadow: 0 0 30px rgba(255, 255, 255, 0.5);
}

.paradise-subtitle {
    font-size: 18px;
    color: rgba(255, 255, 255, 0.9);
    margin: 0;
}

.btn-create-magic {
    position: relative;
    z-index: 2;
    padding: 18px 35px;
    background: white;
    color: var(--berry);
    border-radius: 50px;
    font-weight: 900;
    font-size: 16px;
    text-decoration: none;
    display: flex;
    align-items: center;
    gap: 12px;
    box-shadow: 0 10px 40px rgba(0, 0, 0, 0.2);
    transition: all 0.3s;
}

.btn-create-magic:hover {
    transform: translateY(-5px) scale(1.05);
    box-shadow: 0 15px 50px rgba(0, 0, 0, 0.3);
}

/* ========== FILTERS ========== */
.filter-paradise {
    display: flex;
    gap: 15px;
    margin-bottom: 40px;
    justify-content: center;
}

.filter-pill {
    padding: 14px 28px;
    background: white;
    border: 2px solid var(--cream);
    border-radius: 50px;
    font-weight: 800;
    color: var(--green);
    cursor: pointer;
    display: flex;
    align-items: center;
    gap: 10px;
    transition: all 0.3s;
    font-size: 15px;
}

.filter-pill:hover {
    border-color: var(--coral);
    transform: translateY(-3px);
}

.filter-pill.active {
    background: linear-gradient(135deg, var(--coral), var(--berry));
    color: white;
    border-color: var(--berry);
}

/* ========== EVENT CARDS ========== */
.events-masonry {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(380px, 1fr));
    gap: 30px;
}

.event-masterpiece {
    background: white;
    border-radius: 25px;
    overflow: hidden;
    box-shadow: 0 10px 40px rgba(0, 0, 0, 0.08);
    transition: all 0.4s cubic-bezier(0.34, 1.56, 0.64, 1);
    position: relative;
}

.event-masterpiece:hover {
    transform: translateY(-12px) rotate(-1deg);
    box-shadow: 0 20px 60px rgba(0, 0, 0, 0.15);
}

.event-visual {
    position: relative;
    height: 260px;
    overflow: hidden;
}

.event-image {
    width: 100%;
    height: 100%;
    object-fit: cover;
    transition: all 0.5s;
}

.event-masterpiece:hover .event-image {
    transform: scale(1.1);
}

.event-placeholder {
    width: 100%;
    height: 100%;
}

.placeholder-gradient {
    width: 100%;
    height: 100%;
    position: relative;
    display: flex;
    align-items: center;
    justify-content: center;
}

.placeholder-gradient.Wedding {
    background: linear-gradient(135deg, var(--coral), var(--berry));
}

.placeholder-gradient.Birthday {
    background: linear-gradient(135deg, #FFB6C1, var(--coral));
}

.placeholder-gradient.Corporate {
    background: linear-gradient(135deg, var(--green), #2C3821);
}

.placeholder-gradient.Anniversary {
    background: linear-gradient(135deg, var(--berry), var(--hunter));
}

.placeholder-pattern {
    position: absolute;
    inset: 0;
    background: 
        repeating-linear-gradient(45deg, transparent, transparent 20px, rgba(255, 255, 255, 0.1) 20px, rgba(255, 255, 255, 0.1) 40px);
}

.placeholder-icon {
    position: relative;
    font-size: 90px;
    color: white;
    opacity: 0.9;
    animation: iconPulse 3s infinite ease-in-out;
}

@keyframes iconPulse {
    0%, 100% { transform: scale(1); }
    50% { transform: scale(1.1); }
}

.status-float {
    position: absolute;
    top: 20px;
    right: 20px;
    padding: 10px 20px;
    border-radius: 50px;
    font-size: 13px;
    font-weight: 900;
    display: flex;
    align-items: center;
    gap: 8px;
    backdrop-filter: blur(20px);
    box-shadow: 0 8px 25px rgba(0, 0, 0, 0.2);
}

.status-float.pending {
    background: rgba(255, 193, 7, 0.95);
    color: white;
}

.status-float.confirmed {
    background: rgba(76, 175, 80, 0.95);
    color: white;
}

.status-float.declined {
    background: rgba(244, 67, 54, 0.95);
    color: white;
}

.event-type-tag {
    position: absolute;
    bottom: 20px;
    left: 20px;
    padding: 8px 18px;
    background: white;
    border-radius: 20px;
    font-size: 12px;
    font-weight: 800;
    color: var(--berry);
    display: flex;
    align-items: center;
    gap: 6px;
}

/* ========== CONTENT ========== */
.event-essence {
    padding: 30px;
}

.event-name {
    font-size: 24px;
    font-weight: 900;
    color: var(--green);
    margin: 0 0 20px 0;
    line-height: 1.3;
}

.event-meta {
    display: flex;
    flex-direction: column;
    gap: 15px;
    margin-bottom: 25px;
}

.meta-item {
    display: flex;
    align-items: center;
    gap: 15px;
}

.meta-icon {
    width: 45px;
    height: 45px;
    background: linear-gradient(135deg, var(--coral), var(--berry));
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-size: 18px;
    flex-shrink: 0;
}

.meta-info {
    flex: 1;
}

.meta-label {
    display: block;
    font-size: 11px;
    color: #95A5A6;
    font-weight: 700;
    text-transform: uppercase;
    margin-bottom: 4px;
    letter-spacing: 0.5px;
}

.meta-info strong {
    font-size: 15px;
    color: var(--green);
    font-weight: 800;
}

.meta-grid {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 12px;
}

.meta-small {
    background: var(--cream);
    padding: 12px;
    border-radius: 12px;
    display: flex;
    align-items: center;
    gap: 8px;
    font-size: 13px;
    font-weight: 700;
    color: var(--green);
}

.meta-small i {
    color: var(--coral);
}

/* ========== ACTIONS ========== */
.event-locked,
.event-declined {
    padding: 20px;
    background: linear-gradient(135deg, #FFF5F5, #FFF0F0);
    border-radius: 15px;
    text-align: center;
}

.lock-message,
.declined-message {
    margin-bottom: 15px;
}

.lock-message i,
.declined-message i {
    font-size: 32px;
    color: var(--coral);
    margin-bottom: 10px;
}

.lock-message p,
.declined-message p {
    font-size: 14px;
    color: var(--green);
    font-weight: 600;
    margin: 0;
}

.btn-cancel,
.btn-remove {
    padding: 12px 24px;
    background: var(--berry);
    color: white;
    border: none;
    border-radius: 12px;
    font-weight: 800;
    cursor: pointer;
    display: inline-flex;
    align-items: center;
    gap: 8px;
    transition: all 0.3s;
}

.btn-cancel:hover,
.btn-remove:hover {
    background: var(--hunter);
    transform: scale(1.05);
}

.event-actions {
    display: flex;
    gap: 12px;
}

.btn-enter {
    flex: 1;
    padding: 16px;
    background: linear-gradient(135deg, var(--coral), var(--berry));
    color: white;
    border-radius: 15px;
    font-weight: 900;
    font-size: 15px;
    text-decoration: none;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 10px;
    transition: all 0.3s;
    box-shadow: 0 8px 20px rgba(225, 145, 132, 0.4);
}

.btn-enter:hover {
    transform: translateY(-3px);
    box-shadow: 0 12px 30px rgba(225, 145, 132, 0.6);
}

.action-more {
    display: flex;
    gap: 8px;
}

.btn-icon {
    width: 50px;
    height: 50px;
    background: var(--cream);
    border: none;
    border-radius: 12px;
    color: var(--green);
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 16px;
    transition: all 0.3s;
    text-decoration: none;
}

.btn-icon:hover {
    background: var(--coral);
    color: white;
    transform: scale(1.1);
}
.btn-icon.danger:hover {
    background: var(--berry);
}

/* ========== EMPTY STATE ========== */
.paradise-empty {
    text-align: center;
    padding: 100px 40px;
}

.empty-art {
    position: relative;
    width: 150px;
    height: 150px;
    margin: 0 auto 30px;
}

.empty-circle {
    position: absolute;
    inset: 0;
    background: linear-gradient(135deg, var(--coral), var(--berry));
    border-radius: 50%;
    opacity: 0.2;
    animation: emptyPulse 3s infinite;
}

@keyframes emptyPulse {
    0%, 100% { transform: scale(1); }
    50% { transform: scale(1.2); }
}
.empty-art i {
    position: absolute;
    inset: 0;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 70px;
    color: var(--berry);
}
.paradise-empty h3 {
    font-size: 32px;
    font-weight: 900;
    color: var(--green);
    margin: 0 0 15px 0;
}

.paradise-empty p {
    font-size: 18px;
    color: #7F8C8D;
    margin: 0 0 35px 0;
}

@media (max-width: 768px) {
    .events-masonry {
        grid-template-columns: 1fr;
    }
    
    .paradise-header {
        flex-direction: column;
        text-align: center;
        gap: 30px;
    }
}


/* Sub‑badge inside the status badge */
.sub-status-badge {
    font-size: 10px;
    padding: 4px 8px;
    border-radius: 20px;
    margin-top: 5px;
    background: rgba(0,0,0,0.6);
    color: white;
    font-weight: 600;
    text-align: center;
    letter-spacing: 0.5px;
}
.sub-status-badge.confirmed {
    background: #27ae60;
}
.sub-status-badge.in_progress {
    background: #f39c12;
}
.sub-status-badge.completed {
    background: #3498db;
}
.status-float.confirmed {
    display: flex;
    flex-direction: column;
    align-items: center;
    min-width: 110px;
}
</style>

@push('scripts')
<script>
document.querySelectorAll('.filter-pill').forEach(pill => {
    pill.addEventListener('click', function() {
        const status = this.dataset.status;
        
        document.querySelectorAll('.filter-pill').forEach(p => p.classList.remove('active'));
        this.classList.add('active');
        
        document.querySelectorAll('.event-masterpiece').forEach(card => {
            if (status === 'all' || card.dataset.status === status) {
                card.style.display = 'block';
            } else {
                card.style.display = 'none';
            }
        });
    });
});
</script>
@endpush
@endsection