<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Task extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'event_id',
        'assigned_planner_id',
        'title',
        'description',
        'due_date',
        'status',
        'order_index',
        'source',
        'completed_at',
        'depends_on_task_id',
    ];

    /**
     * Get the attributes that should be cast.
     */
    protected function casts(): array
    {
        return [
            'due_date' => 'date',
            'completed_at' => 'datetime',
        ];
    }

    // ========================================
    // RELATIONSHIPS
    // ========================================

    /**
     * Get the event this task belongs to.
     * Many-to-One: Task -> Event
     */
    public function event()
    {
        return $this->belongsTo(Event::class);
    }

    /**
     * Get the planner assigned to this task.
     * Many-to-One: Task -> User (planner)
     */
    public function assignedPlanner()
    {
        return $this->belongsTo(User::class, 'assigned_planner_id');
    }

    /**
     * Get the task this task depends on (self-referencing).
     * Many-to-One: Task -> Task
     */
    public function dependsOnTask()
    {
        return $this->belongsTo(Task::class, 'depends_on_task_id');
    }

    /**
     * Get tasks that depend on this task (inverse).
     * One-to-Many: Task -> Tasks
     */
    public function dependentTasks()
    {
        return $this->hasMany(Task::class, 'depends_on_task_id');
    }

    /**
     * Get attachments for this task.
     * One-to-Many: Task -> Attachments
     */
    public function attachments()
    {
        return $this->hasMany(Attachment::class);
    }

    // ========================================
    // HELPER METHODS
    // ========================================

    /**
     * Check if task is pending.
     */
    public function isPending(): bool
    {
        return $this->status === 'pending';
    }

    /**
     * Check if task is in progress.
     */
    public function isInProgress(): bool
    {
        return $this->status === 'in_progress';
    }

    /**
     * Check if task is done.
     */
    public function isDone(): bool
    {
        return $this->status === 'done';
    }

    /**
     * Check if task is a default task.
     */
    public function isDefault(): bool
    {
        return $this->source === 'default';
    }

    /**
     * Check if task is custom.
     */
    public function isCustom(): bool
    {
        return $this->source === 'custom';
    }

    /**
     * Check if task has dependency.
     */
    public function hasDependency(): bool
    {
        return !is_null($this->depends_on_task_id);
    }

    /**
     * Check if dependency is complete.
     */
    public function isDependencyComplete(): bool
    {
        if (!$this->hasDependency()) {
            return true; // No dependency means can proceed
        }
        
        return $this->dependsOnTask && $this->dependsOnTask->isDone();
    }

    /**
     * Check if task can be started (dependency complete).
     */
    public function canStart(): bool
    {
        return $this->isDependencyComplete();
    }

    /**
     * Check if task is overdue.
     */
    public function isOverdue(): bool
    {
        if ($this->isDone()) {
            return false; // Completed tasks are never overdue
        }
        
        return $this->due_date->isPast();
    }

    /**
     * Get days until due.
     */
    public function getDaysUntilDue(): int
    {
        return now()->diffInDays($this->due_date, false);
    }

    /**
     * Mark task as in progress.
     */
    public function markAsInProgress(): void
    {
        $this->update([
            'status' => 'in_progress',
        ]);
    }

    /**
     * Mark task as complete.
     */
    public function markAsComplete(): void
    {
        $this->update([
            'status' => 'done',
            'completed_at' => now(),
        ]);

        // Create notification for client
        Notification::create([
            'user_id' => $this->event->client_id,
            'event_id' => $this->event_id,
            'type' => 'task_due',
            'data_json' => [
                'task_id' => $this->id,
                'task_title' => $this->title,
                'completed_by' => $this->assignedPlanner->name,
                'status' => 'completed',
            ],
            'is_read' => false,
        ]);
    }

    /**
     * Get task status emoji.
     */
    public function getStatusEmoji(): string
    {
        return match($this->status) {
            'pending' => '⏸️',
            'in_progress' => '▶️',
            'done' => '✅',
            default => '❓'
        };
    }

    /**
     * Get task summary.
     */
    public function getSummary(): string
    {
        $status = $this->getStatusEmoji();
        $daysUntil = $this->getDaysUntilDue();
        
        $summary = "{$status} {$this->title} - Due: {$this->due_date->format('M d, Y')}";
        
        if (!$this->isDone()) {
            if ($this->isOverdue()) {
                $summary .= " (Overdue by " . abs($daysUntil) . " days)";
            } else {
                $summary .= " ({$daysUntil} days remaining)";
            }
        }
        
        return $summary;
    }
}
