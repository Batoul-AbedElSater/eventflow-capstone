<?php

namespace App\Http\Controllers\Api\Planner;

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
    /**
     * Get tasks for a specific event
     * GET /api/planner/events/{event}/tasks
     */
    public function index(Request $request, $eventId)
    {
        try {
            $userId = $request->user()->id;

            // Verify the planner owns this event
            $event = Event::where('planner_id', $userId)->findOrFail($eventId);

            $tasks = Task::where('event_id', $eventId)
                ->where('user_id', $userId)
                ->with(['assistants:id,name', 'vendors:id,name'])
                ->orderBy('due_date', 'desc')
                ->orderBy('created_at', 'desc')
                ->get()
                ->map(function ($task) {
                    return [
                        'id' => $task->id,
                        'title' => $task->title,
                        'description' => $task->description,
                        'priority' => $task->priority,
                        'status' => $task->status,
                        'progress' => $task->progress,
                        'due_date' => $task->due_date?->format('Y-m-d H:i:s'),
                        'completed_at' => $task->completed_at?->format('Y-m-d H:i:s'),
                        'created_at' => $task->created_at->format('Y-m-d H:i:s'),
                        'assistants' => $task->assistants->map(function ($a) {
                            return ['id' => $a->id, 'name' => $a->name];
                        }),
                        'vendors' => $task->vendors->map(function ($v) {
                            return ['id' => $v->id, 'name' => $v->name];
                        }),
                    ];
                });

            $stats = [
                'todo' => $tasks->where('status', 'pending')->count(),
                'in_progress' => $tasks->where('status', 'in_progress')->count(),
                'done' => $tasks->where('status', 'done')->count(),
                'total' => $tasks->count(),
            ];

            return response()->json([
                'success' => true,
                'data' => [
                    'event' => [
                        'id' => $event->id,
                        'name' => $event->name,
                    ],
                    'tasks' => $tasks,
                    'stats' => $stats,
                ]
            ]);

        } catch (\Exception $e) {
            Log::error('API Task index error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to load tasks',
            ], 500);
        }
    }

    /**
     * Create a new task for an event
     * POST /api/planner/events/{event}/tasks
     */
    public function store(Request $request, $eventId)
    {
        try {
            $userId = $request->user()->id;
            $event = Event::where('planner_id', $userId)->findOrFail($eventId);

            $validated = $request->validate([
                'title' => 'required|string|max:255',
                'description' => 'nullable|string',
                'priority' => 'required|in:low,medium,high,urgent',
                'due_date' => 'nullable|date',
                'progress' => 'nullable|integer|min:0|max:100',
                'assistant_id' => 'nullable|exists:users,id',
                'vendor_ids' => 'nullable|array',
                'vendor_ids.*' => 'exists:vendors,id',
            ]);

            $status = 'pending';
            if (($validated['progress'] ?? 0) >= 100) {
                $status = 'done';
            } elseif (($validated['progress'] ?? 0) > 0) {
                $status = 'in_progress';
            }

            $task = Task::create([
                'user_id' => $userId,
                'event_id' => $eventId,
                'title' => $validated['title'],
                'description' => $validated['description'] ?? null,
                'priority' => $validated['priority'],
                'status' => $status,
                'progress' => $validated['progress'] ?? 0,
                'due_date' => $validated['due_date'] ?? null,
            ]);

            if (!empty($validated['assistant_id'])) {
                TaskAssignment::create([
                    'task_id' => $task->id,
                    'assistant_id' => $validated['assistant_id'],
                    'assigned_by' => $userId,
                ]);
                  // 🔔 Notify assistant
    $assistant = User::find($validated['assistant_id']);
    $planner = $request->user();
    \App\Models\Notification::create([
        'user_id' => $assistant->id,
        'type' => 'task',
        'priority' => $validated['priority'] === 'urgent' ? 'high' : 'medium',
        'title' => 'New Task Assigned to You',
        'message' => "{$planner->name} assigned you the task \"{$task->title}\" for event: {$event->name}",
        'icon' => 'fas fa-tasks',
        'action_url' => '/assistant/tasks',
        'is_read' => false,
        'is_archived' => false,
    ]);
            }

            if (!empty($validated['vendor_ids'])) {
                $task->vendors()->sync($validated['vendor_ids']);
            }

            $task->load(['assistants:id,name', 'vendors:id,name']);

            return response()->json([
                'success' => true,
                'message' => 'Task created successfully!',
                'data' => $task,
            ], 201);

        } catch (\Exception $e) {
            Log::error('API Task store error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to create task',
            ], 500);
        }
    }

    /**
     * Update a task
     * PUT /api/planner/tasks/{task}
     */
    public function update(Request $request, $id)
    {
        try {
            $task = Task::where('user_id', $request->user()->id)->findOrFail($id);

            $validated = $request->validate([
                'title' => 'sometimes|string|max:255',
                'description' => 'nullable|string',
                'priority' => 'sometimes|in:low,medium,high,urgent',
                'due_date' => 'nullable|date',
                'progress' => 'nullable|integer|min:0|max:100',
                'status' => 'nullable|in:pending,in_progress,done',
                'assistant_id' => 'nullable|exists:users,id',
                'vendor_ids' => 'nullable|array',
                'vendor_ids.*' => 'exists:vendors,id',
            ]);

            $task->update($validated);

            if ($request->has('assistant_id')) {
                TaskAssignment::where('task_id', $task->id)->delete();
                if (!empty($validated['assistant_id'])) {
                    TaskAssignment::create([
                        'task_id' => $task->id,
                        'assistant_id' => $validated['assistant_id'],
                        'assigned_by' => $request->user()->id,
                    ]);
                      $assistant = User::find($validated['assistant_id']);
    $planner = $request->user();
    \App\Models\Notification::create([
        'user_id' => $assistant->id,
        'type' => 'task',
        'priority' => 'medium',
        'title' => 'Task Assigned to You',
        'message' => "{$planner->name} assigned you the task \"{$task->title}\"",
        'icon' => 'fas fa-tasks',
        'action_url' => '/assistant/tasks',
        'is_read' => false,
        'is_archived' => false,
    ]);
                }
            }

            if ($request->has('vendor_ids')) {
                $task->vendors()->sync($validated['vendor_ids'] ?? []);
            }

            $task->load(['assistants:id,name', 'vendors:id,name']);

            return response()->json([
                'success' => true,
                'message' => 'Task updated',
                'data' => $task,
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update task',
            ], 500);
        }
    }

    public function getAssistants()
{
    $assistants = \App\Models\User::where('role', 'assistant')
        ->select('id', 'name')
        ->get();
    return response()->json(['success' => true, 'data' => $assistants]);
}

    /**
     * Update task status
     * PUT /api/planner/tasks/{task}/status
     */
    public function updateStatus(Request $request, $id)
    {
        try {
            $task = Task::where('user_id', $request->user()->id)->findOrFail($id);
            $validated = $request->validate(['status' => 'required|in:pending,in_progress,done']);

            $updateData = ['status' => $validated['status']];

            if ($validated['status'] === 'done') {
                $updateData['progress'] = 100;
                $updateData['completed_at'] = now();
            } elseif ($validated['status'] === 'pending' && $task->status === 'done') {
                $updateData['progress'] = 0;
                $updateData['completed_at'] = null;
            } elseif ($validated['status'] === 'in_progress' && $task->status === 'done') {
                $updateData['progress'] = 50;
                $updateData['completed_at'] = null;
            }

            $task->update($updateData);

            return response()->json([
                'success' => true,
                'message' => 'Status updated',
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update status',
            ], 500);
        }
    }

    /**
     * Delete a task
     * DELETE /api/planner/tasks/{task}
     */
    public function destroy(Request $request, $id)
    {
        try {
            $task = Task::where('user_id', $request->user()->id)->findOrFail($id);
            TaskAssignment::where('task_id', $task->id)->delete();
            $task->delete();

            return response()->json([
                'success' => true,
                'message' => 'Task deleted',
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete task',
            ], 500);
        }
    }
}
