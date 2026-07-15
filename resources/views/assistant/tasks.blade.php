@extends('layouts.assistant')

@section('title', 'My Tasks')
@section('page-title', 'My Tasks')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/assistant-tasks.css') }}">
@endpush

@section('content')

{{-- Flash message --}}
@if(session('success'))
    <div class="flash-success">
        <i class="fas fa-check-circle"></i> {{ session('success') }}
    </div>
@endif

{{-- Welcome Banner (Command Center style, stats built into the header) --}}
<div class="welcome-banner">
    <div class="banner-left">
        <h2>Welcome back, {{ $user->name }}!</h2>
        <p>Here's everything assigned to you. Let's get things done.</p>
    </div>

    <div class="banner-stats">
        <div class="banner-stat">
            <div class="banner-stat-value">{{ $totalTasks }}</div>
            <div class="banner-stat-label">Total</div>
        </div>
        <div class="banner-stat urgent">
            <div class="banner-stat-value">{{ $urgentTasks }}</div>
            <div class="banner-stat-label">Urgent</div>
        </div>
        <div class="banner-stat in-progress">
            <div class="banner-stat-value">{{ $inProgressTasks }}</div>
            <div class="banner-stat-label">In Progress</div>
        </div>
        <div class="banner-stat completed">
            <div class="banner-stat-value">{{ $completedTasks }}</div>
            <div class="banner-stat-label">Completed</div>
        </div>
    </div>
</div>

{{-- Filter Bar --}}
<div class="filter-bar">
    <span class="section-title">Assigned Tasks</span>
    <a href="{{ route('assistant.tasks', ['filter' => 'all']) }}"
       class="filter-chip {{ $filter === 'all' ? 'active' : '' }}">All</a>
    <a href="{{ route('assistant.tasks', ['filter' => 'todo']) }}"
       class="filter-chip {{ $filter === 'todo' ? 'active' : '' }}">To Do</a>
    <a href="{{ route('assistant.tasks', ['filter' => 'in_progress']) }}"
       class="filter-chip {{ $filter === 'in_progress' ? 'active' : '' }}">In Progress</a>
    <a href="{{ route('assistant.tasks', ['filter' => 'urgent']) }}"
       class="filter-chip urgent-filter {{ $filter === 'urgent' ? 'active' : '' }}">Urgent</a>
    <a href="{{ route('assistant.tasks', ['filter' => 'done']) }}"
       class="filter-chip done-filter {{ $filter === 'done' ? 'active' : '' }}">Done</a>
</div>

{{-- Task List --}}
<div class="tasks-list">
    @forelse($tasks as $task)
        <div class="task-card priority-{{ $task->priority }} status-{{ $task->status }}">

            {{-- Check circle --}}
            <div class="task-check {{ $task->status === 'done' ? 'checked' : '' }}">
                @if($task->status === 'done')
                    <i class="fas fa-check"></i>
                @endif
            </div>

            {{-- Body --}}
            <div class="task-body">
                <div class="task-title">{{ $task->title }}</div>

                <div class="task-meta">
                    @if($task->event)
                        <span class="task-event-tag">
                            <i class="fas fa-calendar-alt"></i>
                            {{ $task->event->name }}
                        </span>
                    @endif

                    <span class="tag tag-{{ $task->priority }}">{{ ucfirst($task->priority) }}</span>
                    <span class="tag tag-{{ $task->status }}">{{ ucfirst(str_replace('_', ' ', $task->status)) }}</span>
                </div>

                {{-- Planner Name --}}
                @if($task->assignments->first() && $task->assignments->first()->planner)
                    <div class="task-planner-row">
                        <i class="fas fa-user-tie"></i>
                        Assigned by: <strong>{{ $task->assignments->first()->planner->name }}</strong>
                    </div>
                @endif

                @if($task->description)
                    <div class="task-note">
                        <i class="fas fa-comment-dots"></i>
                        {{ $task->description }}
                    </div>
                @endif

                {{-- Progress bar --}}
                <div class="task-progress">
                    <div class="progress-label">
                        <span>Progress</span>
                        <span>{{ $task->progress }}%</span>
                    </div>
                    <div class="progress-bar">
                        <div class="progress-fill {{ $task->progress == 100 ? 'done' : '' }}"
                             style="width: {{ $task->progress }}%"></div>
                    </div>
                </div>

                @if($task->vendors && $task->vendors->count() > 0)
                    <a href="{{ route('assistant.tasks.vendors', $task->id) }}" class="btn-vendors" style="margin-top: 8px; align-self: flex-start;">
                        <i class="fas fa-store"></i> View Vendors ({{ $task->vendors->count() }})
                    </a>
                @endif
            </div>

            {{-- Right --}}
            <div class="task-right">
                @if($task->due_date)
                    <div class="task-due {{ $task->due_date->isPast() && $task->status !== 'done' ? 'overdue' : '' }}">
                        <i class="fas fa-clock"></i>
                        {{ $task->due_date->isPast() && $task->status !== 'done' ? 'Overdue · ' : '' }}
                        {{ $task->due_date->format('M d') }}
                    </div>
                @endif

                @if($task->status !== 'done')
                    <form method="POST" action="{{ route('assistant.tasks.complete', $task->id) }}">
                        @csrf
                        @method('PATCH')
                        <button type="submit" class="btn-done">
                            <i class="fas fa-check"></i> Mark Done
                        </button>
                    </form>
                @else
                    <span style="font-size:12px; color: var(--green); font-weight:700;">
                        <i class="fas fa-check-circle"></i> Done
                    </span>
                @endif
            </div>

        </div>
    @empty
        <div class="empty-state">
            <i class="fas fa-clipboard"></i>
            <p>No tasks assigned yet.</p>
        </div>
    @endforelse
</div>

@endsection