<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\Event;
use App\Models\EventType;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class EventController extends Controller
{
    /**
     * Display all events for client
     */
    public function index()
    {
        $events = Auth::user()->clientEvents()
            ->with(['eventType', 'planner', 'guests', 'tasks'])
            ->orderBy('start_date', 'desc')
            ->get();
        
        return view('client.events.index', compact('events'));
    }

    /**
     * Show create event form
     */
    public function create()
    {
        // Get all event types for dropdown
        $eventTypes = EventType::all();
        
        // Get all planners for selection
        $planners = User::where('role', 'planner')
            ->with('plannerProfile')
            ->get();
        
        return view('client.events.create', compact('eventTypes', 'planners'));
    }

    /**
     * Store new event
     */
    public function store(Request $request)
    {
        // Validate input
        $validated = $request->validate([
            'event_type_id' => 'required|exists:event_types,id',
            'name' => 'required|string|max:255',
            'start_date' => 'required|date|after:today',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'location_text' => 'required|string|max:500',
            'description' => 'nullable|string',
            'guest_estimate' => 'required|integer|min:1',
            'budget_overall' => 'required|numeric|min:0',
            'rsvp_deadline' => 'nullable|date|before:start_date',
            'guest_list_lock' => 'nullable|date|before:rsvp_deadline',
            'planner_id' => 'nullable|exists:users,id',
        ]);

        // Create event
        $event = Event::create([
            'client_id' => Auth::id(),
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

        // CREATE NOTIFICATION FOR PLANNER
            \App\Models\Notification::create([
                'user_id' => $request->planner_id,
                'type' => 'request',
                'priority' => 'high',
                'title' => 'New Event Request',
                'message' => Auth::user()->name . ' requested: ' . $event->name,
                'icon' => 'fas fa-inbox',
                'action_url' => '/planner/requests',
            ]);

        // TODO: Store RSVP deadline and guest list lock in event settings table (future)

        // Redirect to event details with success message
        return redirect()
            ->route('client.events.show', $event->id)
            ->with('success', 'Event created successfully! 🎉');
    }

    /**
     * Show single event details
     */
    public function show($id)
    {
        $event = Event::with([
            'eventType',
            'planner.plannerProfile',
            'guests',
            'budgetCategories',
            'tasks',
            'vendors'
        ])->findOrFail($id);

        // Check if user owns this event
        if ($event->client_id !== Auth::id()) {
            abort(403, 'Unauthorized access');
        }

        return view('client.events.show', compact('event'));
    }
}
