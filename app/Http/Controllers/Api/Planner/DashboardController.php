<?php

namespace App\Http\Controllers\Api\Planner;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Event;
use Carbon\Carbon;

class DashboardController extends Controller
{
    /**
     * Get planner dashboard data for mobile app
     * Only returns weekly calendar with events
     */
    public function index(Request $request)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();
        
        // Get only confirmed events assigned to this planner
        $myEvents = Event::where('planner_id', $user->id)
            ->where('status', 'confirmed')
            ->with(['eventType', 'client', 'tasks'])
            ->orderBy('start_date', 'asc')
            ->get();
        
        // Calendar week - use requested date or current date
        $requestedDate = $request->date 
            ? Carbon::parse($request->date) 
            : Carbon::now();
            
        $weekStart = $requestedDate->copy()->startOfWeek();
        
        $calendarDays = [];
        for ($i = 0; $i < 7; $i++) {
            $day = $weekStart->copy()->addDays($i);
            
            // Get events for this specific day
            $dayEvents = $myEvents->filter(function($event) use ($day) {
                return $event->start_date->isSameDay($day);
            })->values();
            
            $calendarDays[] = [
                'date' => $day->format('Y-m-d'),
                'day_name' => $day->format('l'),
                'day_number' => $day->format('d'),
                'month' => $day->format('M'),
                'is_today' => $day->isToday(),
                'events' => $dayEvents->map(function($event) {
                    return [
                        'id' => $event->id,
                        'title' => $event->name,
                        'client_name' => $event->client->name ?? 'No Client',
                        'start_date' => $event->start_date->format('Y-m-d H:i:s'),
                        'end_date' => $event->end_date?->format('Y-m-d H:i:s'),
                        'location' => $event->location_text,
                       'guests' => $event->guest_estimate ?? $event->guests_count ?? 0,
                        'budget' => $event->budget_overall ?? 0,
                        'event_type' => $event->eventType->name ?? 'General',
                        'status' => $event->status,
                        'tasks_count' => $event->tasks->count(),
                        'completed_tasks' => $event->tasks->where('status', 'done')->count(),
                    ];
                }),
            ];
        }
        
        return response()->json([
            'success' => true,
            'data' => [
                'week_start' => $weekStart->format('Y-m-d'),
                'week_end' => $weekStart->copy()->addDays(6)->format('Y-m-d'),
                'selected_date' => $requestedDate->format('Y-m-d'),
                'calendar_days' => $calendarDays,
            ]
        ]);
    }
    
    /**
     * Get events for a specific date
     */
    public function getDayEvents(Request $request, $date)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();
        
        $selectedDate = Carbon::parse($date);
        
        $dayEvents = Event::where('planner_id', $user->id)
            ->where('status', 'confirmed')
            ->whereDate('start_date', $selectedDate)
            ->with(['eventType', 'client', 'tasks'])
            ->orderBy('start_date', 'asc')
            ->get()
            ->map(function($event) {
                return [
                    'id' => $event->id,
                    'title' => $event->name,
                    'client_name' => $event->client->name ?? 'No Client',
                    'start_date' => $event->start_date->format('Y-m-d H:i:s'),
                    'end_date' => $event->end_date?->format('Y-m-d H:i:s'),
                    'location' => $event->location_text,
                  'guests' => $event->guest_estimate ?? $event->guests_count ?? 0,
                    'budget' => $event->budget_overall ?? 0,
                    'event_type' => $event->eventType->name ?? 'General',
                    'status' => $event->status,
                    'tasks_count' => $event->tasks->count(),
                    'completed_tasks' => $event->tasks->where('status', 'done')->count(),
                ];
            });
        
        return response()->json([
            'success' => true,
            'data' => [
                'date' => $selectedDate->format('Y-m-d'),
                'day_name' => $selectedDate->format('l'),
                'events' => $dayEvents,
                'events_count' => $dayEvents->count(),
            ]
        ]);
    }
}