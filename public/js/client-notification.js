document.addEventListener('DOMContentLoaded', function() {
    
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

    // Fetch notifications - FIXED URL
    async function fetchNotifications() {
        try {
            const response = await fetch('/client/notifications');
            
            if (!response.ok) {
                console.error('Failed to fetch notifications:', response.status);
                return;
            }
            
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

        const recentNotifications = notifications.slice(0, 10);

        if (recentNotifications.length === 0) {
            riverContainer.innerHTML = '<div class="notification-bubble"><div class="bubble-content"><div class="bubble-title">No notifications yet</div></div></div>';
            return;
        }

        recentNotifications.forEach((notification, index) => {
            const bubble = createRiverBubble(notification, index);
            riverContainer.appendChild(bubble);
        });

        // Duplicate for seamless loop
        recentNotifications.forEach((notification, index) => {
            const bubble = createRiverBubble(notification, index);
            riverContainer.appendChild(bubble);
        });

        riverContainer.style.animation = 'none';
        setTimeout(() => {
            riverContainer.style.animation = `flow-left 30s linear infinite`;
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

    // Toggle river
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

    // Open modal
    if (notificationBellBtn) {
        notificationBellBtn.addEventListener('click', async () => {
            console.log('Bell clicked!'); // Debug
            notificationModal.classList.add('active');
            await loadNotificationModal();
        });
    }

    // Close modal
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

    // Load modal
    async function loadNotificationModal() {
        try {
            const statsResponse = await fetch('/client/notifications/stats');
            const stats = await statsResponse.json();
            
            document.getElementById('modalStatTotal').textContent = stats.total_today;
            document.getElementById('modalStatUnread').textContent = stats.unread;
            document.getElementById('modalStatUrgent').textContent = stats.urgent;
            
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
            filteredNotifs = notifications.filter(n => n.type === currentFilter);
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

    // Create modal item
    function createModalNotificationItem(notification) {
        const item = document.createElement('div');
        item.className = `notif-modal-item ${notification.is_read ? 'read' : 'unread'}`;
        
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

        item.addEventListener('click', (e) => {
            if (!e.target.closest('.notif-item-actions')) {
                if (!notification.is_read) {
                    markNotificationAsRead(notification.id);
                }
            }
        });

        const viewBtn = item.querySelector('.view');
        if (viewBtn) {
            viewBtn.addEventListener('click', (e) => {
                e.stopPropagation();
                markNotificationAsRead(notification.id);
                window.location.href = viewBtn.getAttribute('data-url');
            });
        }

        const deleteBtn = item.querySelector('.delete');
        if (deleteBtn) {
            deleteBtn.addEventListener('click', async (e) => {
                e.stopPropagation();
                await archiveNotification(notification.id);
                item.remove();
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
            await fetch('/client/notifications/read-all', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                }
            });
            
            notifications.forEach(n => n.is_read = true);
            updateBadges(0);
            renderModalNotifications();
            
        } catch (error) {
            console.error('Error:', error);
        }
    });

    document.getElementById('modalClearAll')?.addEventListener('click', async () => {
        try {
            await fetch('/client/notifications/archive-all', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                }
            });
            
            notifications = [];
            updateBadges(0);
            renderModalNotifications();
            
        } catch (error) {
            console.error('Error:', error);
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
            'event': 'fas fa-calendar',
            'message': 'fas fa-envelope',
            'request': 'fas fa-inbox'
        };
        return icons[notification.type] || 'fas fa-bell';
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
            await fetch(`/client/notifications/${id}/read`, {
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
            console.error('Error:', error);
        }
    }

    async function archiveNotification(id) {
        try {
            await fetch(`/client/notifications/${id}/archive`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                }
            });
            
            notifications = notifications.filter(n => n.id != id);
            updateBadges(notifications.filter(n => !n.is_read).length);
            
        } catch (error) {
            console.error('Error:', error);
        }
    }

    // Initial fetch
    fetchNotifications();
    
    // Auto-refresh every 30 seconds
    setInterval(fetchNotifications, 30000);

    console.log('✅ Client notifications loaded!');

    // ========== ADD THIS POLLING CODE ==========
let currentUnreadCount = 0;

function pollNotificationStats() {
    fetch('/client/notifications/stats')
        .then(res => res.json())
        .then(data => {
            if (data.unread > currentUnreadCount) {
                // New notification arrived – refresh the list
                fetchNotifications();
                // Optional: show a toast
                showToast('📩 New message received!');
            }
            currentUnreadCount = data.unread;
            // Ensure badge updates (your existing updateBadges will be called by fetchNotifications)
        })
        .catch(err => console.warn('Polling error:', err));
}

// Poll every 15 seconds (in addition to the 30-second fetch)
setInterval(pollNotificationStats, 15000);

function showToast(message) {
    const toast = document.createElement('div');
    toast.textContent = message;
    toast.style.cssText = `
        position: fixed; bottom: 20px; right: 20px; background: #475B35;
        color: white; padding: 12px 20px; border-radius: 12px;
        z-index: 10000; animation: fadeInOut 3s ease;
    `;
    document.body.appendChild(toast);
    setTimeout(() => toast.remove(), 3000);
}

// Add CSS animation if not present
if (!document.querySelector('#toast-style')) {
    const style = document.createElement('style');
    style.id = 'toast-style';
    style.textContent = `
        @keyframes fadeInOut {
            0% { opacity: 0; transform: translateY(20px); }
            15% { opacity: 1; transform: translateY(0); }
            85% { opacity: 1; transform: translateY(0); }
            100% { opacity: 0; transform: translateY(20px); }
        }
    `;
    document.head.appendChild(style);
}
// ========== END POLLING CODE ==========

console.log('✅ Client notifications loaded!');
});