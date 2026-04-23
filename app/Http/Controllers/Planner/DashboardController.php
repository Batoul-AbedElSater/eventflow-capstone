<?php

namespace App\Http\Controllers\Planner;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Event;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        
        // Get planner's events (using YOUR method name)
        $myEvents = $user->eventsOfPlanner()
            ->with(['eventType', 'client', 'guests', 'tasks'])
            ->orderBy('start_date', 'asc')
            ->get();
        
        // Get pending event requests (events without planner)
        $pendingRequests = Event::whereNull('planner_id')
            ->where('status', 'draft')
            ->with(['eventType', 'client'])
            ->orderBy('created_at', 'desc')
            ->get();
        
        // Calculate stats
        $stats = [
            'total_events' => $myEvents->count(),
            'active_events' => $myEvents->whereIn('status', ['planned', 'in_progress'])->count(),
            'completed_events' => $myEvents->where('status', 'completed')->count(),
            'pending_requests' => $pendingRequests->count(),
        ];
        
        // Get this week's events for calendar
        // Get week based on request (default: current week)
        $requestedDate = request('date') ? Carbon::parse(request('date')) : Carbon::now();
        $weekStart = $requestedDate->copy()->startOfWeek();
        $weekEnd = $requestedDate->copy()->endOfWeek();
        
        // Build calendar week (7 days)
        $calendarDays = [];
        for ($i = 0; $i < 7; $i++) {
            $day = $weekStart->copy()->addDays($i);
            $calendarDays[] = [
                'date' => $day,
                'events' => $myEvents->filter(function($event) use ($day) {
                    return $event->start_date->isSameDay($day);
                })
            ];
        }
        
        // Get today's tasks
         $todayTasks = []; //\App\Models\Task::whereHas('event', function($q) use ($user) {
        //         $q->where('planner_id', $user->id);
        //     })
        //     ->where('due_date', '<=', Carbon::now()->endOfDay())
        //     ->where('status', '!=', 'done')
        //     ->orderBy('due_date', 'asc')
        //     ->take(5)
        //     ->get();
        
        // Simple AI: Event Health Analysis (hardcoded logic)
        $eventHealth = $this->calculateEventHealth($myEvents);
        
        // Simple AI: Conflict Detection
        $conflicts = $this->detectConflicts($myEvents);
        
        // Simple AI: Client Happiness (hardcoded)
        $clientHappiness = $this->calculateClientHappiness($myEvents);
        
        return view('planner.dashboard', compact(
            'myEvents', 
            'pendingRequests', 
            'stats', 
            'calendarDays',
            'weekStart',
            'todayTasks',
            'eventHealth',
            'conflicts',
            'clientHappiness'
        ));
    }
    
    /**
     * Simple AI: Calculate event health
     */
    private function calculateEventHealth($events)
    {
        $healthData = [];
        
        foreach ($events->take(3) as $event) {
            $totalTasks = $event->tasks->count();
            $completedTasks = $event->tasks->where('status', 'done')->count();
            $taskProgress = $totalTasks > 0 ? ($completedTasks / $totalTasks) * 100 : 0;
            
            $daysUntilEvent = Carbon::now()->diffInDays($event->start_date, false);
            $timelineHealth = $daysUntilEvent > 30 ? 100 : ($daysUntilEvent > 7 ? 70 : 50);
            
            $overallHealth = ($taskProgress + $timelineHealth) / 2;
            
            $healthData[] = [
                'event' => $event,
                'overall' => round($overallHealth),
                'tasks' => round($taskProgress),
                'timeline' => $timelineHealth,
                'status' => $overallHealth >= 80 ? 'healthy' : ($overallHealth >= 60 ? 'warning' : 'critical')
            ];
        }
        
        return $healthData;
    }
    
    /**
     * Simple AI: Detect scheduling conflicts
     */
    private function detectConflicts($events)
    {
        $conflicts = [];
        
        foreach ($events as $event1) {
            foreach ($events as $event2) {
                if ($event1->id >= $event2->id) continue;
                
                // Check if same day
                if ($event1->start_date->isSameDay($event2->start_date)) {
                    $conflicts[] = [
                        'event1' => $event1,
                        'event2' => $event2,
                        'type' => 'same_day',
                        'severity' => 'warning'
                    ];
                }
            }
        }
        
        return $conflicts;
    }
    
    /**
     * Simple AI: Calculate client happiness
     */
    private function calculateClientHappiness($events)
    {
        $happiness = [];
        
        foreach ($events->take(5) as $event) {
            $score = 7.0; // Base score
            
            // Boost if tasks are on track
            $totalTasks = $event->tasks->count();
            $completedTasks = $event->tasks->where('is_completed', true)->count();
            if ($totalTasks > 0 && ($completedTasks / $totalTasks) > 0.7) {
                $score += 1.5;
            }
            
            // Boost if event is not overdue
            if ($event->start_date > Carbon::now()) {
                $score += 0.5;
            }
            
            $score = min($score, 10); // Cap at 10
            
            $happiness[] = [
                'event' => $event,
                'score' => round($score, 1),
                'mood' => $score >= 8 ? '😊' : ($score >= 6 ? '🙂' : '😐'),
                'trend' => 'up'
            ];
        }
        
        return $happiness;
    }
    
    /**
     * Accept event request
     */
    public function acceptRequest($eventId)
    {
        $event = Event::findOrFail($eventId);
        
        // Check if event is available
        if ($event->planner_id !== null) {
            return back()->with('error', 'Event already has a planner');
        }
        
        // Assign planner
        $event->planner_id = Auth::id();
        $event->status = 'planned';
        $event->save();
        
        return back()->with('success', 'Event accepted! You can now manage this event.');
    }
    
    /**
     * Decline event request
     */
    public function declineRequest($eventId)
    {
        $event = Event::findOrFail($eventId);
        
        return back()->with('success', 'Event request declined');
    }
}