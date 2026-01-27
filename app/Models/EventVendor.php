<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\Pivot;

class EventVendor extends Pivot
{
    /**
     * The table associated with the model.
     */
    protected $table = 'event_vendors';

    /**
     * Indicates if the IDs are auto-incrementing.
     * Composite primary key, so no auto-increment.
     */
    public $incrementing = false;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'event_id',
        'vendor_id',
        'status',
        'agreed_amount',
        'payment_status',
        'contract_url',
        'notes',
    ];

    /**
     * Get the attributes that should be cast.
     */
    protected function casts(): array
    {
        return [
            'agreed_amount' => 'decimal:2',
        ];
    }

    // ========================================
    // RELATIONSHIPS
    // ========================================

    /**
     * Get the event.
     * Many-to-One: EventVendor -> Event
     */
    public function event()
    {
        return $this->belongsTo(Event::class);
    }

    /**
     * Get the vendor.
     * Many-to-One: EventVendor -> Vendor
     */
    public function vendor()
    {
        return $this->belongsTo(Vendor::class);
    }

    // ========================================
    // HELPER METHODS
    // ========================================

    /**
     * Check if vendor is booked.
     */
    public function isBooked(): bool
    {
        return $this->status === 'booked';
    }

    /**
     * Check if vendor is shortlisted.
     */
    public function isShortlisted(): bool
    {
        return $this->status === 'shortlisted';
    }

    /**
     * Check if payment is pending.
     */
    public function isPaymentPending(): bool
    {
        return $this->payment_status === 'pending';
    }

    /**
     * Check if payment is complete.
     */
    public function isPaid(): bool
    {
        return $this->payment_status === 'paid';
    }

    /**
     * Check if payment is overdue.
     */
    public function isOverdue(): bool
    {
        return $this->payment_status === 'overdue';
    }

    /**
     * Mark as booked.
     */
    public function book(float $amount): void
    {
        $this->update([
            'status' => 'booked',
            'agreed_amount' => $amount,
        ]);
    }

    /**
     * Mark payment as paid.
     */
    public function markAsPaid(): void
    {
        $this->update([
            'payment_status' => 'paid',
        ]);
    }

    /**
     * Mark payment as overdue.
     */
    public function markAsOverdue(): void
    {
        $this->update([
            'payment_status' => 'overdue',
        ]);
    }
}
