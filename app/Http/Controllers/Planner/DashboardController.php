<?php

namespace App\Http\Controllers\Planner;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\log;
use App\Models\Event;
use App\Models\Task;
use App\Models\Rating;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();
        $today = Carbon::today();

       // Get planner's events (only confirmed/accepted events)
$myEvents = Event::where('planner_id', $user->id)
   ->whereIn('status', ['confirmed', 'in_progress', 'completed'])
    ->with(['eventType', 'client', 'tasks'])
    ->orderBy('start_date', 'asc')
    ->get();

// Active events = confirmed events (now redundant, but keep for clarity)
$activeEvents = $myEvents;

        // Requests today = pending events created today (with no planner)
        $requestsToday = Event::whereNull('planner_id')
            ->where('status', 'pending')
            ->whereDate('created_at', $today)
            ->count();

        // Today's tasks (tasks due today, not done)
        $todayTasks = Task::where('user_id', $user->id)
            ->whereDate('due_date', '<=', $today)
            ->where('status', '!=', 'done')
            ->orderBy('due_date', 'asc')
            ->get();

        // Stats for header
        $stats = [
            'active_events' => $activeEvents->count(),
            'pending_requests' => $requestsToday,
        ];

        // Calendar week
        $requestedDate = $request->filled('date') ? Carbon::parse($request->input('date')) : Carbon::now();
        $weekStart = $requestedDate->copy()->startOfWeek();
        $weekEnd = $weekStart->copy()->endOfWeek();
        $calendarDays = [];
        for ($i = 0; $i < 7; $i++) {
            $day = $weekStart->copy()->addDays($i);
            $calendarDays[] = [
                'date' => $day,
                'events' => $myEvents->filter(fn($e) => $e->start_date->isSameDay($day))
            ];
        }

        // Time Machine: monthly data (last 12 months)
        $timeMachineData = [];
        $totalCompletedEvents = 0;
        $totalRevenue = 0;
        $monthlyCounts = [];
        for ($i = 11; $i >= 0; $i--) {
            $month = Carbon::now()->subMonths($i);
            $monthEvents = $myEvents->filter(fn($e) => $e->start_date->format('Y-m') === $month->format('Y-m'));
            $count = $monthEvents->count();
            $completed = $monthEvents->where('status', 'completed')->count();
            $revenue = $monthEvents->where('status', 'completed')->sum('budget_overall');
            $timeMachineData[] = [
                'month' => $month->format('M'),
                'year' => $month->format('Y'),
                'count' => $count,
                'revenue' => $revenue,
                'completed' => $completed,
                'is_peak' => $count >= 5,
            ];
            $totalCompletedEvents += $completed;
            $totalRevenue += $revenue;
            $monthlyCounts[] = $count;
        }
        $bestMonth = collect($timeMachineData)->sortByDesc('count')->first();
        $journeyInsights = [
            'total_journey_events' => $totalCompletedEvents,
            'best_month' => $bestMonth['month'] . ' ' . $bestMonth['year'],
            'best_month_count' => $bestMonth['count'],
            'total_journey_revenue' => $totalRevenue,
            'avg_monthly_events' => round(collect($monthlyCounts)->avg(), 1),
        ];

        // Event Health Monitor (real data from tasks & timeline)
        $eventHealth = [];
        foreach ($myEvents->take(5) as $event) {
            $totalTasks = $event->tasks->count();
            $completedTasks = $event->tasks->where('status', 'done')->count();
            $taskProgress = $totalTasks > 0 ? ($completedTasks / $totalTasks) * 100 : 100;
            $daysUntil = Carbon::now()->diffInDays($event->start_date, false);
            $timelineHealth = $daysUntil > 30 ? 100 : ($daysUntil > 7 ? 70 : ($daysUntil > 0 ? 40 : 0));
            $overall = round(($taskProgress + $timelineHealth) / 2);
            $eventHealth[] = [
                'event' => $event,
                'overall' => $overall,
                'tasks' => round($taskProgress),
                'timeline' => $timelineHealth,
                'status' => $overall >= 80 ? 'healthy' : ($overall >= 60 ? 'warning' : 'critical'),
            ];
        }

        // Conflict detector (same day events)
        $conflicts = [];
        foreach ($myEvents as $e1) {
            foreach ($myEvents as $e2) {
                if ($e1->id >= $e2->id) continue;
                if ($e1->start_date->isSameDay($e2->start_date)) {
                    $conflicts[] = ['event1' => $e1, 'event2' => $e2];
                }
            }
        }

        // Weather Guardian (events in the currently viewed week)
        $weekEvents = $myEvents->filter(function ($event) use ($weekStart, $weekEnd) {
            return $event->start_date->between($weekStart, $weekEnd, true);
        });

        $outdoorEvents = $weekEvents->filter(function ($event) {
            $location = mb_strtolower((string) ($event->location_text ?? ''));
            $outdoorKeywords = ['outdoor', 'garden', 'park', 'beach', 'resort', 'farm', 'rooftop', 'pool', 'camp'];

            foreach ($outdoorKeywords as $keyword) {
                if (str_contains($location, $keyword)) {
                    return true;
                }
            }

            return false;
        });

        // If location text does not include known outdoor keywords, still show week events.
        if ($outdoorEvents->isEmpty()) {
            $outdoorEvents = $weekEvents;
        }

        $outdoorEvents = $outdoorEvents->take(3);

        // Weather forecast (mock)
        $weatherForecast = [];
        for ($i = 0; $i < 14; $i++) {
            $day = Carbon::now()->addDays($i);
            $temp = rand(28, 35);
            $rainChance = rand(0, 40);
            $icon = $rainChance > 30 ? '🌧️' : ($rainChance > 15 ? '⛅' : '☀️');
            $weatherForecast[] = [
                'day' => $i === 0 ? 'Today' : ($i === 1 ? 'Tomorrow' : $day->format('D M j')),
                'temp' => $temp,
                'icon' => $icon,
                'rain_chance' => $rainChance,
            ];
        }

        // Client Happiness (based on ratings from clients)
       try {
    $ratings = Rating::where('planner_id', $user->id)->with('event')->get();
} catch (\Exception $e) {
    $ratings = collect();
}
        $clientHappiness = [];
        foreach ($ratings->take(5) as $rating) {
            $clientHappiness[] = [
                'event' => $rating->event,
                'score' => $rating->score,
                'mood' => $rating->score >= 8 ? '😊' : ($rating->score >= 6 ? '🙂' : '😐'),
                'trend' => 'up',
            ];
        }

        // Event requests (pending events without planner)
        $pendingRequests = Event::whereNull('planner_id')
            ->where('status', 'pending')
            ->with(['eventType', 'client'])
            ->orderBy('created_at', 'desc')
            ->get();

        return view('planner.dashboard', compact(
            'stats', 'calendarDays', 'weekStart', 'todayTasks',
            'timeMachineData', 'journeyInsights', 'eventHealth',
            'conflicts', 'outdoorEvents', 'weatherForecast',
            'clientHappiness', 'pendingRequests', 'myEvents'
        ));
    }

    /**
     * Simple AI: Calculate event health
     */
    private function calculateEventHealth($events)
    {
        $healthData = [];
        foreach ($events->take(5) as $event) {
            $totalTasks = $event->tasks->count();
            $completedTasks = $event->tasks->where('status', 'done')->count();
            $taskProgress = $totalTasks > 0 ? ($completedTasks / $totalTasks) * 100 : 100;
            $daysUntil = Carbon::now()->diffInDays($event->start_date, false);
            $timelineHealth = $daysUntil > 30 ? 100 : ($daysUntil > 7 ? 70 : ($daysUntil > 0 ? 40 : 0));
            $overall = round(($taskProgress + $timelineHealth) / 2);
            $healthData[] = [
                'event' => $event,
                'overall' => $overall,
                'tasks' => round($taskProgress),
                'timeline' => $timelineHealth,
                'status' => $overall >= 80 ? 'healthy' : ($overall >= 60 ? 'warning' : 'critical'),
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
        foreach ($events as $e1) {
            foreach ($events as $e2) {
                if ($e1->id >= $e2->id) continue;
                if ($e1->start_date->isSameDay($e2->start_date)) {
                    $conflicts[] = ['event1' => $e1, 'event2' => $e2];
                }
            }
        }
        return $conflicts;
    }

    /**
     * Simple AI: Calculate client happiness (based on ratings, fallback to task progress)
     */
    private function calculateClientHappiness($events)
    {
        $happiness = [];
        foreach ($events->take(5) as $event) {
            // Try to get rating first
            $rating = Rating::where('event_id', $event->id)->first();
            if ($rating) {
                $score = $rating->score;
            } else {
                // Fallback: estimate based on task progress
                $totalTasks = $event->tasks->count();
                $completedTasks = $event->tasks->where('status', 'done')->count();
                $taskProgress = $totalTasks > 0 ? ($completedTasks / $totalTasks) * 100 : 100;
                $score = 5 + ($taskProgress / 20); // 5 to 10 scale
                $score = min($score, 10);
            }
            $happiness[] = [
                'event' => $event,
                'score' => round($score, 1),
                'mood' => $score >= 8 ? '😊' : ($score >= 6 ? '🙂' : '😐'),
                'trend' => 'up',
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
        if ($event->planner_id !== null) {
            return back()->with('error', 'Event already has a planner');
        }
        $event->planner_id = Auth::id();
        $event->status = 'confirmed';
        $event->save();

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

        return back()->with('success', 'Event accepted! You can now manage this event.');
    }

    /**
     * Decline event request
     */
    public function declineRequest($eventId)
    {
        $event = Event::findOrFail($eventId);
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

        return back()->with('success', 'Event request declined.');
    }

    /**
     * Set rain alert for an event (Weather Guardian)
     */
    public function setRainAlert(Request $request, $eventId)
    {
        $event = Event::where('planner_id', Auth::id())->findOrFail($eventId);
        $rainChance = $request->input('rain_chance', 0);

        // Store the rain alert in a session or database – here we just log it
        // In a real implementation, you might create a notification or store in a alerts table.
        Log::info('Rain alert set', [
            'event_id' => $eventId,
            'planner_id' => Auth::id(),
            'rain_chance' => $rainChance,
        ]);

        // Optionally, create a notification for the planner (or client)
        \App\Models\Notification::create([
            'user_id' => Auth::id(),
            'type' => 'weather',
            'priority' => 'medium',
            'title' => 'Rain Alert Active',
            'message' => "You will be notified if rain chance exceeds {$rainChance}% for event '{$event->name}'.",
            'icon' => 'fas fa-cloud-rain',
            'action_url' => '/planner/events/' . $eventId,
        ]);

        return response()->json(['success' => true, 'message' => 'Rain alert set successfully.']);
    }
}
