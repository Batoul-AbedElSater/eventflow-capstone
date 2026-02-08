@extends('layouts.client')

@section('title', 'Tasks - ' . $event->name)

@section('content')
<div class="tasks-container">
    
    <!-- Header -->
    <div class="page-header">
        <div class="header-left">
            <a href="{{ route('client.events.show', $event->id) }}" class="back-link">
                <i class="fas fa-arrow-left"></i> Back to Event
            </a>
            <h1>Tasks & Checklist</h1>
            <p class="event-name">{{ $event->name }}</p>
        </div>
    </div>
    <div class="header-left">
    <a href="{{ route('client.dashboard') }}" class="back-link">
        <i class="fas fa-arrow-left"></i> Back to Dashboard
    </a>

    <!-- Task Summary Cards -->
    <div class="task-summary">
        <div class="summary-card total">
            <div class="card-icon">
                <i class="fas fa-clipboard-list"></i>
            </div>
            <div class="card-content">
                <h3>{{ $totalTasks }}</h3>
                <p>Total Tasks</p>
            </div>
        </div>

        <div class="summary-card completed">
            <div class="card-icon">
                <i class="fas fa-check-circle"></i>
            </div>
            <div class="card-content">
                <h3>{{ $completedTasks }}</h3>
                <p>Completed</p>
            </div>
        </div>

        <div class="summary-card pending">
            <div class="card-icon">
                <i class="fas fa-clock"></i>
            </div>
            <div class="card-content">
                <h3>{{ $pendingTasks }}</h3>
                <p>In Progress</p>
            </div>
        </div>

        <div class="summary-card overdue">
            <div class="card-icon">
                <i class="fas fa-exclamation-triangle"></i>
            </div>
            <div class="card-content">
                <h3>{{ $overdueTasks }}</h3>
                <p>Overdue</p>
            </div>
        </div>
    </div>

    <!-- Overall Progress -->
    <div class="overall-progress-card">
        <div class="progress-header">
            <h3>Overall Progress</h3>
            <span class="progress-label">{{ $completionPercentage }}% complete</span>
        </div>
        <div class="progress-bar-container">
            <div class="progress-bar-fill" style="width: {{ $completionPercentage }}%">
                <span class="progress-text">{{ $completedTasks }} / {{ $totalTasks }} tasks</span>
            </div>
        </div>
    </div>

    @if($totalTasks > 0)
        <!-- Tasks by Status -->
        <div class="tasks-section">
            
            <!-- Pending Tasks -->
            @if($tasksByStatus['pending']->count() > 0)
                <div class="task-group">
                    <div class="group-header">
                        <h3>
                            <i class="fas fa-hourglass-half"></i>
                            Pending Tasks
                            <span class="task-count">{{ $tasksByStatus['pending']->count() }}</span>
                        </h3>
                    </div>
                    <div class="task-list">
                        @foreach($tasksByStatus['pending'] as $task)
                            @include('client.tasks.partials.task-card', ['task' => $task])
                        @endforeach
                    </div>
                </div>
            @endif

            <!-- In Progress Tasks -->
            @if($tasksByStatus['in_progress']->count() > 0)
                <div class="task-group">
                    <div class="group-header">
                        <h3>
                            <i class="fas fa-spinner"></i>
                            In Progress
                            <span class="task-count">{{ $tasksByStatus['in_progress']->count() }}</span>
                        </h3>
                    </div>
                    <div class="task-list">
                        @foreach($tasksByStatus['in_progress'] as $task)
                            @include('client.tasks.partials.task-card', ['task' => $task])
                        @endforeach
                    </div>
                </div>
            @endif

            <!-- Completed Tasks -->
            @if($tasksByStatus['completed']->count() > 0)
                <div class="task-group">
                    <div class="group-header">
                        <h3>
                            <i class="fas fa-check-circle"></i>
                            Completed
                            <span class="task-count">{{ $tasksByStatus['completed']->count() }}</span>
                        </h3>
                    </div>
                    <div class="task-list">
                        @foreach($tasksByStatus['completed'] as $task)
                            @include('client.tasks.partials.task-card', ['task' => $task])
                        @endforeach
                    </div>
                </div>
            @endif

        </div>
    @else
        <!-- Empty State -->
        <div class="empty-state">
            <i class="fas fa-tasks"></i>
            <h3>No Tasks Yet</h3>
            <p>Your event planner will create tasks and manage the checklist for your event.</p>
        </div>
    @endif

    <!-- Info Box -->
    <div class="info-box">
        <i class="fas fa-info-circle"></i>
        <div>
            <strong>Task Management</strong>
            <p>Your event planner manages all tasks and deadlines. You can track progress here and stay updated on what's being done for your event.</p>
        </div>
    </div>

</div>

<link rel="stylesheet" href="{{ asset('css/client-tasks.css') }}">
@endsection