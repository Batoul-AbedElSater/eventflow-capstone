<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PlannerProfile extends Model
{
    use HasFactory;

    /**
     * Primary key is user_id (not auto-incrementing).
     */
    protected $primaryKey = 'user_id';
    public $incrementing = false;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'user_id',
        'bio',
        'years_experience',
        'specialties',
        'portfolio_url',
        'hourly_rate',
        'availability_notes',
    ];

    /**
     * Get the attributes that should be cast.
     */
    protected function casts(): array
    {
        return [
            'specialties' => 'array', // JSON to array
            'hourly_rate' => 'decimal:2',
        ];
    }

    // ========================================
    // RELATIONSHIPS
    // ========================================

    /**
     * Get the user that owns this planner profile.
     * One-to-One (Inverse): PlannerProfile -> User
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}