<?php

namespace App\Http\Controllers\Api\Client;

use App\Http\Controllers\Controller;
use App\Models\Event;
use App\Models\EventType;
use App\Models\User;
use Illuminate\Http\Request;

class EventController extends Controller
{
    /**
     * Get all events (API)
     */
    public function index(Request $request)
    {
        $events = $request->user()->clientEvents()
            ->with(['eventType', 'planner', 'guests', 'tasks'])
            ->orderBy('start_date', 'desc')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $events
        ]);
    }

    /**
     * Get event types and planners for create form (API)
     */
    public function createData(Request $request)
    {
        $eventTypes = EventType::all();
        $planners = User::where('role', 'planner')
            ->with('plannerProfile')
            ->get();

        return response()->json([
            'success' => true,
            'data' => [
                'event_types' => $eventTypes,
                'planners' => $planners
            ]
        ]);
    }

    /**
     * Create event (API)
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'event_type_id' => 'required|exists:event_types,id',
            'name' => 'required|string|max:255',
            'start_date' => 'required|date|after:today',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'location_text' => 'required|string|max:500',
            'description' => 'nullable|string',
            'guest_estimate' => 'required|integer|min:1',
            'budget_overall' => 'required|numeric|min:0',
            'rsvp_deadline' => 'required|date|before:start_date',
            'guest_list_lock' => 'required|date|before:rsvp_deadline',
            'planner_id' => 'nullable|exists:users,id',
        ]);

        $event = Event::create([
            'client_id' => $request->user()->id,
            'planner_id' => $validated['planner_id'] ?? null,
            'event_type_id' => $validated['event_type_id'],
            'name' => $validated['name'],
            'description' => $validated['description'] ?? null,
            'start_date' => $validated['start_date'],
            'end_date' => $validated['end_date'] ?? $validated['start_date'],
            'location_text' => $validated['location_text'],
            'guest_estimate' => $validated['guest_estimate'],
            'budget_overall' => $validated['budget_overall'],
            'status' => 'draft',
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Event created successfully!',
            'data' => $event->load(['eventType', 'planner'])
        ], 201);
    }

    /**
     * Get single event (API)
     */
    public function show(Request $request, $id)
    {
        $event = Event::with([
            'eventType',
            'planner.plannerProfile',
            'guests',
            'budgetCategories',
            'tasks',
            'vendors'
        ])->findOrFail($id);

        // Check ownership
        if ($event->client_id !== $request->user()->id) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized access'
            ], 403);
        }

        return response()->json([
            'success' => true,
            'data' => $event
        ]);
    }
}
