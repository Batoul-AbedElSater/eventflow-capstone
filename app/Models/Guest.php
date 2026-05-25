<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Guest extends Model
{
    use HasFactory;

    protected $fillable = [
        'event_id',
        'name',
        'email',
        'phone',
        'rsvp_token',
        'rsvp_status',
        'rsvp_date',
        'rsvp_message',
        'dietary_restrictions',
        'invitation_sent',
        'invitation_sent_at',
        'plus_one_allowed',
        'plus_one_name',
        'notes',
        'check_in_time',
    ];

    protected $casts = [
        'plus_one_allowed' => 'boolean',
        'invitation_sent' => 'boolean',
        'rsvp_date' => 'datetime',
        'invitation_sent_at' => 'datetime',
        'check_in_time' => 'datetime',
    ];

    /**
     * Get the event this guest belongs to
     */
    public function event()
    {
        return $this->belongsTo(Event::class);
    }

    /**
     * Check if guest has accepted
     */
    public function hasAccepted()
    {
        return $this->rsvp_status === 'accepted';
    }

    /**
     * Check if guest has declined
     */
    public function hasDeclined()
    {
        return $this->rsvp_status === 'declined';
    }

    /**
     * Check if RSVP is pending
     */
    public function isPending()
    {
        return $this->rsvp_status === 'pending';
    }
}