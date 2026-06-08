<?php

namespace App\Http\Controllers\Api\Client;

use App\Http\Controllers\Controller;
use App\Mail\GuestInvitation;
use App\Models\Event;
use App\Models\Guest;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class GuestController extends Controller
{
    /**
     * List all guests for the authenticated client.
     * GET /api/client/guests
     */
    public function index(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'event_id' => ['nullable', 'integer', 'exists:events,id'],
            'status' => ['nullable', Rule::in(['pending', 'accepted', 'declined'])],
            'search' => ['nullable', 'string', 'max:255'],
            'per_page' => ['nullable', 'integer', 'min:1', 'max:100'],
        ]);

        $query = Guest::whereHas('event', function ($query) use ($request) {
            $query->where('client_id', $request->user()->id);
        })->with('event');

        // Filter by event if provided
        if (!empty($validated['event_id'])) {
            $query->where('event_id', $validated['event_id']);
        }

        // Filter by RSVP status
        if (!empty($validated['status'])) {
            $query->where('rsvp_status', $validated['status']);
        }

        // Search functionality
        if (!empty($validated['search'])) {
            $searchTerm = $validated['search'];
            $query->where(function ($q) use ($searchTerm) {
                $q->where('name', 'like', "%{$searchTerm}%")
                  ->orWhere('email', 'like', "%{$searchTerm}%")
                  ->orWhere('phone', 'like', "%{$searchTerm}%");
            });
        }

        $perPage = $validated['per_page'] ?? 20;
        $guests = $query->orderBy('created_at', 'desc')->paginate($perPage);

        return response()->json([
            'success' => true,
            'data' => $guests->map(fn ($guest) => $this->formatGuestResponse($guest)),
            'meta' => [
                'current_page' => $guests->currentPage(),
                'per_page' => $guests->perPage(),
                'total' => $guests->total(),
                'last_page' => $guests->lastPage(),
            ],
        ]);
    }

    /**
     * List guests for a specific event.
     * GET /api/client/events/{event}/guests
     */
    public function byEvent(Request $request, Event $event): JsonResponse
    {
        // Ensure the authenticated client owns this event
        if ($event->client_id !== $request->user()->id) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized access to this event.',
            ], 403);
        }

        $validated = $request->validate([
            'status' => ['nullable', Rule::in(['pending', 'accepted', 'declined'])],
            'search' => ['nullable', 'string', 'max:255'],
            'per_page' => ['nullable', 'integer', 'min:1', 'max:100'],
        ]);

        $query = Guest::where('event_id', $event->id)
            ->with('event');

        // Filter by RSVP status
        if (!empty($validated['status'])) {
            $query->where('rsvp_status', $validated['status']);
        }

        // Search functionality
        if (!empty($validated['search'])) {
            $searchTerm = $validated['search'];
            $query->where(function ($q) use ($searchTerm) {
                $q->where('name', 'like', "%{$searchTerm}%")
                  ->orWhere('email', 'like', "%{$searchTerm}%")
                  ->orWhere('phone', 'like', "%{$searchTerm}%");
            });
        }

        $perPage = $validated['per_page'] ?? 20;
        $guests = $query->orderBy('created_at', 'desc')->paginate($perPage);

        return response()->json([
            'success' => true,
            'data' => $guests->map(fn ($guest) => $this->formatGuestResponse($guest)),
            'event' => [
                'id' => $event->id,
                'name' => $event->name,
                'date' => $event->event_date,
                'venue' => $event->venue,
            ],
            'meta' => [
                'current_page' => $guests->currentPage(),
                'per_page' => $guests->perPage(),
                'total' => $guests->total(),
                'last_page' => $guests->lastPage(),
            ],
        ]);
    }

    /**
     * Store a new guest and optionally send invitation.
     * POST /api/client/events/{event}/guests
     */
    public function store(Request $request, Event $event): JsonResponse
    {
        // Ensure the authenticated client owns this event
        if ($event->client_id !== $request->user()->id) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized access to this event.',
            ], 403);
        }

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255'],
            'phone' => ['nullable', 'string', 'max:20'],
            'plus_one_allowed' => ['nullable', 'boolean'],
            'plus_one_name' => ['nullable', 'string', 'max:255'],
            'dietary_restrictions' => ['nullable', 'string', 'max:255'],
            'notes' => ['nullable', 'string'],
            'send_invitation' => ['nullable', 'boolean'],
        ]);

        // Create guest
        $guest = Guest::create([
            'event_id' => $event->id,
            'name' => $validated['name'],
            'email' => $validated['email'],
            'phone' => $validated['phone'] ?? null,
            'rsvp_token' => Str::random(32),
            'rsvp_status' => 'pending',
            'plus_one_allowed' => $request->boolean('plus_one_allowed', false),
            'plus_one_name' => $validated['plus_one_name'] ?? null,
            'dietary_restrictions' => $validated['dietary_restrictions'] ?? null,
            'notes' => $validated['notes'] ?? null,
        ]);

        $emailSent = false;
        $emailError = null;

        // Send invitation email if requested (defaults to true)
        if ($request->boolean('send_invitation', true)) {
            try {
                Log::info('Attempting to send invitation email to: ' . $guest->email);
                
                Mail::to($guest->email)->send(new GuestInvitation($guest, $event));
                
                Log::info('Invitation email sent successfully to: ' . $guest->email);
                
                $guest->update([
                    'invitation_sent' => true,
                    'invitation_sent_at' => now(),
                ]);
                
                $emailSent = true;
            } catch (\Exception $e) {
                Log::error('Failed to send invitation email: ' . $e->getMessage());
                Log::error('Stack trace: ' . $e->getTraceAsString());
                
                $emailError = $e->getMessage();
            }
        }

        $message = 'Guest added successfully!';
        if ($emailSent) {
            $message = 'Guest added and invitation sent successfully!';
        } elseif ($request->boolean('send_invitation', true)) {
            $message = 'Guest added, but invitation email failed.';
        }

        return response()->json([
            'success' => true,
            'message' => $message,
            'email_sent' => $emailSent,
            'email_error' => $emailError,
            'data' => $this->formatGuestResponse($guest->fresh('event')),
        ], 201);
    }

    /**
     * Show a single guest.
     * GET /api/client/guests/{guest}
     */
    public function show(Request $request, Guest $guest): JsonResponse
    {
        // Ensure the authenticated client owns this guest's event
        if ($guest->event->client_id !== $request->user()->id) {
            return response()->json([
                'success' => false,
                'message' => 'Guest not found.',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $this->formatGuestResponse($guest->load('event')),
        ]);
    }

    /**
     * Update a guest.
     * PUT/PATCH /api/client/guests/{guest}
     */
    public function update(Request $request, Guest $guest): JsonResponse
    {
        // Ensure the authenticated client owns this guest's event
        if ($guest->event->client_id !== $request->user()->id) {
            return response()->json([
                'success' => false,
                'message' => 'Guest not found.',
            ], 404);
        }

        $validated = $request->validate([
            'name' => ['sometimes', 'required', 'string', 'max:255'],
            'email' => ['sometimes', 'required', 'email', 'max:255'],
            'phone' => ['nullable', 'string', 'max:20'],
            'plus_one_allowed' => ['nullable', 'boolean'],
            'plus_one_name' => ['nullable', 'string', 'max:255'],
            'dietary_restrictions' => ['nullable', 'string', 'max:255'],
            'notes' => ['nullable', 'string'],
        ]);

        // Handle boolean properly
        if ($request->has('plus_one_allowed')) {
            $validated['plus_one_allowed'] = $request->boolean('plus_one_allowed');
        }

        $guest->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Guest updated successfully!',
            'data' => $this->formatGuestResponse($guest->fresh('event')),
        ]);
    }

    /**
     * Delete a guest.
     * DELETE /api/client/guests/{guest}
     */
    public function destroy(Request $request, Guest $guest): JsonResponse
    {
        // Ensure the authenticated client owns this guest's event
        if ($guest->event->client_id !== $request->user()->id) {
            return response()->json([
                'success' => false,
                'message' => 'Guest not found.',
            ], 404);
        }

        $guest->delete();

        return response()->json([
            'success' => true,
            'message' => 'Guest removed successfully!',
        ]);
    }

    /**
     * Resend invitation email to a guest.
     * POST /api/client/guests/{guest}/resend
     */
    public function resendInvitation(Request $request, Guest $guest): JsonResponse
    {
        // Ensure the authenticated client owns this guest's event
        if ($guest->event->client_id !== $request->user()->id) {
            return response()->json([
                'success' => false,
                'message' => 'Guest not found.',
            ], 404);
        }

        try {
            Log::info('Resending invitation email to: ' . $guest->email);
            
            Mail::to($guest->email)->send(new GuestInvitation($guest, $guest->event));
            
            Log::info('Invitation email resent successfully to: ' . $guest->email);
            
            $guest->update([
                'invitation_sent' => true,
                'invitation_sent_at' => now(),
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Invitation sent successfully!',
                'data' => $this->formatGuestResponse($guest->fresh('event')),
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to resend invitation email: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to send invitation email. Please try again.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Mark a guest as checked in.
     * POST /api/client/guests/{guest}/check-in
     */
    public function checkIn(Request $request, Guest $guest): JsonResponse
    {
        // Ensure the authenticated client owns this guest's event
        if ($guest->event->client_id !== $request->user()->id) {
            return response()->json([
                'success' => false,
                'message' => 'Guest not found.',
            ], 404);
        }

        $guest->update([
            'check_in_time' => now(),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Guest checked in successfully!',
            'data' => $this->formatGuestResponse($guest->fresh('event')),
        ]);
    }

    /**
     * Undo a guest check-in.
     * DELETE /api/client/guests/{guest}/check-in
     */
    public function undoCheckIn(Request $request, Guest $guest): JsonResponse
    {
        // Ensure the authenticated client owns this guest's event
        if ($guest->event->client_id !== $request->user()->id) {
            return response()->json([
                'success' => false,
                'message' => 'Guest not found.',
            ], 404);
        }

        $guest->update([
            'check_in_time' => null,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Guest check-in removed successfully!',
            'data' => $this->formatGuestResponse($guest->fresh('event')),
        ]);
    }

    /**
     * Format guest data for API response.
     */
    private function formatGuestResponse(Guest $guest): array
    {
        return [
            'id' => $guest->id,
            'event_id' => $guest->event_id,
            'event' => $guest->relationLoaded('event') && $guest->event ? [
                'id' => $guest->event->id,
                'name' => $guest->event->name,
                'event_date' => $guest->event->event_date,
                'venue' => $guest->event->venue,
            ] : null,
            'name' => $guest->name,
            'email' => $guest->email,
            'phone' => $guest->phone,
            'rsvp_status' => $guest->rsvp_status,
            'rsvp_token' => $guest->rsvp_token,
            'plus_one_allowed' => (bool) $guest->plus_one_allowed,
            'plus_one_name' => $guest->plus_one_name,
            'dietary_restrictions' => $guest->dietary_restrictions,
            'notes' => $guest->notes,
            'invitation_sent' => (bool) $guest->invitation_sent,
            'invitation_sent_at' => $guest->invitation_sent_at,
            'check_in_time' => $guest->check_in_time,
            'created_at' => $guest->created_at,
            'updated_at' => $guest->updated_at,
        ];
    }
}