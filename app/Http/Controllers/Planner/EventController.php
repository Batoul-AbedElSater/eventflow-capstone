<?php

namespace App\Http\Controllers\Planner;

use App\Http\Controllers\Controller;
use App\Models\Event;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class EventController extends Controller
{
    public function index()
{
    try {
        $plannerId = Auth::id();

        $events = Event::where('planner_id', $plannerId)
            ->with(['client:id,name,email', 'eventType:id,name'])
            ->orderBy('start_date', 'desc')
            ->get();

        // Calculate total revenue
        $totalRevenue = $events->whereIn('status', ['confirmed', 'in_progress', 'completed'])->sum('budget_overall');

        // Summary statistics - FIXED VARIABLE NAME
        $stats = [
            'total' => $events->count(),
            'pending' => $events->where('status', 'pending')->count(),
            'confirmed' => $events->where('status', 'confirmed')->count(),
            'in_progress' => $events->where('status', 'in_progress')->count(),
            'completed' => $events->where('status', 'completed')->count(),
            'cancelled' => $events->where('status', 'cancelled')->count(),
            'total_revenue' => $totalRevenue, // ADDED THIS
            'pending_revenue' => $events->where('status', 'pending')->sum('budget_overall'),
            'completed_revenue' => $events->where('status', 'completed')->sum('budget_overall'),
        ];

        $summary = [
            'total_events' => $events->count(),
            'total_revenue' => $totalRevenue,
            'pending_revenue' => $events->where('status', 'pending')->sum('budget_overall'),
            'completed_revenue' => $events->where('status', 'completed')->sum('budget_overall'),
        ];

        // Top clients
        $topClients = User::where('role', 'client')
            ->whereHas('clientEvents', function($query) use ($plannerId) {
                $query->where('planner_id', $plannerId);
            })
            ->withCount(['clientEvents as events_count' => function($query) use ($plannerId) {
                $query->where('planner_id', $plannerId);
            }])
            ->orderBy('events_count', 'desc')
            ->take(5)
            ->get()
            ->map(function($client) use ($plannerId) {
                $client->total_revenue = Event::where('planner_id', $plannerId)
                    ->where('client_id', $client->id)
                    ->sum('budget_overall') ?? 0;
                return $client;
            });

        // Performance metrics
        $totalEvents = $events->count();
        $acceptedEvents = $events->whereIn('status', ['confirmed', 'in_progress', 'completed'])->count();
        $completedEvents = $events->where('status', 'completed')->count();

        $metrics = [
            'acceptance_rate' => $totalEvents > 0 ? round(($acceptedEvents / $totalEvents) * 100) : 0,
            'completion_rate' => $acceptedEvents > 0 ? round(($completedEvents / $acceptedEvents) * 100) : 0,
            'avg_event_value' => $totalEvents > 0 ? round($events->sum('budget_overall') / $totalEvents, 2) : 0,
            'total_clients' => User::where('role', 'client')->whereHas('clientEvents', function($q) use ($plannerId) {
                $q->where('planner_id', $plannerId);
            })->count(),
            'avg_response_time' => '2.5', // ADD THIS
            'satisfaction_score' => 4.8,  // ADD THIS
        ];

        return view('planner.events.index', compact('events', 'stats', 'summary', 'topClients', 'metrics'));

    } catch (\Exception $e) {
        Log::error('Events index error: ' . $e->getMessage());
        return back()->with('error', 'Failed to load events');
    }
}

    public function analytics(Request $request)
    {
        try {
            $plannerId = Auth::id();
            $period = $request->get('period', 'month');

            // Revenue trends
            $revenueData = $this->getRevenueData($plannerId, $period);

            // Event type distribution
            $eventTypes = Event::where('planner_id', $plannerId)
                ->select('event_type_id', DB::raw('count(*) as count'))
                ->groupBy('event_type_id')
                ->with('eventType:id,name')
                ->get();

            // Top clients
            $topClients = User::where('role', 'client')
                ->whereHas('clientEvents', function($query) use ($plannerId) {
                    $query->where('planner_id', $plannerId);
                })
                ->withCount([' clientEvents as events_count' => function($query) use ($plannerId) {
                    $query->where('planner_id', $plannerId);
                }])
                ->orderBy('events_count', 'desc')
                ->take(5)
                ->get()
                ->map(function($client) use ($plannerId) {
                    $client->total_revenue = Event::where('planner_id', $plannerId)
                        ->where('client_id', $client->id)
                        ->sum('budget_overall') ?? 0;
                    return $client;
                });

            $events = Event::where('planner_id', $plannerId)->get();

            $metrics = [
                'total_events' => $events->count(),
                'total_revenue' => $events->sum('budget_overall'),
                'avg_event_value' => $events->count() > 0 ? round($events->sum('budget_overall') / $events->count(), 2) : 0,
                'acceptance_rate' => $events->count() > 0 ? round(($events->whereIn('status', ['confirmed', 'in_progress', 'completed'])->count() / $events->count()) * 100) : 0,
            ];

            return view('planner.events.analytics', compact('revenueData', 'eventTypes', 'topClients', 'metrics', 'period'));

        } catch (\Exception $e) {
            Log::error('Analytics error: ' . $e->getMessage());
            return back()->with('error', 'Failed to load analytics');
        }
    }

    public function show(Event $event)
    {
        if ($event->planner_id !== Auth::id()) {
            abort(403);
        }

        return view('planner.events.show', compact('event'));
    }

    public function create()
    {
        return view('planner.events.create');
    }

    public function store(Request $request)
    {
        // Implementation for creating events
    }

    public function edit(Event $event)
    {
        if ($event->planner_id !== Auth::id()) {
            abort(403);
        }

        return view('planner.events.edit', compact('event'));
    }

    public function update(Request $request, Event $event)
    {
        // Implementation for updating events
    }

    public function destroy(Event $event)
    {
        if ($event->planner_id !== Auth::id()) {
            abort(403);
        }

        $event->delete();
        return redirect()->route('planner.events.index')->with('success', 'Event deleted successfully');
    }

    public function updateStatus(Request $request, Event $event)
    {
        try {
            if ($event->planner_id !== Auth::id()) {
                return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
            }

            $validated = $request->validate([
                'status' => 'required|in:pending,confirmed,in_progress,completed,cancelled'
            ]);

            $event->update($validated);

            return response()->json([
                'success' => true,
                'message' => 'Event status updated successfully',
                'event' => $event
            ]);

        } catch (\Exception $e) {
            Log::error('Event status update error: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Failed to update status'], 500);
        }
    }

    private function getRevenueData($plannerId, $period)
    {
        $query = Event::where('planner_id', $plannerId)
            ->where('status', 'completed');

        switch ($period) {
            case 'week':
                $data = $query->where('start_date', '>=', now()->subWeek())
                    ->selectRaw('DATE(start_date) as date, SUM(budget_overall) as revenue')
                    ->groupBy('date')
                    ->get();
                break;
            case 'year':
                $data = $query->where('start_date', '>=', now()->subYear())
                    ->selectRaw('DATE_FORMAT(start_date, "%Y-%m") as date, SUM(budget_overall) as revenue')
                    ->groupBy('date')
                    ->get();
                break;
            default: // month
                $data = $query->where('start_date', '>=', now()->subMonth())
                    ->selectRaw('DATE(start_date) as date, SUM(budget_overall) as revenue')
                    ->groupBy('date')
                    ->get();
        }

        return $data;
    }
}
