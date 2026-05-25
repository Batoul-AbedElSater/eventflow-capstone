<div class="task-card-epic" 
     data-task-id="{{ $task->id }}" 
     data-priority="{{ $task->priority ?? 'medium' }}"
     data-status="{{ $task->status }}"
     draggable="true">
    
    <div class="task-card-header">
        <div class="task-checkbox-container">
            <input type="checkbox" 
                   class="task-checkbox-epic" 
                   id="task-{{ $task->id }}"
                   {{ in_array($task->status, ['done', 'completed']) ? 'checked' : '' }}
                   onchange="quickComplete({{ $task->id }}, this.checked)">
            <label for="task-{{ $task->id }}" class="checkbox-label-epic"></label>
        </div>

        <div class="priority-indicator {{ $task->priority ?? 'medium' }}">
            @if(($task->priority ?? 'medium') === 'urgent')
                <i class="fas fa-exclamation-circle"></i>
            @elseif(($task->priority ?? 'medium') === 'high')
                <i class="fas fa-arrow-up"></i>
            @elseif(($task->priority ?? 'medium') === 'medium')
                <i class="fas fa-minus"></i>
            @else
                <i class="fas fa-arrow-down"></i>
            @endif
        </div>

        <button class="task-menu-btn" onclick="openTaskMenu({{ $task->id }}, event)">
            <i class="fas fa-ellipsis-h"></i>
        </button>
    </div>

    <div class="task-card-body" onclick="openTaskDetails({{ $task->id }})">
        <h4 class="task-title">{{ $task->title }}</h4>
        
        @if($task->description)
            <p class="task-description">{{ Str::limit($task->description, 80) }}</p>
        @endif

        @if($task->event)
            <div class="task-event-tag">
                <i class="fas fa-calendar"></i>
                <span>{{ Str::limit($task->event->name, 20) }}</span>
            </div>
        @endif

        @if($task->progress > 0)
            <div class="task-progress-bar-container">
                <div class="task-progress-bar" style="width: {{ $task->progress }}%"></div>
                <span class="task-progress-text">{{ $task->progress }}%</span>
            </div>
        @endif
    </div>

    <div class="task-card-footer">
        @if($task->due_date ?? $task->deadline)
            @php
                $dueDate = $task->due_date ?? $task->deadline;
                $hoursUntil = now()->diffInHours($dueDate, false);
                $isOverdue = $hoursUntil < 0;
                $isUrgent = $hoursUntil > 0 && $hoursUntil < 24;
            @endphp
            <div class="task-due-date {{ $isOverdue ? 'overdue' : ($isUrgent ? 'urgent' : '') }}">
                <i class="fas fa-clock"></i>
                <span>
                    @if($isOverdue)
                        Overdue
                    @else
                        {{ $dueDate->format('M d, h:i A') }}
                    @endif
                </span>
            </div>
        @endif

        <div class="task-actions">
            <button class="task-action-btn edit" onclick="editTask({{ $task->id }})" title="Edit">
                <i class="fas fa-edit"></i>
            </button>
            <button class="task-action-btn duplicate" onclick="duplicateTask({{ $task->id }})" title="Duplicate">
                <i class="fas fa-copy"></i>
            </button>
            <button class="task-action-btn delete" onclick="deleteTask({{ $task->id }})" title="Delete">
                <i class="fas fa-trash"></i>
            </button>
        </div>
    </div>

    <div class="task-glow-effect"></div>
</div>