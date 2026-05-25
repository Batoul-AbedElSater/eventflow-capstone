<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\Event;
use App\Models\Guest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Mail;
use App\Mail\GuestInvitation;

class GuestController extends Controller
{
    /**
     * Show form to create guest
     */
    public function create($eventId)
    {
        $event = Event::where('client_id', Auth::id())->findOrFail($eventId);
        
        return view('client.guests.create', compact('event'));
    }

    /**
     * Store a new guest and send invitation email
     */
    public function store(Request $request, $eventId)
        {
            $event = Event::where('client_id', Auth::id())->findOrFail($eventId);

            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'email' => 'required|email|max:255',
                'phone' => 'nullable|string|max:20',
                'plus_one_allowed' => 'nullable|boolean',
                'dietary_restrictions' => 'nullable|string|max:255',
                'notes' => 'nullable|string',
            ]);

            // Set defaults
            $validated['event_id'] = $event->id;
            $validated['rsvp_status'] = 'pending';
            $validated['rsvp_token'] = Str::random(32);
            $validated['plus_one_allowed'] = $request->has('plus_one_allowed') ? true : false;
            
            $guest = Guest::create($validated);

            // Send invitation email
            try {
                // Log email attempt
                \Log::info('Attempting to send email to: ' . $guest->email);
                
                Mail::to($guest->email)->send(new GuestInvitation($guest, $event));
                
                // Log success
                \Log::info('Email sent successfully to: ' . $guest->email);
                
                // Mark invitation as sent
                $guest->update([
                    'invitation_sent' => true,
                    'invitation_sent_at' => now(),
                ]);
                
                $successMessage = 'Guest added and invitation sent successfully!';
            } catch (\Exception $e) {
                // Log detailed error
                \Log::error('Email send failed: ' . $e->getMessage());
                \Log::error('Stack trace: ' . $e->getTraceAsString());
                
                $successMessage = 'Guest added, but invitation email failed: ' . $e->getMessage();
            }

            return redirect()->route('client.events.show', $event->id)
                ->with('success', $successMessage);
        }
    
   

    /**
     * Show single guest
     */
    public function show($id)
    {
        $guest = Guest::whereHas('event', function($query) {
            $query->where('client_id', Auth::id());
        })->findOrFail($id);

        return view('client.guests.show', compact('guest'));
    }

    /**
     * Update guest
     */
    public function update(Request $request, $id)
    {
        $guest = Guest::whereHas('event', function($query) {
            $query->where('client_id', Auth::id());
        })->findOrFail($id);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'phone' => 'nullable|string|max:20',
            'plus_one_allowed' => 'nullable|boolean',
            'dietary_restrictions' => 'nullable|string|max:255',
            'notes' => 'nullable|string',
        ]);

        $validated['plus_one_allowed'] = $request->has('plus_one_allowed') ? true : false;

        $guest->update($validated);

        return redirect()->route('client.events.show', $guest->event_id)
            ->with('success', 'Guest updated successfully!');
    }

    /**
     * Delete guest
     */
    public function destroy($id)
    {
        $guest = Guest::whereHas('event', function($query) {
            $query->where('client_id', Auth::id());
        })->findOrFail($id);

        $eventId = $guest->event_id;
        $guest->delete();

        return redirect()->route('client.events.show', $eventId)
            ->with('success', 'Guest removed successfully!');
    }

    /**
     * List all guests for client
     */
    public function index()
    {
        $guests = Guest::whereHas('event', function($query) {
            $query->where('client_id', Auth::id());
        })->with('event')->get();

        return view('client.guests.index', compact('guests'));
    }

   
    /**
 * Resend invitation email
 */
        public function resendInvitation($id)
        {
            $guest = Guest::whereHas('event', function($query) {
                $query->where('client_id', Auth::id());
            })->findOrFail($id);

            try {
                Mail::to($guest->email)->send(new GuestInvitation($guest, $guest->event));
                
                $guest->update([
                    'invitation_sent' => true,
                    'invitation_sent_at' => now(),
                ]);
                
                return response()->json(['success' => true, 'message' => 'Invitation sent successfully!']);
            } catch (\Exception $e) {
                \Log::error('Email send failed: ' . $e->getMessage());
                return response()->json(['success' => false, 'message' => 'Failed to send email: ' . $e->getMessage()], 500);
            }
        }
}