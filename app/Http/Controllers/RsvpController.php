<?php

namespace App\Http\Controllers;

use App\Models\Guest;
use Illuminate\Http\Request;

class RsvpController extends Controller
{
    /**
     * Show RSVP form
     */
    public function show($token)
    {
        $guest = Guest::where('rsvp_token', $token)->firstOrFail();
        $event = $guest->event;
        
        // Check if already responded
        if ($guest->rsvp_status !== 'pending') {
            return view('rsvp.already-responded', compact('guest', 'event'));
        }
        
        return view('rsvp.form', compact('guest', 'event'));
    }

    /**
     * Update RSVP response
     */
    public function update(Request $request, $token)
    {
        $guest = Guest::where('rsvp_token', $token)->firstOrFail();
        
        $validated = $request->validate([
            'rsvp_status' => 'required|in:accepted,declined',
            'plus_one_name' => 'nullable|string|max:255',
            'dietary_restrictions' => 'nullable|string|max:255',
            'rsvp_message' => 'nullable|string|max:500',
        ]);
        
        // Use only columns that exist in your database
        $guest->update([
            'rsvp_status' => $validated['rsvp_status'],
            'plus_one_name' => $validated['plus_one_name'] ?? null,
            'dietary_restrictions' => $validated['dietary_restrictions'] ?? $guest->dietary_restrictions,
            'rsvp_message' => $validated['rsvp_message'] ?? null,
            // Don't update rsvp_date or rsvp_at - let it auto-update or use what exists
        ]);
        
        return view('rsvp.thank-you', compact('guest'));
    }
}