<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TaskAssignment extends Model
{
    protected $fillable = [
        'task_id',
        'assistant_id',
        'assigned_by',
    ];

    public function task()
    {
        return $this->belongsTo(Task::class);
    }
    public function assistant()
    {
        return $this->belongsTo(User::class, 'assistant_id');
    }
    public function planner()
    {
        return $this->belongsTo(User::class, 'assigned_by');
    }
}