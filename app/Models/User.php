<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'phone',
        'avatar_url',
        'notification_preferences',
        'bio',
        'theme',
        'language',
        'timezone',
        'email_notifications',
        'push_notifications',
        'event_reminders',
        'marketing_emails',
        'two_factor_enabled',
        'is_active',
    ];

    /**
     * The attributes that should be hidden for serialization.
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
        'notification_preferences' => 'array',
        'email_notifications' => 'boolean',
        'push_notifications' => 'boolean',
        'event_reminders' => 'boolean',
        'marketing_emails' => 'boolean',
        'two_factor_enabled' => 'boolean',
        'is_active' => 'boolean',
    ];

    // ========================================
    // RELATIONSHIPS
    // ========================================

    /**
     * Get events where this user is the client.
     */
    public function clientEvents()
    {
        return $this->hasMany(Event::class, 'client_id');
    }

    /**
     * Get events where this user is the planner.
     */
    public function plannerEvents()
    {
        return $this->hasMany(Event::class, 'planner_id');
    }

    /**
     * Get events where this user is the planner (alias for plannerEvents).
     */
    public function eventsOfPlanner()
    {
        return $this->plannerEvents();
    }

    /**
     * Get the client profile for this user.
     */
    public function clientProfile()
    {
        return $this->hasOne(ClientProfile::class);
    }

    /**
     * Get the planner profile for this user.
     */
    public function plannerProfile()
    {
        return $this->hasOne(PlannerProfile::class);
    }

    /**
     * Ratings this planner received from clients.
     */
    public function plannerRatings()
    {
        return $this->hasMany(Rating::class, 'planner_id');
    }

    /**
     * Get the staff (assistant) profile for this user.
     */
    public function staffProfile()
    {
        return $this->hasOne(StaffProfile::class, 'user_id');
    }

    /**
     * Get messages sent by this user.
     */
    public function sentMessages()
    {
        return $this->hasMany(Message::class, 'sender_id');
    }

    /**
     * Get message threads where this user is the client.
     */
    public function clientThreads()
    {
        return $this->hasMany(MessageThread::class, 'client_id');
    }

    /**
     * Get message threads where this user is the planner.
     */
    public function plannerThreads()
    {
        return $this->hasMany(MessageThread::class, 'planner_id');
    }

    /**
     * Get unread messages count for this user.
     */
    public function unreadMessagesCount()
    {
        return Message::whereHas('thread', function ($query) {
                $query->where('client_id', $this->id)
                    ->orWhere('planner_id', $this->id);
            })
            ->where('sender_id', '!=', $this->id)
            ->where('is_read', false)
            ->count();
    }

    /**
     * Get notifications for this user.
     */
    public function notifications()
    {
        return $this->hasMany(Notification::class);
    }

    /**
     * Get notification preferences for this user.
     */
    public function notificationPreference()
    {
        return $this->hasOne(NotificationPreference::class);
    }

    /**
     * Get user preferences (general settings).
     */
    public function preferences()
    {
        return $this->hasOne(UserPreference::class);
    }

    /**
     * Get user settings (detailed settings).
     */
    public function settings()
    {
        return $this->hasOne(UserSetting::class);
    }

    /**
     * Get tasks assigned to this user (as assistant).
     */
    public function assignedTasks()
    {
        return $this->belongsToMany(
            Task::class,
            'task_assignments',
            'assistant_id',
            'task_id'
        )->withPivot('assigned_by')->withTimestamps();
    }

    /**
     * Get tasks this user has assigned to others.
     */
    public function assignedOutTasks()
    {
        return $this->hasMany(TaskAssignment::class, 'assigned_by');
    }

    /**
     * Get tasks this user created.
     */
    public function createdTasks()
    {
        return $this->hasMany(Task::class, 'user_id');
    }

    /**
     * Get assistants assigned to tasks by this user.
     */
    public function assistants()
    {
        return $this->hasManyThrough(
            User::class,
            TaskAssignment::class,
            'assigned_by',
            'id',
            'id',
            'assistant_id'
        )->distinct();
    }

    /**
     * Get vendors favorited by this user.
     */
    public function favoriteVendors()
    {
        return $this->belongsToMany(Vendor::class, 'user_vendor_favorites');
    }

    /**
     * Get vendor orders placed by this user (as assistant).
     */
    public function vendorOrders()
    {
        return $this->hasMany(VendorOrder::class, 'assistant_id');
    }

    // ========================================
    // ROLE CHECKS
    // ========================================

    /**
     * Check if user is a client.
     */
    public function isClient(): bool
    {
        return $this->role === 'client';
    }

    /**
     * Check if user is a planner.
     */
    public function isPlanner(): bool
    {
        return $this->role === 'planner';
    }

    /**
     * Check if user is an assistant.
     */
    public function isAssistant(): bool
    {
        return $this->role === 'assistant';
    }

    /**
     * Check if user is an admin.
     */
    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    /**
     * Get the user's profile based on role.
     */
    public function profile()
    {
        if ($this->isClient()) {
            return $this->clientProfile;
        } elseif ($this->isPlanner()) {
            return $this->plannerProfile;
        } elseif ($this->isAssistant()) {
            return $this->staffProfile;
        }
        return null;
    }

    // ========================================
    // SETTINGS HELPERS
    // ========================================

    /**
     * Get user preference by key.
     */
    public function getPreference($key, $default = null)
    {
        if ($this->preferences) {
            return $this->preferences->$key ?? $default;
        }
        return $default;
    }

    /**
     * Set user preference.
     */
    public function setPreference($key, $value)
    {
        if (!$this->preferences) {
            $this->preferences()->create([$key => $value]);
        } else {
            $this->preferences->update([$key => $value]);
        }
        return $this;
    }

    /**
     * Get user setting by key.
     */
    public function getSetting($key, $default = null)
    {
        if ($this->settings) {
            return $this->settings->$key ?? $default;
        }
        return $default;
    }

    /**
     * Set user setting.
     */
    public function setSetting($key, $value)
    {
        if (!$this->settings) {
            $this->settings()->create([$key => $value]);
        } else {
            $this->settings->update([$key => $value]);
        }
        return $this;
    }

    /**
     * Check if user has completed profile setup.
     */
    public function hasCompletedProfile(): bool
    {
        return $this->name && $this->email && $this->phone;
    }

    /**
     * Get full profile data for API.
     */
    public function getFullProfile()
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'email' => $this->email,
            'phone' => $this->phone,
            'role' => $this->role,
            'avatar' => $this->avatar,
            'bio' => $this->bio,
            'theme' => $this->theme,
            'language' => $this->language,
            'timezone' => $this->timezone,
            'profile' => $this->profile(),
            'preferences' => $this->preferences,
            'settings' => $this->settings,
        ];
    }

    // ========================================
    // OVERRIDES
    // ========================================

    /**
     * Get the user's avatar URL.
     */
public function getAvatarUrlAttribute($value)
{
    // If there is an uploaded image in the avatar_url column,
    // return only its stored path.
    if (!empty($value)) {
        return $value;
    }

    // Otherwise, no uploaded image.
    return null;
}
}