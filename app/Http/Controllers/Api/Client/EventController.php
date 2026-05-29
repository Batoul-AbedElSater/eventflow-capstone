<?php

namespace App\Http\Controllers\Api\Client;

use App\Http\Controllers\Controller;
use App\Models\Event;
use App\Models\EventType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class EventController extends Controller
{
    /**
     * Display a listing of events
     */
    public function index()
    {
        $events = Event::where('client_id', Auth::id())
            ->with('eventType', 'planner')
            ->orderBy('start_date', 'desc')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $events
        ]);
    }

    /**
     * Store a newly created event
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'event_type_id' => 'required|exists:event_types,id',
            'description' => 'nullable|string',
            'start_date' => 'required|date',
            'start_time' => 'required',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'end_time' => 'nullable',
            'location_text' => 'required|string',
            'guest_estimate' => 'required|integer|min:1',
            'budget_overall' => 'required|numeric|min:0',
            'planner_id' => 'nullable|exists:users,id',
        ]);

        $validated['client_id'] = Auth::id();
        $validated['status'] = $request->planner_id ? 'pending' : 'draft';

        $event = Event::create($validated);

        return response()->json([
            'success' => true,
            'message' => 'Event created successfully',
            'data' => $event->load('eventType', 'planner')
        ], 201);
    }

    /**
     * Display the specified event
     */
    public function show($id)
    {
        $event = Event::where('client_id', Auth::id())
            ->with('eventType', 'planner', 'invitations')
            ->findOrFail($id);

        return response()->json([
            'success' => true,
            'data' => $event
        ]);
    }

    /**
     * Update the specified event
     */
    public function update(Request $request, $id)
    {
        $event = Event::where('client_id', Auth::id())->findOrFail($id);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'event_type_id' => 'required|exists:event_types,id',
            'description' => 'nullable|string',
            'start_date' => 'required|date',
            'start_time' => 'required',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'end_time' => 'nullable',
            'location_text' => 'required|string',
            'guest_estimate' => 'required|integer|min:1',
            'budget_overall' => 'required|numeric|min:0',
        ]);

        $event->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Event updated successfully',
            'data' => $event->load('eventType', 'planner')
        ]);
    }

    /**
     * Remove the specified event
     */
    public function destroy($id)
    {
        $event = Event::where('client_id', Auth::id())->findOrFail($id);

        if ($event->event_photo) {
            Storage::disk('public')->delete($event->event_photo);
        }

        $event->delete();

        return response()->json([
            'success' => true,
            'message' => 'Event deleted successfully'
        ]);
    }

    /**
     * Upload event photo
     */
    public function uploadPhoto(Request $request, $id)
    {
        $event = Event::where('client_id', Auth::id())->findOrFail($id);

        $request->validate([
            'photo' => 'required|image|mimes:jpeg,png,jpg,webp|max:2048',
        ]);

        // Delete old photo
        if ($event->event_photo) {
            Storage::disk('public')->delete($event->event_photo);
        }

        $path = $request->file('photo')->store('event-photos', 'public');
        $event->update(['event_photo' => $path]);

        return response()->json([
            'success' => true,
            'message' => 'Photo uploaded successfully',
            'photo_url' => Storage::url($path)
        ]);
    }

    /**
     * Delete event photo
     */
    public function deletePhoto($id)
    {
        $event = Event::where('client_id', Auth::id())->findOrFail($id);

        if ($event->event_photo) {
            Storage::disk('public')->delete($event->event_photo);
            $event->update(['event_photo' => null]);
        }

        return response()->json([
            'success' => true,
            'message' => 'Photo deleted successfully'
        ]);
    }
}
