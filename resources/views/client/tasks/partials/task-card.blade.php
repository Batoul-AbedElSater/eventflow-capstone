@php
    $isOverdue = $task->status !== 'completed' && $task->deadline < now();
    $daysUntilDeadline = now()->diffInDays($task->deadline, false);
@endphp

<div class="task-card {{ $task->status }} {{ $isOverdue ? 'overdue' : '' }}">
    <div class="task-header">
        <div class="task-priority {{ $task->priority }}">
            @switch($task->priority)
                @case('high')
                    <i class="fas fa-exclamation-circle"></i>
                    <span>High Priority</span>
                    @break
                @case('medium')
                    <i class="fas fa-minus-circle"></i>
                    <span>Medium</span>
                    @break
                @case('low')
                    <i class="fas fa-arrow-down"></i>
                    <span>Low</span>
                    @break
            @endswitch
        </div>
        
        <div class="task-status-badge">
            @switch($task->status)
                @case('pending')
                    <span class="badge pending">
                        <i class="fas fa-clock"></i> Pending
                    </span>
                    @break
                @case('in_progress')
                    <span class="badge in-progress">
                        <i class="fas fa-spinner"></i> In Progress
                    </span>
                    @break
                @case('completed')
                    <span class="badge completed">
                        <i class="fas fa-check-circle"></i> Completed
                    </span>
                    @break
            @endswitch
        </div>
    </div>

    <div class="task-body">
        <h4 class="task-title">{{ $task->title }}</h4>
        
        @if($task->description)
            <p class="task-description">{{ $task->description }}</p>
        @endif

        <div class="task-meta">
            <div class="meta-item">
                <i class="fas fa-calendar"></i>
                <span>
                    Deadline: 
                    <strong>{{ $task->deadline->format('M d, Y') }}</strong>
                </span>
            </div>

            @if($isOverdue)
                <div class="meta-item overdue-badge">
                    <i class="fas fa-exclamation-triangle"></i>
                    <span>Overdue by {{ abs($daysUntilDeadline) }} days</span>
                </div>
            @elseif($task->status !== 'completed')
                <div class="meta-item">
                    <i class="fas fa-hourglass-half"></i>
                    <span>
                        @if($daysUntilDeadline == 0)
                            Due today
                        @elseif($daysUntilDeadline == 1)
                            Due tomorrow
                        @else
                            {{ $daysUntilDeadline }} days left
                        @endif
                    </span>
                </div>
            @endif

            @if($task->assigned_to)
                <div class="meta-item">
                    <i class="fas fa-user"></i>
                    <span>Assigned to: <strong>{{ $task->assignedUser->name ?? 'Planner' }}</strong></span>
                </div>
            @endif
        </div>
    </div>

    @if($task->status === 'completed' && $task->completed_at)
        <div class="task-footer completed-footer">
            <i class="fas fa-check"></i>
            <span>Completed on {{ $task->completed_at->format('M d, Y') }}</span>
        </div>
    @endif
</div>