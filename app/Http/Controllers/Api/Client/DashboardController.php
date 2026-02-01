<?php

namespace App\Http\Controllers\Api\Client;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    /**
     * Get client dashboard data (JSON for mobile)
     */
    public function index(Request $request)
    {
        $user = $request->user(); // Get authenticated user from token
        
        // Get events
        $events = $user->clientEvents()
            ->with(['eventType', 'planner', 'guests', 'budgetCategories', 'tasks'])
            ->orderBy('start_date', 'asc')
            ->get();
        
        // Calculate stats
        $stats = [
            'total_events' => $events->count(),
            'active_events' => $events->whereIn('status', ['planning', 'in_progress'])->count(),
            'total_guests' => $events->sum(fn($e) => $e->guests->count()),
            'total_rsvp' => $events->sum(fn($e) => $e->guests->whereIn('rsvp_status', ['accepted', 'declined'])->count()),
        ];
        
        // Get upcoming event
        $upcomingEvent = $events->where('status', '!=', 'completed')
            ->where('start_date', '>=', now())
            ->first();
        
        // Return JSON response
        return response()->json([
            'success' => true,
            'data' => [
                'stats' => $stats,
                'events' => $events,
                'upcoming_event' => $upcomingEvent,
                'days_until' => $upcomingEvent ? now()->diffInDays($upcomingEvent->start_date) : null,
            ]
        ]);
    }
}
