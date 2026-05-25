<div class="kanban-card-supreme" 
     data-event-id="{{ $event->id }}" 
     data-status="{{ $event->status }}"
     data-type="{{ $event->eventType->slug ?? 'other' }}"
     draggable="true">
    
    {{-- Card Header with Priority & Type --}}
    <div class="kanban-card-header">
        <div class="card-priority-badge {{ $event->priority ?? 'medium' }}">
            @if(isset($event->priority) && $event->priority === 'high')
                <i class="fas fa-exclamation-circle"></i>
            @elseif(isset($event->priority) && $event->priority === 'urgent')
                <i class="fas fa-bolt"></i>
            @else
                <i class="fas fa-circle"></i>
            @endif
        </div>
        
        <div class="card-type-badge" style="background: {{ $event->eventType->color ?? '#475B35' }}">
            {{ $event->eventType->name ?? 'Event' }}
        </div>
    </div>

    {{-- Event Photo/Gradient --}}
    <div class="kanban-card-image">
        @if($event->event_photo)
            <img src="{{ asset('storage/' . $event->event_photo) }}" alt="{{ $event->name }}">
        @else
            <div class="card-gradient-placeholder {{ strtolower($event->eventType->slug ?? 'default') }}">
                <i class="fas fa-{{ $event->eventType->icon ?? 'calendar' }}"></i>
            </div>
        @endif
        
        {{-- Days Until Badge --}}
        @php
            $daysUntil = (int) max(0, now()->diffInDays($event->start_date, false));
        @endphp
        <div class="days-until-badge {{ $daysUntil <= 7 ? 'urgent' : ($daysUntil <= 30 ? 'soon' : '') }}">
            <i class="fas fa-clock"></i> {{ $daysUntil }} days
        </div>
    </div>

    {{-- Event Details --}}
    <div class="kanban-card-content">
        <h4 class="card-event-name">{{ Str::limit($event->name, 40) }}</h4>
        
        <div class="card-client-info">
            <div class="client-avatar-small">
                {{ strtoupper(substr($event->client->name, 0, 1)) }}
            </div>
            <span class="client-name">{{ $event->client->name }}</span>
        </div>

        <div class="card-meta-info">
            <div class="meta-item">
                <i class="fas fa-calendar"></i>
                <span>{{ $event->start_date->format('M d, Y') }}</span>
            </div>
            
            @if($event->location_text)
            <div class="meta-item">
                <i class="fas fa-map-marker-alt"></i>
                <span>{{ Str::limit($event->location_text, 25) }}</span>
            </div>
            @endif

            @if($event->guests_count > 0)
            <div class="meta-item">
                <i class="fas fa-users"></i>
                <span>{{ $event->guests_count }} guests</span>
            </div>
            @endif
        </div>

        {{-- Budget Badge --}}
        @if($event->budget)
        <div class="card-budget-badge">
            <i class="fas fa-dollar-sign"></i>
            <span>${{ number_format($event->budget, 0) }}</span>
        </div>
        @endif
    </div>

    {{-- Card Actions --}}
    <div class="kanban-card-actions">
        <a href="{{ route('planner.events.show', $event->id) }}" class="card-action-btn view">
            <i class="fas fa-eye"></i>
        </a>
        
        <button class="card-action-btn message" onclick="openMessaging({{ $event->id }})">
            <i class="fas fa-envelope"></i>
        </button>
        
        @if($event->status === 'pending')
        <button class="card-action-btn accept" onclick="quickAccept({{ $event->id }})">
            <i class="fas fa-check"></i>
        </button>
        <button class="card-action-btn decline" onclick="quickDecline({{ $event->id }})">
            <i class="fas fa-times"></i>
        </button>
        @endif
    </div>

</div>