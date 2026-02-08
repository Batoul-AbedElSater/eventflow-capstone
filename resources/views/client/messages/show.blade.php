@extends('layouts.client')

@section('title', 'Chat - ' . $thread->planner->name)

@section('content')
<div class="chat-container">
    
    <!-- Chat Header -->
    <div class="chat-header">
        <div class="header-left">
            <a href="{{ route('client.messages') }}" class="back-btn">
                <i class="fas fa-arrow-left"></i>
            </a>
            <div class="planner-info">
                <div class="avatar-circle">
                    {{ strtoupper(substr($thread->planner->name, 0, 1)) }}
                </div>
                <div>
                    <h2>{{ $thread->planner->name }}</h2>
                    <p class="event-name">
                        <i class="fas fa-calendar"></i>
                        {{ $thread->event->name }}
                    </p>
                </div>
            </div>
        </div>
        <a href="{{ route('client.events.show', $thread->event_id) }}" class="btn-secondary">
            <i class="fas fa-eye"></i> View Event
        </a>
    </div>

    <!-- Messages Area -->
    <div class="messages-area" id="messages-area">
        @if($thread->messages->count() > 0)
            @foreach($thread->messages as $message)
                <div class="message {{ $message->sender_id === Auth::id() ? 'sent' : 'received' }}">
                    <div class="message-avatar">
                        {{ strtoupper(substr($message->sender->name, 0, 1)) }}
                    </div>
                    <div class="message-content">
                        <div class="message-header">
                            <span class="sender-name">{{ $message->sender->name }}</span>
                            <span class="message-time">{{ $message->created_at->format('g:i A') }}</span>
                        </div>
                        <div class="message-text">
                            {{ $message->body }}
                        </div>
                    </div>
                </div>
            @endforeach
        @else
            <div class="no-messages">
                <i class="fas fa-comment-dots"></i>
                <p>No messages yet. Start the conversation!</p>
            </div>
        @endif
    </div>

    <!-- Message Input -->
    <div class="message-input-area">
        <form id="message-form">
            <textarea 
                id="message-input" 
                placeholder="Type your message..."
                rows="1"></textarea>
            <button type="submit" class="send-btn">
                <i class="fas fa-paper-plane"></i>
            </button>
        </form>
    </div>

</div>

<script>
    const THREAD_ID = {{ $thread->id }};
    const CSRF_TOKEN = '{{ csrf_token() }}';
    const CURRENT_USER_ID = {{ Auth::id() }};
</script>
<script src="{{ asset('js/client-messages.js') }}"></script>
<link rel="stylesheet" href="{{ asset('css/client-messages.css') }}">
@endsection