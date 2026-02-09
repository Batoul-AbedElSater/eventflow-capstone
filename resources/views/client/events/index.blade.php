@extends('layouts.client')

@section('title', 'My Events')

@section('content')
<div class="events-list-container">
    
    <!-- Back to Dashboard -->
    <a href="{{ route('client.dashboard') }}" class="back-link">
        <i class="fas fa-arrow-left"></i> Back to Dashboard
    </a>

    <!-- Page Header -->
    <div class="page-header-section">
        <div class="page-header-content">
            <h1><i class="fas fa-calendar-alt"></i> My Events</h1>
            <p class="subtitle">Manage all your special occasions in one place</p>
        </div>
        <a href="{{ route('client.events.create') }}" class="btn-primary">
            <i class="fas fa-plus"></i> Create New Event
        </a>
    </div>

    @if($events->count() > 0)
        <div class="events-grid-fancy">
            @foreach($events as $event)
                <div class="event-card-fancy">
                    <div class="event-card-header">
                        <div class="event-type-icon">
                            @switch($event->eventType->name)
                                @case('Wedding') 💒 @break
                                @case('Birthday') 🎂 @break
                                @case('Corporate') 💼 @break
                                @default 🎉
                            @endswitch
                        </div>
                        <span class="status-badge {{ $event->status }}">
                            {{ ucfirst($event->status) }}
                        </span>
                    </div>
                    
                    <h3 class="event-title">{{ $event->name }}</h3>
                    
                    <div class="event-details-fancy">
                        <p class="event-date">
                            <i class="fas fa-calendar"></i>
                            <strong>{{ $event->start_date->format('M d, Y') }}</strong>
                        </p>
                        <p class="event-location">
                            <i class="fas fa-map-marker-alt"></i>
                            <span>{{ Str::limit($event->location_text, 40) }}</span>
                        </p>
                        <p class="event-guests">
                            <i class="fas fa-users"></i>
                            <span>{{ $event->guests->count() }} Guests</span>
                        </p>
                    </div>
                    
                    <a href="{{ route('client.events.show', $event->id) }}" class="btn-view-event">
                        <span>View Details</span>
                        <i class="fas fa-arrow-right"></i>
                    </a>
                </div>
            @endforeach
        </div>
    @else
        <div class="empty-state-fancy">
            <div class="empty-icon">
                <i class="fas fa-calendar-plus"></i>
            </div>
            <h3>No Events Yet</h3>
            <p>Create your first event and start planning something amazing!</p>
            <a href="{{ route('client.events.create') }}" class="btn-primary">
                <i class="fas fa-plus"></i> Create Your First Event
            </a>
        </div>
    @endif
</div>

<style>
.events-list-container {
    max-width: 1400px;
    margin: 0 auto;
}

.back-link {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    color: #586041;
    text-decoration: none;
    font-weight: 600;
    margin-bottom: 30px;
    transition: all 0.3s;
}

.back-link:hover {
    color: #353935;
    transform: translateX(-5px);
}

.page-header-section {
    background: linear-gradient(135deg, #586041, #353935);
    border-radius: 20px;
    padding: 40px;
    margin-bottom: 40px;
    display: flex;
    justify-content: space-between;
    align-items: center;
    box-shadow: 0 10px 40px rgba(0,0,0,0.15);
}

.page-header-content h1 {
    color: #FFFFF0;
    font-size: 36px;
    margin-bottom: 8px;
    display: flex;
    align-items: center;
    gap: 15px;
}

.page-header-content .subtitle {
    color: rgba(255,255,240,0.8);
    font-size: 16px;
}

.events-grid-fancy {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(380px, 1fr));
    gap: 30px;
}

.event-card-fancy {
    background: #FFFFFF;
    border-radius: 20px;
    padding: 30px;
    box-shadow: 0 5px 20px rgba(0,0,0,0.08);
    transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
    border: 2px solid transparent;
}

.event-card-fancy:hover {
    transform: translateY(-8px);
    box-shadow: 0 15px 40px rgba(0,0,0,0.15);
    border-color: #586041;
}

.event-card-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 20px;
}

.event-type-icon {
    font-size: 48px;
}

.status-badge {
    padding: 8px 16px;
    border-radius: 25px;
    font-size: 12px;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.status-badge.draft { background: rgba(74,144,226,0.15); color: #4A90E2; }
.status-badge.planned { background: rgba(126,211,33,0.15); color: #7ED321; }
.status-badge.in_progress { background: rgba(245,166,35,0.15); color: #F5A623; }
.status-badge.completed { background: rgba(144,19,254,0.15); color: #9013FE; }
.status-badge.cancelled { background: rgba(208,2,27,0.15); color: #D0021B; }

.event-title {
    font-size: 24px;
    font-weight: 700;
    color: #353935;
    margin-bottom: 20px;
}

.event-details-fancy {
    display: flex;
    flex-direction: column;
    gap: 12px;
    margin-bottom: 25px;
    padding: 20px;
    background: #F5F5DC;
    border-radius: 12px;
}

.event-details-fancy p {
    display: flex;
    align-items: center;
    gap: 12px;
    font-size: 15px;
    color: #353935;
}

.event-details-fancy i {
    width: 20px;
    color: #586041;
    font-size: 16px;
}

.btn-view-event {
    display: flex;
    align-items: center;
    justify-content: space-between;
    width: 100%;
    padding: 16px 24px;
    background: linear-gradient(135deg, #586041, #353935);
    color: #FFFFF0;
    text-decoration: none;
    border-radius: 12px;
    font-weight: 600;
    transition: all 0.3s;
}

.btn-view-event:hover {
    transform: translateX(5px);
    box-shadow: 0 5px 20px rgba(88,96,65,0.4);
}

.empty-state-fancy {
    background: linear-gradient(135deg, #FFFFF0, #F5F5DC);
    border-radius: 24px;
    padding: 80px 40px;
    text-align: center;
    box-shadow: 0 10px 40px rgba(0,0,0,0.08);
}

.empty-icon {
    font-size: 100px;
    margin-bottom: 30px;
    color: #586041;
    opacity: 0.3;
}

.empty-state-fancy h3 {
    font-size: 32px;
    color: #353935;
    margin-bottom: 15px;
}

.empty-state-fancy p {
    font-size: 18px;
    color: #888;
    margin-bottom: 35px;
}
</style>
@endsection