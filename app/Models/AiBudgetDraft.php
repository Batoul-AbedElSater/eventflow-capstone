<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AiBudgetDraft extends Model
{
    use HasFactory;

    protected $fillable = [
        'event_id',
        'planner_id',
        'ai_response',
        'status',
    ];

    protected $casts = [
        'ai_response' => 'array',
    ];

    public function event()
    {
        return $this->belongsTo(Event::class);
    }

    public function planner()
    {
        return $this->belongsTo(User::class, 'planner_id');
    }
}