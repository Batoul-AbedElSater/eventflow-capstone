<?php

namespace App\Http\Controllers\Planner;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        
        // Get planner's events
        $events = $user->plannerEvents()
            ->with(['eventType', 'client', 'guests', 'tasks'])
            ->orderBy('start_date', 'asc')
            ->get();
        
        // Calculate stats
        $stats = [
            'total_events' => $events->count(),
            'active_events' => $events->whereIn('status', ['planned', 'in_progress'])->count(),
            'completed_events' => $events->where('status', 'completed')->count(),
            'total_revenue' => $events->sum('budget_overall') * 0.15, // 15% commission
        ];
        
        // Get today's events
        $todayEvents = $events->whereBetween('start_date', [
            now()->startOfDay(),
            now()->endOfDay()
        ]);
        
        // Get pending requests (events without planner assigned)
        $pendingRequests = \App\Models\Event::whereNull('planner_id')
            ->where('status', 'draft')
            ->count();
        
        return view('planner.dashboard', compact('events', 'stats', 'todayEvents', 'pendingRequests'));
    }
}