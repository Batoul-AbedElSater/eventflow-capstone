document.addEventListener('DOMContentLoaded', function() {
    
    console.log('🔔 Assistant notification JS loaded');

    const notificationBellBtn = document.getElementById('notificationBellBtn');
    const notificationModal = document.getElementById('notificationModal');
    const notifCloseBtn = document.getElementById('notifCloseBtn');
    const headerNotifBadge = document.getElementById('headerNotifBadge');
    const notifModalList = document.getElementById('notifModalList');

    let notifications = [];
    let currentFilter = 'all';

    // Fetch notifications
    async function fetchNotifications() {
        try {
            console.log('Fetching notifications...');
            const response = await fetch('/assistant/notifications');
            
            if (!response.ok) {
                console.error('Failed to fetch notifications:', response.status);
                return;
            }
            
            const data = await response.json();
            notifications = data.notifications || [];
            updateBadges(data.unread_count || 0);
            console.log('Notifications loaded:', notifications.length);
            
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

    // Open modal
    if (notificationBellBtn) {
        notificationBellBtn.addEventListener('click', async function(e) {
            e.preventDefault();
            e.stopPropagation();
            console.log('Bell clicked - opening modal');
            
            await fetchNotifications();
            notificationModal.classList.add('active');
            loadModalStats();
            renderModalNotifications();
        });
    }

    // Close modal
    if (notifCloseBtn) {
        notifCloseBtn.addEventListener('click', function(e) {
            e.preventDefault();
            notificationModal.classList.remove('active');
        });
    }
    document.querySelectorAll('.notif-tab').forEach(function(tab) {
    tab.addEventListener('click', function() {
        document.querySelectorAll('.notif-tab').forEach(function(item) {
            item.classList.remove('active');
        });

        this.classList.add('active');
        currentFilter = this.dataset.filter || 'all';
        renderModalNotifications();
    });
});

    // Click outside to close
    if (notificationModal) {
        notificationModal.addEventListener('click', function(e) {
            if (e.target === notificationModal || e.target.classList.contains('notification-modal-overlay')) {
                notificationModal.classList.remove('active');
            }
        });
    }

    // Load modal stats
    async function loadModalStats() {
        try {
            const response = await fetch('/assistant/notifications/stats');
            if (!response.ok) return;
            const stats = await response.json();
            
            const totalEl = document.getElementById('modalStatTotal');
            const unreadEl = document.getElementById('modalStatUnread');
            const urgentEl = document.getElementById('modalStatUrgent');
            
            if (totalEl) totalEl.textContent = stats.total_today || 0;
            if (unreadEl) unreadEl.textContent = stats.unread || 0;
            if (urgentEl) urgentEl.textContent = stats.urgent || 0;
            
        } catch (error) {
            console.error('Error loading stats:', error);
        }
    }

    // Render modal notifications
  function renderModalNotifications() {
    if (!notifModalList) return;

    const visibleNotifications = notifications.filter(matchesNotificationFilter);

    console.log('Rendering notifications, count:', notifications.length);

    if (visibleNotifications.length === 0) {
        notifModalList.innerHTML = `
            <div class="notif-empty-state">
                <i class="fas fa-inbox"></i>
                <p>${getEmptyNotificationMessage()}</p>
            </div>
        `;
        return;
    }

    notifModalList.innerHTML = '';

    visibleNotifications.forEach(function(notification) {
            const item = document.createElement('div');
            item.className = 'notif-modal-item ' + (notification.is_read ? 'read' : 'unread');
            
            const timeAgo = getTimeAgo(notification.created_at);
            const iconClass = notification.icon || getNotificationIcon(notification.type);
            
            item.innerHTML = `
                <div class="notif-item-icon">
                    <i class="${iconClass}"></i>
                </div>
                <div class="notif-item-content">
                    <h4>${escapeHtml(notification.title)}</h4>
                    <p>${escapeHtml(notification.message)}</p>
                    <span class="notif-item-time">${timeAgo}</span>
                </div>
                <div class="notif-item-actions">
                    <button class="notif-item-btn delete" data-id="${notification.id}" title="Delete">
                        <i class="fas fa-trash"></i>
                    </button>
                </div>
            `;

            // Mark as read when clicked
            item.addEventListener('click', function(e) {
                if (!e.target.closest('.notif-item-actions')) {
                    if (!notification.is_read) {
                        markNotificationAsRead(notification.id);
                        this.classList.remove('unread');
                        this.classList.add('read');
                        const unreadCount = notifications.filter(n => !n.is_read).length;
                        updateBadges(unreadCount);
                    }
                }
            });

            // Delete button
            var deleteBtn = item.querySelector('.delete');
            if (deleteBtn) {
                deleteBtn.addEventListener('click', function(e) {
                    e.stopPropagation();
                    archiveNotification(notification.id);
                    item.remove();
                    // Check if list is empty
                    if (notifModalList.children.length === 0) {
                        renderModalNotifications();
                    }
                    const unreadCount = notifications.filter(n => !n.is_read).length;
                    updateBadges(unreadCount);
                });
            }

            notifModalList.appendChild(item);
        });
    }

    // Mark all as read
    var markAllBtn = document.getElementById('modalMarkAllRead');
    if (markAllBtn) {
        markAllBtn.addEventListener('click', async function() {
            try {
                await fetch('/assistant/notifications/read-all', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    }
                });
                
                notifications.forEach(function(n) {
                    n.is_read = true;
                });
                updateBadges(0);
                renderModalNotifications();
                
            } catch (error) {
                console.error('Error:', error);
            }
        });
    }

    // Clear all
    var clearAllBtn = document.getElementById('modalClearAll');
    if (clearAllBtn) {
        clearAllBtn.addEventListener('click', async function() {
            if (!confirm('Clear all notifications?')) return;
            try {
                await fetch('/assistant/notifications/archive-all', {
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
    }

    // Helper functions
    function getTimeAgo(timestamp) {
        var now = new Date();
        var time = new Date(timestamp);
        var diff = Math.floor((now - time) / 1000);

        if (diff < 60) return 'Just now';
        if (diff < 3600) return Math.floor(diff / 60) + 'm ago';
        if (diff < 86400) return Math.floor(diff / 3600) + 'h ago';
        return Math.floor(diff / 86400) + 'd ago';
    }

    function escapeHtml(text) {
        var div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    }

    function getNotificationIcon(type) {
        switch (type) {
            case 'task': return 'fas fa-tasks';
            case 'order': return 'fas fa-shopping-cart';
            case 'event': return 'fas fa-calendar';
            case 'message': return 'fas fa-envelope';
            case 'urgent': return 'fas fa-exclamation-circle';
            default: return 'fas fa-bell';
        }
    }

    async function markNotificationAsRead(id) {
        try {
            await fetch('/assistant/notifications/' + id + '/read', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                }
            });
            
            var notification = notifications.find(function(n) {
                return n.id == id;
            });
            if (notification) {
                notification.is_read = true;
            }
            
        } catch (error) {
            console.error('Error:', error);
        }
    }

function matchesNotificationFilter(notification) {
    var type = (notification.type || '').toLowerCase();
    var priority = (notification.priority || '').toLowerCase();

    if (currentFilter === 'task') {
        return type.includes('task');
    }

    if (currentFilter === 'urgent') {
        return priority === 'urgent';
    }

    return true;
}

function getEmptyNotificationMessage() {
    if (currentFilter === 'task') return 'No task notifications found';
    if (currentFilter === 'urgent') return 'No urgent notifications found';
    return 'No notifications found';
}

    async function archiveNotification(id) {
        try {
            await fetch('/assistant/notifications/' + id + '/archive', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                }
            });
            
            notifications = notifications.filter(function(n) {
                return n.id != id;
            });
            
        } catch (error) {
            console.error('Error:', error);
        }
    }

    // Initial fetch
    fetchNotifications();
    
    // Auto-refresh every 15 seconds
    setInterval(fetchNotifications, 15000);

    console.log('✅ Assistant notifications fully loaded!');
});