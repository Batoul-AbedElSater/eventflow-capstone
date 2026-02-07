<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\Event;
use Illuminate\Support\Facades\Auth;

class BudgetController extends Controller
{
    /**
     * Show budget overview for an event (Client view-only)
     */
    public function show($eventId)
    {
        $event = Event::with(['budgetCategories'])->findOrFail($eventId);
        
        // Verify ownership
        if ($event->client_id !== Auth::id()) {
            abort(403, 'Unauthorized access');
        }
        
        // Calculate budget stats
        $totalBudget = $event->budget_overall;
        $totalSpent = $event->budgetCategories->sum('spent_amount');
        $totalAllocated = $event->budgetCategories->sum('allocated_amount');
        $remaining = $totalBudget - $totalSpent;
        $percentageSpent = $totalBudget > 0 ? round(($totalSpent / $totalBudget) * 100, 1) : 0;
        
        return view('client.budget.show', compact(
            'event',
            'totalBudget',
            'totalSpent',
            'totalAllocated',
            'remaining',
            'percentageSpent'
        ));
    }
}
