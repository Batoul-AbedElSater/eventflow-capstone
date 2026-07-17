// ============================================
// PLANNER EVENTS - ULTRA CREATIVE JS
// ============================================

let currentView = 'kanban';
let allEvents = [];
let currentMonth = new Date();
let revenueChart = null;
let eventTypeChart = null;

document.addEventListener('DOMContentLoaded', function() {
    console.log('✅ Planner Events Page Loaded');

    initializeViewToggle();
    initializeKanbanDragDrop();
    initializeFilters();
    initializeCalendar();
    initializeFAB();

    // Load analytics on demand
    document.querySelector('[data-view="analytics"]')?.addEventListener('click', loadAnalytics);
});

// ============================================
// VIEW TOGGLE
// ============================================

function initializeViewToggle() {
    const viewButtons = document.querySelectorAll('.view-toggle-btn');

    viewButtons.forEach(btn => {
        btn.addEventListener('click', function() {
            const view = this.dataset.view;

            // Update active button
            viewButtons.forEach(b => b.classList.remove('active'));
            this.classList.add('active');

            // Switch views
            document.querySelectorAll('.view-container').forEach(v => v.style.display = 'none');

            if (view === 'kanban') {
                document.getElementById('kanbanView').style.display = 'block';
            } else if (view === 'calendar') {
                document.getElementById('calendarView').style.display = 'block';
                renderCalendar();
            } else if (view === 'analytics') {
                document.getElementById('analyticsView').style.display = 'block';
            }

            currentView = view;
        });
    });
}

// ============================================
// KANBAN DRAG & DROP
// ============================================

function initializeKanbanDragDrop() {
    const containers = document.querySelectorAll('.kanban-cards-container');

    containers.forEach(container => {
        new Sortable(container, {
            group: 'kanban',
            animation: 200,
            ghostClass: 'dragging',
            dragClass: 'dragging',
            onEnd: function(evt) {
                const eventId = evt.item.dataset.eventId;
                const newStatus = evt.to.closest('.kanban-column').dataset.status;

                updateEventStatus(eventId, newStatus);
            }
        });
    });
}

async function updateEventStatus(eventId, newStatus) {
    try {
        const response = await fetch(`/planner/events/${eventId}/status`, {
            method: 'PUT',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            },
            body: JSON.stringify({ status: newStatus })
        });

        if (!response.ok) throw new Error('Failed to update status');

        const data = await response.json();

        // Show success notification
        showNotification('Event status updated successfully!', 'success');

        // Update column counts
        updateColumnCounts();

    } catch (error) {
        console.error('Error:', error);
        showNotification('Failed to update status', 'error');
        location.reload(); // Revert on error
    }
}

function updateColumnCounts() {
    document.querySelectorAll('.kanban-column').forEach(column => {
        const status = column.dataset.status;
        const visibleCount = Array.from(column.querySelectorAll('.kanban-card-supreme'))
            .filter(card => card.style.display !== 'none').length;
        column.querySelector('.column-count').textContent = visibleCount;
    });
}

// ============================================
// FILTERS & SEARCH
// ============================================

function initializeFilters() {
    const searchInput = document.getElementById('eventSearch');
    const typeFilter = document.getElementById('filterType');

    console.log('🔍 Filter elements found:', {
        searchInput: !!searchInput,
        typeFilter: !!typeFilter,
    });

    if (typeFilter) {
        const availableTypes = Array.from(document.querySelectorAll('.kanban-card-supreme'))
            .map(card => card.dataset.type);
        console.log('🔍 data-type values found on cards:', availableTypes);
        console.log('🔍 dropdown option values:', Array.from(typeFilter.options).map(o => o.value));
    }

    searchInput?.addEventListener('input', applyFilters);
    typeFilter?.addEventListener('change', applyFilters);
}

function applyFilters() {
    const searchTerm = (document.getElementById('eventSearch')?.value || '').trim().toLowerCase();
    const typeFilter = (document.getElementById('filterType')?.value || '').trim().toLowerCase();

    document.querySelectorAll('.kanban-card-supreme').forEach(card => {
        const eventNameEl = card.querySelector('.card-event-name');
        const clientNameEl = card.querySelector('.client-name');

        const eventName = (eventNameEl?.textContent || '').toLowerCase();
        const clientName = (clientNameEl?.textContent || '').toLowerCase();
        // Guard against missing/empty data-type instead of letting it throw
        const eventType = (card.dataset.type || '').trim().toLowerCase();

        const matchesSearch = searchTerm === '' || eventName.includes(searchTerm) || clientName.includes(searchTerm);
        const matchesType = typeFilter === '' || eventType === typeFilter;

        card.style.display = (matchesSearch && matchesType) ? '' : 'none';
    });

    updateColumnCounts();
}

// ============================================
// CALENDAR HEAT MAP
// ============================================

function initializeCalendar() {
    document.getElementById('prevMonth')?.addEventListener('click', () => {
        currentMonth.setMonth(currentMonth.getMonth() - 1);
        renderCalendar();
    });

    document.getElementById('nextMonth')?.addEventListener('click', () => {
        currentMonth.setMonth(currentMonth.getMonth() + 1);
        renderCalendar();
    });
}

function renderCalendar() {
    const grid = document.getElementById('calendarGrid');
    const monthName = document.getElementById('currentMonth');

    if (!grid) return;

    const year = currentMonth.getFullYear();
    const month = currentMonth.getMonth();

    monthName.textContent = currentMonth.toLocaleDateString('en-US', {
        month: 'long',
        year: 'numeric'
    });

    grid.innerHTML = '';

    // Day headers
    const dayNames = ['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'];
    dayNames.forEach(day => {
        const header = document.createElement('div');
        header.className = 'calendar-day-header';
        header.textContent = day;
        grid.appendChild(header);
    });

    // Get first day of month
    const firstDay = new Date(year, month, 1).getDay();
    const daysInMonth = new Date(year, month + 1, 0).getDate();

    // Empty cells before month starts
    for (let i = 0; i < firstDay; i++) {
        const empty = document.createElement('div');
        grid.appendChild(empty);
    }

    // Calendar days
    for (let day = 1; day <= daysInMonth; day++) {
        const dayEl = document.createElement('div');
        dayEl.className = 'calendar-day';

        // Mock event count (replace with real data)
        const eventCount = Math.floor(Math.random() * 5);
        dayEl.classList.add(`level-${Math.min(eventCount, 4)}`);

        dayEl.innerHTML = `
            <div class="calendar-day-number">${day}</div>
            ${eventCount > 0 ? `<div class="calendar-day-events">${eventCount}</div>` : ''}
        `;

        dayEl.addEventListener('click', () => {
            showDayEvents(year, month, day);
        });

        grid.appendChild(dayEl);
    }
}

function showDayEvents(year, month, day) {
    // Show modal with events for this day
    alert(`Events for ${month + 1}/${day}/${year}`);
}

// ============================================
// ANALYTICS CHARTS
// ============================================

async function loadAnalytics() {
    const period = document.getElementById('revenuePeriod')?.value || 'month';

    try {
        const response = await fetch(`/planner/events/analytics?period=${period}`);
        const data = await response.json();

        renderRevenueChart(data.revenue);
        renderEventTypeChart(data.event_types);

    } catch (error) {
        console.error('Error loading analytics:', error);
    }
}

function renderRevenueChart(data) {
    const ctx = document.getElementById('revenueChart');
    if (!ctx) return;

    if (revenueChart) {
        revenueChart.destroy();
    }

    revenueChart = new Chart(ctx, {
        type: 'line',
        data: {
            labels: data.map(d => d.date || d.month),
            datasets: [{
                label: 'Revenue',
                data: data.map(d => d.revenue),
                borderColor: '#E19184',
                backgroundColor: 'rgba(225, 145, 132, 0.1)',
                borderWidth: 3,
                tension: 0.4,
                fill: true
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: false
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        callback: function(value) {
                            return '$' + value.toLocaleString();
                        }
                    }
                }
            }
        }
    });
}

function renderEventTypeChart(data) {
    const ctx = document.getElementById('eventTypeChart');
    if (!ctx) return;

    if (eventTypeChart) {
        eventTypeChart.destroy();
    }

    eventTypeChart = new Chart(ctx, {
        type: 'doughnut',
        data: {
            labels: data.map(d => d.name),
            datasets: [{
                data: data.map(d => d.count),
                backgroundColor: [
                    '#E19184',
                    '#C63E4E',
                    '#475B35',
                    '#7ED321',
                    '#4A90E2'
                ]
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'bottom'
                }
            }
        }
    });
}

// Period change
document.getElementById('revenuePeriod')?.addEventListener('change', loadAnalytics);

// ============================================
// FLOATING ACTION BUTTON
// ============================================

function initializeFAB() {
    document.querySelectorAll('.fab-action-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            const action = this.dataset.action;

            switch(action) {
                case 'accept-all':
                    acceptAllPending();
                    break;
                case 'export':
                    exportEvents();
                    break;
                case 'analytics':
                    document.querySelector('[data-view="analytics"]').click();
                    break;
                case 'messages':
                    window.location.href = '/planner/messages';
                    break;
            }
        });
    });
}

async function acceptAllPending() {
    if (!confirm('Accept all pending events?')) return;

    const pendingCards = document.querySelectorAll('[data-status="pending"]');

    for (const card of pendingCards) {
        await updateEventStatus(card.dataset.eventId, 'confirmed');
    }

    location.reload();
}

function exportEvents() {
    // Export to CSV
    alert('Exporting events...');
}

// ============================================
// QUICK ACTIONS
// ============================================

window.quickAccept = async function(eventId) {
    if (!confirm('Accept this event?')) return;
    await updateEventStatus(eventId, 'confirmed');
}

window.quickDecline = async function(eventId) {
    if (!confirm('Decline this event?')) return;
    await updateEventStatus(eventId, 'declined');
}

window.openMessaging = function(eventId) {
    window.location.href = `/planner/messages?event=${eventId}`;
}

// ============================================
// NOTIFICATIONS
// ============================================

function showNotification(message, type = 'success') {
    const notification = document.createElement('div');
    notification.className = `notification notification-${type}`;
    notification.innerHTML = `
        <i class="fas fa-${type === 'success' ? 'check-circle' : 'exclamation-circle'}"></i>
        <span>${message}</span>
    `;
    notification.style.cssText = `
        position: fixed;
        top: 30px;
        right: 30px;
        background: ${type === 'success' ? '#7ED321' : '#D0021B'};
        color: white;
        padding: 18px 24px;
        border-radius: 15px;
        display: flex;
        align-items: center;
        gap: 12px;
        font-weight: 700;
        box-shadow: 0 10px 30px rgba(0,0,0,0.3);
        z-index: 10000;
        animation: slideIn 0.4s ease-out;
    `;

    document.body.appendChild(notification);

    setTimeout(() => {
        notification.style.animation = 'slideOut 0.4s ease-out';
        setTimeout(() => notification.remove(), 400);
    }, 3000);
}

// Add animation styles
const style = document.createElement('style');
style.textContent = `
    @keyframes slideIn {
        from {
            transform: translateX(400px);
            opacity: 0;
        }
        to {
            transform: translateX(0);
            opacity: 1;
        }
    }
    @keyframes slideOut {
        from {
            transform: translateX(0);
            opacity: 1;
        }
        to {
            transform: translateX(400px);
            opacity: 0;
        }
    }
`;
document.head.appendChild(style);

console.log('✅ Planner Events Scripts Initialized');
