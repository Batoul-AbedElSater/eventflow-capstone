@extends('layouts.client')

@section('title', 'Messages')

@section('content')
<div class="messages-container">
    
    <!-- Header -->
    <div class="page-header">
        <h1>Messages</h1>
        <p class="subtitle">Communicate with your event planner</p>
    </div>

    @if(session('error'))
        <div class="alert alert-error">
            <i class="fas fa-exclamation-circle"></i>
            {{ session('error') }}
        </div>
    @endif

    <!-- Message Threads List -->
    @if($threads->count() > 0)
        <div class="threads-list">
            @foreach($threads as $thread)
                <a href="{{ route('client.messages.show', $thread->id) }}" class="thread-card">
                    <div class="thread-avatar">
                        <div class="avatar-circle">
                            {{ strtoupper(substr($thread->planner->name, 0, 1)) }}
                        </div>
                        @if($thread->unread_count > 0)
                            <span class="unread-badge">{{ $thread->unread_count }}</span>
                        @endif
                    </div>
                    
                    <div class="thread-content">
                        <div class="thread-header">
                            <h3>{{ $thread->planner->name }}</h3>
                            <span class="thread-time">
                                {{ $thread->updated_at->diffForHumans() }}
                            </span>
                        </div>
                        <p class="thread-event">
                            <i class="fas fa-calendar"></i>
                            {{ $thread->event->name }}
                        </p>
                        @if($thread->messages->count() > 0)
                            <p class="thread-preview">
                                {{ Str::limit($thread->messages->first()->message_text, 80) }}
                            </p>
                        @else
                            <p class="thread-preview no-messages">No messages yet</p>
                        @endif
                    </div>
                    
                    <div class="thread-arrow">
                        <i class="fas fa-chevron-right"></i>
                    </div>
                </a>
            @endforeach
        </div>
    @else
        <!-- Empty State -->
        <div class="empty-state">
            <i class="fas fa-comments"></i>
            <h3>No Messages Yet</h3>
            <p>Start a conversation with your event planner from the event details page.</p>
        </div>
    @endif

</div>

<link rel="stylesheet" href="{{ asset('css/client-messages.css') }}">
@endsection