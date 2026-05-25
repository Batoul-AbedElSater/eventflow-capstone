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
    
    // Restore timer if exists
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
                // Timer expired while away
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
            showNotification('⚡ POWER MODE ACTIVATED!', 'success');
        } else {
            this.classList.remove('active');
            document.querySelectorAll('.task-card-epic').forEach(card => {
                card.classList.remove('power-mode-active');
            });
            showNotification('Power Mode Deactivated', 'info');
        }
    });
}

// ========== FOCUS TIMER MODAL with STOP functionality & persistence ==========
function initializeFocusTimerModal() {
    const timerBtn = document.getElementById('focusTimerBtn');
    const modal = document.getElementById('focusTimerModal');
    const closeModal = document.getElementById('closeFocusModal');
    const cancelBtn = document.getElementById('cancelFocusModal');
    const startBtn = document.getElementById('startFocusBtn');
    const titleInput = document.getElementById('focusTitle');
    const minutesInput = document.getElementById('focusMinutesPopup');
    
    if (!timerBtn || !modal) return;
    
    // Remove old listener and clone to avoid duplicates
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
        
        // Save end time to localStorage
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
        
        // Update localStorage each second to sync remaining time
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
    } catch (error) {
        showNotification('Failed to load task', 'error');
    }
}

async function handleTaskSubmit(e) {
    e.preventDefault();
    const taskId = document.getElementById('taskId').value;
    const formData = {
        title: document.getElementById('taskTitle').value,
        description: document.getElementById('taskDescription').value,
        priority: document.getElementById('taskPriority').value,
        event_id: document.getElementById('taskEvent').value || null,
        due_date: document.getElementById('taskDueDate').value || null,
        progress: parseInt(document.getElementById('taskProgress').value)
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

// Inject keyframe animations and modal styles if not already present
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