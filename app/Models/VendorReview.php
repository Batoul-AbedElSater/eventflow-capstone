<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VendorReview extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'vendor_id',
        'event_id',
        'client_id',
        'rating',
        'comment',
    ];

    /**
     * Get the attributes that should be cast.
     */
    protected function casts(): array
    {
        return [
            'rating' => 'decimal:2',
        ];
    }

    // ========================================
    // BOOT METHOD (Update vendor rating after review)
    // ========================================

    /**
     * The "booted" method of the model.
     */
    protected static function booted(): void
    {
        // Update vendor rating after creating review
        static::created(function (VendorReview $review) {
            $review->vendor->updateRating();
        });

        // Update vendor rating after updating review
        static::updated(function (VendorReview $review) {
            $review->vendor->updateRating();
        });

        // Update vendor rating after deleting review
        static::deleted(function (VendorReview $review) {
            $review->vendor->updateRating();
        });
    }

    // ========================================
    // RELATIONSHIPS
    // ========================================

    /**
     * Get the vendor being reviewed.
     * Many-to-One: VendorReview -> Vendor
     */
    public function vendor()
    {
        return $this->belongsTo(Vendor::class);
    }

    /**
     * Get the event this review is for.
     * Many-to-One: VendorReview -> Event
     */
    public function event()
    {
        return $this->belongsTo(Event::class);
    }

    /**
     * Get the client who wrote the review.
     * Many-to-One: VendorReview -> User (client)
     */
    public function client()
    {
        return $this->belongsTo(User::class, 'client_id');
    }

    // ========================================
    // HELPER METHODS
    // ========================================

    /**
     * Get star rating display.
     */
    public function getStarsDisplay(): string
    {
        $stars = str_repeat('⭐', (int) round($this->rating));
        return "{$stars} {$this->rating}";
    }

    /**
     * Check if review has a comment.
     */
    public function hasComment(): bool
    {
        return !empty($this->comment);
    }

    /**
     * Get review summary.
     */
    public function getSummary(): string
    {
        $summary = "{$this->client->name} rated {$this->vendor->name}: {$this->rating}/5.00";
        
        if ($this->hasComment()) {
            $summary .= " - \"{$this->comment}\"";
        }
        
        return $summary;
    }

    /**
     * Check if rating is positive (4.0 or above).
     */
    public function isPositive(): bool
    {
        return $this->rating >= 4.0;
    }

    /**
     * Check if rating is negative (below 3.0).
     */
    public function isNegative(): bool
    {
        return $this->rating < 3.0;
    }
}
