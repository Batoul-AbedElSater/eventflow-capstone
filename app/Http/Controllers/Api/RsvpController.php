<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Guest;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class RsvpController extends Controller
{
    /**
     * Show RSVP details by token (public - no auth required)
     * GET /api/rsvp/{token}
     * 
     * Equivalent to web: RsvpController@show
     * But returns JSON for mobile instead of HTML views
     */
    public function show($token): JsonResponse
    {
        $guest = Guest::where('rsvp_token', $token)
            ->with('event')
            ->first();

        if (!$guest) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid or expired invitation link.',
            ], 404);
        }

        // Check if already responded (matching web logic)
        $alreadyResponded = $guest->rsvp_status !== 'pending';

        return response()->json([
            'success' => true,
            'data' => [
                'already_responded' => $alreadyResponded,
                'guest' => [
                    'id' => $guest->id,
                    'name' => $guest->name,
                    'email' => $guest->email,
                    'plus_one_allowed' => (bool) $guest->plus_one_allowed,
                    'current_status' => $guest->rsvp_status,
                    'dietary_restrictions' => $guest->dietary_restrictions,
                    'rsvp_message' => $guest->rsvp_message,
                ],
                'event' => [
                    'id' => $guest->event->id,
                    'name' => $guest->event->name,
                    'date' => $guest->event->event_date,
                    'time' => $guest->event->start_time ?? null,
                    'venue' => $guest->event->venue,
                    'description' => $guest->event->description,
                ],
            ],
        ]);
    }

    /**
     * Submit RSVP response (public - no auth required)
     * POST /api/rsvp/{token}
     * 
     * Equivalent to web: RsvpController@update
     * But uses 'submit' as method name for RESTful convention
     */
    public function submit(Request $request, $token): JsonResponse
    {
        $guest = Guest::where('rsvp_token', $token)->first();

        if (!$guest) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid or expired invitation link.',
            ], 404);
        }

        // Check if already responded
        if ($guest->rsvp_status !== 'pending') {
            return response()->json([
                'success' => false,
                'message' => 'You have already responded to this invitation.',
                'data' => [
                    'current_status' => $guest->rsvp_status,
                ],
            ], 422);
        }

        $validated = $request->validate([
            'rsvp_status' => ['required', 'in:accepted,declined'],
            'plus_one_name' => ['nullable', 'string', 'max:255'],
            'dietary_restrictions' => ['nullable', 'string', 'max:255'],
            'rsvp_message' => ['nullable', 'string', 'max:500'],
        ]);

        // Update guest RSVP (matching web version fields)
        $guest->update([
            'rsvp_status' => $validated['rsvp_status'],
            'plus_one_name' => $validated['plus_one_name'] ?? null,
            'dietary_restrictions' => $validated['dietary_restrictions'] ?? $guest->dietary_restrictions,
            'rsvp_message' => $validated['rsvp_message'] ?? null,
        ]);

        // Prepare response message
        $message = $validated['rsvp_status'] === 'accepted' 
            ? 'Thank you! Your attendance has been confirmed.' 
            : 'Thank you for letting us know. You will be missed!';

        return response()->json([
            'success' => true,
            'message' => $message,
            'data' => [
                'guest_id' => $guest->id,
                'guest_name' => $guest->name,
                'status' => $guest->rsvp_status,
                'event_name' => $guest->event->name,
            ],
        ]);
    }
}