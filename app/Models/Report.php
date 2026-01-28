<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Report extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'event_id',
        'summary',                      // Text summary of event
        'guest_count_derived',          // Total guests
        'budget_utilization_derived',   // Budget used percentage
        'vendor_status_json',           // Vendor statuses as JSON
        'task_completion_rate_derived', // Task completion percentage
    ];

    /**
     * Get the attributes that should be cast.
     */
    protected function casts(): array
    {
        return [
            'vendor_status_json' => 'array',           // Cast JSON to array
            'budget_utilization_derived' => 'decimal:2', // 2 decimal places
            'task_completion_rate_derived' => 'decimal:2',
            'guest_count_derived' => 'integer',
            'created_at' => 'datetime',
            'updated_at' => 'datetime',
        ];
    }

    // ========================================
    // RELATIONSHIPS
    // ========================================

    /**
     * Get the event this report belongs to.
     * Many-to-One: Report -> Event
     */
    public function event()
    {
        return $this->belongsTo(Event::class);
    }

    // ========================================
    // HELPER METHODS
    // ========================================

    /**
     * Generate report data from event
     * This will be called to create/update report
     */
    public static function generateForEvent(Event $event): self
    {
        // Calculate guest count
        $guestCount = $event->guests()->count();

        // Calculate budget utilization
        $totalSpent = $event->budgetCategories()
            ->sum('spent_amount');
        $budgetUtilization = $event->budget_overall > 0 
            ? ($totalSpent / $event->budget_overall) * 100 
            : 0;

        // Calculate task completion rate
        $totalTasks = $event->tasks()->count();
        $completedTasks = $event->tasks()->where('status', 'done')->count();
        $taskCompletionRate = $totalTasks > 0 
            ? ($completedTasks / $totalTasks) * 100 
            : 0;

        // Get vendor statuses
        $vendorStatuses = $event->eventVendors()
            ->with('vendor')
            ->get()
            ->map(fn($ev) => [
                'vendor_name' => $ev->vendor->name,
                'status' => $ev->status,
                'payment_status' => $ev->payment_status,
            ])
            ->toArray();

        // Create or update report
        return $event->reports()->updateOrCreate(
            ['event_id' => $event->id],
            [
                'summary' => "Event report generated on " . now()->format('Y-m-d'),
                'guest_count_derived' => $guestCount,
                'budget_utilization_derived' => $budgetUtilization,
                'vendor_status_json' => $vendorStatuses,
                'task_completion_rate_derived' => $taskCompletionRate,
            ]
        );
    }

    /**
     * Check if budget is over limit
     */
    public function isBudgetOverLimit(): bool
    {
        return $this->budget_utilization_derived > 100;
    }

    /**
     * Check if tasks are mostly complete
     */
    public function areTasksMostlyComplete(): bool
    {
        return $this->task_completion_rate_derived >= 80;
    }
}
