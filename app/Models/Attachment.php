<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Attachment extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'event_id',    // Optional: attached to event
        'task_id',     // Optional: attached to task
        'message_id',  // Optional: attached to message
        'url',         // File URL/path
        'type',        // File type (image, pdf, doc, etc.)
    ];

    /**
     * Get the attributes that should be cast.
     */
    protected function casts(): array
    {
        return [
            'created_at' => 'datetime',
            'updated_at' => 'datetime',
        ];
    }

    // ========================================
    // RELATIONSHIPS
    // ========================================

    /**
     * Get the event this attachment belongs to (if any).
     * Many-to-One: Attachment -> Event
     */
    public function event()
    {
        return $this->belongsTo(Event::class);
    }

    /**
     * Get the task this attachment belongs to (if any).
     * Many-to-One: Attachment -> Task
     */
    public function task()
    {
        return $this->belongsTo(Task::class);
    }

    /**
     * Get the message this attachment belongs to (if any).
     * Many-to-One: Attachment -> Message
     */
    public function message()
    {
        return $this->belongsTo(Message::class);
    }

    // ========================================
    // HELPER METHODS
    // ========================================

    /**
     * Check if attachment is an image
     */
    public function isImage(): bool
    {
        return $this->type === 'image';
    }

    /**
     * Check if attachment is a PDF
     */
    public function isPdf(): bool
    {
        return $this->type === 'pdf';
    }

    /**
     * Check if attachment is a document
     */
    public function isDocument(): bool
    {
        return in_array($this->type, ['doc', 'docx', 'pdf']);
    }

    /**
     * Get file extension from URL
     */
    public function getExtension(): string
    {
        return pathinfo($this->url, PATHINFO_EXTENSION);
    }

    /**
     * Get file name from URL
     */
    public function getFileName(): string
    {
        return basename($this->url);
    }
}
