@extends('layouts.client')

@section('title', 'Messages')

@section('content')
<div class="messages-container-epic">

    <div class="messages-layout-epic">
        <!-- Events Sidebar -->
        <div class="events-sidebar-epic">
            <div class="sidebar-header">
                <h3><i class="fas fa-calendar-alt"></i> Your Events</h3>
            </div>
            <div class="events-list-epic">
                     @forelse($events as $event)
                        <div class="event-card-epic"
                            data-event-id="{{ $event->id }}"
                            data-event-name="{{ $event->name }}"
                            data-planner-name="{{ $event->planner->name ?? 'Planner' }}"
                            onclick="loadMessagesFromCard(this)">
                            <div class="event-card-content">
                                <div class="event-icon">
                                    <i class="fas fa-calendar-check"></i>
                                </div>
                                <div class="event-details">
                                    <h4>{{ Str::limit($event->name, 25) }}</h4>
                                    <p><i class="fas fa-user-tie"></i> {{ $event->planner->name ?? 'Planner' }}</p>
                                    <span class="event-date">{{ \Carbon\Carbon::parse($event->start_date)->format('M d, Y') }}</span>
                                </div>
                            </div>
                        </div>
                        @empty
                <div class="empty-state">
                    <i class="fas fa-calendar-times"></i>
                    <p>No events yet</p>
                </div>
                @endforelse
            </div>
        </div>

        <!-- Chat Area -->
        <div class="chat-area-epic">
            <div class="chat-empty-state" id="emptyState">
                <div class="empty-icon">
                    <i class="fas fa-comments"></i>
                </div>
                <h3>Select an event to start messaging</h3>
                <p>Choose an event from the sidebar to view messages</p>
            </div>

            <div class="chat-active-state" id="chatState" style="display: none;">
                <!-- Chat Header -->
                <div class="chat-header-epic">
                    <div class="chat-header-content">
                        <div class="client-avatar">
                            <span id="plannerInitials">PL</span>
                        </div>
                        <div class="chat-header-info">
                            <h3 id="eventName">Event Name</h3>
                            <p id="plannerName">Planner Name</p>
                        </div>
                    </div>
                    <!-- ADD THIS DELETE BUTTON -->
                    <button class="delete-chat-btn" id="deleteChatBtn" title="Delete all messages">
                        <i class="fas fa-trash-alt"></i> Clear Chat
                    </button>
                </div>

                <!-- Messages Container -->
                <div class="messages-list-epic" id="messagesList">
                    <!-- Messages will be loaded here -->
                </div>

                <!-- Input Area -->
                <div class="message-input-area-epic">
                    <form id="messageForm" class="message-form-epic">
                        <textarea
                            id="messageInput"
                            placeholder="Type your message..."
                            rows="1"
                            required></textarea>
                        <button type="submit" class="send-btn-epic">
                            <i class="fas fa-paper-plane"></i>
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<link rel="stylesheet" href="{{ asset('css/planner-messages.css') }}">
<script src="{{ asset('js/client-messages.js') }}"></script>
@endsection
