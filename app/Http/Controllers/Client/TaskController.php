<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\Event;
use Illuminate\Support\Facades\Auth;

class TaskController extends Controller
{
    /**
     * Show tasks for an event (Client view-only)
     */
    public function index($eventId)
    {
        $event = Event::with(['tasks' => function($query) {
            $query->orderBy('deadline', 'asc');
        }])->findOrFail($eventId);
        
        // Verify ownership
        if ($event->client_id !== Auth::id()) {
            abort(403, 'Unauthorized access');
        }
        
        // Calculate task stats
        $totalTasks = $event->tasks->count();
        $completedTasks = $event->tasks->where('status', 'completed')->count();
        $pendingTasks = $event->tasks->whereIn('status', ['pending', 'in_progress'])->count();
        $overdueTasks = $event->tasks->where('status', '!=', 'completed')
            ->where('deadline', '<', now())->count();
        
        $completionPercentage = $totalTasks > 0 
            ? round(($completedTasks / $totalTasks) * 100, 1) 
            : 0;
        
        // Group tasks by status
        $tasksByStatus = [
            'pending' => $event->tasks->where('status', 'pending'),
            'in_progress' => $event->tasks->where('status', 'in_progress'),
            'completed' => $event->tasks->where('status', 'completed'),
        ];
        
        return view('client.tasks.index', compact(
            'event',
            'totalTasks',
            'completedTasks',
            'pendingTasks',
            'overdueTasks',
            'completionPercentage',
            'tasksByStatus'
        ));
    }
}
