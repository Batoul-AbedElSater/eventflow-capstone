<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MessageThread extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'event_id',
        'client_id',
        'planner_id',
    ];

    // ========================================
    // RELATIONSHIPS
    // ========================================

    /**
     * Get the event this thread belongs to.
     * Many-to-One: MessageThread -> Event
     */
    public function event()
    {
        return $this->belongsTo(Event::class);
    }

    /**
     * Get the client in this thread.
     * Many-to-One: MessageThread -> User (client)
     */
    public function client()
    {
        return $this->belongsTo(User::class, 'client_id');
    }

    /**
     * Get the planner in this thread.
     * Many-to-One: MessageThread -> User (planner)
     */
    public function planner()
    {
        return $this->belongsTo(User::class, 'planner_id');
    }

    /**
     * Get messages in this thread.
     * One-to-Many: MessageThread -> Messages
     */
    public function messages()
    {
        return $this->hasMany(Message::class, 'thread_id');
    }

    // ========================================
    // HELPER METHODS
    // ========================================

    /**
     * Get latest message in thread.
     */
    public function getLatestMessage()
    {
        return $this->messages()->latest('sent_at')->first();
    }

    /**
     * Get unread message count for a user.
     */
    public function getUnreadCountForUser(int $userId): int
    {
        // This would require additional 'read_by' tracking
        // For now, return 0 as placeholder
        return 0;
    }

    /**
     * Check if user is participant.
     */
    public function isParticipant(int $userId): bool
    {
        return $this->client_id === $userId || $this->planner_id === $userId;
    }

    /**
     * Get the other participant (opposite of given user).
     */
    public function getOtherParticipant(int $userId)
    {
        if ($this->client_id === $userId) {
            return $this->planner;
        } elseif ($this->planner_id === $userId) {
            return $this->client;
        }
        
        return null;
    }

    /**
     * Send message in this thread.
     */
    public function sendMessage(int $senderId, ?string $body = null, ?string $imageUrl = null): Message
    {
        // Validate sender is participant
        if (!$this->isParticipant($senderId)) {
            throw new \Exception('User is not a participant in this thread');
        }

        $message = $this->messages()->create([
            'sender_id' => $senderId,
            'body' => $body,
            'image_url' => $imageUrl,
            'sent_at' => now(),
        ]);

        // Get recipient
        $recipient = $this->getOtherParticipant($senderId);

        // Create notification for recipient
        Notification::create([
            'user_id' => $recipient->id,
            'event_id' => $this->event_id,
            'type' => 'new_message',
            'data_json' => [
                'sender_id' => $senderId,
                'sender_name' => User::find($senderId)->name,
                'message_preview' => $body ? substr($body, 0, 50) : 'Sent an image',
                'thread_id' => $this->id,
            ],
            'is_read' => false,
        ]);

        return $message;
    }

    /**
     * Get thread title (event name).
     */
    public function getTitle(): string
    {
        return "Chat about: {$this->event->name}";
    }
}
