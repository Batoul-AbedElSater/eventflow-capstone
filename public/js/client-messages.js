// ============================================
// CLIENT MESSAGES - COMPLETE IMPLEMENTATION
// ============================================

let currentEventId = null;
let messageCheckInterval = null;

document.addEventListener('DOMContentLoaded', function() {
    console.log('💬 Client Messages System Initialized');
    initializeMessageForm();
});

// ============================================
// LOAD MESSAGES - FIX CARD CLICK
// ============================================

window.loadMessagesFromCard = function(element) {
    const eventId = element.dataset.eventId;
    const eventName = element.dataset.eventName;
    const plannerName = element.dataset.plannerName;
    
    loadMessages(eventId, eventName, plannerName);
}

window.loadMessages = async function(eventId, eventName, plannerName) {
    try {
        currentEventId = eventId;
        
        // Update active state
        document.querySelectorAll('.event-card-epic').forEach(card => {
            card.classList.remove('active');
        });
        
        // Find and mark active card
        const clickedCard = document.querySelector(`[data-event-id="${eventId}"]`);
        if (clickedCard) {
            clickedCard.classList.add('active');
        }
        
        // Show chat state
        const emptyState = document.getElementById('emptyState');
        const chatState = document.getElementById('chatState');
        
        if (emptyState) emptyState.style.display = 'none';
        if (chatState) chatState.style.display = 'flex';
        
        // Update header
        const eventNameEl = document.getElementById('eventName');
        const plannerNameEl = document.getElementById('plannerName');
        const plannerInitialsEl = document.getElementById('plannerInitials');
        
        if (eventNameEl) eventNameEl.textContent = eventName;
        if (plannerNameEl) plannerNameEl.textContent = plannerName;
        if (plannerInitialsEl) plannerInitialsEl.textContent = getInitials(plannerName);
        
        // ========== ADD DELETE CHAT BUTTON HANDLER ==========
        const deleteBtn = document.getElementById('deleteChatBtn');
        if (deleteBtn) {
            // Remove previous listener to avoid duplicates
            const newDeleteBtn = deleteBtn.cloneNode(true);
            deleteBtn.parentNode.replaceChild(newDeleteBtn, deleteBtn);
            
            newDeleteBtn.addEventListener('click', async () => {
                if (!currentEventId) return;
                const confirmed = confirm('⚠️ Delete all messages in this conversation? This action cannot be undone.');
                if (!confirmed) return;
                
                try {
                    const response = await fetch(`/client/events/${currentEventId}/messages`, {
                        method: 'DELETE',
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                            'Accept': 'application/json',
                            'Content-Type': 'application/json'
                        }
                    });
                    const data = await response.json();
                    if (data.success) {
                        const messagesList = document.getElementById('messagesList');
                        messagesList.innerHTML = `
                            <div class="no-messages-state">
                                <div class="no-messages-icon"><i class="fas fa-inbox"></i></div>
                                <p>Chat cleared. Start fresh!</p>
                            </div>
                        `;
                        showNotification('Chat cleared successfully', 'success');
                    } else {
                        showNotification(data.message || 'Failed to clear chat', 'error');
                    }
                } catch (error) {
                    console.error('Delete chat error:', error);
                    showNotification('Error clearing chat', 'error');
                }
            });
        }
        // ========== END DELETE BUTTON HANDLER ==========
        
        // Fetch messages
        const response = await fetch(`/client/events/${eventId}/messages`, {
            headers: {
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            }
        });
        
        if (!response.ok) {
            throw new Error('Failed to load messages');
        }
        
        const data = await response.json();
        
        if (data.success) {
            displayMessages(data.messages);
            
            // Start auto-refresh
            if (messageCheckInterval) {
                clearInterval(messageCheckInterval);
            }
            messageCheckInterval = setInterval(() => refreshMessages(eventId), 5000);
        } else {
            showNotification(data.message || 'Failed to load messages', 'error');
        }
        
    } catch (error) {
        console.error('Load messages error:', error);
        showNotification('Failed to load messages', 'error');
    }
}

// ============================================
// DISPLAY MESSAGES
// ============================================

function displayMessages(messages) {
    const messagesList = document.getElementById('messagesList');
    
    if (!messagesList) {
        console.error('Messages list element not found!');
        return;
    }
    
    messagesList.innerHTML = '';
    
    if (messages.length === 0) {
        messagesList.innerHTML = `
            <div class="no-messages-state">
                <div class="no-messages-icon">
                    <i class="fas fa-inbox"></i>
                </div>
                <p>No messages yet. Start the conversation!</p>
            </div>
        `;
        return;
    }
    
    messages.forEach(msg => {
        const messageDiv = createMessageBubble(msg);
        messagesList.appendChild(messageDiv);
    });
    
    scrollToBottom();
}

function createMessageBubble(msg) {
    const div = document.createElement('div');
    div.className = `message-bubble ${msg.is_mine ? 'mine' : 'theirs'}`;
    
    div.innerHTML = `
        <div class="message-content">
            ${!msg.is_mine ? `<div class="message-sender">${escapeHtml(msg.sender_name)}</div>` : ''}
            <p class="message-text">${escapeHtml(msg.message)}</p>
            <span class="message-time">${msg.created_at}</span>
        </div>
    `;
    
    return div;
}

// ============================================
// SEND MESSAGE
// ============================================

function initializeMessageForm() {
    const form = document.getElementById('messageForm');
    const textarea = document.getElementById('messageInput');
    
    if (!form || !textarea) {
        console.log('Message form elements not found yet');
        return;
    }
    
    // Auto-resize textarea
    textarea.addEventListener('input', function() {
        this.style.height = 'auto';
        this.style.height = Math.min(this.scrollHeight, 150) + 'px';
    });
    
    // Submit on Enter (Shift+Enter for new line)
    textarea.addEventListener('keydown', function(e) {
        if (e.key === 'Enter' && !e.shiftKey) {
            e.preventDefault();
            form.dispatchEvent(new Event('submit'));
        }
    });
    
    form.addEventListener('submit', handleSendMessage);
}

async function handleSendMessage(e) {
    e.preventDefault();
    
    if (!currentEventId) {
        showNotification('Please select an event first', 'error');
        return;
    }
    
    const textarea = document.getElementById('messageInput');
    const messagesList = document.getElementById('messagesList');
    
    if (!textarea || !messagesList) {
        console.error('Required elements not found');
        return;
    }
    
    const message = textarea.value.trim();
    
    if (!message) {
        return;
    }
    
    try {
        const response = await fetch(`/client/events/${currentEventId}/messages`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'X-Requested-With': 'XMLHttpRequest'
            },
            body: JSON.stringify({ message })
        });
        
        if (!response.ok) {
            throw new Error('Failed to send message');
        }
        
        const data = await response.json();
        
        if (data.success) {
            // Clear input
            textarea.value = '';
            textarea.style.height = 'auto';
            
            // Remove "no messages" state if exists
            const noMessagesState = messagesList.querySelector('.no-messages-state');
            if (noMessagesState) {
                noMessagesState.remove();
            }
            
            // Add message to list
            const messageDiv = createMessageBubble(data.message);
            messagesList.appendChild(messageDiv);
            
            scrollToBottom();
            
        } else {
            showNotification(data.message || 'Failed to send message', 'error');
        }
        
    } catch (error) {
        console.error('Send message error:', error);
        showNotification('Failed to send message', 'error');
    }
}

// ============================================
// AUTO-REFRESH MESSAGES
// ============================================

async function refreshMessages(eventId) {
    if (!currentEventId || currentEventId !== eventId) {
        return;
    }
    
    try {
        const response = await fetch(`/client/events/${eventId}/messages`, {
            headers: {
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            }
        });
        
        if (!response.ok) return;
        
        const data = await response.json();
        
        if (data.success) {
            const messagesList = document.getElementById('messagesList');
            if (!messagesList) return;
            
            const currentScrollPosition = messagesList.scrollTop;
            const wasAtBottom = messagesList.scrollHeight - messagesList.scrollTop <= messagesList.clientHeight + 50;
            
            displayMessages(data.messages);
            
            if (wasAtBottom) {
                scrollToBottom();
            } else {
                messagesList.scrollTop = currentScrollPosition;
            }
        }
        
    } catch (error) {
        console.error('Refresh messages error:', error);
    }
}

// ============================================
// UTILITY FUNCTIONS
// ============================================

function scrollToBottom() {
    const messagesList = document.getElementById('messagesList');
    if (messagesList) {
        setTimeout(() => {
            messagesList.scrollTo({
                top: messagesList.scrollHeight,
                behavior: 'smooth'
            });
        }, 100);
    }
}

function getInitials(name) {
    if (!name) return 'PL';
    const parts = name.split(' ');
    if (parts.length >= 2) {
        return (parts[0][0] + parts[1][0]).toUpperCase();
    }
    return name.substring(0, 2).toUpperCase();
}

function escapeHtml(text) {
    const div = document.createElement('div');
    div.textContent = text;
    return div.innerHTML;
}

function showNotification(message, type = 'success') {
    const notification = document.createElement('div');
    notification.className = `notification-toast ${type}`;
    
    const bgColor = type === 'success' ? '#475B35' : type === 'error' ? '#C63E4E' : '#E19184';
    
    notification.innerHTML = `
        <i class="fas fa-${type === 'success' ? 'check-circle' : 'exclamation-circle'}"></i>
        <span>${message}</span>
    `;
    
    notification.style.cssText = `
        position: fixed;
        top: 30px;
        right: 30px;
        background: ${bgColor};
        color: white;
        padding: 18px 28px;
        border-radius: 15px;
        display: flex;
        align-items: center;
        gap: 12px;
        font-weight: 700;
        font-size: 15px;
        box-shadow: 0 10px 35px rgba(0,0,0,0.3);
        z-index: 100000;
        animation: slideInRight 0.4s ease-out, slideOutRight 0.4s ease-out 2.6s;
    `;
    
    document.body.appendChild(notification);
    setTimeout(() => notification.remove(), 3000);
}

const style = document.createElement('style');
style.textContent = `
    @keyframes slideInRight {
        from { transform: translateX(400px); opacity: 0; }
        to { transform: translateX(0); opacity: 1; }
    }
    
    @keyframes slideOutRight {
        from { transform: translateX(0); opacity: 1; }
        to { transform: translateX(400px); opacity: 0; }
    }
`;
document.head.appendChild(style);

console.log('✅ Client Messages JavaScript Fully Loaded');