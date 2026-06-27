<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Task extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',  
        'assigned_planner_id',    
        'event_id',
        'title',
        'description',
        'priority',
        'status',
        'due_date',
        'progress',
        'completed_at',
    ];

    protected $casts = [
        'due_date'     => 'datetime',
        'completed_at' => 'datetime',
    ];

    
    public function creator()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function event()
    {
        return $this->belongsTo(Event::class);
    }

   
    public function assignments()
    {
        return $this->hasMany(TaskAssignment::class);
    }


    public function assistants()
    {
        return $this->belongsToMany(
            User::class,
            'task_assignments',
            'task_id',
            'assistant_id'
        )->withPivot('assigned_by')->withTimestamps();
    }

   

    public function scopeForAssistant($query, $assistantId)
    {
        return $query->whereHas('assignments', function ($q) use ($assistantId) {
            $q->where('assistant_id', $assistantId);
        });
    }

    public function scopeCreatedBy($query, $plannerId)
    {
        return $query->where('user_id', $plannerId);
    }

    public function scopeUrgent($query)
    {
        return $query->where('priority', 'urgent')->where('status', '!=', 'completed');
    }

    public function vendors()
    {
    return $this->belongsToMany(Vendor::class, 'task_vendor')->withTimestamps();
    }
    public function vendorOrders()
    {
    return $this->hasMany(VendorOrder::class);
    }

}