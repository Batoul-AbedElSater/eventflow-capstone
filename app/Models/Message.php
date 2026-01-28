<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Message extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'thread_id',
        'sender_id',
        'body',
        'image_url',
        'sent_at',
    ];

    /**
     * Get the attributes that should be cast.
     */
    protected function casts(): array
    {
        return [
            'sent_at' => 'datetime',
        ];
    }

    // ========================================
    // RELATIONSHIPS
    // ========================================

    /**
     * Get the thread this message belongs to.
     * Many-to-One: Message -> MessageThread
     */
    public function thread()
    {
        return $this->belongsTo(MessageThread::class, 'thread_id');
    }

    /**
     * Get the sender of this message.
     * Many-to-One: Message -> User
     */
    public function sender()
    {
        return $this->belongsTo(User::class, 'sender_id');
    }

    /**
     * Get attachments for this message.
     * One-to-Many: Message -> Attachments
     */
    public function attachments()
    {
        return $this->hasMany(Attachment::class);
    }

    // ========================================
    // HELPER METHODS
    // ========================================

    /**
     * Check if message has text body.
     */
    public function hasBody(): bool
    {
        return !empty($this->body);
    }

    /**
     * Check if message has image.
     */
    public function hasImage(): bool
    {
        return !empty($this->image_url);
    }

    /**
     * Check if message is from client.
     */
    public function isFromClient(): bool
    {
        return $this->sender_id === $this->thread->client_id;
    }

    /**
     * Check if message is from planner.
     */
    public function isFromPlanner(): bool
    {
        return $this->sender_id === $this->thread->planner_id;
    }

    /**
     * Get time ago display.
     */
    public function getTimeAgo(): string
    {
        return $this->sent_at->diffForHumans();
    }

    /**
     * Get preview text (first 50 characters).
     */
    public function getPreview(): string
    {
        if ($this->hasBody()) {
            return substr($this->body, 0, 50) . (strlen($this->body) > 50 ? '...' : '');
        }
        
        if ($this->hasImage()) {
            return '📷 Sent an image';
        }
        
        return '[Empty message]';
    }

    /**
     * Check if message was sent by given user.
     */
    public function isSentBy(int $userId): bool
    {
        return $this->sender_id === $userId;
    }
}
