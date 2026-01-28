<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class NotificationPreference extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'user_id',
        'email_enabled',    // Send email notifications?
        'push_enabled',     // Send push notifications?
        'rsvp_alerts',      // Alert on RSVP changes?
        'task_alerts',      // Alert on task deadlines?
        'message_alerts',   // Alert on new messages?
        'budget_alerts',    // Alert on budget issues?
    ];

    /**
     * Get the attributes that should be cast.
     */
    protected function casts(): array
    {
        return [
            'email_enabled' => 'boolean',
            'push_enabled' => 'boolean',
            'rsvp_alerts' => 'boolean',
            'task_alerts' => 'boolean',
            'message_alerts' => 'boolean',
            'budget_alerts' => 'boolean',
        ];
    }

    // ========================================
    // RELATIONSHIPS
    // ========================================

    /**
     * Get the user who owns these preferences.
     * One-to-One (Inverse): NotificationPreference -> User
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // ========================================
    // HELPER METHODS
    // ========================================

    /**
     * Check if user wants email notifications
     */
    public function wantsEmailNotifications(): bool
    {
        return $this->email_enabled;
    }

    /**
     * Check if user wants push notifications
     */
    public function wantsPushNotifications(): bool
    {
        return $this->push_enabled;
    }

    /**
     * Check if specific alert type is enabled
     */
    public function isAlertEnabled(string $type): bool
    {
        return match($type) {
            'rsvp' => $this->rsvp_alerts,
            'task' => $this->task_alerts,
            'message' => $this->message_alerts,
            'budget' => $this->budget_alerts,
            default => false,
        };
    }
}
