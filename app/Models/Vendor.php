<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Vendor extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'name',
        'category',
        'email',
        'phone',
        'website',
        'rating_avg',
        'review_count',
        'description',
        'location',
    ];

    /**
     * Get the attributes that should be cast.
     */
    protected function casts(): array
    {
        return [
            'rating_avg' => 'decimal:2',
        ];
    }

    // ========================================
    // RELATIONSHIPS
    // ========================================

    /**
     * Get events associated with this vendor (through pivot table).
     * Many-to-Many: Vendor -> Events
     */
    public function events()
    {
        return $this->belongsToMany(Event::class, 'event_vendors')
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
     * One-to-Many: Vendor -> EventVendors
     */
    public function eventVendors()
    {
        return $this->hasMany(EventVendor::class);
    }

    /**
     * Get reviews for this vendor.
     * One-to-Many: Vendor -> VendorReviews
     */
    public function reviews()
    {
        return $this->hasMany(VendorReview::class);
    }

    /**
     * Get expenses paid to this vendor.
     * One-to-Many: Vendor -> Expenses
     */
    public function expenses()
    {
        return $this->hasMany(Expense::class);
    }

    // ========================================
    // HELPER METHODS
    // ========================================

    /**
     * Update vendor rating based on reviews.
     */
    public function updateRating(): void
    {
        $this->update([
            'rating_avg' => $this->reviews()->avg('rating') ?? 0,
            'review_count' => $this->reviews()->count(),
        ]);
    }

    /**
     * Check if vendor has reviews.
     */
    public function hasReviews(): bool
    {
        return $this->review_count > 0;
    }

    /**
     * Get formatted rating display.
     */
    public function getRatingDisplay(): string
    {
        if (!$this->hasReviews()) {
            return 'No reviews yet';
        }
        
        return "⭐ {$this->rating_avg} ({$this->review_count} reviews)";
    }

    /**
     * Get total times booked.
     */
    public function getTotalBookings(): int
    {
        return $this->eventVendors()
                    ->where('status', 'booked')
                    ->count();
    }

    /**
     * Check if vendor is available (not deleted, active).
     */
    public function isAvailable(): bool
    {
        return !$this->trashed(); // If using soft deletes
    }
}
