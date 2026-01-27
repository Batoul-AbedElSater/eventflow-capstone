<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Guest extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'event_id',
        'name',
        'email',
        'phone',
        'invite_token',
        'rsvp_status',
        'rsvp_at',
        'is_vip',
        'plus_one_allowed',
        'plus_one_name',
        'check_in_time',
    ];

    /**
     * Get the attributes that should be cast.
     */
    protected function casts(): array
    {
        return [
            'rsvp_at' => 'datetime',
            'is_vip' => 'boolean',
            'plus_one_allowed' => 'boolean',
            'check_in_time' => 'datetime',
        ];
    }

    // ========================================
    // BOOT METHOD (Auto-generate invite token)
    // ========================================

    /**
     * The "booted" method of the model.
     */
    protected static function booted(): void
    {
        // Auto-generate invite_token when creating guest
        static::creating(function (Guest $guest) {
            if (empty($guest->invite_token)) {
                $guest->invite_token = Str::random(32);
            }
        });
    }

    // ========================================
    // RELATIONSHIPS
    // ========================================

    /**
     * Get the event this guest belongs to.
     * Many-to-One: Guest -> Event
     */
    public function event()
    {
        return $this->belongsTo(Event::class);
    }

    // ========================================
    // HELPER METHODS
    // ========================================

    /**
     * Check if guest has accepted invitation.
     */
    public function hasAccepted(): bool
    {
        return $this->rsvp_status === 'accepted';
    }

    /**
     * Check if guest has declined invitation.
     */
    public function hasDeclined(): bool
    {
        return $this->rsvp_status === 'declined';
    }

    /**
     * Check if RSVP is still pending.
     */
    public function isPending(): bool
    {
        return $this->rsvp_status === 'pending';
    }

    /**
     * Check if guest has checked in.
     */
    public function hasCheckedIn(): bool
    {
        return !is_null($this->check_in_time);
    }

    /**
     * Get RSVP link URL.
     */
    public function getRsvpUrl(): string
    {
        return url("/rsvp/{$this->invite_token}");
    }

    /**
     * Accept invitation.
     */
    public function accept(): void
    {
        $this->update([
            'rsvp_status' => 'accepted',
            'rsvp_at' => now(),
        ]);
    }

    /**
     * Decline invitation.
     */
    public function decline(): void
    {
        $this->update([
            'rsvp_status' => 'declined',
            'rsvp_at' => now(),
        ]);
    }

    /**
     * Check in guest.
     */
    public function checkIn(): void
    {
        $this->update([
            'check_in_time' => now(),
        ]);
    }
}
