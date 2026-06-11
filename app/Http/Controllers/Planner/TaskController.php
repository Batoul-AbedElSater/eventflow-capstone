<?php

namespace App\Http\Controllers\Planner;

use App\Http\Controllers\Controller;
use App\Models\Task;
use App\Models\TaskAssignment;
use App\Models\Event;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class TaskController extends Controller
{
    public function index()
    {
        $userId = Auth::id();

        $tasks = Task::where('user_id', $userId)
             ->with(['event:id,name', 'assistants:id,name', 'vendors:id,name']) 
            ->orderBy('due_date', 'desc')
            ->orderBy('created_at', 'desc')
            ->get();

        $events = Event::where('planner_id', $userId)
            ->whereIn('status', ['confirmed', 'in_progress'])
            ->select('id', 'name')
            ->orderBy('name')
            ->get();

        $assistants = User::where('role', 'assistant')->select('id', 'name', 'email')->get();

        $stats = [
            'todo' => $tasks->where('status', 'pending')->count(),
            'in_progress' => $tasks->where('status', 'in_progress')->count(),
            'completed_today' => $tasks->where('status', 'done')
                ->where('updated_at', '>=', now()->startOfDay())
                ->count(),
            'productivity_score' => $this->calculateProductivityScore($tasks),
            'pomodoros_today' => 0,
        ];

        $gamification = [
            'level' => 1,
            'current_xp' => 0,
            'next_level_xp' => 100,
            'xp_percentage' => 0,
            'streak' => 0,
            'achievements' => 0,
        ];
        $vendors = \App\Models\Vendor::select('id', 'name')->orderBy('name')->get();


       return view('planner.tasks.index', compact('tasks', 'events', 'stats', 'gamification', 'assistants', 'vendors'));
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
                'assistant_id' => 'nullable|exists:users,id',
                'vendor_ids' => 'nullable|array',
                'vendor_ids.*' => 'exists:vendors,id',
                ]);

            $userId = Auth::id();
            $status = 'pending';
                if (($validated['progress'] ?? 0) >= 100) {
                  $status = 'done';
            } elseif (($validated['progress'] ?? 0) > 0) {
             $status = 'in_progress';
            }

            $task = Task::create([
                'user_id' => $userId,
                'title' => $validated['title'],
                'description' => $validated['description'] ?? null,
                'priority' => $validated['priority'],
                'event_id' => $validated['event_id'] ?? null,
                'status' => $status,
                'progress' => $validated['progress'] ?? 0,
                'deadline' => $validated['due_date'] ?? null,
                'due_date' => $validated['due_date'] ?? null,
            ]);

            // Assign single assistant
            if (!empty($validated['assistant_id'])) {
                TaskAssignment::create([
                    'task_id' => $task->id,
                    'assistant_id' => $validated['assistant_id'],
                    'assigned_by' => $userId,
                ]);
            }
            if (!empty($validated['vendor_ids'])) {
                $task->vendors()->sync($validated['vendor_ids']);
            }

            $task->load(['event:id,name', 'assistants:id,name', 'vendors:id,name']);

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
           ->with(['event:id,name', 'assistants:id,name', 'vendors:id,name'])  
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
                'assistant_id' => 'nullable|exists:users,id',
                'vendor_ids' => 'nullable|array',
                'vendor_ids.*' => 'exists:vendors,id',
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

            if (isset($validated['progress'])) {
                if ($validated['progress'] >= 100) {
                    $updateData['status'] = 'done';
                } elseif ($validated['progress'] > 0 && ($task->status === 'pending')) {
                    $updateData['status'] = 'in_progress';
                }
            }

            $task->update($updateData);

            // Sync single assistant
            if ($request->has('assistant_id')) {
                // Remove old assignment
                TaskAssignment::where('task_id', $task->id)->delete();
                
                // Create new assignment if not empty
                if (!empty($validated['assistant_id'])) {
                    TaskAssignment::create([
                        'task_id' => $task->id,
                        'assistant_id' => $validated['assistant_id'],
                        'assigned_by' => Auth::id(),
                    ]);
                }
            }

            // After syncing assistant...
if ($request->has('vendor_ids')) {
    $task->vendors()->sync($validated['vendor_ids'] ?? []);
}

          $task->load(['event:id,name', 'assistants:id,name', 'vendors:id,name']);

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

        $updateData = ['status' => $validated['status']];
        
      
        if ($validated['status'] === 'done') {
            $updateData['progress'] = 100;
            $updateData['completed_at'] = now();
        }
        
        
        if ($validated['status'] === 'pending' && $task->status === 'done') {
            $updateData['progress'] = 0;
            $updateData['completed_at'] = null;
        }
        
        
        if ($validated['status'] === 'in_progress' && $task->status === 'done') {
            $updateData['progress'] = 50;
            $updateData['completed_at'] = null;
        }

        $task->update($updateData);
        return response()->json(['success' => true]);
    } catch (\Exception $e) {
        return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
    }
}

    public function assignAssistant(Request $request, $taskId)
    {
        try {
            $task = Task::where('user_id', Auth::id())->findOrFail($taskId);

            $validated = $request->validate([
                'assistant_id' => 'required|exists:users,id',
            ]);

            // Remove old assignment first (one task = one assistant)
            TaskAssignment::where('task_id', $task->id)->delete();

            // Create new assignment
            TaskAssignment::create([
                'task_id' => $task->id,
                'assistant_id' => $validated['assistant_id'],
                'assigned_by' => Auth::id(),
            ]);

            $assistant = User::find($validated['assistant_id']);

            return response()->json([
                'success' => true,
                'message' => "{$assistant->name} assigned to task!",
                'assistant' => $assistant->only('id', 'name')
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to assign assistant'
            ], 500);
        }
    }

    public function removeAssistant(Request $request, $taskId, $assistantId)
    {
        try {
            $task = Task::where('user_id', Auth::id())->findOrFail($taskId);

            TaskAssignment::where('task_id', $task->id)
                ->where('assistant_id', $assistantId)
                ->delete();

            $assistant = User::find($assistantId);

            return response()->json([
                'success' => true,
                'message' => "{$assistant->name} removed from task"
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to remove assistant'
            ], 500);
        }
    }

    public function getAssignedAssistants($taskId)
    {
        try {
            $task = Task::where('user_id', Auth::id())->findOrFail($taskId);
            $assistants = $task->assistants()->select('id', 'name', 'email')->get();

            return response()->json([
                'success' => true,
                'assistants' => $assistants
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to load assistants'
            ], 500);
        }
    }

    public function destroy($id)
    {
        try {
            $task = Task::where('user_id', Auth::id())->findOrFail($id);
            TaskAssignment::where('task_id', $task->id)->delete();
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

            $newTask->load(['event:id,name', 'assistants:id,name']);

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