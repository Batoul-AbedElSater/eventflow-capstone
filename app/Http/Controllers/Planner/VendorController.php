<?php

namespace App\Http\Controllers\Planner;

use App\Http\Controllers\Controller;
use App\Models\Vendor;
use App\Models\Event;
use App\Models\VendorOrder;
use Illuminate\Support\Facades\Auth;

class VendorController extends Controller
{
    public function index(Event $event)
    {
        if ($event->planner_id !== Auth::id()) {
            abort(403);
        }

        $vendors = Vendor::all();

        return view('planner.events.vendor.vendor', compact('event', 'vendors'));
    }

    public function show(Event $event, Vendor $vendor)
    {
        if ($event->planner_id !== Auth::id()) {
            abort(403);
        }

        // Load orders with proper relationships
        $vendor->load(['orders' => function($query) use ($event) {
            $query->whereHas('task', function($q) use ($event) {
                $q->where('event_id', $event->id);
            })->with(['task', 'assistant']);
        }]);

        return view('planner.events.vendor.vendor_details', compact('event', 'vendor'));
    }

    public function favorites(int $eventId)
    {
        $event = Event::findOrFail($eventId);
        $vendors = $event->vendors()->wherePivot('is_favorite', true)->get();

        return view('planner.events.vendor.vendor_favorites', compact('event', 'vendors'));
    }

    public function toggleFavorite(Event $event, Vendor $vendor)
    {
        if ($event->planner_id !== Auth::id()) {
            abort(403);
        }

        $existing = $event->vendors()->where('vendor_id', $vendor->id)->first();

        if ($existing) {
            $currentStatus = $existing->pivot->is_favorite;
            $event->vendors()->updateExistingPivot($vendor->id, [
                'is_favorite' => !$currentStatus,
            ]);
        } else {
            $event->vendors()->attach($vendor->id, ['is_favorite' => true]);
        }

        return redirect()->back();
    }

    public function removeFavorite(Event $event, Vendor $vendor)
    {
        if ($event->planner_id !== Auth::id()) {
            abort(403);
        }

        $event->vendors()->detach($vendor->id);

        return redirect()->route('planner.events.vendors.favorites', $event->id);
    }
}