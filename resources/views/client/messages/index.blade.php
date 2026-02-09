@extends('layouts.client')

@section('title', 'Messages')

@section('content')
<div class="messages-container">
    
    <!-- Back to Dashboard -->
    <a href="{{ route('client.dashboard') }}" class="back-link">
        <i class="fas fa-arrow-left"></i> Back to Dashboard
    </a>

    <!-- Page Header -->
    <div class="page-header-section">
        <div class="page-header-content">
            <h1><i class="fas fa-envelope"></i> Messages</h1>
            <p class="subtitle">Communicate with your event planners</p>
        </div>
    </div>

    @if(session('error'))
        <div class="alert alert-error">
            <i class="fas fa-exclamation-circle"></i>
            {{ session('error') }}
        </div>
    @endif

    @if($threads->count() > 0)
        <div class="threads-list-fancy">
            @foreach($threads as $thread)
                <a href="{{ route('client.messages.show', $thread->id) }}" class="thread-card-fancy">
                    <div class="thread-avatar-fancy">
                        <div class="avatar-circle-large">
                            {{ strtoupper(substr($thread->planner->name, 0, 1)) }}
                        </div>
                        @if($thread->unread_count > 0)
                            <span class="unread-badge-large">{{ $thread->unread_count }}</span>
                        @endif
                    </div>
                    
                    <div class="thread-content-fancy">
                        <div class="thread-header-fancy">
                            <h3>{{ $thread->planner->name }}</h3>
                            <span class="thread-time">
                                {{ $thread->updated_at->diffForHumans() }}
                            </span>
                        </div>
                        <p class="thread-event-fancy">
                            <i class="fas fa-calendar"></i>
                            {{ $thread->event->name }}
                        </p>
                        @if($thread->messages->count() > 0)
                            <p class="thread-preview-fancy">
                                {{ Str::limit($thread->messages->first()->body, 80) }}
                            </p>
                        @else
                            <p class="thread-preview-fancy no-messages">No messages yet</p>
                        @endif
                    </div>
                    
                    <div class="thread-arrow-fancy">
                        <i class="fas fa-chevron-right"></i>
                    </div>
                </a>
            @endforeach
        </div>
    @else
        <div class="empty-state-fancy">
            <div class="empty-icon">
                <i class="fas fa-comments"></i>
            </div>
            <h3>No Messages Yet</h3>
            <p>Start a conversation with your event planner from the event details page</p>
        </div>
    @endif

</div>

<style>
.messages-container {
    max-width: 1200px;
    margin: 0 auto;
}

.threads-list-fancy {
    display: flex;
    flex-direction: column;
    gap: 20px;
}

.thread-card-fancy {
    background: #FFFFFF;
    border-radius: 16px;
    padding: 25px;
    display: flex;
    align-items: center;
    gap: 25px;
    text-decoration: none;
    transition: all 0.3s;
    box-shadow: 0 4px 15px rgba(0,0,0,0.08);
    border: 2px solid transparent;
}

.thread-card-fancy:hover {
    transform: translateX(8px);
    box-shadow: 0 8px 30px rgba(0,0,0,0.15);
    border-color: #586041;
}

.thread-avatar-fancy {
    position: relative;
}

.avatar-circle-large {
    width: 70px;
    height: 70px;
    border-radius: 50%;
    background: linear-gradient(135deg, #586041, #353935);
    color: #FFFFFF;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 28px;
    font-weight: 700;
    box-shadow: 0 4px 15px rgba(88,96,65,0.3);
}

.unread-badge-large {
    position: absolute;
    top: -5px;
    right: -5px;
    background: #D0021B;
    color: #FFFFFF;
    width: 28px;
    height: 28px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 13px;
    font-weight: 700;
    box-shadow: 0 2px 10px rgba(208,2,27,0.4);
}

.thread-content-fancy {
    flex: 1;
}

.thread-header-fancy {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 8px;
}

.thread-header-fancy h3 {
    font-size: 20px;
    font-weight: 700;
    color: #353935;
}

.thread-time {
    font-size: 13px;
    color: #888;
}

.thread-event-fancy {
    font-size: 15px;
    color: #586041;
    margin-bottom: 8px;
    display: flex;
    align-items: center;
    gap: 8px;
    font-weight: 600;
}

.thread-preview-fancy {
    font-size: 14px;
    color: #666;
}

.thread-preview-fancy.no-messages {
    font-style: italic;
    opacity: 0.6;
}

.thread-arrow-fancy {
    color: #586041;
    font-size: 20px;
}
</style>
@endsection