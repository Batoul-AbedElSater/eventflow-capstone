<div class="kanban-card-supreme"
     data-event-id="{{ $event->id }}"
     data-status="{{ $event->status }}"
     data-type="{{ $event->event_type_id }}"
     data-update-url="{{ route('planner.events.status', $event->id) }}"
     draggable="true">

    {{-- Card Header with Priority, Type & Days Until --}}
    <div class="kanban-card-header">
        @php
            $daysUntil = (int) max(0, now()->diffInDays($event->start_date, false));
        @endphp
        <div class="days-until-badge days-until-inline {{ $daysUntil <= 7 ? 'urgent' : ($daysUntil <= 30 ? 'soon' : '') }}">
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
        @if($event->budget || $event->budget_overall)
        <div class="card-budget-badge">
            <i class="fas fa-dollar-sign"></i>
            <span>${{ number_format((float) ($event->budget->total_client_budget ?? $event->budget_overall ?? 0), 0) }}</span>
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
    </div>

</div>
