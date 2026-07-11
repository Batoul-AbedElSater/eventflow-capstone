<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BudgetItem extends Model
{
    use HasFactory;

    public const STATUS_PENDING = 'pending';

    public const STATUS_CONFIRMED = 'confirmed';

    public const STATUS_PAID = 'paid';

    public const STATUSES = [
        self::STATUS_PENDING,
        self::STATUS_CONFIRMED,
        self::STATUS_PAID,
    ];

    protected $fillable = [
        'budget_id',
        'category',
        'title',
        'description',
        'estimated_cost',
        'actual_cost',
        'assistant_fee',
        'suggested_orders',
        'status',
        'notes',
    ];

    protected $casts = [
        'estimated_cost' => 'decimal:2',
        'actual_cost' => 'decimal:2',
        'assistant_fee' => 'decimal:2',
        'suggested_orders' => 'array',
    ];

    public function budget()
    {
        return $this->belongsTo(Budget::class);
    }
}
