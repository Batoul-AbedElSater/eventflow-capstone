@extends('layouts.client')

@section('title', 'My Events')

@section('content')
<div class="events-list-container">
    <div class="page-header">
        <h1>My Events</h1>
        <a href="{{ route('client.events.create') }}" class="btn-primary">
            <i class="fas fa-plus"></i> Create New Event
        </a>
    </div>

    @if($events->count() > 0)
        <div class="events-grid">
            @foreach($events as $event)
                <div class="event-card">
                    <div class="event-header">
                        <h3>{{ $event->name }}</h3>
                        <span class="status-badge {{ $event->status }}">
                            {{ ucfirst($event->status) }}
                        </span>
                    </div>
                    <p class="event-date">
                        <i class="fas fa-calendar"></i>
                        {{ $event->start_date->format('M d, Y') }}
                    </p>
                    <p class="event-location">
                        <i class="fas fa-map-marker-alt"></i>
                        {{ $event->location_text }}
                    </p>
                    <a href="{{ route('client.events.show', $event->id) }}" class="btn-view">
                        View Details
                    </a>
                </div>
            @endforeach
        </div>
    @else
        <div class="empty-state">
            <i class="fas fa-calendar-plus"></i>
            <h3>No Events Yet</h3>
            <p>Create your first event to get started!</p>
        </div>
    @endif
</div>

<link rel="stylesheet" href="{{ asset('css/client-dashboard.css') }}">
@endsection