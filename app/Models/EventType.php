<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EventType extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'name',
        'default_tasks',
        'description',
    ];

    /**
     * Get the attributes that should be cast.
     */
    protected function casts(): array
    {
        return [
            'default_tasks' => 'array', // JSON to array
        ];
    }

    // ========================================
    // RELATIONSHIPS
    // ========================================

    /**
     * Get events of this type.
     * One-to-Many: EventType -> Events
     */
    public function events()
    {
        return $this->hasMany(Event::class, 'event_type_id');
    }

    // ========================================
    // HELPER METHODS
    // ========================================

    /**
     * Get default tasks as formatted list.
     */
    public function getDefaultTasksList(): array
    {
        return $this->default_tasks ?? [];
    }
}
