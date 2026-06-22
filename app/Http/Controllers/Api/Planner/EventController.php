<?php

namespace App\Http\Controllers\Api\Planner;

use App\Http\Controllers\Controller;
use App\Models\Event;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class EventController extends Controller
{
    /**
     * Get all planner events with stats
     * GET /api/planner/events
     */
    public function index(Request $request)
    {
        try {
            $plannerId = $request->user()->id;

            $events = Event::where('planner_id', $plannerId)
                ->whereIn('status', ['confirmed', 'in_progress', 'completed'])
                ->with(['client:id,name,email', 'eventType:id,name'])
                ->orderBy('start_date', 'desc')
                ->get()
                ->map(function ($event) {
                    return [
                        'id' => $event->id,
                        'name' => $event->name,
                        'client_name' => $event->client->name ?? 'N/A',
                        'event_type' => $event->eventType->name ?? 'N/A',
                        'start_date' => $event->start_date->format('Y-m-d'),
                        'start_date_iso' => $event->start_date->toISOString(),
                        'location' => $event->location_text,
                        'guest_estimate' => $event->guest_estimate,
                        'budget' => $event->budget_overall,
                        'status' => $event->status,
                        'description' => $event->description,
                    ];
                });

            $totalRevenue = $events->sum('budget');

            $stats = [
                'total' => $events->count(),
                'confirmed' => $events->where('status', 'confirmed')->count(),
                'in_progress' => $events->where('status', 'in_progress')->count(),
                'completed' => $events->where('status', 'completed')->count(),
                'total_revenue' => $totalRevenue,
                'completed_revenue' => $events->where('status', 'completed')->sum('budget'),
            ];

            $totalEvents = $events->count();
            $completedEvents = $events->where('status', 'completed')->count();

            $metrics = [
                'completion_rate' => $totalEvents > 0 ? round(($completedEvents / $totalEvents) * 100) : 0,
                'avg_event_value' => $totalEvents > 0 ? round($totalRevenue / $totalEvents, 2) : 0,
                'total_clients' => User::where('role', 'client')
                    ->whereHas('clientEvents', function ($q) use ($plannerId) {
                        $q->where('planner_id', $plannerId)
                          ->whereIn('status', ['confirmed', 'in_progress', 'completed']);
                    })->count(),
            ];

            return response()->json([
                'success' => true,
                'data' => [
                    'events' => $events,
                    'stats' => $stats,
                    'metrics' => $metrics,
                ]
            ]);

        } catch (\Exception $e) {
            Log::error('API Planner events index error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to load events',
            ], 500);
        }
    }

    /**
     * Get single event details
     * GET /api/planner/events/{event}
     */
    public function show(Request $request, $id)
    {
        try {
            $event = Event::where('planner_id', $request->user()->id)
                ->with(['client:id,name,email,phone', 'eventType:id,name', 'guests', 'tasks'])
                ->findOrFail($id);

            return response()->json([
                'success' => true,
                'data' => [
                    'id' => $event->id,
                    'name' => $event->name,
                    'description' => $event->description,
                    'start_date' => $event->start_date->format('Y-m-d'),
                    'end_date' => $event->end_date?->format('Y-m-d'),
                    'location' => $event->location_text,
                    'guest_estimate' => $event->guest_estimate,
                    'budget' => $event->budget_overall,
                    'status' => $event->status,
                    'event_type' => $event->eventType->name ?? null,
                    'client' => [
                        'id' => $event->client->id ?? null,
                        'name' => $event->client->name ?? 'N/A',
                        'email' => $event->client->email ?? 'N/A',
                        'phone' => $event->client->phone ?? 'N/A',
                    ],
                    'guests' => $event->guests->map(function ($guest) {
                        return [
                            'id' => $guest->id,
                            'name' => $guest->name,
                            'email' => $guest->email,
                            'rsvp_status' => $guest->rsvp_status,
                        ];
                    }),
                    'tasks_count' => $event->tasks->count(),
                    'completed_tasks' => $event->tasks->where('status', 'done')->count(),
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Event not found',
            ], 404);
        }
    }

    /**
     * Update event status
     * PUT /api/planner/events/{event}/status
     */
    public function updateStatus(Request $request, $id)
    {
        try {
            $event = Event::where('planner_id', $request->user()->id)->findOrFail($id);

            $validated = $request->validate([
                'status' => 'required|in:confirmed,in_progress,completed'
            ]);

            $event->update($validated);

            return response()->json([
                'success' => true,
                'message' => 'Event status updated',
                'data' => [
                    'id' => $event->id,
                    'status' => $event->status,
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update status',
            ], 500);
        }
    }

    /**
     * Delete event
     * DELETE /api/planner/events/{event}
     */
    public function destroy(Request $request, $id)
    {
        try {
            $event = Event::where('planner_id', $request->user()->id)->findOrFail($id);
            $event->delete();

            return response()->json([
                'success' => true,
                'message' => 'Event deleted successfully',
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete event',
            ], 500);
        }
    }

    /**
     * Get analytics data
     * GET /api/planner/events/analytics
     */
    public function analytics(Request $request)
    {
        try {
            $plannerId = $request->user()->id;
            $period = $request->get('period', 'month');

            $revenueData = $this->getRevenueData($plannerId, $period);

            $eventTypes = Event::where('planner_id', $plannerId)
                ->whereIn('status', ['confirmed', 'in_progress', 'completed'])
                ->select('event_type_id', DB::raw('count(*) as count'))
                ->whereNotNull('event_type_id')
                ->groupBy('event_type_id')
                ->with('eventType:id,name')
                ->get()
                ->map(function ($item) {
                    return [
                        'name' => $item->eventType->name ?? 'Unknown',
                        'count' => $item->count,
                    ];
                });

            $events = Event::where('planner_id', $plannerId)
                ->whereIn('status', ['confirmed', 'in_progress', 'completed'])
                ->get();

            $metrics = [
                'total_events' => $events->count(),
                'total_revenue' => $events->sum('budget_overall'),
                'avg_event_value' => $events->count() > 0 ? round($events->sum('budget_overall') / $events->count(), 2) : 0,
                'completion_rate' => $events->count() > 0 ? round(($events->where('status', 'completed')->count() / $events->count()) * 100) : 0,
            ];

            return response()->json([
                'success' => true,
                'data' => [
                    'revenue_data' => $revenueData,
                    'event_types' => $eventTypes,
                    'metrics' => $metrics,
                    'period' => $period,
                ]
            ]);

        } catch (\Exception $e) {
            Log::error('API Planner analytics error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to load analytics',
            ], 500);
        }
    }

    private function getRevenueData($plannerId, $period)
    {
        $query = Event::where('planner_id', $plannerId)
            ->where('status', 'completed');

        switch ($period) {
            case 'week':
                return $query->where('start_date', '>=', now()->subWeek())
                    ->selectRaw('DATE(start_date) as date, SUM(budget_overall) as revenue')
                    ->groupBy('date')
                    ->orderBy('date')
                    ->get()
                    ->map(function ($item) {
                        return ['date' => $item->date, 'revenue' => $item->revenue];
                    });
            case 'year':
                return $query->where('start_date', '>=', now()->subYear())
                    ->selectRaw("DATE_FORMAT(start_date, '%Y-%m') as date, SUM(budget_overall) as revenue")
                    ->groupBy('date')
                    ->orderBy('date')
                    ->get()
                    ->map(function ($item) {
                        return ['date' => $item->date, 'revenue' => $item->revenue];
                    });
            default:
                return $query->where('start_date', '>=', now()->subMonth())
                    ->selectRaw('DATE(start_date) as date, SUM(budget_overall) as revenue')
                    ->groupBy('date')
                    ->orderBy('date')
                    ->get()
                    ->map(function ($item) {
                        return ['date' => $item->date, 'revenue' => $item->revenue];
                    });
        }
    }
}