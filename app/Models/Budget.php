<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Budget extends Model
{
    use HasFactory;

    public const STATUS_DRAFT = 'draft';

    public const STATUS_APPROVED = 'approved';

    public const STATUS_FINALIZED = 'finalized';

    public const STATUS_SHARED = 'shared';

    public const STATUSES = [
        self::STATUS_DRAFT,
        self::STATUS_APPROVED,
        self::STATUS_FINALIZED,
        self::STATUS_SHARED,
    ];

    protected $fillable = [
        'event_id',
        'planner_id',
        'total_client_budget',
        'planner_fee',
        'total_assistant_fees',
        'estimated_total',
        'actual_total',
        'status',
        'shared_with_client',
        'planner_notes',
    ];

    protected $casts = [
        'total_client_budget' => 'decimal:2',
        'planner_fee' => 'decimal:2',
        'total_assistant_fees' => 'decimal:2',
        'estimated_total' => 'decimal:2',
        'actual_total' => 'decimal:2',
        'shared_with_client' => 'boolean',
    ];

    public function event()
    {
        return $this->belongsTo(Event::class);
    }

    public function planner()
    {
        return $this->belongsTo(User::class, 'planner_id');
    }

    public function items()
    {
        return $this->hasMany(BudgetItem::class);
    }
}
