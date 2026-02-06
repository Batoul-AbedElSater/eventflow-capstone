<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Guest extends Model
{
    use HasFactory;

    protected $fillable = [
        'event_id',
        'name',
        'email',
        'phone',
        'rsvp_token', // Updated from invite_token
        'rsvp_status',
        'rsvp_date',  // Updated from rsvp_at to match migration
        'plus_one_allowed',
        'plus_one_name',
        'dietary_restrictions',
        'notes',
        'invitation_sent',
        'check_in_time',
    ];

    protected $casts = [
        'rsvp_date' => 'datetime',
        'plus_one_allowed' => 'boolean',
        'invitation_sent' => 'boolean',
        'check_in_time' => 'datetime',
    ];

    protected static function booted(): void
    {
        static::creating(function (Guest $guest) {
            // Updated to rsvp_token to match your migration!
            if (empty($guest->rsvp_token)) {
                $guest->rsvp_token = Str::random(32);
            }
        });
    }

    public function event()
    {
        return $this->belongsTo(Event::class);
    }

    // Helper for the RSVP URL
    public function getRsvpUrl(): string
    {
        return url("/rsvp/{$this->rsvp_token}");
    }
}