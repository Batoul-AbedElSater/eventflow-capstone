document.addEventListener('DOMContentLoaded', function() {
    const messageForm = document.getElementById('message-form');
    const messageInput = document.getElementById('message-input');
    const messagesArea = document.getElementById('messages-area');
    
    // Auto-scroll to bottom on load
    scrollToBottom();
    
    // Auto-resize textarea
    messageInput.addEventListener('input', function() {
        this.style.height = 'auto';
        this.style.height = (this.scrollHeight) + 'px';
    });
    
    // Submit message
    messageForm.addEventListener('submit', async function(e) {
        e.preventDefault();
        
        const messageText = messageInput.value.trim();
        
        if (!messageText) return;
        
        // Disable input while sending
        messageInput.disabled = true;
        
        try {
            const response = await fetch(`/client/messages/${THREAD_ID}`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': CSRF_TOKEN,
                },
                body: JSON.stringify({
                    message: messageText
                })
            });
            
            const data = await response.json();
            
            if (data.success) {
                // Add message to UI
                addMessageToUI(data.message);
                
                // Clear input
                messageInput.value = '';
                messageInput.style.height = 'auto';
                
                // Scroll to bottom
                scrollToBottom();
            } else {
                alert('Failed to send message');
            }
        } catch (error) {
            console.error('Error:', error);
            alert('Failed to send message');
        } finally {
            messageInput.disabled = false;
            messageInput.focus();
        }
    });
    
    // Add message to UI
    function addMessageToUI(message) {
        const isSent = message.sender_id === CURRENT_USER_ID;
        const messageDiv = document.createElement('div');
        messageDiv.className = `message ${isSent ? 'sent' : 'received'}`;
        
        const time = new Date(message.created_at);
        const timeString = time.toLocaleTimeString('en-US', { 
            hour: 'numeric', 
            minute: '2-digit',
            hour12: true 
        });
        
        messageDiv.innerHTML = `
            <div class="message-avatar">
                ${message.sender.name.charAt(0).toUpperCase()}
            </div>
            <div class="message-content">
                <div class="message-header">
                    <span class="sender-name">${message.sender.name}</span>
                    <span class="message-time">${timeString}</span>
                </div>
                <div class="message-text">
                    ${escapeHtml(message.message_text)}
                </div>
            </div>
        `;
        
        // Remove "no messages" if it exists
        const noMessages = messagesArea.querySelector('.no-messages');
        if (noMessages) {
            noMessages.remove();
        }
        
        messagesArea.appendChild(messageDiv);
    }
    
    // Scroll to bottom
    function scrollToBottom() {
        messagesArea.scrollTop = messagesArea.scrollHeight;
    }
    
    // Escape HTML
    function escapeHtml(text) {
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    }
    
    console.log('✅ Messages loaded!');
});