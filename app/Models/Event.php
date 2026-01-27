<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Event extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'client_id',
        'planner_id',
        'event_type_id',
        'name',
        'description',
        'start_date',
        'end_date',
        'location_text',
        'guest_estimate',
        'budget_overall',
        'status',
        'cancelled_at',
        'completed_at',
    ];

    /**
     * Get the attributes that should be cast.
     */
    protected function casts(): array
    {
        return [
            'start_date' => 'date',
            'end_date' => 'date',
            'budget_overall' => 'decimal:2',
            'cancelled_at' => 'datetime',
            'completed_at' => 'datetime',
        ];
    }

    // ========================================
    // RELATIONSHIPS
    // ========================================

    /**
     * Get the client (user) who owns this event.
     * Many-to-One: Event -> User (client)
     */
    public function client()
    {
        return $this->belongsTo(User::class, 'client_id');
    }

    /**
     * Get the planner (user) assigned to this event.
     * Many-to-One: Event -> User (planner)
     */
    public function planner()
    {
        return $this->belongsTo(User::class, 'planner_id');
    }

    /**
     * Get the event type.
     * Many-to-One: Event -> EventType
     */
    public function eventType()
    {
        return $this->belongsTo(EventType::class, 'event_type_id');
    }

    /**
     * Get guests for this event.
     * One-to-Many: Event -> Guests
     */
    public function guests()
    {
        return $this->hasMany(Guest::class);
    }

    /**
     * Get budget categories for this event.
     * One-to-Many: Event -> BudgetCategories
     */
    public function budgetCategories()
    {
        return $this->hasMany(BudgetCategory::class);
    }

    /**
     * Get tasks for this event.
     * One-to-Many: Event -> Tasks
     */
    public function tasks()
    {
        return $this->hasMany(Task::class);
    }

    /**
     * Get message threads for this event.
     * One-to-Many: Event -> MessageThreads
     */
    public function messageThreads()
    {
        return $this->hasMany(MessageThread::class);
    }

    /**
     * Get vendors associated with this event (through pivot table).
     * Many-to-Many: Event -> Vendors
     */
    public function vendors()
    {
        return $this->belongsToMany(Vendor::class, 'event_vendors')
                    ->withPivot([
                        'status',
                        'agreed_amount',
                        'payment_status',
                        'contract_url',
                        'notes'
                    ])
                    ->withTimestamps();
    }

    /**
     * Get event-vendor pivot records directly.
     * One-to-Many: Event -> EventVendors
     */
    public function eventVendors()
    {
        return $this->hasMany(EventVendor::class);
    }

    /**
     * Get notifications for this event.
     * One-to-Many: Event -> Notifications
     */
    public function notifications()
    {
        return $this->hasMany(Notification::class);
    }

    /**
     * Get the report for this event.
     * One-to-One: Event -> Report
     */
    public function report()
    {
        return $this->hasOne(Report::class);
    }

    /**
     * Get attachments for this event.
     * One-to-Many: Event -> Attachments
     */
    public function attachments()
    {
        return $this->hasMany(Attachment::class);
    }

    // ========================================
    // HELPER METHODS
    // ========================================

    /**
     * Check if event is draft.
     */
    public function isDraft(): bool
    {
        return $this->status === 'draft';
    }

    /**
     * Check if event is completed.
     */
    public function isCompleted(): bool
    {
        return $this->status === 'completed';
    }

    /**
     * Check if event is cancelled.
     */
    public function isCancelled(): bool
    {
        return $this->status === 'cancelled';
    }

    /**
     * Check if event has a planner assigned.
     */
    public function hasPlanner(): bool
    {
        return !is_null($this->planner_id);
    }

    /**
     * Get total budget spent (sum of all category spent amounts).
     */
    public function getTotalSpent()
    {
        return $this->budgetCategories()->sum('spent_amount');
    }

    /**
     * Get budget remaining.
     */
    public function getBudgetRemaining()
    {
        return $this->budget_overall - $this->getTotalSpent();
    }

    /**
     * Get RSVP statistics.
     */
    public function getRsvpStats()
    {
        return [
            'total' => $this->guests()->count(),
            'accepted' => $this->guests()->where('rsvp_status', 'accepted')->count(),
            'declined' => $this->guests()->where('rsvp_status', 'declined')->count(),
            'pending' => $this->guests()->where('rsvp_status', 'pending')->count(),
        ];
    }
}
