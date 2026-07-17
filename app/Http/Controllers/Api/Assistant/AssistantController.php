<?php

namespace App\Http\Controllers\Api\Assistant;

use App\Http\Controllers\Controller;
use App\Models\Task;
use App\Models\TaskAssignment;
use App\Models\VendorOrder;
use App\Models\Notification;
use App\Models\Vendor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;  

class AssistantController extends Controller
{
    /**
     * Get all tasks assigned to this assistant
     */
    public function tasks(Request $request)
    {
        $user = $request->user();
        $filter = $request->get('filter', 'all');

        $query = Task::whereHas('assignments', function ($q) use ($user) {
                $q->where('assistant_id', $user->id);
            })
            ->with(['event:id,name', 'vendors:id,name', 'assignments' => fn($q) => $q->where('assistant_id', $user->id)->with('planner:id,name')])
            ->orderByRaw("FIELD(priority, 'urgent', 'high', 'medium', 'low')")
            ->orderBy('due_date', 'asc');

        if ($filter === 'urgent') {
            $query->where('priority', 'urgent')->where('status', '!=', 'done');
        } elseif ($filter === 'done') {
            $query->where('status', 'done');
        } elseif ($filter === 'in_progress') {
            $query->where('status', 'in_progress');
        } elseif ($filter === 'pending') {
            $query->where('status', 'pending');
        }

        $tasks = $query->get();

        $base = Task::whereHas('assignments', fn($q) => $q->where('assistant_id', $user->id));

        $stats = [
            'total' => (clone $base)->count(),
            'urgent' => (clone $base)->where('priority', 'urgent')->where('status', '!=', 'done')->count(),
            'in_progress' => (clone $base)->where('status', 'in_progress')->count(),
            'completed' => (clone $base)->where('status', 'done')->count(),
            'pending' => (clone $base)->whereIn('status', ['pending', 'in_progress'])->count(),
        ];

        return response()->json([
            'success' => true,
            'data' => [
                'tasks' => $tasks,
                'stats' => $stats,
            ]
        ]);
    }

    /**
     * Mark a task as completed
     */
  public function completeTask(Request $request, $taskId)
{
    $user = $request->user();

    $isAssigned = TaskAssignment::where('task_id', $taskId)
        ->where('assistant_id', $user->id)
        ->exists();

    if (!$isAssigned) {
        return response()->json(['success' => false, 'message' => 'Not assigned to this task'], 403);
    }

    $task = Task::with('event')->findOrFail($taskId);
    $task->update([
        'status' => 'done',
        'progress' => 100,
        'completed_at' => now(),
    ]);

    // Notify planner
    if ($task->event && $task->event->planner_id) {
        try {
            Notification::create([
                'user_id' => $task->event->planner_id,
                'type' => 'task',
                'priority' => 'medium',
                'title' => 'Task Completed',
                'message' => $user->name . ' marked "' . $task->title . '" as done for ' . $task->event->name,
                'icon' => 'fas fa-check-circle',
                'action_url' => '/planner/events/' . $task->event->id . '/tasks',
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to create task-completed notification: ' . $e->getMessage());
        }
    }

    return response()->json([
        'success' => true,
        'message' => 'Task marked as complete!',
        'data' => $task
    ]);
}

    /**
     * Get vendors for a specific task
     */
    public function taskVendors($taskId, Request $request)
    {
        $task = Task::with('vendors')->findOrFail($taskId);

        $isAssigned = TaskAssignment::where('task_id', $taskId)
            ->where('assistant_id', $request->user()->id)
            ->exists();

        if (!$isAssigned) {
            return response()->json(['success' => false, 'message' => 'Not assigned'], 403);
        }

        return response()->json([
            'success' => true,
            'data' => [
                'task' => $task,
                'vendors' => $task->vendors
            ]
        ]);
    }

    /**
     * Get vendor details
     */
    public function vendorShow($vendorId)
    {
        $vendor = Vendor::findOrFail($vendorId);

        return response()->json([
            'success' => true,
            'data' => $vendor
        ]);
    }

    /**
     * Place or update an order
     */
    public function submitOrder(Request $request, $taskId, $vendorId)
{
    $validated = $request->validate([
        'price' => 'required|numeric|min:0',
        'notes' => 'nullable|string|max:500',
    ]);

    $task = Task::with('event')->findOrFail($taskId);
    $vendor = Vendor::findOrFail($vendorId);

    $isAssigned = TaskAssignment::where('task_id', $taskId)
        ->where('assistant_id', $request->user()->id)
        ->exists();

    if (!$isAssigned) {
        return response()->json(['success' => false, 'message' => 'Not assigned'], 403);
    }

    $isNewOrder = !VendorOrder::where('task_id', $taskId)
        ->where('vendor_id', $vendorId)
        ->where('assistant_id', $request->user()->id)
        ->exists();

    $order = VendorOrder::updateOrCreate(
        [
            'task_id' => $taskId,
            'vendor_id' => $vendorId,
            'assistant_id' => $request->user()->id,
        ],
        [
            'price' => $validated['price'],
            'notes' => $validated['notes'],
        ]
    );

    // Notify planner
    if ($task->event && $task->event->planner_id) {
        try {
            Notification::create([
                'user_id' => $task->event->planner_id,
                'type' => 'order',
                'priority' => 'medium',
                'title' => 'New Order Placed',
                'message' => $request->user()->name . ' placed an order with ' . $vendor->name . ' for ' . $task->event->name,
                'icon' => 'fas fa-shopping-cart',
                'action_url' => '/planner/events/' . $task->event->id . '/vendors/' . $vendor->id,
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to create order notification: ' . $e->getMessage());
        }
    }

    return response()->json([
        'success' => true,
        'message' => $isNewOrder ? 'Order placed successfully!' : 'Order updated successfully!',
        'data' => $order
    ]);
}

    /**
     * Get all orders for this assistant
     */
    public function myOrders(Request $request)
    {
        $orders = VendorOrder::where('assistant_id', $request->user()->id)
            ->with(['task.event', 'vendor'])
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $orders
        ]);
    }

    /**
     * Dashboard summary
     */
    public function dashboard(Request $request)
    {
        $user = $request->user();
        $base = Task::whereHas('assignments', fn($q) => $q->where('assistant_id', $user->id));

        return response()->json([
            'success' => true,
            'data' => [
                'pending_count' => (clone $base)->whereIn('status', ['pending', 'in_progress'])->count(),
                'completed_today' => (clone $base)->where('status', 'done')->whereDate('updated_at', today())->count(),
                'urgent_count' => (clone $base)->where('priority', 'urgent')->where('status', '!=', 'done')->count(),
            ]
        ]);
    }
    public function deleteOrder(Request $request, $orderId)
{
    $order = \App\Models\VendorOrder::where('id', $orderId)
        ->where('assistant_id', $request->user()->id)
        ->firstOrFail();
    
    $order->delete();
    
    return response()->json([
        'success' => true,
        'message' => 'Order deleted successfully!'
    ]);
}
}