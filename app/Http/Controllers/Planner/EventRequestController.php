<?php

namespace App\Http\Controllers\Planner;

use App\Http\Controllers\Controller;
use App\Models\Event;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class EventRequestController extends Controller
{
    public function index(Request $request)
    {
        // Base query
        $query = Event::where('planner_id', Auth::id())
            ->where('status', 'pending')
            ->with(['client', 'eventType']);

        // Filters
        if ($request->has('filter')) {
            switch ($request->filter) {
                case 'this-week':
                    $query->whereBetween('start_date', [
                        Carbon::now()->startOfWeek(),
                        Carbon::now()->endOfWeek()
                    ]);
                    break;
                case 'high-budget':
                    $query->where('budget_overall', '>=', 50000);
                    break;
            }
        }

        // Sorting
        if ($request->has('sort')) {
            switch ($request->sort) {
                case 'newest':
                    $query->orderBy('created_at', 'desc');
                    break;
                case 'oldest':
                    $query->orderBy('created_at', 'asc');
                    break;
                case 'budget-high':
                    $query->orderBy('budget_overall', 'desc');
                    break;
                case 'budget-low':
                    $query->orderBy('budget_overall', 'asc');
                    break;
                case 'guests':
                    $query->orderBy('guest_estimate', 'desc');
                    break;
                default:
                    $query->orderBy('created_at', 'desc');
            }
        } else {
            $query->orderBy('created_at', 'desc');
        }

        $pendingRequests = $query->paginate(10);

        // Calculate stats
        $acceptedToday = Event::where('planner_id', Auth::id())
            ->where('status', 'confirmed')
            ->whereDate('updated_at', today())
            ->count();

        $declinedToday = Event::where('planner_id', Auth::id())
            ->where('status', 'declined')
            ->whereDate('updated_at', today())
            ->count();

        return view('planner.requests', compact('pendingRequests', 'acceptedToday', 'declinedToday'));
    }

 public function accept($eventId)
{
    $event = Event::where('id', $eventId)
        ->where('status', 'pending')
        ->firstOrFail();

    // Direct database update (bypasses any model events)
    Event::where('id', $event->id)->update([
        'planner_id' => Auth::id(),
        'status' => 'confirmed',
    ]);

    // Refresh the instance
    $event->refresh();

    // Log to verify
    \Log::info('Event accepted (direct update)', [
        'id' => $event->id,
        'new_status' => $event->status,
    ]);

    // Create notification for client
    \App\Models\Notification::create([
        'user_id' => $event->client_id,
        'type' => 'event',
        'priority' => 'high',
        'title' => 'Event Request Accepted!',
        'message' => 'Your event "' . $event->name . '" has been accepted by the planner.',
        'icon' => 'fas fa-check-circle',
        'action_url' => '/client/events/' . $event->id,
    ]);

    return redirect()->route('planner.requests')->with('success', 'Event request accepted successfully!');
}

    public function remove($eventId)
        {
            $event = Event::where('planner_id', Auth::id())
                ->where('status', 'declined')
                ->findOrFail($eventId);

            // Notify client
            \App\Models\Notification::create([
                'user_id' => $event->client_id,
                'type' => 'event',
                'priority' => 'low',
                'title' => 'Event Removed',
                'message' => 'The declined event "' . $event->name . '" has been removed from records.',
                'icon' => 'fas fa-trash',
                'action_url' => '/client/dashboard',
            ]);

            $event->delete();

            return redirect()->route('planner.requests')->with('success', 'Event removed successfully.');
        }

    public function decline($eventId)
    {
        $event = Event::where('planner_id', Auth::id())
            ->where('id', $eventId)
            ->where('status', 'pending')
            ->firstOrFail();

        $event->status = 'declined';
        $event->save();

        // Create notification for client
        \App\Models\Notification::create([
            'user_id' => $event->client_id,
            'type' => 'event',
            'priority' => 'medium',
            'title' => 'Event Request Declined',
            'message' => 'Your event "' . $event->name . '" was declined. Please contact the planner for more information.',
            'icon' => 'fas fa-times-circle',
            'action_url' => '/client/dashboard',
        ]);

        return redirect()->route('planner.requests')->with('success', 'Event request declined.');
    }
}