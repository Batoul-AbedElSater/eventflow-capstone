@extends('layouts.planner')

@section('title', 'Tasks Command Center')

@section('content')
<div class="tasks-page-epic">

    <!-- Hero Header -->
    <div class="tasks-header-epic">
        <div class="header-content-epic">
            <div class="header-text-epic">
                <h1>Tasks Command Center</h1>
            </div>
        </div>

        <div class="header-actions-epic">
            <button class="action-btn-epic power-mode-btn" id="powerModeBtn" type="button">
                <i class="fas fa-bolt"></i> Power Mode
            </button>
            <button class="action-btn-epic create-btn" id="createTaskBtn" type="button">
                <i class="fas fa-plus"></i> New Task
            </button>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="stats-wave-cards">
        <div class="wave-card todo">
            <div class="wave-card-icon"><i class="fas fa-list"></i></div>
            <div class="wave-card-content">
                <span class="wave-card-number">{{ $stats['todo'] }}</span>
                <span class="wave-card-label">To Do</span>
            </div>
        </div>
        <div class="wave-card progress">
            <div class="wave-card-icon"><i class="fas fa-spinner"></i></div>
            <div class="wave-card-content">
                <span class="wave-card-number">{{ $stats['in_progress'] }}</span>
                <span class="wave-card-label">In Progress</span>
            </div>
        </div>
        <div class="wave-card completed">
            <div class="wave-card-icon"><i class="fas fa-check-circle"></i></div>
            <div class="wave-card-content">
                <span class="wave-card-number">{{ $stats['completed_today'] }}</span>
                <span class="wave-card-label">Completed Today</span>
            </div>
        </div>
    </div>

    <!-- Kanban Board -->
    <div class="kanban-board-epic">
        <!-- Pending Column -->
        <div class="task-column" data-status="pending">
            <div class="column-header pending-header">
                <div class="column-title"><i class="fas fa-clock"></i><span>To Do</span></div>
                <span class="task-count">{{ $tasks->where('status', 'pending')->count() }}</span>
            </div>
            <div class="tasks-drop-zone" data-status="pending" id="pendingTasks">
                @foreach($tasks->where('status', 'pending') as $task)
                    @include('planner.tasks.partials.task-card', ['task' => $task])
                @endforeach
            </div>
        </div>

        <!-- In Progress Column -->
        <div class="task-column" data-status="in_progress">
            <div class="column-header progress-header">
                <div class="column-title"><i class="fas fa-spinner"></i><span>In Progress</span></div>
                <span class="task-count">{{ $tasks->where('status', 'in_progress')->count() }}</span>
            </div>
            <div class="tasks-drop-zone" data-status="in_progress" id="inProgressTasks">
                @foreach($tasks->where('status', 'in_progress') as $task)
                    @include('planner.tasks.partials.task-card', ['task' => $task])
                @endforeach
            </div>
        </div>

        <!-- Done Column -->
        <div class="task-column" data-status="done">
            <div class="column-header done-header">
                <div class="column-title"><i class="fas fa-check-circle"></i><span>Completed</span></div>
                <span class="task-count">{{ $tasks->where('status', 'done')->count() }}</span>
            </div>
            <div class="tasks-drop-zone" data-status="done" id="doneTasks">
                @foreach($tasks->where('status', 'done') as $task)
                    @include('planner.tasks.partials.task-card', ['task' => $task])
                @endforeach
            </div>
        </div>
    </div>
</div>

<!-- Task Modal (existing) -->
<div class="task-modal-epic" id="taskModal">
    <div class="task-modal-overlay" onclick="closeTaskModal()"></div>
    <div class="task-modal-content">
        <div class="task-modal-scroll-inner">
            <div class="task-modal-header">
                <h2 id="modalTitle">Create New Task</h2>
                <button class="modal-close-btn" id="closeTaskModalBtn"><i class="fas fa-times"></i></button>
            </div>
            <form id="taskForm" class="task-form-epic">
                <input type="hidden" id="taskId" name="task_id">
                <div class="form-group-epic">
                    <label for="taskTitle">Task Title *</label>
                    <input type="text" id="taskTitle" required placeholder="Enter task title...">
                </div>
                <div class="form-group-epic">
                    <label for="taskDescription">Description</label>
                    <textarea id="taskDescription" rows="3" placeholder="Add details..."></textarea>
                </div>
                <div class="form-row-epic">
                    <div class="form-group-epic">
                        <label for="taskPriority">Priority</label>
                        <select id="taskPriority">
                            <option value="low">Low</option>
                            <option value="medium" selected>Medium</option>
                            <option value="high">High</option>
                            <option value="urgent">Urgent</option>
                        </select>
                    </div>
                    <div class="form-group-epic">
                        <label for="taskEvent">Event</label>
                        <select id="taskEvent">
                            <option value="">No Event</option>
                            @foreach($events as $event)
                                <option value="{{ $event->id }}">{{ $event->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="form-group-epic">
                        <label for="taskAssistant">Assign Assistant</label>
                        <select id="taskAssistant">
                            <option value="">No Assistant</option>
                            @foreach($assistants as $assistant)
                                <option value="{{ $assistant->id }}">{{ $assistant->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="form-group-epic">
                        <label>Select Vendors</label>

                        {{-- Custom Select Box --}}
                        <div class="vendor-select-box" id="vendorSelectBox" style="border: 1px solid #ddd; border-radius: 8px; background: white; cursor: pointer; min-height: 42px; padding: 8px 12px; position: relative;" onclick="event.stopPropagation(); toggleVendorDropdown()">
                            <span id="vendorSelectText" style="color: #999; font-size: 14px;">No vendors selected</span>
                            <span style="position: absolute; right: 12px; top: 12px; color: #999;">▼</span>
                        </div>

                        {{-- Dropdown List --}}
                        <div id="vendorDropdown" class="vendor-dropdown hidden" style="border: 1px solid #ddd; border-top: none; border-radius: 0 0 8px 8px; background: white; max-height: 150px; overflow-y: auto; z-index: 1000;">
                            @foreach($vendors as $vendor)
                                <div class="vendor-list-item"
                                     data-vendor-id="{{ $vendor->id }}"
                                     data-vendor-name="{{ $vendor->name }}"
                                     style="display: flex; align-items: center; gap: 8px; padding: 10px 14px; cursor: pointer; border-bottom: 1px solid #f0f0f0;">
                                    <span class="vendor-check-icon" style="width: 20px; height: 20px; border: 2px solid #ddd; border-radius: 4px; display: flex; align-items: center; justify-content: center; font-size: 12px; color: transparent;">✓</span>
                                    <span style="font-size: 14px; color: #333;">{{ $vendor->name }}</span>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
                <div class="form-row-epic">
                    <div class="form-group-epic">
                        <label for="taskDueDate">Due Date</label>
                        <input type="datetime-local" id="taskDueDate">
                    </div>
                    <div class="form-group-epic">
                        <label>Progress <span class="progress-value" id="progressValue">0%</span></label>
                        <input type="range" id="taskProgress" min="0" max="100" value="0">
                    </div>
                </div>
                <div class="form-actions-epic">
                    <button type="button" class="btn-secondary-epic" id="cancelTaskBtn">Cancel</button>
                    <button type="submit" class="btn-primary-epic"> Save Task</button>
                </div>
            </form>
        </div><!-- /.task-modal-scroll-inner -->
    </div><!-- /.task-modal-content -->
</div><!-- /.task-modal-epic -->

<!-- Achievement Popup -->
<div class="achievement-popup" id="achievementPopup">
    <div class="achievement-content">
        <div class="achievement-icon"><i class="fas fa-trophy"></i></div>
        <div class="achievement-text">
            <h3 id="achievementTitle">Achievement Unlocked!</h3>
            <p id="achievementDescription">You're awesome!</p>
        </div>
    </div>
</div>



<link rel="stylesheet" href="{{ asset('css/planner-tasks.css') }}">
<script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.0/Sortable.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/canvas-confetti@1.6.0/dist/confetti.browser.min.js"></script>
<script src="{{ asset('js/planner-tasks.js') }}"></script>
@endsection
