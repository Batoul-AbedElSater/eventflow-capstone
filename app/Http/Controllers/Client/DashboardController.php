<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    /**
     * Display client dashboard
     * Shows: Quick stats, event cards, activity feed, pending actions
     */
    public function index()
    {
        $user = Auth::user(); // Get logged-in user
        
        // Get all client's events with relationships
        $events = $user->clientEvents() // Events where user is client
            ->with(['eventType', 'planner', 'guests', 'budgetCategories', 'tasks']) // Load related data
            ->orderBy('start_date', 'asc') // Sort by date
            ->get();
        
        // Calculate quick stats
        $stats = [
            'total_events' => $events->count(),
            'active_events' => $events->whereIn('status', ['planning', 'in_progress'])->count(),
            'total_guests' => $events->sum(fn($e) => $e->guests->count()),
            'total_rsvp' => $events->sum(fn($e) => $e->guests->whereIn('rsvp_status', ['accepted', 'declined'])->count()),
        ];
        
        // Get upcoming event (next event by date)
        $upcomingEvent = $events->where('status', '!=', 'completed')
            ->where('start_date', '>=', now())
            ->first();
        
        // Calculate days until upcoming event
        $daysUntil = $upcomingEvent ? now()->diffInDays($upcomingEvent->start_date) : null;
        
        // Return view with data
        return view('client.dashboard', compact('events', 'stats', 'upcomingEvent', 'daysUntil'));
    }
}
