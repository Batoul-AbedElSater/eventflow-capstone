// Planner Dashboard JavaScript
document.addEventListener('DOMContentLoaded', function() {
    
    // Smooth scroll
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', function (e) {
            e.preventDefault();
            const target = document.querySelector(this.getAttribute('href'));
            if (target) {
                target.scrollIntoView({ behavior: 'smooth' });
            }
        });
    });

    // Task checkbox toggle
    document.querySelectorAll('.task-checkbox').forEach(checkbox => {
        checkbox.addEventListener('change', function() {
            const taskItem = this.closest('.task-item');
            if (this.checked) {
                taskItem.style.opacity = '0.5';
                taskItem.style.textDecoration = 'line-through';
            } else {
                taskItem.style.opacity = '1';
                taskItem.style.textDecoration = 'none';
            }
        });
    });

    // Auto-dismiss alerts
    setTimeout(() => {
        document.querySelectorAll('.alert-success, .alert-danger').forEach(alert => {
            alert.style.transition = 'opacity 0.5s';
            alert.style.opacity = '0';
            setTimeout(() => alert.remove(), 500);
        });
    }, 5000);

    // ============================================
    // VOICE COMMANDER
    // ============================================
    const voiceBtn = document.getElementById('voiceCommanderBtn');
   const voiceModal = document.getElementById('voiceCommanderModal');
    const voiceCloseBtn = document.getElementById('voiceCloseBtn');
    const voiceActionBtn = document.getElementById('voiceActionBtn');
    const voiceAnimation = document.querySelector('.voice-animation');
    const voiceStatusText = document.getElementById('voiceStatusText');
    const voiceSubtext = document.getElementById('voiceSubtext');
    const transcriptText = document.getElementById('transcriptText');
    const responseText = document.getElementById('responseText');

    let recognition = null;
    let isListening = false;


    // Check if browser supports speech recognition
    if ('webkitSpeechRecognition' in window || 'SpeechRecognition' in window) {
        const SpeechRecognition = window.SpeechRecognition || window.webkitSpeechRecognition;
        recognition = new SpeechRecognition();
        recognition.continuous = false;
        recognition.interimResults = false;
        recognition.lang = 'en-US';
    }

    // Open modal
    if (voiceBtn) {
        voiceBtn.addEventListener('click', () => {
            voiceModal.classList.add('active');
        });
    }

    // Close modal
    if (voiceCloseBtn) {
        voiceCloseBtn.addEventListener('click', () => {
            voiceModal.classList.remove('active');
            stopListening();
        });
    }

    // Start/Stop listening
    if (voiceActionBtn) {
        voiceActionBtn.addEventListener('click', () => {
            if (isListening) {
                stopListening();
            } else {
                startListening();
            }
        });
    }

    function startListening() {
        if (!recognition) {
            responseText.textContent = 'Sorry, your browser doesn\'t support voice recognition. Try Chrome or Edge.';
            return;
        }

        isListening = true;
        voiceBtn.classList.add('listening');
        voiceActionBtn.classList.add('listening');
        voiceActionBtn.innerHTML = '<i class="fas fa-stop"></i><span>Stop Listening</span>';
        voiceAnimation.classList.add('listening');
        voiceStatusText.textContent = 'Listening...';
        voiceSubtext.textContent = 'Speak your command now';
        transcriptText.textContent = '—';
        responseText.textContent = '—';

        recognition.start();
    }

    function stopListening() {
        if (recognition && isListening) {
            recognition.stop();
        }
        isListening = false;
        voiceBtn.classList.remove('listening');
        voiceActionBtn.classList.remove('listening');
        voiceActionBtn.innerHTML = '<i class="fas fa-microphone"></i><span>Start Listening</span>';
        voiceAnimation.classList.remove('listening');
        voiceStatusText.textContent = 'Click to start listening...';
        voiceSubtext.textContent = 'Try: "Show today\'s tasks" or "What\'s my schedule?"';
    }

    // Handle speech recognition results
    if (recognition) {
        recognition.onresult = (event) => {
            const command = event.results[0][0].transcript.toLowerCase();
            transcriptText.textContent = command;
            processVoiceCommand(command);
            stopListening();
        };

        recognition.onerror = (event) => {
            console.error('Speech recognition error:', event.error);
            responseText.textContent = 'Oops! I couldn\'t hear you clearly. Please try again.';
            stopListening();
        };
    }

    // Process voice commands
    // Process voice commands
function processVoiceCommand(command) {
    let response = '';
    let action = null;

    // Dashboard
    if (command.includes('dashboard') || command.includes('home')) {
        response = 'Going to dashboard...';
        action = () => window.location.href = '/planner/dashboard';
    }
    // Event Requests
    else if (command.includes('request')) {
        response = 'Opening event requests...';
        action = () => window.location.href = '/planner/requests';
    }
    // My Events
    else if (command.includes('event') || command.includes('my event')) {
        response = 'Opening my events...';
        action = () => window.location.href = '/planner/events';
    }
    // Tasks
    else if (command.includes('task')) {
        response = 'Opening tasks...';
        action = () => window.location.href = '/planner/tasks';
    }
    // Messages
    else if (command.includes('message')) {
        response = 'Opening messages...';
        action = () => window.location.href = '/planner/messages';
    }
    // Analytics
    else if (command.includes('analytic')) {
        response = 'Opening analytics...';
        action = () => window.location.href = '/planner/analytics';
    }
    // Calendar (scroll on dashboard)
    else if (command.includes('calendar') || command.includes('schedule')) {
        response = 'Showing calendar...';
        action = () => {
            voiceModal.classList.remove('active');
            if (window.location.pathname !== '/planner/dashboard') {
                window.location.href = '/planner/dashboard';
            } else {
                document.querySelector('.calendar-section')?.scrollIntoView({ behavior: 'smooth' });
            }
        };
    }
    // Notifications
    else if (command.includes('notification')) {
        response = 'Opening notifications...';
        action = () => {
            voiceModal.classList.remove('active');
            document.getElementById('notificationBellBtn')?.click();
        };
    }
    else {
        response = `I heard "${command}". Try: Dashboard, Requests, Events, Tasks, Messages, Analytics, Calendar, or Notifications.`;
    }

    responseText.textContent = response;

    if (action) {
        setTimeout(action, 1500);
    }
}

    // Command chips click
    document.querySelectorAll('.command-chip').forEach(chip => {
        chip.addEventListener('click', function() {
            const command = this.textContent.replace(/"/g, '').toLowerCase();
            transcriptText.textContent = command;
            processVoiceCommand(command);
        });
    });

    // ============================================
    // MOOD RING DASHBOARD
    // ============================================
    const moodBtn = document.getElementById('moodSelectorBtn');
    const moodModal = document.getElementById('moodModal');
    const moodCloseBtn = document.getElementById('moodCloseBtn');
    const moodOptions = document.querySelectorAll('.mood-option');
    const currentMoodText = document.getElementById('currentMoodText');

    // Load saved mood from localStorage
    const savedMood = localStorage.getItem('dashboardMood') || 'productive';
    applyMood(savedMood);

    // Open mood modal
    if (moodBtn) {
        moodBtn.addEventListener('click', (e) => {
            e.preventDefault();
            console.log('Mood button clicked!');
            moodModal.classList.add('active');
        });
    }

    // Close mood modal
    if (moodCloseBtn) {
        moodCloseBtn.addEventListener('click', (e) => {
            e.preventDefault();
            console.log('Close button clicked!');
            moodModal.classList.remove('active');
        });
    }

    // Click outside to close
    if (moodModal) {
        moodModal.addEventListener('click', (e) => {
            if (e.target === moodModal || e.target.classList.contains('mood-modal-overlay')) {
                moodModal.classList.remove('active');
            }
        });
    }

    // Select mood
    moodOptions.forEach(option => {
        option.addEventListener('click', function(e) {
            e.preventDefault();
            e.stopPropagation();
            
            const mood = this.getAttribute('data-mood');
            console.log('Mood selected:', mood);
            
            // Remove active from all
            moodOptions.forEach(opt => opt.classList.remove('active'));
            
            // Add active to clicked
            this.classList.add('active');
            
            // Apply mood
            applyMood(mood);
            
            // Save to localStorage
            localStorage.setItem('dashboardMood', mood);
            
            // Close modal after 800ms
            setTimeout(() => {
                moodModal.classList.remove('active');
            }, 800);
        });
    });

    function applyMood(mood) {
        console.log('Applying mood:', mood);
        
        // Remove all mood classes
        document.body.classList.remove('mood-productive', 'mood-energized', 'mood-calm', 'mood-creative', 'mood-stressed', 'mood-sleepy');
        
        // Add selected mood class
        document.body.classList.add('mood-' + mood);
        
        // Update button text
        const moodNames = {
            'productive': 'Productive',
            'energized': 'Energized',
            'calm': 'Calm',
            'creative': 'Creative',
            'stressed': 'Stressed',
            'sleepy': 'Sleepy'
        };
        
        if (currentMoodText) {
            currentMoodText.textContent = moodNames[mood] || 'Productive';
        }
        
        // Update active state in modal
        moodOptions.forEach(opt => {
            if (opt.getAttribute('data-mood') === mood) {
                opt.classList.add('active');
            } else {
                opt.classList.remove('active');
            }
        });
    }
    // ============================================
    // NOTIFICATION SYSTEM - COMPLETE REDESIGN
    // ============================================
    const riverContainer = document.getElementById('riverContainer');
    const notificationRiver = document.getElementById('notificationRiver');
    const riverToggleBtn = document.getElementById('riverToggleBtn');
    const notificationBellBtn = document.getElementById('notificationBellBtn');
    const notificationModal = document.getElementById('notificationModal');
    const notifCloseBtn = document.getElementById('notifCloseBtn');
    const headerNotifBadge = document.getElementById('headerNotifBadge');
    const notifModalList = document.getElementById('notifModalList');

    let notifications = [];
    let riverHidden = false;
    let currentFilter = 'all';

    // Fetch notifications from server
    async function fetchNotifications() {
        try {
            const response = await fetch('/planner/notifications');
            const data = await response.json();
            
            notifications = data.notifications;
            updateBadges(data.unread_count);
            renderRiverNotifications();
            
        } catch (error) {
            console.error('Error fetching notifications:', error);
        }
    }

    // Update badges
    function updateBadges(count) {
        if (headerNotifBadge) {
            headerNotifBadge.textContent = count;
            headerNotifBadge.style.display = count > 0 ? 'flex' : 'none';
        }
    }

    // Render notifications in river
    function renderRiverNotifications() {
        if (!riverContainer) return;

        riverContainer.innerHTML = '';

        const groupedNotifications = groupNotifications(notifications.slice(0, 10));

        groupedNotifications.forEach((notification, index) => {
            const bubble = createRiverBubble(notification, index);
            riverContainer.appendChild(bubble);
        });

        // Duplicate for seamless loop
        groupedNotifications.forEach((notification, index) => {
            const bubble = createRiverBubble(notification, index);
            riverContainer.appendChild(bubble);
        });

        riverContainer.style.animation = 'none';
        setTimeout(() => {
            const speed = calculateRiverSpeed();
            riverContainer.style.animation = `flow-left ${speed}s linear infinite`;
        }, 10);
    }

    // Create river bubble
    function createRiverBubble(notification, index) {
        const bubble = document.createElement('div');
        bubble.className = `notification-bubble ${getColorClass(notification.priority)}`;
        bubble.style.animationDelay = `${index * 0.1}s`;

        bubble.innerHTML = `
            <div class="bubble-icon">
                <i class="${getIconClass(notification)}"></i>
            </div>
            <div class="bubble-content">
                <div class="bubble-title">${escapeHtml(notification.title)}</div>
                <div class="bubble-message">${escapeHtml(notification.message)}</div>
                <div class="bubble-time">${getTimeAgo(notification.created_at)}</div>
            </div>
        `;

        bubble.addEventListener('click', () => {
            if (notification.action_url) {
                markNotificationAsRead(notification.id);
                window.location.href = notification.action_url;
            }
        });

        return bubble;
    }

    // Group notifications
    function groupNotifications(notifs) {
        const typeCount = {};
        const grouped = [];

        notifs.forEach(notif => {
            typeCount[notif.type] = (typeCount[notif.type] || 0) + 1;
        });

        const processed = new Set();

        notifs.forEach(notif => {
            if (processed.has(notif.type)) return;

            if (typeCount[notif.type] >= 5) {
                grouped.push({
                    id: `group-${notif.type}`,
                    type: notif.type,
                    priority: notif.priority,
                    title: `${typeCount[notif.type]} ${notif.type} notifications`,
                    message: 'Click to view all',
                    icon: notif.icon,
                    is_grouped: true,
                    count: typeCount[notif.type],
                    created_at: notif.created_at,
                    action_url: notif.action_url
                });
                processed.add(notif.type);
            } else {
                grouped.push(notif);
            }
        });

        return grouped;
    }

    // Toggle river visibility
    if (riverToggleBtn) {
        riverToggleBtn.addEventListener('click', () => {
            riverHidden = !riverHidden;
            
            if (riverHidden) {
                notificationRiver.classList.add('hidden');
                riverToggleBtn.innerHTML = '<i class="fas fa-chevron-down"></i>';
            } else {
                notificationRiver.classList.remove('hidden');
                riverToggleBtn.innerHTML = '<i class="fas fa-chevron-up"></i>';
            }
        });
    }

    // Open notification modal
    if (notificationBellBtn) {
        notificationBellBtn.addEventListener('click', async () => {
            notificationModal.classList.add('active');
            await loadNotificationModal();
        });
    }

    // Close notification modal
    if (notifCloseBtn) {
        notifCloseBtn.addEventListener('click', () => {
            notificationModal.classList.remove('active');
        });
    }

    // Click outside to close
    if (notificationModal) {
        notificationModal.addEventListener('click', (e) => {
            if (e.target === notificationModal || e.target.classList.contains('notification-modal-overlay')) {
                notificationModal.classList.remove('active');
            }
        });
    }

    // Load notification modal
    async function loadNotificationModal() {
        try {
            const statsResponse = await fetch('/planner/notifications/stats');
            const stats = await statsResponse.json();
            
            document.getElementById('modalStatTotal').textContent = stats.total_today;
            document.getElementById('modalStatUnread').textContent = stats.unread;
          
            
            renderModalNotifications();
            
        } catch (error) {
            console.error('Error loading modal:', error);
        }
    }

    // Render modal notifications
    function renderModalNotifications() {
        if (!notifModalList) return;

let filteredNotifs = notifications;

if (currentFilter !== 'all') {
    filteredNotifs = notifications.filter(function(n) {
        const type = (n.type || '').toLowerCase();
        const priority = (n.priority || '').toLowerCase();
        const title = (n.title || '').toLowerCase();
        const message = (n.message || '').toLowerCase();

        const isOrder =
            type.includes('order') ||
            title.includes('order') ||
            message.includes('order');

        if (currentFilter === 'urgent') {
            return priority === 'urgent';
        }

        if (currentFilter === 'order') {
            return isOrder;
        }

        if (currentFilter === 'task') {
            return !isOrder && (
                type.includes('task') ||
                title.includes('task')
            );
        }

        return type.includes(currentFilter) ||
               title.includes(currentFilter);
    });
}

        if (filteredNotifs.length === 0) {
            notifModalList.innerHTML = `
                <div class="notif-empty-state">
                    <i class="fas fa-inbox"></i>
                    <p>No notifications here!</p>
                </div>
            `;
            return;
        }

        notifModalList.innerHTML = '';

        filteredNotifs.forEach(notification => {
            const item = createModalNotificationItem(notification);
            notifModalList.appendChild(item);
        });
    }

    // Create modal notification item
    function createModalNotificationItem(notification) {
        const item = document.createElement('div');
        item.className = `notif-modal-item ${notification.is_read ? 'read' : 'unread'} ${notification.priority}`;
        
        item.innerHTML = `
            <div class="notif-item-icon ${getColorClass(notification.priority)}">
                <i class="${getIconClass(notification)}"></i>
            </div>
            <div class="notif-item-content">
                <h4>${escapeHtml(notification.title)}</h4>
                <p>${escapeHtml(notification.message)}</p>
                <span class="notif-item-time">${getTimeAgo(notification.created_at)}</span>
            </div>
            <div class="notif-item-actions">
                ${notification.action_url ? `
                    <button class="notif-item-btn view" data-url="${notification.action_url}" title="View">
                        <i class="fas fa-arrow-right"></i>
                    </button>
                ` : ''}
                <button class="notif-item-btn delete" data-id="${notification.id}" title="Delete">
                    <i class="fas fa-trash"></i>
                </button>
            </div>
        `;

        // Click to mark as read
        item.addEventListener('click', (e) => {
            if (!e.target.closest('.notif-item-actions')) {
                if (!notification.is_read) {
                    markNotificationAsRead(notification.id);
                    item.classList.remove('unread');
                    item.classList.add('read');
                }
            }
        });

        // View button
        const viewBtn = item.querySelector('.view');
        if (viewBtn) {
            viewBtn.addEventListener('click', (e) => {
                e.stopPropagation();
                markNotificationAsRead(notification.id);
                window.location.href = viewBtn.getAttribute('data-url');
            });
        }

        // Delete button
        const deleteBtn = item.querySelector('.delete');
        if (deleteBtn) {
            deleteBtn.addEventListener('click', async (e) => {
                e.stopPropagation();
                await archiveNotification(notification.id);
                item.style.animation = 'fadeOut 0.3s';
                setTimeout(() => item.remove(), 300);
            });
        }

        return item;
    }

    // Filter tabs
    document.querySelectorAll('.notif-tab').forEach(tab => {
        tab.addEventListener('click', function() {
            document.querySelectorAll('.notif-tab').forEach(t => t.classList.remove('active'));
            this.classList.add('active');
            currentFilter = this.getAttribute('data-filter');
            renderModalNotifications();
        });
    });

    // Mark all as read
    document.getElementById('modalMarkAllRead')?.addEventListener('click', async () => {
        try {
            await fetch('/planner/notifications/read-all', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                }
            });
            
            notifications.forEach(n => n.is_read = true);
            updateBadges(0);
            renderModalNotifications();
            showToast('All notifications marked as read!', 'success');
            
        } catch (error) {
            console.error('Error marking all as read:', error);
        }
    });

    // Clear all
    document.getElementById('modalClearAll')?.addEventListener('click', async () => {
        if (!confirm('Delete all notifications?')) return;
        
        try {
            for (const notif of notifications) {
                await archiveNotification(notif.id);
            }
            
            notifications = [];
            updateBadges(0);
            renderModalNotifications();
            renderRiverNotifications();
            showToast('All notifications cleared!', 'success');
            
        } catch (error) {
            console.error('Error clearing notifications:', error);
        }
    });

    // Helper functions
    function getColorClass(priority) {
        const colors = {
            'low': 'notification-blue',
            'medium': 'notification-yellow',
            'high': 'notification-orange',
            'urgent': 'notification-red'
        };
        return colors[priority] || 'notification-blue';
    }

    function getIconClass(notification) {
        if (notification.icon) return notification.icon;

        const icons = {
            'task': 'fas fa-tasks',
            'event': 'fas fa-calendar',
            'request': 'fas fa-inbox',
            'message': 'fas fa-envelope',
            'weather': 'fas fa-cloud-sun',
            'conflict': 'fas fa-exclamation-triangle',
            'health': 'fas fa-heartbeat'
        };
        return icons[notification.type] || 'fas fa-bell';
    }

    function calculateRiverSpeed() {
        const body = document.body;
        
        if (body.classList.contains('mood-stressed')) {
            return 45;
        } else if (body.classList.contains('mood-sleepy')) {
            return 50;
        } else if (body.classList.contains('mood-energized')) {
            return 20;
        }
        
        return 30;
    }

    function getTimeAgo(timestamp) {
        const now = new Date();
        const time = new Date(timestamp);
        const diff = Math.floor((now - time) / 1000);

        if (diff < 60) return 'Just now';
        if (diff < 3600) return `${Math.floor(diff / 60)}m ago`;
        if (diff < 86400) return `${Math.floor(diff / 3600)}h ago`;
        return `${Math.floor(diff / 86400)}d ago`;
    }

    function escapeHtml(text) {
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    }

    async function markNotificationAsRead(id) {
        try {
            await fetch(`/planner/notifications/${id}/read`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                }
            });
            
            const notification = notifications.find(n => n.id == id);
            if (notification) {
                notification.is_read = true;
            }
            
            updateBadges(notifications.filter(n => !n.is_read).length);
            
        } catch (error) {
            console.error('Error marking notification as read:', error);
        }
    }

    async function archiveNotification(id) {
        try {
            await fetch(`/planner/notifications/${id}/archive`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                }
            });
            
            notifications = notifications.filter(n => n.id != id);
            updateBadges(notifications.filter(n => !n.is_read).length);
            
        } catch (error) {
            console.error('Error archiving notification:', error);
        }
    }

    function showToast(message, type = 'info') {
        const toast = document.createElement('div');
        toast.className = `toast toast-${type}`;
        toast.style.cssText = `
            position: fixed;
            bottom: 100px;
            right: 30px;
            background: ${type === 'success' ? '#7ED321' : '#D0021B'};
            color: white;
            padding: 15px 25px;
            border-radius: 12px;
            box-shadow: 0 6px 25px rgba(0,0,0,0.2);
            z-index: 10002;
            animation: slideInRight 0.3s;
        `;
        toast.textContent = message;
        
        document.body.appendChild(toast);
        
        setTimeout(() => {
            toast.style.animation = 'fadeOut 0.3s';
            setTimeout(() => toast.remove(), 300);
        }, 3000);
    }

    // Auto-fetch notifications
    fetchNotifications();
    setInterval(fetchNotifications, 30000);

    if (riverContainer) {
        riverContainer.addEventListener('mouseenter', () => {
            riverContainer.style.animationPlayState = 'paused';
        });

        riverContainer.addEventListener('mouseleave', () => {
            riverContainer.style.animationPlayState = 'running';
        });
    }

    // ============================================
// MOOD SELECTOR - CHANGES SIDEBAR COLORS
// ============================================

document.addEventListener('DOMContentLoaded', function() {
    initializeMoodSelector();
});

function initializeMoodSelector() {
    const moodBtn = document.getElementById('moodSelectorBtn');
    const moodModal = document.getElementById('moodModal');
    const moodCloseBtn = document.getElementById('moodCloseBtn');
    const moodOptions = document.querySelectorAll('.mood-option');
    
    // Open mood modal
    moodBtn?.addEventListener('click', () => {
        moodModal?.classList.add('active');
    });
    
    // Close mood modal
    moodCloseBtn?.addEventListener('click', () => {
        moodModal?.classList.remove('active');
    });
    
    moodModal?.addEventListener('click', (e) => {
        if (e.target === moodModal) {
            moodModal.classList.remove('active');
        }
    });
    
    // Handle mood selection
    moodOptions.forEach(option => {
        option.addEventListener('click', function() {
            const mood = this.dataset.mood;
            applyMood(mood);
            moodModal?.classList.remove('active');
        });
    });
    
    // Load saved mood
    const savedMood = localStorage.getItem('plannerMood') || 'productive';
    applyMood(savedMood);
}

function applyMood(mood) {
    const sidebar = document.querySelector('.planner-sidebar');
    const header = document.querySelector('.header');
    const currentMoodText = document.getElementById('currentMoodText');
    
    // Save mood
    localStorage.setItem('plannerMood', mood);
    
    // Update mood text
    if (currentMoodText) {
        currentMoodText.textContent = mood.charAt(0).toUpperCase() + mood.slice(1);
    }
    
    // Remove all mood classes
    sidebar?.classList.remove('mood-productive', 'mood-energized', 'mood-calm', 'mood-creative', 'mood-stressed', 'mood-sleepy');
    header?.classList.remove('mood-productive', 'mood-energized', 'mood-calm', 'mood-creative', 'mood-stressed', 'mood-sleepy');
    
    // Add new mood class
    sidebar?.classList.add(`mood-${mood}`);
    header?.classList.add(`mood-${mood}`);
}

});