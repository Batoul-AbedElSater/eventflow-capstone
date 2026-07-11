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
        <h4 class="task-title">{{ $task->title }}</h4>
    </div>

    <div class="task-card-body" onclick="openTaskDetails({{ $task->id }})">

        @if($task->description)
            <p class="task-description">{{ Str::limit($task->description, 80) }}</p>
        @endif

        @if($task->event)
            <div class="task-event-tag">
                <span>{{ Str::limit($task->event->name, 20) }}</span>
            </div>
        @endif

 @if($task->assistants && $task->assistants->first())
    {{-- Show assigned assistant --}}
    <div class="task-assistant-tag">
      <button class="assistant-icon">
     <i class="fas fa-user"></i>
</button>
        <span class="assistant-name">{{ $task->assistants->first()->name }}</span>
        <button class="remove-assistant-btn"
                onclick="event.stopPropagation(); removeAssistant({{ $task->id }}, {{ $task->assistants->first()->id }})"
                title="Remove {{ $task->assistants->first()->name }}">
        </button>
    </div>
@else
    {{-- Opens the Edit Task modal to assign assistant --}}
    <button class="assign-assistant-btn"
            onclick="event.stopPropagation(); openTaskModal({{ $task->id }})">
        <i class="fas fa-user-plus"></i> Assign Assistant
    </button>
@endif

@if($task->vendors && $task->vendors->count() > 0)
    <div class="task-vendors-row">
        <i class="fas fa-store"></i>
        <span>{{ $task->vendors->pluck('name')->join(', ') }}</span>
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
                    @if($isOverdue) Overdue
                    @else {{ $dueDate->format('M d, h:i A') }}
                    @endif
                </span>
            </div>
        @endif

        <div class="task-actions">
            <button class="task-action-btn edit" onclick="editTask({{ $task->id }})" title="Edit">
                <i class="fas fa-edit"></i>
            </button>
            <button class="task-action-btn delete" onclick="deleteTask({{ $task->id }})" title="Delete">
                <i class="fas fa-trash"></i>
            </button>
        </div>
    </div>

    <div class="task-glow-effect"></div>
</div>

