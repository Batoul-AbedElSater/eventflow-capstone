<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\Event;
use App\Models\User;
use App\Models\EventType;
use App\Models\Notification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use App\Models\Rating;

class EventController extends Controller
{
   public function index()
        {
            $clientId = Auth::id();
            
            $events = Event::where('client_id', $clientId)
                ->with(['eventType', 'planner'])   // client doesn't need 'client' relation – it's themselves
                ->orderBy('start_date', 'desc')
                ->get();

            // Summary statistics for the client (optional)
            $summary = [
                'total_events' => $events->count(),
                'pending' => $events->where('status', 'pending')->count(),
                'confirmed' => $events->where('status', 'confirmed')->count(),
                'declined' => $events->where('status', 'declined')->count(),
                'completed' => $events->where('status', 'completed')->count(),
                'total_budget' => $events->sum('budget_overall'),
            ];
            
            $stats = $summary; // if your blade uses $stats

            // Top clients doesn't make sense for a client page – you can remove or keep empty
            $topClients = collect();
            $metrics = [];

            return view('client.events.index', compact('events', 'summary', 'stats', 'topClients', 'metrics'));
        }

    public function create()
    {
        $eventTypes = EventType::all();

        $plannerQuery = \App\Models\User::where('role', 'planner')
            ->with('plannerProfile')
            ->withAvg('plannerRatings as rating_avg', 'score')
            ->withCount('plannerRatings as review_count')
            ->orderByDesc('rating_avg')
            ->orderBy('name');

        $planners = $plannerQuery->get();

        // Fallback for legacy data where role might not be set correctly.
        if ($planners->isEmpty()) {
            $planners = \App\Models\User::whereHas('plannerProfile')
                ->with('plannerProfile')
                ->withAvg('plannerRatings as rating_avg', 'score')
                ->withCount('plannerRatings as review_count')
                ->orderByDesc('rating_avg')
                ->orderBy('name')
                ->get();
        }

        return view('client.events.create', compact('eventTypes', 'planners'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'event_type_id' => 'required|exists:event_types,id',
            'description' => 'nullable|string',
            'start_date' => 'required|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'location_text' => 'required|string',
            'guest_estimate' => 'required|integer|min:1',
            'budget_overall' => 'required|numeric|min:0',
            'planner_id' => [
                'nullable',
                Rule::exists('users', 'id')->where(fn ($query) => $query->where('role', 'planner')),
            ],
            'event_photo' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
        ]);

        // Handle photo upload
        if ($request->hasFile('event_photo')) {
            $validated['event_photo'] = $request->file('event_photo')->store('event-photos', 'public');
        }

        $validated['client_id'] = Auth::id();
        $validated['status'] = $request->planner_id ? 'pending' : 'draft';

        $event = Event::create($validated);

        // Notify planner if assigned
      if ($request->planner_id) {
    try {
        Notification::create([
            'user_id' => $request->planner_id,
            'type' => 'request',
            'priority' => 'high',
            'title' => 'New Event Request',
            'message' => Auth::user()->name . ' has requested you to plan: ' . $event->name,
            'icon' => 'fas fa-calendar-plus',
            'action_url' => '/planner/requests',
        ]);
    } catch (\Exception $e) {}
}

        return redirect()->route('client.events.show', $event->id)
            ->with('success', 'Event created successfully!');
    }

    public function show($id)
        {
            $event = Event::where('client_id', Auth::id())
                ->with([
                    'eventType',
                    'planner',
                    'planner.plannerProfile',
                    'planner.plannerRatings',
                    'guests',
                    'budget.items',
                ])
                ->findOrFail($id);

            return view('client.events.show', compact('event'));
        }

    public function edit($id)
    {
        $event = Event::where('client_id', Auth::id())->findOrFail($id);
        $eventTypes = EventType::all();

        return view('client.events.edit', compact('event', 'eventTypes'));
    }

    public function storeRating(Request $request, $eventId)
    {
        try {
            $event = Event::where('client_id', Auth::id())->findOrFail($eventId);
            if ($event->status !== 'completed') {
                return response()->json(['success' => false, 'message' => 'Event not completed yet.']);
            }
            $existing = Rating::where('event_id', $eventId)->first();
            if ($existing) {
                return response()->json(['success' => false, 'message' => 'Already rated.']);
            }
            Rating::create([
                'event_id' => $eventId,
                'client_id' => Auth::id(),
                'planner_id' => $event->planner_id,
                'score' => $request->score,
                'review' => $request->review,
            ]);
            return response()->json(['success' => true]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    public function update(Request $request, $id)
    {
        $event = Event::where('client_id', Auth::id())->findOrFail($id);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'event_type_id' => 'required|exists:event_types,id',
            'description' => 'nullable|string',
            'start_date' => 'required|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'location_text' => 'required|string',
            'guest_estimate' => 'required|integer|min:1',
            'budget_overall' => 'required|numeric|min:0',
            'event_photo' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
        ]);

        // Handle photo upload
        if ($request->hasFile('event_photo')) {
            // Delete old photo
            if ($event->event_photo) {
                Storage::disk('public')->delete($event->event_photo);
            }
            $validated['event_photo'] = $request->file('event_photo')->store('event-photos', 'public');
        }

        $event->update($validated);

        // Notify planner of changes
        if ($event->planner_id) {
            Notification::create([
                'user_id' => $event->planner_id,
                'type' => 'event',
                'priority' => 'medium',
                'title' => 'Event Updated',
                'message' => Auth::user()->name . ' updated event details: ' . $event->name,
                'icon' => 'fas fa-edit',
                'action_url' => '/planner/events/' . $event->id,
            ]);
        }

        return redirect()->route('client.events.show', $event->id)
            ->with('success', 'Event updated successfully!');
    }

    public function destroy($id)
    {
        $event = Event::where('client_id', Auth::id())->findOrFail($id);

        // Notify planner if assigned
      if ($event->planner_id) {
    try {
        Notification::create([
            'user_id' => $event->planner_id,
            'type' => 'event',
            'priority' => 'medium',
            'title' => 'Event Deleted',
            'message' => Auth::user()->name . ' deleted the event: ' . $event->name,
            'icon' => 'fas fa-trash',
            'action_url' => '/planner/dashboard',
        ]);
    } catch (\Exception $e) {
        // notifications table not ready yet
    }
}

        // Delete photo if exists
        if ($event->event_photo) {
            Storage::disk('public')->delete($event->event_photo);
        }

        $event->delete();

        return redirect()->route('client.dashboard')
            ->with('success', 'Event deleted successfully.');
    }
}