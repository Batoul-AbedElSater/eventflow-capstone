<?php

namespace App\Http\Controllers\Planner;

use App\Http\Controllers\Controller;
use App\Models\Task;
use App\Models\Event;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class TaskController extends Controller
{
    public function index()
    {
        $userId = Auth::id();

        $tasks = Task::where('user_id', $userId)
            ->with('event:id,name')
            ->orderBy('due_date', 'desc')
            ->orderBy('created_at', 'desc')
            ->get();

        $events = Event::where('planner_id', $userId)
            ->select('id', 'name')
            ->orderBy('name')
            ->get();

        // Calculate stats
        $stats = [
            'todo' => $tasks->where('status', 'pending')->count(),
            'in_progress' => $tasks->where('status', 'in_progress')->count(),
            'completed_today' => $tasks->where('status', 'done')
                ->where('updated_at', '>=', now()->startOfDay())
                ->count(),
            'productivity_score' => $this->calculateProductivityScore($tasks),
            'pomodoros_today' => 0,
        ];

        // Gamification data
        $gamification = [
            'level' => 1,
            'current_xp' => 0,
            'next_level_xp' => 100,
            'xp_percentage' => 0,
            'streak' => 0,
            'achievements' => 0,
        ];

        return view('planner.tasks.index', compact('tasks', 'events', 'stats', 'gamification'));
    }

    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'title' => 'required|string|max:255',
                'description' => 'nullable|string',
                'priority' => 'required|in:low,medium,high,urgent',
                'event_id' => 'nullable|exists:events,id',
                'due_date' => 'nullable|date',
                'progress' => 'nullable|integer|min:0|max:100',
            ]);

            $userId = Auth::id();

            $taskData = [
                'user_id' => $userId,
                'user_id' => $userId,
                'title' => $validated['title'],
                'description' => $validated['description'] ?? null,
                'priority' => $validated['priority'],
                'event_id' => $validated['event_id'] ?? null,
                'status' => 'pending',
                'progress' => $validated['progress'] ?? 0,
                'deadline' => $validated['due_date'] ?? null,
                'due_date' => $validated['due_date'] ?? null,
            ];

            $task = Task::create($taskData);
            $task->load('event:id,name');

            return response()->json([
                'success' => true,
                'message' => 'Task created successfully!',
                'task' => $task
            ]);

        } catch (\Exception $e) {
            Log::error('Task creation error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to create task: ' . $e->getMessage()
            ], 500);
        }
    }

    public function show($id)
    {
        $task = Task::where('user_id', Auth::id())
            ->with('event:id,name')
            ->findOrFail($id);

        return response()->json($task);
    }

    public function update(Request $request, $id)
    {
        try {
            $task = Task::where('user_id', Auth::id())->findOrFail($id);

            $validated = $request->validate([
                'title' => 'required|string|max:255',
                'description' => 'nullable|string',
                'priority' => 'required|in:low,medium,high,urgent',
                'event_id' => 'nullable|exists:events,id',
                'due_date' => 'nullable|date',
                'progress' => 'nullable|integer|min:0|max:100',
                'status' => 'nullable|in:pending,in_progress,done',
            ]);

            $updateData = [
                'title' => $validated['title'],
                'description' => $validated['description'] ?? null,
                'priority' => $validated['priority'],
                'event_id' => $validated['event_id'] ?? null,
                'progress' => $validated['progress'] ?? 0,
                'deadline' => $validated['due_date'] ?? null,
                'due_date' => $validated['due_date'] ?? null,
            ];

            if (isset($validated['status'])) {
                $updateData['status'] = $validated['status'];
            }

            $task->update($updateData);
            $task->load('event:id,name');

            return response()->json([
                'success' => true,
                'message' => 'Task updated successfully!',
                'task' => $task
            ]);

        } catch (\Exception $e) {
            Log::error('Task update error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to update task: ' . $e->getMessage()
            ], 500);
        }
    }

   public function updateStatus(Request $request, $id)
{
    try {
        $task = Task::where('user_id', Auth::id())->findOrFail($id);
        $validated = $request->validate(['status' => 'required|in:pending,in_progress,done']);
        $task->update(['status' => $validated['status']]);
        return response()->json(['success' => true]);
    } catch (\Exception $e) {
        return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
    }
}

    public function destroy($id)
    {
        try {
            $task = Task::where('user_id', Auth::id())->findOrFail($id);
            $task->delete();

            return response()->json([
                'success' => true,
                'message' => 'Task deleted successfully!'
            ]);

        } catch (\Exception $e) {
            Log::error('Task delete error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete task'
            ], 500);
        }
    }

    public function duplicate($id)
    {
        try {
            $task = Task::where('user_id', Auth::id())->findOrFail($id);

            $newTask = $task->replicate();
            $newTask->status = 'pending';
            $newTask->progress = 0;
            $newTask->title = $task->title . ' (Copy)';
            $newTask->completed_at = null;
            $newTask->save();

            $newTask->load('event:id,name');

            return response()->json([
                'success' => true,
                'task' => $newTask,
                'message' => 'Task duplicated successfully!'
            ]);

        } catch (\Exception $e) {
            Log::error('Task duplicate error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to duplicate task'
            ], 500);
        }
    }

    private function calculateProductivityScore($tasks)
    {
        $total = $tasks->count();
        if ($total === 0) return 100;

        $completed = $tasks->where('status', 'done')->count();
        $inProgress = $tasks->where('status', 'in_progress')->count();

        return (int) ((($completed + ($inProgress * 0.5)) / $total) * 100);
    }
}
