// ============================================
// PLANNER MESSAGES - COMPLETE IMPLEMENTATION
// ============================================

let currentEventId = null;
let messageCheckInterval = null;

document.addEventListener('DOMContentLoaded', function() {
    console.log('💬 Messages System Initialized');
    initializeMessageForm();
});

window.loadMessagesFromCard = function(element) {
    const eventId = element.dataset.eventId;
    const eventName = element.dataset.eventName;
    const clientName = element.dataset.clientName;
    
    loadMessages(eventId, eventName, clientName);
}

// ============================================
// LOAD MESSAGES FOR EVENT
// ============================================

window.loadMessages = async function(eventId, eventName, clientName) {
    try {
        currentEventId = eventId;
        
        // Update active state
        document.querySelectorAll('.event-card-epic').forEach(card => {
            card.classList.remove('active');
        });
        // Note: event.target may not be the card itself; we use the clicked card from the element
        const clickedCard = document.querySelector(`.event-card-epic[data-event-id="${eventId}"]`);
        if (clickedCard) clickedCard.classList.add('active');
        
        // Show chat state
        const emptyState = document.getElementById('emptyState');
        const chatState = document.getElementById('chatState');
        if (emptyState) emptyState.style.display = 'none';
        if (chatState) chatState.style.display = 'flex';
        
        // Update header
        document.getElementById('eventName').textContent = eventName;
        document.getElementById('clientName').textContent = clientName;
        document.getElementById('clientInitials').textContent = getInitials(clientName);
        
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
                    const response = await fetch(`/planner/events/${currentEventId}/messages`, {
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
        const response = await fetch(`/planner/messages/${eventId}`, {
            headers: {
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            }
        });
        
        if (!response.ok) {
            const errorText = await response.text();
            console.error('Response error:', errorText);
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
    
    // Scroll to bottom
    scrollToBottom();
}

function createMessageBubble(msg) {
    const div = document.createElement('div');
    div.className = `message-bubble ${msg.is_mine ? 'mine' : 'theirs'}`;
    
    // Use a fallback name
    const senderName = msg.sender_name || (msg.is_mine ? 'You' : 'User');
    
    div.innerHTML = `
        <div class="message-content">
            ${!msg.is_mine ? `<div class="message-sender">${escapeHtml(senderName)}</div>` : ''}
            <p class="message-text">${escapeHtml(msg.message)}</p>
            <span class="message-time">${msg.created_at || 'Just now'}</span>
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
    
    // Auto-resize textarea
    textarea?.addEventListener('input', function() {
        this.style.height = 'auto';
        this.style.height = Math.min(this.scrollHeight, 150) + 'px';
    });
    
    // Submit on Enter (Shift+Enter for new line)
    textarea?.addEventListener('keydown', function(e) {
        if (e.key === 'Enter' && !e.shiftKey) {
            e.preventDefault();
            form.dispatchEvent(new Event('submit'));
        }
    });
    
    form?.addEventListener('submit', handleSendMessage);
}

async function handleSendMessage(e) {
    e.preventDefault();
    
    if (!currentEventId) {
        showNotification('Please select an event first', 'error');
        return;
    }
    
    const textarea = document.getElementById('messageInput');
    const message = textarea.value.trim();
    
    if (!message) return;
    
    // Disable send button to prevent double-sending
    const sendBtn = document.querySelector('.send-btn-epic');
    if (sendBtn) sendBtn.disabled = true;
    
    try {
        const response = await fetch(`/planner/messages/${currentEventId}`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'X-Requested-With': 'XMLHttpRequest'
            },
            body: JSON.stringify({ message })
        });
        
        if (!response.ok) throw new Error('Failed to send message');
        
        const data = await response.json();
        
        if (data.success) {
            textarea.value = '';
            textarea.style.height = 'auto';
            
            const messagesList = document.getElementById('messagesList');
            const noMessagesState = messagesList.querySelector('.no-messages-state');
            if (noMessagesState) noMessagesState.remove();
            
            const messageDiv = createMessageBubble(data.message);
            messagesList.appendChild(messageDiv);
            scrollToBottom();
        } else {
            showNotification(data.message || 'Failed to send message', 'error');
        }
        
    } catch (error) {
        console.error('Send message error:', error);
        showNotification('Failed to send message', 'error');
    } finally {
        if (sendBtn) sendBtn.disabled = false;
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
        const response = await fetch(`/planner/messages/${eventId}`, {
            headers: {
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            }
        });
        
        if (!response.ok) return;
        
        const data = await response.json();
        
        if (data.success) {
            const messagesList = document.getElementById('messagesList');
            const currentScrollPosition = messagesList.scrollTop;
            const wasAtBottom = messagesList.scrollHeight - messagesList.scrollTop <= messagesList.clientHeight + 50;
            
            displayMessages(data.messages);
            
            // Only scroll to bottom if user was already at bottom
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
        messagesList.scrollTo({
            top: messagesList.scrollHeight,
            behavior: 'smooth'
        });
    }
}

function getInitials(name) {
    if (!name) return 'CL';
    const parts = name.split(' ');
    if (parts.length >= 2) {
        return parts[0][0] + parts[1][0];
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

// Add CSS for no messages state
const style = document.createElement('style');
style.textContent = `
    .no-messages-state {
        text-align: center;
        padding: 80px 30px;
        color: #95A5A6;
    }
    
    .no-messages-icon {
        width: 100px;
        height: 100px;
        background: linear-gradient(135deg, #EFE7DA, #F8F9FA);
        border-radius: 50%;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-size: 48px;
        margin-bottom: 20px;
    }
    
    .no-messages-state p {
        font-size: 16px;
        font-weight: 600;
        color: #7F8C8D;
    }
    
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

console.log('✅ Messages JavaScript Fully Loaded');