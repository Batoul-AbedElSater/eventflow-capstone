<?php

namespace App\Http\Controllers\Api\Client;

use App\Http\Controllers\Controller;
use App\Models\Event;
use App\Models\Guest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class InvitationController extends Controller
{
    /**
     * Send invitations to all unsent guests for an event
     * POST /api/client/events/{event}/send-invitations
     */
    public function sendAll(Request $request, Event $event)
    {
        // Ensure the authenticated client owns this event
        if ($event->client_id !== $request->user()->id) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized access to this event.',
            ], 403);
        }

        $guests = $event->guests()
            ->where('invitation_sent', false)
            ->get();

        if ($guests->isEmpty()) {
            return response()->json([
                'success' => false,
                'message' => 'No pending invitations to send.',
            ], 400);
        }

        $successCount = 0;
        $failedCount = 0;
        $errors = [];

        foreach ($guests as $guest) {
            try {
                // Generate token if not exists
                if (!$guest->rsvp_token) {
                    $guest->rsvp_token = Str::random(32);
                    $guest->save();
                }

                // Send invitation email
                Mail::to($guest->email)->send(new \App\Mail\GuestInvitation($guest, $event));

                // Mark as sent
                $guest->update([
                    'invitation_sent' => true,
                    'invitation_sent_at' => now(),
                ]);

                $successCount++;
            } catch (\Exception $e) {
                $failedCount++;
                $errors[] = [
                    'guest_id' => $guest->id,
                    'guest_name' => $guest->name,
                    'guest_email' => $guest->email,
                    'error' => $e->getMessage(),
                ];

                \Log::error("Failed to send invitation to {$guest->email}: " . $e->getMessage());
            }
        }

        return response()->json([
            'success' => $successCount > 0,
            'message' => "Invitations sent to {$successCount} guests." . 
                ($failedCount > 0 ? " Failed for {$failedCount} guests." : ""),
            'data' => [
                'sent_count' => $successCount,
                'failed_count' => $failedCount,
                'total_guests' => $guests->count(),
                'errors' => $errors,
            ],
        ], $successCount > 0 ? 200 : 500);
    }

    /**
     * Send invitation to specific guests
     * POST /api/client/events/{event}/send-invitations/selected
     */
    public function sendSelected(Request $request, Event $event)
    {
        // Ensure the authenticated client owns this event
        if ($event->client_id !== $request->user()->id) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized access to this event.',
            ], 403);
        }

        $validated = $request->validate([
            'guest_ids' => ['required', 'array', 'min:1'],
            'guest_ids.*' => ['required', 'integer', 'exists:guests,id'],
        ]);

        $guests = Guest::whereIn('id', $validated['guest_ids'])
            ->where('event_id', $event->id)
            ->get();

        if ($guests->isEmpty()) {
            return response()->json([
                'success' => false,
                'message' => 'No valid guests found.',
            ], 400);
        }

        $successCount = 0;
        $failedCount = 0;
        $errors = [];

        foreach ($guests as $guest) {
            try {
                if (!$guest->rsvp_token) {
                    $guest->rsvp_token = Str::random(32);
                    $guest->save();
                }

                Mail::to($guest->email)->send(new \App\Mail\GuestInvitation($guest, $event));

                $guest->update([
                    'invitation_sent' => true,
                    'invitation_sent_at' => now(),
                ]);

                $successCount++;
            } catch (\Exception $e) {
                $failedCount++;
                $errors[] = [
                    'guest_id' => $guest->id,
                    'guest_name' => $guest->name,
                    'error' => $e->getMessage(),
                ];

                \Log::error("Failed to send invitation to {$guest->email}: " . $e->getMessage());
            }
        }

        return response()->json([
            'success' => $successCount > 0,
            'message' => "Invitations sent to {$successCount} guests." . 
                ($failedCount > 0 ? " Failed for {$failedCount} guests." : ""),
            'data' => [
                'sent_count' => $successCount,
                'failed_count' => $failedCount,
                'errors' => $errors,
            ],
        ], $successCount > 0 ? 200 : 500);
    }

    /**
     * Get invitation statistics for an event
     * GET /api/client/events/{event}/invitation-stats
     */
    public function stats(Request $request, Event $event)
    {
        if ($event->client_id !== $request->user()->id) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized access to this event.',
            ], 403);
        }

        $totalGuests = $event->guests()->count();
        $sentCount = $event->guests()->where('invitation_sent', true)->count();
        $pendingCount = $event->guests()->where('invitation_sent', false)->count();
        
        $responses = [
            'accepted' => $event->guests()->where('rsvp_status', 'accepted')->count(),
            'declined' => $event->guests()->where('rsvp_status', 'declined')->count(),
            'pending' => $event->guests()->where('rsvp_status', 'pending')->count(),
        ];

        return response()->json([
            'success' => true,
            'data' => [
                'total_guests' => $totalGuests,
                'invitations_sent' => $sentCount,
                'invitations_pending' => $pendingCount,
                'rsvp_responses' => $responses,
                'response_rate' => $sentCount > 0 
                    ? round((($responses['accepted'] + $responses['declined']) / $sentCount) * 100, 1) 
                    : 0,
            ],
        ]);
    }
}