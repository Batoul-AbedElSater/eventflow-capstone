<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasFactory, Notifiable, HasApiTokens;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'phone',
        'notification_preferences',
        'avatar_url',
    ];

    /**
     * The attributes that should be hidden for serialization.
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

       /**
     * Get events where user is the planner
     */
    public function EventsOfPlanner()
    {
        return $this->hasMany(Event::class, 'planner_id');
    }

    // ========================================
    // RELATIONSHIPS
    // ========================================

    /**
     * Get the client profile for this user.
     * One-to-One: User -> ClientProfile
     */
    public function clientProfile()
    {
        return $this->hasOne(ClientProfile::class);
    }

    /**
     * Get the planner profile for this user.
     * One-to-One: User -> PlannerProfile
     */
    public function plannerProfile()
    {
        return $this->hasOne(PlannerProfile::class);
    }

    /**
     * Get events where this user is the client.
     * One-to-Many: User (client) -> Events
     */
    public function clientEvents()
    {
        return $this->hasMany(Event::class, 'client_id');
    }

    /**
     * Get events where this user is the planner.
     * One-to-Many: User (planner) -> Events
     */
    public function plannerEvents()
    {
        return $this->hasMany(Event::class, 'planner_id');
    }

    /**
     * Get tasks assigned to this user (planner).
     * One-to-Many: User (planner) -> Tasks
     */
    public function assignedTasks()
    {
        return $this->hasMany(Task::class, 'assigned_planner_id');
    }

    /**
     * Get message threads where this user is the client.
     * One-to-Many: User (client) -> MessageThreads
     */
    public function clientThreads()
    {
        return $this->hasMany(MessageThread::class, 'client_id');
    }

    /**
     * Get message threads where this user is the planner.
     * One-to-Many: User (planner) -> MessageThreads
     */
    public function plannerThreads()
    {
        return $this->hasMany(MessageThread::class, 'planner_id');
    }

        /**
     * Get unread messages count
     */
    public function unreadMessagesCount()
    {
        return \App\Models\Message::whereHas('thread', function($query) {
            $query->where('client_id', $this->id);
        })
        ->where('sender_id', '!=', $this->id)
        ->where('is_read', false)
        ->count();
    }

    /**
     * Get messages sent by this user.
     * One-to-Many: User -> Messages
     */
    public function sentMessages()
    {
        return $this->hasMany(Message::class, 'sender_id');
    }

    /**
     * Get notifications for this user.
     * One-to-Many: User -> Notifications
     */
    public function notifications()
    {
        return $this->hasMany(Notification::class);
    }

    /**
     * Get notification preferences for this user.
     * One-to-One: User -> NotificationPreference
     */
    public function notificationPreference()
    {
        return $this->hasOne(NotificationPreference::class);
    }

    /**
     * Get vendor reviews written by this user (client).
     * One-to-Many: User (client) -> VendorReviews
     */
    public function vendorReviews()
    {
        return $this->hasMany(VendorReview::class, 'client_id');
    }

    // ========================================
    // HELPER METHODS
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
     * Get user's full profile (client or planner).
     */
    public function profile()
    {
        return $this->isClient() 
            ? $this->clientProfile 
            : $this->plannerProfile;
    }
}

