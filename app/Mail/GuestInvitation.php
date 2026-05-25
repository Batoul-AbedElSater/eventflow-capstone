<?php

namespace App\Mail;

use App\Models\Guest;
use App\Models\Event;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class GuestInvitation extends Mailable
{
    use Queueable, SerializesModels;

    public $guest;
    public $event;
    public $rsvpUrl;

    /**
     * Create a new message instance.
     */
    public function __construct(Guest $guest, Event $event)
    {
        $this->guest = $guest;
        $this->event = $event;
        $this->rsvpUrl = route('rsvp.show', $guest->rsvp_token);
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'You\'re Invited: ' . $this->event->name,
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.guest-invitation',
        );
    }

    /**
     * Get the attachments for the message.
     */
    public function attachments(): array
    {
        return [];
    }
}