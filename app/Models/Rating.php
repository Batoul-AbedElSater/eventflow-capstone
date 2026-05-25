<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Rating extends Model
{
    use HasFactory;

    protected $fillable = ['event_id', 'client_id', 'planner_id', 'score', 'review'];

    public function event() { return $this->belongsTo(Event::class); }
    public function client() { return $this->belongsTo(User::class, 'client_id'); }
    public function planner() { return $this->belongsTo(User::class, 'planner_id'); }
}