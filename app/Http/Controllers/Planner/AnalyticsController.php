<?php

namespace App\Http\Controllers\Planner;

use App\Http\Controllers\Controller;
use App\Models\Rating;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class AnalyticsController extends Controller
{
    public function index()
    {
    /** @var \App\Models\User $user */
        $user = Auth::user();

        // Get all planner events
       $allEvents = $user->plannerEvents()
            ->with(['eventType', 'client', 'tasks'])
            ->get();

        // Monthly event breakdown (last 12 months)
        $monthlyData = [];
        for ($i = 11; $i >= 0; $i--) {
            $month = Carbon::now()->subMonths($i);
            $count = $allEvents->filter(function($event) use ($month) {
                return $event->start_date->format('Y-m') === $month->format('Y-m');
            })->count();

            $monthlyData[] = [
                'month' => $month->format('M'),
                'count' => $count,
                'full_date' => $month->format('Y-m')
            ];
        }

        // Revenue by month (15% commission)
        $revenueData = [];
        for ($i = 11; $i >= 0; $i--) {
            $month = Carbon::now()->subMonths($i);
            $revenue = $allEvents->filter(function($event) use ($month) {
                return $event->start_date->format('Y-m') === $month->format('Y-m')
                    && $event->status === 'completed';
            })->sum('budget_overall') * 0.15;

            $revenueData[] = [
                'month' => $month->format('M'),
                'revenue' => $revenue
            ];
        }

        // Event type breakdown
        $eventTypeStats = $allEvents->groupBy('event_type_id')->map(function($events, $typeId) {
            return [
                'name' => $events->first()->eventType->name ?? 'Unknown',
                'count' => $events->count(),
                'revenue' => $events->where('status', 'completed')->sum('budget_overall') * 0.15
            ];
        })->values();

        // Task completion rate
        $totalTasks = \App\Models\Task::whereHas('event', function($q) use ($user) {
            $q->where('planner_id', $user->id);
        })->count();

        $completedTasks = \App\Models\Task::whereHas('event', function($q) use ($user) {
            $q->where('planner_id', $user->id);
        })->where('status', 'done')->count();

        $taskCompletionRate = $totalTasks > 0 ? ($completedTasks / $totalTasks) * 100 : 0;

        // Client satisfaction average (normalize 1-5 ratings to 10-point display when needed)
        $ratingQuery = Rating::where('planner_id', $user->id);
        $avgSatisfactionRaw = $ratingQuery->avg('score');
        $avgSatisfaction = 0;
        if (! is_null($avgSatisfactionRaw)) {
            $avgSatisfaction = $avgSatisfactionRaw <= 5 ? $avgSatisfactionRaw * 2 : $avgSatisfactionRaw;
        }
        $avgSatisfaction = round($avgSatisfaction, 1);

        // Performance stats
        $stats = [
            'total_events' => $allEvents->count(),
            'completed_events' => $allEvents->where('status', 'completed')->count(),
            'active_events' => $allEvents->whereIn('status', ['confirmed', 'planned', 'in_progress'])->count(),
            'total_revenue' => $allEvents->where('status', 'completed')->sum('budget_overall') * 0.15,
            'avg_event_value' => $allEvents->where('status', 'completed')->avg('budget_overall') * 0.15,
            'task_completion_rate' => round($taskCompletionRate, 1),
            'avg_satisfaction' => $avgSatisfaction,
            'total_clients' => $allEvents->unique('client_id')->count(),
        ];

        // Milestones & achievements (dynamic, based on real data)
        $eventsByDate = $allEvents
            ->filter(fn ($event) => ! is_null($event->start_date))
            ->sortBy('start_date')
            ->values();

        $first10Unlocked = $stats['total_events'] >= 10;
        $first10Date = $first10Unlocked && $eventsByDate->get(9)
            ? $eventsByDate->get(9)->start_date->format('M Y')
            : 'In progress';

        $highSatisfactionUnlocked = $avgSatisfaction >= 8;
        $latestRatingDate = $ratingQuery->latest('created_at')->value('created_at');
        $highSatisfactionDate = $highSatisfactionUnlocked && $latestRatingDate
            ? Carbon::parse($latestRatingDate)->format('M Y')
            : 'In progress';

        $revenue50kUnlocked = $stats['total_revenue'] >= 50000;
        $revenueThresholdDate = 'In progress';
        if ($revenue50kUnlocked) {
            $runningRevenue = 0;
            foreach ($eventsByDate->where('status', 'completed') as $event) {
                $runningRevenue += (float) $event->budget_overall * 0.15;
                if ($runningRevenue >= 50000) {
                    $revenueThresholdDate = $event->start_date->format('M Y');
                    break;
                }
            }
        }

        $taskMasteryUnlocked = $stats['task_completion_rate'] >= 80;
        $taskMasteryDate = $taskMasteryUnlocked ? Carbon::now()->format('M Y') : 'In progress';

        $milestones = [
            ['icon' => '🎖️', 'title' => 'First 10 Events', 'date' => $first10Date, 'unlocked' => $first10Unlocked],
            ['icon' => '⭐', 'title' => 'High Satisfaction (8+/10)', 'date' => $highSatisfactionDate, 'unlocked' => $highSatisfactionUnlocked],
            ['icon' => '💰', 'title' => '$50K Revenue', 'date' => $revenueThresholdDate, 'unlocked' => $revenue50kUnlocked],
            ['icon' => '✅', 'title' => 'Task Mastery (80%+)', 'date' => $taskMasteryDate, 'unlocked' => $taskMasteryUnlocked],
        ];

        // Predictions
        $predictions = [
            'next_month' => 'Based on trends, you\'re on track for ' . ceil($monthlyData[11]['count'] * 1.2) . ' events next month',
            'year_total' => 'You\'re on track for ' . ($stats['total_events'] * 2) . ' events this year',
            'busy_season' => 'Your busiest season is May - July',
        ];
        if (request()->expectsJson()) {
    return response()->json(compact('stats', 'monthlyData', 'revenueData', 'eventTypeStats', 'milestones', 'predictions'));
}

        return view('planner.events.analytics', compact(
            'stats',
            'monthlyData',
            'revenueData',
            'eventTypeStats',
            'milestones',
            'predictions'
        ));
    }
}
