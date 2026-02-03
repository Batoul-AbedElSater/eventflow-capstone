<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\Guest;
use App\Models\Event;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class GuestController extends Controller
{
    /**
     * Store new guest
     */
    public function store(Request $request, $eventId)
    {
        // Get event and verify ownership
        $event = Event::findOrFail($eventId);
        
        if ($event->client_id !== Auth::id()) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized access'
            ], 403);
        }

        // Validate input
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'phone' => 'nullable|string|max:20',
            'dietary_restrictions' => 'nullable|string|max:255',
            'plus_one_allowed' => 'nullable|boolean',
            'plus_one_name' => 'nullable|string|max:255',
            'notes' => 'nullable|string',
        ]);

        // Create guest
        $guest = Guest::create([
            'event_id' => $eventId,
            'name' => $validated['name'],
            'email' => $validated['email'],
            'phone' => $validated['phone'] ?? null,
            'dietary_restrictions' => $validated['dietary_restrictions'] ?? null,
            'plus_one_allowed' => $validated['plus_one_allowed'] ?? false,
            'plus_one_name' => $validated['plus_one_name'] ?? null,
            'notes' => $validated['notes'] ?? null,
            'rsvp_status' => 'pending',
            'invitation_sent' => false,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Guest added successfully!',
            'guest' => $guest
        ], 201);
    }

    /**
     * Update guest
     */
    public function update(Request $request, $eventId, $guestId)
    {
        // Get event and verify ownership
        $event = Event::findOrFail($eventId);
        
        if ($event->client_id !== Auth::id()) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized access'
            ], 403);
        }

        // Get guest
        $guest = Guest::where('event_id', $eventId)->findOrFail($guestId);

        // Validate input
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'phone' => 'nullable|string|max:20',
            'dietary_restrictions' => 'nullable|string|max:255',
            'plus_one_allowed' => 'nullable|boolean',
            'plus_one_name' => 'nullable|string|max:255',
            'notes' => 'nullable|string',
        ]);

        // Update guest
        $guest->update([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'phone' => $validated['phone'] ?? null,
            'dietary_restrictions' => $validated['dietary_restrictions'] ?? null,
            'plus_one_allowed' => $validated['plus_one_allowed'] ?? false,
            'plus_one_name' => $validated['plus_one_name'] ?? null,
            'notes' => $validated['notes'] ?? null,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Guest updated successfully!',
            'guest' => $guest
        ]);
    }

    /**
     * Delete guest
     */
    public function destroy($eventId, $guestId)
    {
        // Get event and verify ownership
        $event = Event::findOrFail($eventId);
        
        if ($event->client_id !== Auth::id()) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized access'
            ], 403);
        }

        // Get and delete guest
        $guest = Guest::where('event_id', $eventId)->findOrFail($guestId);
        $guest->delete();

        return response()->json([
            'success' => true,
            'message' => 'Guest removed successfully!'
        ]);
    }
}
