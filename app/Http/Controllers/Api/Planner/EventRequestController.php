<?php

namespace App\Http\Controllers\Api\Planner;

use App\Http\Controllers\Controller;
use App\Models\Event;
use App\Models\Notification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class EventRequestController extends Controller
{
    /**
     * List all pending event requests for the authenticated planner
     * GET /api/planner/requests
     */
    public function index(Request $request)
    {
        try {
            $query = Event::where('planner_id', $request->user()->id)
                ->where('status', 'pending')
                ->with(['client:id,name,email,phone', 'eventType:id,name']);

            // Filters
            if ($request->has('filter')) {
                switch ($request->filter) {
                    case 'this-week':
                        $query->whereBetween('start_date', [
                            Carbon::now()->startOfWeek(),
                            Carbon::now()->endOfWeek()
                        ]);
                        break;
                    case 'high-budget':
                        $query->where('budget_overall', '>=', 50000);
                        break;
                }
            }

            // Search
            if ($request->has('search')) {
                $search = $request->search;
                $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                      ->orWhereHas('client', function ($q) use ($search) {
                          $q->where('name', 'like', "%{$search}%");
                      });
                });
            }

            // Sorting
            $sortBy = $request->get('sort', 'newest');
            switch ($sortBy) {
                case 'newest':
                    $query->orderBy('created_at', 'desc');
                    break;
                case 'oldest':
                    $query->orderBy('created_at', 'asc');
                    break;
                case 'budget-high':
                    $query->orderBy('budget_overall', 'desc');
                    break;
                case 'budget-low':
                    $query->orderBy('budget_overall', 'asc');
                    break;
                case 'guests':
                    $query->orderBy('guest_estimate', 'desc');
                    break;
                default:
                    $query->orderBy('created_at', 'desc');
            }

            $perPage = $request->get('per_page', 10);
            $pendingRequests = $query->paginate($perPage);

            // Statistics
            $acceptedToday = Event::where('planner_id', $request->user()->id)
                ->where('status', 'confirmed')
                ->whereDate('updated_at', today())
                ->count();

            $declinedToday = Event::where('planner_id', $request->user()->id)
                ->where('status', 'declined')
                ->whereDate('updated_at', today())
                ->count();

            return response()->json([
                'success' => true,
                'data' => [
                    'requests' => $pendingRequests->map(function ($event) {
                        return [
                            'id' => $event->id,
                            'name' => $event->name,
                            'event_type' => $event->eventType->name ?? 'N/A',
                            'start_date' => $event->start_date->format('M d, Y'),
                            'start_date_iso' => $event->start_date->toISOString(),
                            'location' => $event->location_text,
                            'guest_estimate' => $event->guest_estimate,
                            'budget_overall' => number_format($event->budget_overall, 2),
                            'budget_raw' => $event->budget_overall,
                            'description' => $event->description,
                            'client' => [
                                'id' => $event->client->id ?? null,
                                'name' => $event->client->name ?? 'N/A',
                                'email' => $event->client->email ?? 'N/A',
                                'phone' => $event->client->phone ?? 'N/A',
                            ],
                            'created_at' => $event->created_at->format('M d, Y h:i A'),
                            'time_ago' => $event->created_at->diffForHumans(),
                        ];
                    }),
                    'stats' => [
                        'accepted_today' => $acceptedToday,
                        'declined_today' => $declinedToday,
                        'total_pending' => $pendingRequests->total(),
                    ],
                    'pagination' => [
                        'current_page' => $pendingRequests->currentPage(),
                        'per_page' => $pendingRequests->perPage(),
                        'total' => $pendingRequests->total(),
                        'last_page' => $pendingRequests->lastPage(),
                    ],
                ],
            ]);

        } catch (\Exception $e) {
            Log::error('API Planner requests index error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to load requests',
            ], 500);
        }
    }

    /**
     * Accept an event request
     * POST /api/planner/requests/{event}/accept
     */
    public function accept(Request $request, $eventId)
    {
        try {
            $event = Event::where('id', $eventId)
                ->where('status', 'pending')
                ->firstOrFail();

            // Update event status
            Event::where('id', $event->id)->update([
                'planner_id' => $request->user()->id,
                'status' => 'confirmed',
            ]);

            $event->refresh();

            Log::info('Event accepted by planner', [
                'event_id' => $event->id,
                'planner_id' => $request->user()->id,
            ]);

            // Notify client
            try {
                Notification::create([
                    'user_id' => $event->client_id,
                    'type' => 'event',
                    'priority' => 'high',
                    'title' => 'Event Request Accepted!',
                    'message' => 'Your event "' . $event->name . '" has been accepted by the planner.',
                    'icon' => 'fas fa-check-circle',
                    'action_url' => '/client/events/' . $event->id,
                    'is_read' => false,
                    'is_archived' => false,
                ]);
            } catch (\Exception $e) {
                Log::warning('Failed to create acceptance notification: ' . $e->getMessage());
            }

            return response()->json([
                'success' => true,
                'message' => 'Event request accepted successfully!',
                'data' => [
                    'event_id' => $event->id,
                    'event_name' => $event->name,
                    'status' => 'confirmed',
                ],
            ]);

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Event not found or already processed',
            ], 404);
        } catch (\Exception $e) {
            Log::error('API Planner accept request error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to accept request',
            ], 500);
        }
    }

    /**
     * Decline an event request
     * POST /api/planner/requests/{event}/decline
     */
    public function decline(Request $request, $eventId)
    {
        try {
            $event = Event::where('planner_id', $request->user()->id)
                ->where('id', $eventId)
                ->where('status', 'pending')
                ->firstOrFail();

            $event->status = 'declined';
            $event->save();

            Log::info('Event declined by planner', [
                'event_id' => $event->id,
                'planner_id' => $request->user()->id,
            ]);

            // Notify client
            try {
                Notification::create([
                    'user_id' => $event->client_id,
                    'type' => 'event',
                    'priority' => 'medium',
                    'title' => 'Event Request Declined',
                    'message' => 'Your event "' . $event->name . '" was declined. Please contact the planner for more information.',
                    'icon' => 'fas fa-times-circle',
                    'action_url' => '/client/dashboard',
                    'is_read' => false,
                    'is_archived' => false,
                ]);
            } catch (\Exception $e) {
                Log::warning('Failed to create decline notification: ' . $e->getMessage());
            }

            return response()->json([
                'success' => true,
                'message' => 'Event request declined.',
                'data' => [
                    'event_id' => $event->id,
                    'event_name' => $event->name,
                    'status' => 'declined',
                ],
            ]);

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Event not found or already processed',
            ], 404);
        } catch (\Exception $e) {
            Log::error('API Planner decline request error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to decline request',
            ], 500);
        }
    }

    /**
     * Get request statistics
     * GET /api/planner/requests/stats
     */
    public function stats(Request $request)
    {
        try {
            $plannerId = $request->user()->id;

            return response()->json([
                'success' => true,
                'data' => [
                    'pending' => Event::where('planner_id', $plannerId)
                        ->where('status', 'pending')
                        ->count(),
                    'accepted_today' => Event::where('planner_id', $plannerId)
                        ->where('status', 'confirmed')
                        ->whereDate('updated_at', today())
                        ->count(),
                    'declined_today' => Event::where('planner_id', $plannerId)
                        ->where('status', 'declined')
                        ->whereDate('updated_at', today())
                        ->count(),
                    'total_confirmed' => Event::where('planner_id', $plannerId)
                        ->where('status', 'confirmed')
                        ->count(),
                ],
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to load statistics',
            ], 500);
        }
    }
}