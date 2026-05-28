<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Task extends Model
{
    use HasFactory;

   protected $fillable = [
        'user_id',
        'user_id',
        'event_id',
        'title',
        'description',
        'priority',
        'status',
        'due_date',
        'deadline',
        'progress',
        'completed_at',
        'order_index',
        'source',
    ];

    protected $casts = [
        'due_date' => 'datetime',
        'deadline' => 'datetime',
        'completed_at' => 'datetime',
        'progress' => 'integer',
    ];

    /**
     * Get the user who owns this task
     */
    public function user() // CHANGED from planner
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function planner()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Get the event this task belongs to
     */
    public function event()
    {
        return $this->belongsTo(Event::class);
    }

    /**
     * Check if task is overdue
     */
    public function isOverdue()
    {
        return $this->due_date && now()->gt($this->due_date) && $this->status !== 'completed';
    }

    /**
     * Check if task is urgent (less than 24 hours)
     */
    public function isUrgent()
    {
        if (!$this->due_date || $this->status === 'completed') {
            return false;
        }

        $hoursUntil = now()->diffInHours($this->due_date, false);
        return $hoursUntil > 0 && $hoursUntil < 24;
    }
}
