<?php

namespace App\Http\Controllers\Planner;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class AnalyticsController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        
        // Get all planner events
        $allEvents = $user->eventsOfPlanner()
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
        
        // Client satisfaction average
        $avgSatisfaction = 8.5; // Mock data - calculate from reviews in real app
        
        // Performance stats
        $stats = [
            'total_events' => $allEvents->count(),
            'completed_events' => $allEvents->where('status', 'completed')->count(),
            'active_events' => $allEvents->whereIn('status', ['planned', 'in_progress'])->count(),
            'total_revenue' => $allEvents->where('status', 'completed')->sum('budget_overall') * 0.15,
            'avg_event_value' => $allEvents->where('status', 'completed')->avg('budget_overall') * 0.15,
            'task_completion_rate' => round($taskCompletionRate, 1),
            'avg_satisfaction' => $avgSatisfaction,
            'total_clients' => $allEvents->unique('client_id')->count(),
        ];
        
        // Milestones & achievements
        $milestones = [
            ['icon' => '🎖️', 'title' => 'First 10 Events', 'date' => 'May 2024', 'unlocked' => $stats['total_events'] >= 10],
            ['icon' => '⭐', 'title' => '5-Star Rating', 'date' => 'Jun 2024', 'unlocked' => $avgSatisfaction >= 4.5],
            ['icon' => '💰', 'title' => '$50K Revenue', 'date' => 'Jul 2024', 'unlocked' => $stats['total_revenue'] >= 50000],
            ['icon' => '🔥', 'title' => '30-Day Streak', 'date' => 'Aug 2024', 'unlocked' => false],
        ];
        
        // Predictions
        $predictions = [
            'next_month' => 'Based on trends, you\'re on track for ' . ceil($monthlyData[11]['count'] * 1.2) . ' events next month',
            'year_total' => 'You\'re on track for ' . ($stats['total_events'] * 2) . ' events this year',
            'busy_season' => 'Your busiest season is May - July',
        ];
        
        return view('planner.analytics', compact(
            'stats',
            'monthlyData',
            'revenueData',
            'eventTypeStats',
            'milestones',
            'predictions'
        ));
    }
}