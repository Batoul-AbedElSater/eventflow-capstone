// ============================================
// PLANNER TASKS - FULL FUNCTIONALITY
// ============================================

let powerModeActive = false;
let focusTimerInterval = null;
let focusTimeRemaining = 0;
let currentFocusTitle = '';

// Check for existing timer on page load
document.addEventListener('DOMContentLoaded', function() {
    console.log('Tasks JS loaded');
    initializeDragAndDrop();
    initializePowerMode();
    initializeFocusTimerModal();
    initializeTaskModal();
    initializeTaskActions();
    updateColumnCounts();
    restoreFocusTimer();
});

function restoreFocusTimer() {
    const savedEnd = localStorage.getItem('focusTimerEnd');
    const savedTitle = localStorage.getItem('focusTimerTitle');
    if (savedEnd && savedTitle) {
        const now = Date.now();
        const endTime = parseInt(savedEnd);
        if (endTime > now) {
            const remainingSeconds = Math.floor((endTime - now) / 1000);
            if (remainingSeconds > 0) {
                currentFocusTitle = savedTitle;
                focusTimeRemaining = remainingSeconds;
                const timerButton = document.getElementById('focusTimerBtn');
                if (timerButton) {
                    timerButton.innerHTML = `<i class="fas fa-stop-circle"></i> ${savedTitle} - ${Math.floor(remainingSeconds/60)}:${(remainingSeconds%60).toString().padStart(2,'0')}`;
                    timerButton.classList.add('timer-active');
                    startFocusTimer(timerButton, savedTitle, Math.ceil(remainingSeconds/60), true);
                }
            } else {
                localStorage.removeItem('focusTimerEnd');
                localStorage.removeItem('focusTimerTitle');
                alert(`🎉 Focus session "${savedTitle}" completed while you were away!`);
            }
        } else {
            localStorage.removeItem('focusTimerEnd');
            localStorage.removeItem('focusTimerTitle');
        }
    }
}

// ========== POWER MODE ==========
function initializePowerMode() {
    const powerBtn = document.getElementById('powerModeBtn');
    if (!powerBtn) return;

    const newBtn = powerBtn.cloneNode(true);
    powerBtn.parentNode.replaceChild(newBtn, powerBtn);

    newBtn.addEventListener('click', function(e) {
        e.preventDefault();
        powerModeActive = !powerModeActive;

        if (powerModeActive) {
            this.classList.add('active');
            document.querySelectorAll('.task-card-epic').forEach(card => {
                card.classList.add('power-mode-active');
            });
        } else {
            this.classList.remove('active');
            document.querySelectorAll('.task-card-epic').forEach(card => {
                card.classList.remove('power-mode-active');
            });
        }
    });
}

// ========== FOCUS TIMER MODAL ==========
function initializeFocusTimerModal() {
    const timerBtn = document.getElementById('focusTimerBtn');
    const modal = document.getElementById('focusTimerModal');
    const closeModal = document.getElementById('closeFocusModal');
    const cancelBtn = document.getElementById('cancelFocusModal');
    const startBtn = document.getElementById('startFocusBtn');
    const titleInput = document.getElementById('focusTitle');
    const minutesInput = document.getElementById('focusMinutesPopup');

    if (!timerBtn || !modal) return;

    const newTimerBtn = timerBtn.cloneNode(true);
    timerBtn.parentNode.replaceChild(newTimerBtn, timerBtn);

    newTimerBtn.addEventListener('click', function(e) {
        e.preventDefault();
        if (focusTimerInterval) {
            stopFocusTimer(this);
        } else {
            modal.classList.add('active');
            if (titleInput) titleInput.value = '';
            if (minutesInput) minutesInput.value = '25';
        }
    });

    const closeModalHandler = () => modal.classList.remove('active');
    if (closeModal) closeModal.addEventListener('click', closeModalHandler);
    if (cancelBtn) cancelBtn.addEventListener('click', closeModalHandler);
    modal.addEventListener('click', function(e) {
        if (e.target === modal) closeModalHandler();
    });

    startBtn.addEventListener('click', function() {
        let title = titleInput?.value.trim();
        if (!title) title = 'Focus Session';
        let minutes = parseInt(minutesInput?.value) || 25;
        if (minutes < 1) minutes = 1;
        if (minutes > 180) minutes = 180;

        currentFocusTitle = title;
        focusTimeRemaining = minutes * 60;

        const timerButton = document.getElementById('focusTimerBtn');
        if (timerButton) {
            timerButton.innerHTML = `<i class="fas fa-stop-circle"></i> Stop Timer (${title})`;
            timerButton.classList.add('timer-active');
        }

        const endTime = Date.now() + (minutes * 60 * 1000);
        localStorage.setItem('focusTimerEnd', endTime);
        localStorage.setItem('focusTimerTitle', title);

        modal.classList.remove('active');
        startFocusTimer(timerButton, title, minutes);
    });
}

function startFocusTimer(button, title, minutes, isRestored = false) {
    if (focusTimerInterval) clearInterval(focusTimerInterval);

    focusTimerInterval = setInterval(() => {
        if (focusTimeRemaining <= 0) {
            clearInterval(focusTimerInterval);
            focusTimerInterval = null;
            button.innerHTML = '<i class="fas fa-clock"></i> Focus Timer';
            button.classList.remove('timer-active');
            localStorage.removeItem('focusTimerEnd');
            localStorage.removeItem('focusTimerTitle');

            const msg = `🎉 Focus session "${title}" (${minutes} min) complete! Take a break!`;
            alert(msg);
            showNotification(msg, 'success');

            if (typeof confetti !== 'undefined') {
                confetti({
                    particleCount: 150,
                    spread: 70,
                    origin: { y: 0.6 },
                    colors: ['#E19184', '#C63E4E', '#475B35']
                });
            }
            return;
        }

        focusTimeRemaining--;
        const minutesLeft = Math.floor(focusTimeRemaining / 60);
        const secondsLeft = focusTimeRemaining % 60;
        button.innerHTML = `<i class="fas fa-stop-circle"></i> ${title} - ${minutesLeft}:${secondsLeft.toString().padStart(2, '0')}`;

        if (!isRestored) {
            const newEndTime = Date.now() + (focusTimeRemaining * 1000);
            localStorage.setItem('focusTimerEnd', newEndTime);
        }
    }, 1000);

    if (!isRestored) {
        showNotification(`🎯 Focus session "${title}" started! ${minutes} minutes`, 'success');
    }
}

function stopFocusTimer(button) {
    if (focusTimerInterval) {
        clearInterval(focusTimerInterval);
        focusTimerInterval = null;
    }
    localStorage.removeItem('focusTimerEnd');
    localStorage.removeItem('focusTimerTitle');
    button.innerHTML = '<i class="fas fa-clock"></i> Focus Timer';
    button.classList.remove('timer-active');
    showNotification('Focus timer stopped', 'info');
}

// ========== DRAG & DROP ==========
function initializeDragAndDrop() {
    const dropZones = document.querySelectorAll('.tasks-drop-zone');
    dropZones.forEach(zone => {
        new Sortable(zone, {
            group: 'tasks',
            animation: 200,
            ghostClass: 'dragging',
            onEnd: async function(evt) {
                const taskId = evt.item.dataset.taskId;
                const newStatus = evt.to.dataset.status;
                await updateTaskStatus(taskId, newStatus);
            }
        });
    });
}

async function updateTaskStatus(taskId, newStatus) {
    try {
        const response = await fetch(`/planner/tasks/${taskId}/status`, {
            method: 'PUT',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            },
            body: JSON.stringify({ status: newStatus })
        });
        if (!response.ok) throw new Error();
        const data = await response.json();
        if (data.celebration) celebrateTaskCompletion();
        updateColumnCounts();
    } catch (error) {
        showNotification('Failed to update task', 'error');
        location.reload();
    }
}

function updateColumnCounts() {
    document.querySelectorAll('.task-column').forEach(column => {
        const count = column.querySelectorAll('.task-card-epic').length;
        const countEl = column.querySelector('.task-count');
        if (countEl) countEl.textContent = count;
    });
}

// ========== TASK MODAL ==========
function initializeTaskModal() {
    const createBtn = document.getElementById('createTaskBtn');
    const closeBtn = document.getElementById('closeTaskModalBtn');
    const cancelBtn = document.getElementById('cancelTaskBtn');
    const form = document.getElementById('taskForm');
    const progressRange = document.getElementById('taskProgress');
    const progressVal = document.getElementById('progressValue');

    // .main-content has `position: relative; z-index: 1;` in planner-dashboard.css,
    // which creates its own stacking context and traps any descendant's z-index
    // inside it — so the modal's z-index:10000 never gets compared directly to
    // the header's z-index:1000. Moving the modal to be a direct child of <body>
    // escapes that stacking context entirely so it can stack above the header.
    const modalEl = document.getElementById('taskModal');
    if (modalEl && modalEl.parentElement !== document.body) {
        document.body.appendChild(modalEl);
    }

    if (createBtn) createBtn.addEventListener('click', () => openTaskModal());
    if (closeBtn) closeBtn.addEventListener('click', closeTaskModal);
    if (cancelBtn) cancelBtn.addEventListener('click', closeTaskModal);
    if (progressRange && progressVal) {
        progressRange.addEventListener('input', function() {
            progressVal.textContent = this.value + '%';
        });
    }
    if (form) form.addEventListener('submit', handleTaskSubmit);
}

function openTaskModal(taskId = null) {
    const modal = document.getElementById('taskModal');
    const form = document.getElementById('taskForm');
    const title = document.getElementById('modalTitle');
    if (!modal || !form || !title) return;
    form.reset();
    document.getElementById('taskId').value = taskId || '';
    if (taskId) {
        title.textContent = 'Edit Task';
        loadTaskData(taskId);
    } else {
        title.textContent = 'Create New Task';
        document.getElementById('progressValue').textContent = '0%';
    }
    modal.classList.add('active');
}

function closeTaskModal() {
    const modal = document.getElementById('taskModal');
    if (modal) modal.classList.remove('active');
}

async function loadTaskData(taskId) {
    try {
        const response = await fetch(`/planner/tasks/${taskId}`);
        const task = await response.json();
        document.getElementById('taskTitle').value = task.title;
        document.getElementById('taskDescription').value = task.description || '';
        document.getElementById('taskPriority').value = task.priority || 'medium';
        document.getElementById('taskEvent').value = task.event_id || '';
        document.getElementById('taskDueDate').value = task.due_date ? task.due_date.substring(0,16) : '';
        document.getElementById('taskProgress').value = task.progress || 0;
        document.getElementById('progressValue').textContent = (task.progress || 0) + '%';

        // ✅ Load assigned assistant
        if (task.assistants && task.assistants.length > 0) {
            const assistantSelect = document.getElementById('taskAssistant');
            if (assistantSelect) {
                assistantSelect.value = task.assistants[0].id;
            }
        }
        if (task.vendors && task.vendors.length > 0) {
            const vendorSelect = document.getElementById('taskVendors');
            if (vendorSelect) {
                const vendorIds = task.vendors.map(v => v.id.toString());
                Array.from(vendorSelect.options).forEach(opt => {
                    opt.selected = vendorIds.includes(opt.value);
                });
            }
        }
    } catch (error) {
        showNotification('Failed to load task', 'error');
    }
}

async function handleTaskSubmit(e) {
    e.preventDefault();

    const submitBtn = document.querySelector('#taskForm .btn-primary-epic');
    if (submitBtn) submitBtn.disabled = true;  // ✅ Prevent double click

    const taskId = document.getElementById('taskId').value;

    const assistantSelect = document.getElementById('taskAssistant');
    const assistantId = assistantSelect?.value || null;


     const vendorIds = typeof getSelectedVendorIds === 'function' ? getSelectedVendorIds() : [];
    const formData = {
        title: document.getElementById('taskTitle').value,
        description: document.getElementById('taskDescription').value,
        priority: document.getElementById('taskPriority').value,
        event_id: document.getElementById('taskEvent').value || null,
        due_date: document.getElementById('taskDueDate').value || null,
        progress: parseInt(document.getElementById('taskProgress').value),
        assistant_id: assistantId,
        vendor_ids: vendorIds
    };

    try {
        const url = taskId ? `/planner/tasks/${taskId}` : '/planner/tasks';
        const method = taskId ? 'PUT' : 'POST';
        const response = await fetch(url, {
            method: method,
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            },
            body: JSON.stringify(formData)
        });
        if (!response.ok) throw new Error();
        const data = await response.json();
        showNotification(data.message, 'success');
        closeTaskModal();
        setTimeout(() => location.reload(), 800);
    } catch (error) {
        showNotification('Failed to save task', 'error');
        if (submitBtn) submitBtn.disabled = false;  // Re-enable on error
    }
}

// ========== TASK ACTIONS ==========
function initializeTaskActions() {
    document.querySelectorAll('.task-checkbox-epic').forEach(cb => {
        cb.addEventListener('change', function() {
            const taskCard = this.closest('.task-card-epic');
            const taskId = taskCard.dataset.taskId;
            quickComplete(taskId, this.checked);
        });
    });
}

async function quickComplete(taskId, isCompleted) {
    const newStatus = isCompleted ? 'done' : 'pending';
    await updateTaskStatus(taskId, newStatus);
}

window.editTask = function(taskId) { openTaskModal(taskId); };
window.deleteTask = async function(taskId) {
    if (!confirm('Delete this task permanently?')) return;
    try {
        const response = await fetch(`/planner/tasks/${taskId}`, {
            method: 'DELETE',
            headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content }
        });
        if (!response.ok) throw new Error();
        showNotification('Task deleted', 'success');
        const card = document.querySelector(`[data-task-id="${taskId}"]`);
        if (card) card.remove();
        updateColumnCounts();
    } catch (error) {
        showNotification('Delete failed', 'error');
    }
};
window.duplicateTask = async function(taskId) {
    try {
        const response = await fetch(`/planner/tasks/${taskId}/duplicate`, {
            method: 'POST',
            headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content }
        });
        if (!response.ok) throw new Error();
        showNotification('Task duplicated', 'success');
        setTimeout(() => location.reload(), 800);
    } catch (error) {
        showNotification('Duplicate failed', 'error');
    }
};
window.openTaskDetails = function(taskId) { openTaskModal(taskId); };
window.openTaskMenu = function(taskId, event) { event.stopPropagation(); };

// ============================================
// ✅ ASSISTANT ASSIGNMENT FUNCTIONS
// ============================================

/**
 * Show modal to assign an assistant to a task
 */
window.showAssignAssistantModal = function(taskId) {
    const modal = document.getElementById('assignAssistantModal');
    if (modal) {
        document.getElementById('assignTaskId').value = taskId;
        modal.style.display = 'block';
    } else {
        // Fallback to prompt if modal doesn't exist
        const assistantId = prompt('Enter Assistant ID to assign:');
        if (assistantId) {
            assignAssistantToTask(taskId, assistantId);
        }
    }
};

window.closeAssignModal = function() {
    const modal = document.getElementById('assignAssistantModal');
    if (modal) modal.style.display = 'none';
};

window.confirmAssignAssistant = function() {
    const taskId = document.getElementById('assignTaskId')?.value;
    const assistantId = document.getElementById('assignAssistantSelect')?.value;

    if (!assistantId) {
        alert('Please select an assistant');
        return;
    }

    assignAssistantToTask(taskId, assistantId);
    window.closeAssignModal();
};

async function assignAssistantToTask(taskId, assistantId) {
    try {
        const response = await fetch(`/planner/tasks/${taskId}/assign`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Accept': 'application/json'
            },
            body: JSON.stringify({ assistant_id: assistantId })
        });
        const data = await response.json();
        if (data.success) {
            showNotification(data.message, 'success');
            location.reload();
        } else {
            showNotification(data.message || 'Failed to assign', 'error');
        }
    } catch (error) {
        showNotification('Failed to assign assistant', 'error');
    }
}

window.removeAssistant = async function(taskId, assistantId) {
    if (!confirm('Remove this assistant from the task?')) return;

    try {
        const response = await fetch(`/planner/tasks/${taskId}/unassign/${assistantId}`, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Accept': 'application/json'
            }
        });
        const data = await response.json();
        if (data.success) {
            showNotification(data.message, 'success');
            location.reload();
        } else {
            showNotification(data.message || 'Failed to remove', 'error');
        }
    } catch (error) {
        showNotification('Failed to remove assistant', 'error');
    }
};

// ============================================
// HELPERS
// ============================================

function celebrateTaskCompletion() {
    if (typeof confetti !== 'undefined') {
        const colors = powerModeActive ? ['#E19184', '#C63E4E', '#475B35', '#FFD700'] : ['#E19184', '#C63E4E'];
        confetti({ particleCount: powerModeActive ? 150 : 60, spread: 70, origin: { y: 0.6 }, colors: colors });
    }
}

function showNotification(message, type = 'success') {
    const notif = document.createElement('div');
    const bgColor = type === 'success' ? '#475B35' : type === 'error' ? '#C63E4E' : '#E19184';
    notif.innerHTML = `<i class="fas fa-${type === 'success' ? 'check-circle' : 'exclamation-circle'}"></i><span>${message}</span>`;
    notif.style.cssText = `
        position: fixed; top: 30px; right: 30px; background: ${bgColor}; color: white;
        padding: 18px 28px; border-radius: 15px; display: flex; align-items: center; gap: 12px;
        font-weight: 700; font-size: 15px; box-shadow: 0 10px 35px rgba(0,0,0,0.3);
        z-index: 100000; animation: slideInRight 0.4s ease-out, slideOutRight 0.4s ease-out 2.6s;
    `;
    document.body.appendChild(notif);
    setTimeout(() => notif.remove(), 3000);
}

// ========== VENDOR SELECTION ==========
const selectedVendors = [];

document.addEventListener('DOMContentLoaded', function() {
    const dropdown = document.getElementById('vendorDropdown');
    if (!dropdown) return;

    dropdown.addEventListener('click', function(e) {
        const item = e.target.closest('.vendor-list-item');
        if (!item) return;

        const vendorId = item.dataset.vendorId;
        const vendorName = item.dataset.vendorName;
        const checkIcon = item.querySelector('.vendor-check-icon');

        if (item.classList.contains('selected')) {
            item.classList.remove('selected');
            checkIcon.style.color = 'transparent';
            checkIcon.style.background = 'transparent';
            checkIcon.style.borderColor = '#ddd';
            const index = selectedVendors.findIndex(v => v.id === vendorId);
            if (index > -1) selectedVendors.splice(index, 1);
        } else {
            item.classList.add('selected');
            checkIcon.style.color = 'white';
            checkIcon.style.background = '#C63E4E';
            checkIcon.style.borderColor = '#C63E4E';
            selectedVendors.push({ id: vendorId, name: vendorName });
        }

        updateVendorSelectText();
    });

    // Close dropdown when clicking outside
    document.addEventListener('click', function(e) {
        if (!e.target.closest('#vendorSelectBox') && !e.target.closest('#vendorDropdown')) {
            const dropdown = document.getElementById('vendorDropdown');
            if (dropdown && !dropdown.classList.contains('hidden')) {
                dropdown.classList.add('hidden');
                window.removeEventListener('scroll', vendorDropdownReposition, true);
                window.removeEventListener('resize', vendorDropdownReposition);
            }
        }
    });
});

function updateVendorSelectText() {
    const textEl = document.getElementById('vendorSelectText');
    if (selectedVendors.length === 0) {
        textEl.textContent = 'No vendors selected';
        textEl.style.color = '#999';
    } else {
        textEl.textContent = selectedVendors.map(v => v.name).join(', ');
        textEl.style.color = '#333';
    }
}

function getSelectedVendorIds() {
    return selectedVendors.map(v => v.id);
}

// ---- Vendor dropdown open/close + positioning (fixed, viewport-relative) ----
function toggleVendorDropdown() {
    const dropdown = document.getElementById('vendorDropdown');
    const box = document.getElementById('vendorSelectBox');
    const arrow = document.getElementById('vendorArrow');
    if (!dropdown || !box) return;

    const isHidden = dropdown.classList.contains('hidden');

    if (isHidden) {
        positionVendorDropdown(box, dropdown);
        dropdown.classList.remove('hidden');
        // Keep it glued to the select box if the modal scrolls or the window resizes
        window.addEventListener('scroll', vendorDropdownReposition, true);
        window.addEventListener('resize', vendorDropdownReposition);
    } else {
        dropdown.classList.add('hidden');
        window.removeEventListener('scroll', vendorDropdownReposition, true);
        window.removeEventListener('resize', vendorDropdownReposition);
    }

    if (arrow) arrow.classList.toggle('open');
}

function vendorDropdownReposition() {
    const dropdown = document.getElementById('vendorDropdown');
    const box = document.getElementById('vendorSelectBox');
    if (!dropdown || !box || dropdown.classList.contains('hidden')) return;
    positionVendorDropdown(box, dropdown);
}

function positionVendorDropdown(box, dropdown) {
    const rect = box.getBoundingClientRect();
    const dropdownMaxHeight = 150; // matches CSS max-height
    const spaceBelow = window.innerHeight - rect.bottom;
    const spaceAbove = rect.top;

    const openUpward = spaceBelow < dropdownMaxHeight && spaceAbove > spaceBelow;

    dropdown.style.position = 'fixed';
    dropdown.style.left = rect.left + 'px';
    dropdown.style.width = rect.width + 'px';
    dropdown.style.zIndex = '100001';

    if (openUpward) {
        dropdown.style.bottom = (window.innerHeight - rect.top) + 'px';
        dropdown.style.top = 'auto';
        dropdown.style.borderRadius = '8px 8px 0 0';
        dropdown.style.borderTop = '1px solid #ddd';
        dropdown.style.borderBottom = 'none';
    } else {
        dropdown.style.top = rect.bottom + 'px';
        dropdown.style.bottom = 'auto';
        dropdown.style.borderRadius = '0 0 8px 8px';
        dropdown.style.borderTop = 'none';
        dropdown.style.borderBottom = '1px solid #ddd';
    }
}

// Inject keyframe animations and modal styles
if (!document.querySelector('#tasks-dynamic-styles')) {
    const style = document.createElement('style');
    style.id = 'tasks-dynamic-styles';
    style.textContent = `
        @keyframes slideInRight {
            from { transform: translateX(400px); opacity: 0; }
            to { transform: translateX(0); opacity: 1; }
        }
        @keyframes slideOutRight {
            from { transform: translateX(0); opacity: 1; }
            to { transform: translateX(400px); opacity: 0; }
        }
        .dragging { opacity: 0.5; cursor: grabbing; }
        .focus-timer-modal {
            display: none;
            position: fixed;
            inset: 0;
            z-index: 100000;
            align-items: center;
            justify-content: center;
        }
        .focus-timer-modal.active {
            display: flex;
        }
        .focus-modal-overlay {
            position: absolute;
            inset: 0;
            background: rgba(0,0,0,0.7);
            backdrop-filter: blur(8px);
        }
        .focus-modal-content {
            position: relative;
            background: white;
            border-radius: 30px;
            width: 90%;
            max-width: 450px;
            padding: 30px;
            z-index: 1;
            animation: slideUp 0.3s ease;
        }
        .focus-modal-header {
            text-align: center;
            margin-bottom: 25px;
        }
        .focus-modal-icon {
            width: 60px;
            height: 60px;
            background: linear-gradient(135deg, var(--coral), var(--berry));
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 15px;
            font-size: 28px;
            color: white;
        }
        .focus-modal-header h2 {
            font-size: 24px;
            margin-bottom: 5px;
            color: var(--green);
        }
        .focus-modal-header p {
            color: #7F8C8D;
            font-size: 14px;
        }
        .focus-field {
            margin-bottom: 20px;
        }
        .focus-field label {
            display: block;
            font-weight: 700;
            margin-bottom: 8px;
            color: var(--green);
        }
        .focus-input {
            width: 100%;
            padding: 12px;
            border: 2px solid var(--cream);
            border-radius: 12px;
            font-size: 14px;
            transition: 0.3s;
        }
        .focus-input:focus {
            outline: none;
            border-color: var(--coral);
        }
        .focus-modal-actions {
            display: flex;
            gap: 15px;
            margin-top: 25px;
        }
        .focus-cancel-btn, .focus-start-btn {
            flex: 1;
            padding: 12px;
            border-radius: 12px;
            font-weight: 700;
            cursor: pointer;
            transition: 0.3s;
            border: none;
        }
        .focus-cancel-btn {
            background: var(--cream);
            color: var(--green);
        }
        .focus-start-btn {
            background: linear-gradient(135deg, var(--coral), var(--berry));
            color: white;
        }
        .focus-start-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 15px rgba(225,145,132,0.4);
        }
        .focus-modal-close {
            position: absolute;
            top: 15px;
            right: 20px;
            background: none;
            border: none;
            font-size: 20px;
            cursor: pointer;
            color: var(--green);
        }
        .timer-active {
            background: linear-gradient(135deg, #D0021B, #A00116) !important;
            border: none !important;
        }
        @keyframes slideUp {
            from { transform: translateY(50px); opacity: 0; }
            to { transform: translateY(0); opacity: 1; }
        }
    `;
    document.head.appendChild(style);
}
