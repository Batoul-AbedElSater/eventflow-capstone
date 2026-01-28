<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Notification extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'user_id',      // Who receives this notification
        'event_id',     // Which event it's about
        'type',         // Type of notification (rsvp_update, task_due, etc.)
        'data_json',    // Additional data as JSON
        'is_read',      // Has user read it?
    ];

    /**
     * Get the attributes that should be cast.
     */
    protected function casts(): array
    {
        return [
            'data_json' => 'array',  // Cast JSON to array automatically
            'is_read' => 'boolean',  // Cast to true/false
            'created_at' => 'datetime', // Laravel's timestamp
            'updated_at' => 'datetime',
        ];
    }

    // ========================================
    // RELATIONSHIPS
    // ========================================

    /**
     * Get the user who receives this notification.
     * Many-to-One: Notification -> User
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the event this notification is about.
     * Many-to-One: Notification -> Event
     */
    public function event()
    {
        return $this->belongsTo(Event::class);
    }

    // ========================================
    // HELPER METHODS
    // ========================================

    /**
     * Mark notification as read
     */
    public function markAsRead()
    {
        $this->update(['is_read' => true]);
    }

    /**
     * Check if notification is unread
     */
    public function isUnread(): bool
    {
        return !$this->is_read;
    }
}
