@extends('layouts.client')

@section('title', $event->name)

@section('content')

<style>
    .star-rating .star {
    font-size: 30px;
    cursor: pointer;
    color: #ccc;
    transition: color 0.2s;
}
.star-rating .star.active {
    color: #ffc107;
}
.rating-card {
    background: white;
    padding: 20px;
    border-radius: 16px;
    margin-top: 30px;
    box-shadow: 0 2px 12px rgba(0,0,0,0.1);
}
.rating-card textarea {
    width: 100%;
    margin: 15px 0;
    padding: 10px;
    border-radius: 8px;
    border: 1px solid #ddd;
}
</style>
<div class="event-show-container">
    
    {{-- Event Header with Photo --}}
    <div class="event-show-header">
        <div class="event-hero-section">
            @if($event->event_photo)
                <img src="{{ asset('storage/' . $event->event_photo) }}" alt="{{ $event->name }}" class="event-hero-image">
            @else
                <div class="event-hero-placeholder">
                    <div class="hero-gradient {{ $event->eventType->name }}">
                        <div class="hero-icon">
                            @if($event->eventType->name === 'Wedding')
                                💒
                            @elseif($event->eventType->name === 'Birthday')
                                🎂
                            @elseif($event->eventType->name === 'Corporate')
                                💼
                            @else
                                🎉
                            @endif
                        </div>
                    </div>
                </div>
            @endif
            <div class="hero-overlay"></div>
            
            <div class="hero-content">
                <div class="breadcrumb">
                    <a href="{{ route('client.dashboard') }}"><i class="fas fa-home"></i> Dashboard</a>
                    <i class="fas fa-chevron-right"></i>
                    <a href="{{ route('client.events.index') }}">My Events</a>
                    <i class="fas fa-chevron-right"></i>
                    <span>{{ $event->name }}</span>
                </div>
                
                <h1 class="event-hero-title">{{ $event->name }}</h1>
                
                <div class="event-meta-badges">
                    <span class="meta-badge type">
                        <i class="fas fa-tag"></i> {{ $event->eventType->name }}
                    </span>
                    <span class="meta-badge status {{ $event->status }}">
                        @if($event->status === 'pending')
                            <i class="fas fa-clock"></i> Pending Approval
                        @elseif($event->status === 'confirmed')
                            <i class="fas fa-check-circle"></i> Confirmed
                        @elseif($event->status === 'declined')
                            <i class="fas fa-times-circle"></i> Declined
                        @elseif($event->status === 'in_progress')
                            <i class="fas fa-spinner"></i> In Progress
                        @elseif($event->status === 'completed')
                            <i class="fas fa-flag-checkered"></i> Completed
                        @else
                            <i class="fas fa-file"></i> Draft
                        @endif
                    </span>
                </div>

                <div class="event-hero-actions">
                    <a href="{{ route('client.events.edit', $event->id) }}" class="btn-hero-action edit">
                        <i class="fas fa-edit"></i> Edit Event
                    </a>
                    <a href="#" onclick="document.querySelector('.event-tab[data-tab=\'messages\']').click(); return false;" class="btn-hero-action messages">
                        <i class="fas fa-comments"></i> Messages
                    </a>
                    <form method="POST" action="{{ route('client.events.destroy', $event->id) }}" style="display: inline;">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn-hero-action delete" onclick="return confirm('Delete this event permanently?')">
                            <i class="fas fa-trash"></i> Delete
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    {{-- Event Stats Bar --}}
    <div class="event-stats-bar">
        <div class="stat-item">
            <div class="stat-icon">
                <i class="fas fa-calendar-day"></i>
            </div>
            <div class="stat-info">
                <span class="stat-label">Event Date</span>
                <strong>{{ $event->start_date->format('M d, Y') }}</strong>
            </div>
        </div>

        <div class="stat-item">
            <div class="stat-icon">
                <i class="fas fa-clock"></i>
            </div>
            <div class="stat-info">
                <span class="stat-label">Days Until</span>
                <strong>{{ (int) max(0, now()->diffInDays($event->start_date, false)) }} days</strong>
            </div>
        </div>

        <div class="stat-item">
            <div class="stat-icon">
                <i class="fas fa-users"></i>
            </div>
            <div class="stat-info">
                <span class="stat-label">Total Guests</span>
                <strong>{{ $event->guest_estimate }} people</strong>
            </div>
        </div>

        <div class="stat-item">
            <div class="stat-icon">
                <i class="fas fa-map-marker-alt"></i>
            </div>
            <div class="stat-info">
                <span class="stat-label">Location</span>
                <strong>{{ Str::limit($event->location_text, 30) }}</strong>
            </div>
        </div>
    </div>

    {{-- Main Content Tabs --}}
    <div class="event-tabs-container">
        <div class="event-tabs">
            <button class="event-tab active" data-tab="overview">
                <i class="fas fa-info-circle"></i> Overview
            </button>
            <button class="event-tab" data-tab="guests">
                <i class="fas fa-users"></i> Guests ({{ $event->invitations->count() }})
            </button>
            <button class="event-tab" data-tab="messages">
                <i class="fas fa-comments"></i> Messages
            </button>
        </div>

        {{-- Tab Content --}}
        <div class="tab-content active" id="overview-tab">
            <div class="overview-grid">
                {{-- Event Details Card --}}
                <div class="overview-card">
                    <div class="card-header">
                        <div class="card-icon">
                            <i class="fas fa-info-circle"></i>
                        </div>
                        <h3>Event Details</h3>
                    </div>
                    <div class="card-body">
                        <div class="detail-row">
                            <span class="detail-label">Event Name</span>
                            <strong>{{ $event->name }}</strong>
                        </div>
                        <div class="detail-row">
                            <span class="detail-label">Event Type</span>
                            <strong>{{ $event->eventType->name }}</strong>
                        </div>
                        <div class="detail-row">
                            <span class="detail-label">Start Date</span>
                            <strong>{{ $event->start_date->format('l, F j, Y') }}</strong>
                        </div>
                        @if($event->end_date)
                        <div class="detail-row">
                            <span class="detail-label">End Date</span>
                            <strong>{{ $event->end_date->format('l, F j, Y') }}</strong>
                        </div>
                        @endif
                        <div class="detail-row">
                            <span class="detail-label">Location</span>
                            <strong>{{ $event->location_text }}</strong>
                        </div>
                        <div class="detail-row">
                            <span class="detail-label">Guest Estimate</span>
                            <strong>{{ $event->guest_estimate }} people</strong>
                        </div>
                    </div>
                </div>

                {{-- Description Card --}}
                @if($event->description)
                <div class="overview-card">
                    <div class="card-header">
                        <div class="card-icon">
                            <i class="fas fa-align-left"></i>
                        </div>
                        <h3>Description</h3>
                    </div>
                    <div class="card-body">
                        <p class="event-description">{{ $event->description }}</p>
                    </div>
                </div>
                @endif

                {{-- Budget Card --}}
                <div class="overview-card">
                    <div class="card-header">
                        <div class="card-icon">
                            <i class="fas fa-coins"></i>
                        </div>
                        <h3>Budget Overview</h3>
                    </div>
                    <div class="card-body">
                        <div class="budget-summary">
                            <div class="budget-item total">
                                <span class="budget-label">Total Budget</span>
                                <strong>{{ number_format($event->budget_overall, 2) }} SAR</strong>
                            </div>
                            <div class="budget-item spent">
                                <span class="budget-label">Amount Spent</span>
                                <strong>{{ number_format($event->getTotalSpent(), 2) }} SAR</strong>
                            </div>
                            <div class="budget-item remaining">
                                <span class="budget-label">Remaining</span>
                                <strong>{{ number_format($event->budget_overall - $event->getTotalSpent(), 2) }} SAR</strong>
                            </div>
                        </div>
                        <div class="budget-progress-bar">
                            @php
                                $budgetPercent = $event->budget_overall > 0 ? min(100, ($event->getTotalSpent() / $event->budget_overall) * 100) : 0;
                            @endphp
                            <div class="budget-progress-fill" style="width: {{ $budgetPercent }}%"></div>
                        </div>
                        <p class="budget-percentage">{{ round($budgetPercent) }}% of budget used</p>
                    </div>
                </div>

                {{-- Planner Info --}}
                @if($event->planner)
                <div class="overview-card">
                    <div class="card-header">
                        <div class="card-icon">
                            <i class="fas fa-user-tie"></i>
                        </div>
                        <h3>Event Planner</h3>
                    </div>
                    <div class="card-body">
                        <div class="planner-info">
                            <div class="planner-avatar">
                                {{ strtoupper(substr($event->planner->name, 0, 1)) }}
                            </div>
                            <div class="planner-details">
                                <h4>{{ $event->planner->name }}</h4>
                                <p>{{ $event->planner->email }}</p>
                                <a href="{{ route('client.events.messages.index', $event->id) }}" class="btn-message-planner">
                                    <i class="fas fa-comment"></i> Send Message
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
                @endif
            </div>
        </div>

        {{-- Guests Tab --}}
                <div class="tab-content" id="guests-tab">
                    <div class="guests-header-luxury">
                        <div class="guests-header-left">
                            <h3>Guest List ({{ $event->guests->count() }})</h3>
                            <div class="guest-stats-mini">
                                <span class="stat-badge accepted">
                                    <i class="fas fa-check"></i> {{ $event->guests->where('rsvp_status', 'accepted')->count() }} Accepted
                                </span>
                                <span class="stat-badge pending">
                                    <i class="fas fa-clock"></i> {{ $event->guests->where('rsvp_status', 'pending')->count() }} Pending
                                </span>
                                <span class="stat-badge declined">
                                    <i class="fas fa-times"></i> {{ $event->guests->where('rsvp_status', 'declined')->count() }} Declined
                                </span>
                            </div>
                        </div>
                        <div class="guests-actions">
                            <button onclick="exportGuestsToPDF()" class="btn-export-pdf">
                                <i class="fas fa-file-pdf"></i> Export PDF
                            </button>
                            <button onclick="exportGuestsToExcel()" class="btn-export-excel">
                                <i class="fas fa-file-excel"></i> Export Excel
                            </button>
                            <a href="{{ route('client.guests.create', $event->id) }}" class="btn-add-guest">
                                <i class="fas fa-user-plus"></i> Add Guest
                            </a>
                        </div>
                    </div>

                    @if($event->guests->count() > 0)
                        <div class="guests-table-container">
                            <table class="guests-table-luxury" id="guestsTable">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Name</th>
                                        <th>Email</th>
                                        <th>Phone</th>
                                        <th>RSVP Status</th>
                                        <th>Plus One</th>
                                        <th>Dietary</th>
                                        <th>Invitation</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($event->guests as $index => $guest)
                                        <tr class="guest-row {{ $guest->rsvp_status }}">
                                            <td class="guest-number">{{ $index + 1 }}</td>
                                            <td class="guest-name">
                                                <div class="name-with-avatar">
                                                    <div class="guest-avatar-small">
                                                        {{ strtoupper(substr($guest->name, 0, 1)) }}
                                                    </div>
                                                    <strong>{{ $guest->name }}</strong>
                                                </div>
                                            </td>
                                            <td class="guest-email">{{ $guest->email }}</td>
                                            <td class="guest-phone">{{ $guest->phone ?? 'N/A' }}</td>
                                            <td class="guest-status">
                                                <span class="status-pill {{ $guest->rsvp_status }}">
                                                    @if($guest->rsvp_status === 'accepted')
                                                        <i class="fas fa-check-circle"></i> Accepted
                                                    @elseif($guest->rsvp_status === 'declined')
                                                        <i class="fas fa-times-circle"></i> Declined
                                                    @else
                                                        <i class="fas fa-clock"></i> Pending
                                                    @endif
                                                </span>
                                            </td>
                                            <td class="guest-plus-one">
                                                @if($guest->plus_one_allowed)
                                                    <span class="plus-one-badge">
                                                        <i class="fas fa-user-plus"></i> Allowed
                                                    </span>
                                                @else
                                                    <span class="no-plus-one">—</span>
                                                @endif
                                            </td>
                                            <td class="guest-dietary">
                                                {{ $guest->dietary_restrictions ?? '—' }}
                                            </td>
                                            <td class="guest-invitation">
                                                @if($guest->invitation_sent)
                                                    <span class="invitation-sent">
                                                        <i class="fas fa-check"></i> Sent
                                                    </span>
                                                    <small class="sent-date">{{ $guest->invitation_sent_at->format('M d') }}</small>
                                                @else
                                                    <span class="invitation-not-sent">Not sent</span>
                                                @endif
                                            </td>
                                            <td class="guest-actions">
                                                <div class="action-buttons">
                                                    @if(!$guest->invitation_sent)
                                                        <button onclick="resendInvitation({{ $guest->id }})" class="btn-action send" title="Send Invitation">
                                                            <i class="fas fa-paper-plane"></i>
                                                        </button>
                                                    @else
                                                        <button onclick="resendInvitation({{ $guest->id }})" class="btn-action resend" title="Resend">
                                                            <i class="fas fa-redo"></i>
                                                        </button>
                                                    @endif
                                                    <form method="POST" action="{{ route('client.guests.destroy', $guest->id) }}" style="display: inline;">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="btn-action delete" onclick="return confirm('Remove this guest?')" title="Delete">
                                                            <i class="fas fa-trash"></i>
                                                        </button>
                                                    </form>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="empty-state-small">
                            <i class="fas fa-users"></i>
                            <p>No guests added yet</p>
                            <a href="{{ route('client.guests.create', $event->id) }}" class="btn-primary-gradient">
                                <i class="fas fa-user-plus"></i> Add Your First Guest
                            </a>
                        </div>
                    @endif
                </div>

        {{-- Messages Tab --}}
        <div class="tab-content" id="messages-tab">
            <div class="messages-container">
                <div class="messages-header">
                    <h3>Event Messages</h3>
                    <p>Chat with your planner about this event</p>
                </div>
                
                <div class="messages-box" id="messagesBox">
                    {{-- Messages will load here --}}
                </div>

                <div class="message-input-container">
                    <form id="messageForm" class="message-form">
                        @csrf
                        <input type="hidden" name="event_id" value="{{ $event->id }}">
                        <textarea name="message" id="messageInput" placeholder="Type your message..." rows="2"></textarea>
                        <button type="submit" class="btn-send-message">
                            <i class="fas fa-paper-plane"></i> Send
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
    @if($event->status === 'completed' && !$event->rating)
<div class="rating-container">
    <div class="rating-card-luxury">
        <div class="rating-header">
            <div class="rating-icon">
                <i class="fas fa-star"></i>
            </div>
            <div>
                <h3>Rate Your Experience</h3>
                <p>How was your event with the planner?</p>
            </div>
        </div>
        <div class="rating-stars-container">
            <div class="stars-group" id="starsGroup">
                @for($i = 1; $i <= 10; $i++)
                    <span class="star-rating-item" data-value="{{ $i }}">★</span>
                @endfor
            </div>
            <div class="rating-feedback" id="ratingFeedback">Click a star to rate</div>
        </div>
        <button id="submitRatingBtn" class="btn-submit-rating">Submit Rating</button>
    </div>
</div>
@endif




   


@endsection

<style>
.rating-container {
    margin-top: 40px;
}
.rating-card-luxury {
    background: white;
    border-radius: 28px;
    padding: 30px;
    box-shadow: 0 12px 32px rgba(0,0,0,0.08);
    border: 1px solid var(--cream, #EFE7DA);
    transition: 0.3s;
}
.rating-header {
    display: flex;
    align-items: center;
    gap: 18px;
    margin-bottom: 24px;
}
.rating-icon {
    width: 60px;
    height: 60px;
    background: linear-gradient(135deg, var(--coral, #E19184), var(--berry, #C63E4E));
    border-radius: 20px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 28px;
    color: white;
}
.rating-header h3 {
    font-size: 24px;
    font-weight: 800;
    color: var(--green, #475B35);
    margin-bottom: 4px;
}
.rating-header p {
    font-size: 14px;
    color: #7F8C8D;
    margin: 0;
}
.rating-stars-container {
    text-align: center;
    padding: 20px 0;
}
.stars-group {
    display: flex;
    justify-content: center;
    gap: 12px;
    margin-bottom: 15px;
    flex-wrap: wrap;
}
.star-rating-item {
    font-size: 36px;
    cursor: pointer;
    color: #ddd;
    transition: all 0.2s ease;
}
.star-rating-item:hover,
.star-rating-item.active {
    color: #FFD700;
    text-shadow: 0 0 8px rgba(255,215,0,0.5);
    transform: scale(1.1);
}
.rating-feedback {
    font-size: 14px;
    color: #7F8C8D;
    margin-top: 10px;
}
.btn-submit-rating {
    background: linear-gradient(135deg, var(--coral, #E19184), var(--berry, #C63E4E));
    color: white;
    border: none;
    padding: 14px 28px;
    border-radius: 50px;
    font-weight: 800;
    font-size: 15px;
    cursor: pointer;
    width: 100%;
    transition: all 0.3s;
}
.btn-submit-rating:hover {
    transform: translateY(-3px);
    box-shadow: 0 8px 20px rgba(225,145,132,0.4);
}
</style>

@push('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf-autotable/3.5.31/jspdf.plugin.autotable.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.5/xlsx.full.min.js"></script>

<script>
// ============================================
// TAB SWITCHING
// ============================================

document.querySelectorAll('.event-tab').forEach(tab => {
    tab.addEventListener('click', function(e) {
        e.preventDefault();
        
        // Remove active class from all tabs
        document.querySelectorAll('.event-tab').forEach(t => t.classList.remove('active'));
        document.querySelectorAll('.tab-content').forEach(c => c.classList.remove('active'));
        
        // Add active class to clicked tab
        this.classList.add('active');
        const tabId = this.dataset.tab;
        document.getElementById(tabId + '-tab').classList.add('active');
        
        // Load messages if messages tab
        if (tabId === 'messages') {
            loadMessages();
            startMessagePolling();
        } else {
            stopMessagePolling();
        }
    });
});

// ============================================
// MESSAGES FUNCTIONALITY
// ============================================

let messagePollingInterval = null;

async function loadMessages() {
    try {
        const response = await fetch('/client/events/{{ $event->id }}/messages', {
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
            }
        });
        
        if (!response.ok) {
            throw new Error('Failed to load messages');
        }
        
        const data = await response.json();
        displayMessages(data.messages);
        
    } catch (error) {
        console.error('Error loading messages:', error);
        document.getElementById('messagesContainer').innerHTML = `
            <div class="error-message">
                <i class="fas fa-exclamation-triangle"></i>
                <p>Failed to load messages. Please refresh the page.</p>
            </div>
        `;
    }
}

function displayMessages(messages) {
    const container = document.getElementById('messagesContainer');
    container.innerHTML = '';
    
    if (messages.length === 0) {
        container.innerHTML = `
            <div class="empty-messages">
                <i class="fas fa-comments"></i>
                <p>No messages yet. Start the conversation!</p>
            </div>
        `;
        return;
    }
    
    messages.forEach(message => {
        const messageEl = createMessageElement(message);
        container.appendChild(messageEl);
    });
    
    // Scroll to bottom
    container.scrollTop = container.scrollHeight;
}

function createMessageElement(message) {
    const isSent = message.sender_id === {{ Auth::id() }};
    const div = document.createElement('div');
    div.className = `message-item ${isSent ? 'sent' : 'received'}`;
    
    const initial = message.sender_name.charAt(0).toUpperCase();
    const time = new Date(message.created_at).toLocaleTimeString('en-US', {
        hour: 'numeric',
        minute: '2-digit',
        hour12: true
    });
    
    div.innerHTML = `
        <div class="message-avatar">${initial}</div>
        <div class="message-content">
            <div class="message-text">${escapeHtml(message.message)}</div>
            <div class="message-time">${time}</div>
        </div>
    `;
    
    return div;
}

async function sendMessage(e) {
    e.preventDefault();
    
    const input = document.getElementById('messageInput');
    const message = input.value.trim();
    
    if (!message) return;
    
    try {
        const response = await fetch('/client/events/{{ $event->id }}/messages', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'X-Requested-With': 'XMLHttpRequest',
            },
            body: JSON.stringify({ message })
        });
        
        if (!response.ok) {
            const errorData = await response.json();
            throw new Error(errorData.message || 'Failed to send message');
        }
        
        input.value = '';
        input.style.height = 'auto';
        
        await loadMessages();
        
    } catch (error) {
        console.error('Error sending message:', error);
        alert('Failed to send message: ' + error.message);
    }
}

function startMessagePolling() {
    if (messagePollingInterval) {
        clearInterval(messagePollingInterval);
    }
    messagePollingInterval = setInterval(loadMessages, 5000);
}

function stopMessagePolling() {
    if (messagePollingInterval) {
        clearInterval(messagePollingInterval);
        messagePollingInterval = null;
    }
}

function escapeHtml(text) {
    const div = document.createElement('div');
    div.textContent = text;
    return div.innerHTML;
}

// Message form
document.getElementById('messageForm')?.addEventListener('submit', sendMessage);

// Auto-resize textarea
document.getElementById('messageInput')?.addEventListener('input', function() {
    this.style.height = 'auto';
    this.style.height = (this.scrollHeight) + 'px';
});

// ============================================
// GUEST EXPORT FUNCTIONS
// ============================================

function exportGuestsToPDF() {
    const { jsPDF } = window.jspdf;
    const doc = new jsPDF();
    
    doc.setFontSize(18);
    doc.setTextColor(71, 91, 53);
    doc.text('{{ $event->name }} - Guest List', 14, 20);
    
    doc.setFontSize(10);
    doc.setTextColor(100);
    doc.text('Generated on: ' + new Date().toLocaleDateString(), 14, 28);
    
    const tableData = [];
    const rows = document.querySelectorAll('.guests-table-luxury tbody tr');
    
    rows.forEach(row => {
        const cells = row.querySelectorAll('td');
        tableData.push([
            cells[0].textContent.trim(),
            cells[1].textContent.trim(),
            cells[2].textContent.trim(),
            cells[3].textContent.trim(),
            cells[4].textContent.trim(),
            cells[5].textContent.trim(),
            cells[6].textContent.trim(),
        ]);
    });
    
    doc.autoTable({
        startY: 35,
        head: [['#', 'Name', 'Email', 'Phone', 'Status', 'Plus One', 'Dietary']],
        body: tableData,
        theme: 'grid',
        headStyles: {
            fillColor: [71, 91, 53],
            textColor: 255,
            fontStyle: 'bold'
        },
        styles: {
            fontSize: 9,
            cellPadding: 4
        },
        alternateRowStyles: {
            fillColor: [239, 231, 218]
        }
    });
    
    doc.save('{{ Str::slug($event->name) }}-guests.pdf');
}

function exportGuestsToExcel() {
    const table = document.getElementById('guestsTable');
    const wb = XLSX.utils.table_to_book(table, {sheet: 'Guests'});
    XLSX.writeFile(wb, '{{ Str::slug($event->name) }}-guests.xlsx');
}

async function resendInvitation(guestId) {
    if (!confirm('Resend invitation email to this guest?')) return;
    
    try {
        const response = await fetch(`/client/guests/${guestId}/resend`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            }
        });
        
        const data = await response.json();
        
        if (data.success) {
            alert('Invitation sent successfully!');
            location.reload();
        } else {
            alert('Failed to send invitation.');
        }
        
    } catch (error) {
        console.error('Error:', error);
        alert('An error occurred.');
    }
}


let selectedRating = 0;
document.querySelectorAll('.star-rating-item').forEach(star => {
    star.addEventListener('click', function() {
        selectedRating = parseInt(this.getAttribute('data-value'));
        document.querySelectorAll('.star-rating-item').forEach(s => s.classList.remove('active'));
        for(let i=0; i<selectedRating; i++) {
            document.querySelectorAll('.star-rating-item')[i].classList.add('active');
        }
        document.getElementById('ratingFeedback').innerHTML = `You selected ${selectedRating} out of 10 stars`;
    });
});
document.getElementById('submitRatingBtn')?.addEventListener('click', function() {
    if(selectedRating === 0) {
        alert('Please select a rating (1-10 stars).');
        return;
    }
    fetch('{{ route("client.rating.store", $event->id) }}', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
        body: JSON.stringify({ score: selectedRating, review: '' })
    })
    .then(res => res.json())
    .then(data => {
        if(data.success) {
            alert('Thank you for rating!');
            location.reload();
        } else {
            alert(data.message || 'Error submitting rating');
        }
    })
    .catch(err => alert('Error: ' + err.message));
});

console.log('✅ Event show page scripts loaded');
</script>
@endpush