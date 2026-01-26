<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ClientProfile extends Model
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
        'organization_name',
        'preferences',
        'preferred_budget_range',
    ];

    /**
     * Get the attributes that should be cast.
     */
    protected function casts(): array
    {
        return [
            'preferences' => 'array', // JSON to array
        ];
    }

    // ========================================
    // RELATIONSHIPS
    // ========================================

    /**
     * Get the user that owns this client profile.
     * One-to-One (Inverse): ClientProfile -> User
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}