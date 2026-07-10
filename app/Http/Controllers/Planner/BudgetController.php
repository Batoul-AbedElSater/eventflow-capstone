<?php

namespace App\Http\Controllers\Planner;

use App\Http\Controllers\Controller;
use App\Models\Event;
use Illuminate\Http\Request;

class BudgetController extends Controller
{
    public function index($eventId)
    {
        $event = Event::where('planner_id', auth()->id())
            ->with(['eventType', 'aiBudgetDraft'])
            ->findOrFail($eventId);

        return view('planner.events.budget', compact('event'));
    }
}