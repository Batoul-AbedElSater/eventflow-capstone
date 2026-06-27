<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Notification extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'type',
        'priority',
        'title',
        'message',
        'icon',
        'action_url',
        'is_read',
        'is_archived',
        'read_at',
    ];

    protected $casts = [
        'is_read' => 'boolean',
        'is_archived' => 'boolean',
        'read_at' => 'datetime',
    ];

    // Relationships
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Scopes
    public function scopeUnread($query)
    {
        return $query->where('is_read', false);
    }

    public function scopeActive($query)
    {
        return $query->where('is_archived', false);
    }

    public function scopePriority($query, $priority)
    {
        return $query->where('priority', $priority);
    }

    // Helpers
    public function markAsRead()
    {
        $this->update([
            'is_read' => true,
            'read_at' => now(),
        ]);
    }

    public function archive()
    {
        $this->update(['is_archived' => true]);
    }

    public function getColorClass()
    {
        return match($this->priority) {
            'low' => 'notification-blue',
            'medium' => 'notification-yellow',
            'high' => 'notification-orange',
            'urgent' => 'notification-red',
            default => 'notification-blue'
        };
    }

 public function getIconClass()
{
    if ($this->icon) {
        return $this->icon;
    }

    return match($this->type) {
        'task' => 'fas fa-tasks',
        'order' => 'fas fa-shopping-cart',
        'event' => 'fas fa-calendar',
        'request' => 'fas fa-inbox',
        'message' => 'fas fa-envelope',
        'weather' => 'fas fa-cloud-sun',
        'conflict' => 'fas fa-exclamation-triangle',
        'health' => 'fas fa-heartbeat',
        default => 'fas fa-bell'
    };
}
}