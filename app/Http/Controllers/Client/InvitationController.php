<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\Event;
use App\Models\Guest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class InvitationController extends Controller
{
    /**
     * Send invitations to all guests or selected guests
     */
      public function send(Request $request, $eventId)
{
    try {
        $event = Event::with(['eventType', 'guests'])->findOrFail($eventId);
        
        if ($event->client_id !== Auth::id()) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }

        $guests = $event->guests()->where('invitation_sent', false)->get();

        if ($guests->isEmpty()) {
            return response()->json(['success' => false, 'message' => 'No pending invitations'], 400);
        }

        $successCount = 0;

        foreach ($guests as $guest) {
            if (!$guest->rsvp_token) {
                $guest->rsvp_token = Str::random(32);
                $guest->save();
            }

            // SEND REAL EMAIL
            Mail::send('emails.invitation', [
                'guest' => $guest,
                'event' => $event,
                'client' => Auth::user(),
                'rsvpUrl' => route('rsvp.show', $guest->rsvp_token)
            ], function ($message) use ($guest, $event) {
                $message->to($guest->email, $guest->name)
                        ->subject("You're Invited: {$event->name}");
            });

            $guest->update(['invitation_sent' => true, 'invitation_sent_at' => now()]);
            $successCount++;
        }

        return response()->json([
            'success' => true,
            'message' => "Sent to {$successCount} guests!",
            'sent_count' => $successCount
        ]);
        
    } catch (\Exception $e) {
        return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
    }
}

    /**
     * Show RSVP page (public - no auth required)
     */
    public function showRsvp($token)
    {
        $guest = Guest::where('rsvp_token', $token)
            ->with('event.eventType')
            ->firstOrFail();

        return view('rsvp.show', compact('guest'));
    }

    /**
     * Submit RSVP response
     */
    public function submitRsvp(Request $request, $token)
    {
        $guest = Guest::where('rsvp_token', $token)->firstOrFail();

        $validated = $request->validate([
            'rsvp_status' => 'required|in:accepted,declined',
            'plus_one_name' => 'nullable|string|max:255',
            'dietary_restrictions' => 'nullable|string|max:255',
            'message' => 'nullable|string|max:500',
        ]);

        // Update guest
        $guest->update([
            'rsvp_status' => $validated['rsvp_status'],
            'plus_one_name' => $validated['plus_one_name'] ?? $guest->plus_one_name,
            'dietary_restrictions' => $validated['dietary_restrictions'] ?? $guest->dietary_restrictions,
            'rsvp_message' => $validated['message'] ?? null,
            'rsvp_date' => now(),
        ]);

        return redirect()->route('rsvp.show', $token)
            ->with('success', 'Thank you! Your RSVP has been recorded.');
    }
}
