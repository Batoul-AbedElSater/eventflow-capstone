<?php

namespace App\Http\Controllers\Assistant;

use App\Http\Controllers\Controller;
use App\Models\Task;
use App\Models\TaskAssignment;
use Illuminate\Http\Request;

class AssistantController extends Controller
{
    public function dashboard(Request $request)
    {
        return redirect()->route('assistant.tasks');
    }

    public function tasks(Request $request)
    {
        $user   = $request->user();
        $filter = $request->get('filter', 'all');

        $query = Task::whereHas('assignments', function ($q) use ($user) {
                        $q->where('assistant_id', $user->id);
                    })
                    ->with([
                        'event',
                        'assignments' => fn($q) => $q->where('assistant_id', $user->id)
                                                      ->with('planner'),
                    ])
                    ->orderByRaw("FIELD(priority, 'urgent', 'high', 'medium', 'low')")
                    ->orderBy('due_date', 'asc');

        // Filters — status values: pending, in_progress, done
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

        // Stats
        $base = Task::whereHas('assignments', fn($q) => $q->where('assistant_id', $user->id));

        $totalTasks        = (clone $base)->count();
        $urgentTasks       = (clone $base)->where('priority', 'urgent')->where('status', '!=', 'done')->count();
        $inProgressTasks   = (clone $base)->where('status', 'in_progress')->count();
        $completedTasks    = (clone $base)->where('status', 'done')->count();
        $pendingTasksCount = (clone $base)->whereIn('status', ['pending', 'in_progress'])->count();

        return view('assistant.tasks', compact(
            'user', 'tasks', 'filter',
            'totalTasks', 'urgentTasks', 'inProgressTasks', 'completedTasks',
            'pendingTasksCount'
        ));
    }

    public function completeTask(Request $request, Task $task)
    {
        $isAssigned = TaskAssignment::where('task_id', $task->id)
                                    ->where('assistant_id', $request->user()->id)
                                    ->exists();

        abort_if(!$isAssigned, 403, 'You are not assigned to this task.');

        $task->update([
            'status'       => 'done',
            'progress'     => 100,
            'completed_at' => now(),
        ]);

        return redirect()->back()->with('success', "Task \"{$task->title}\" marked as complete!");
    }

    public function taskVendors($taskId)
{
    $task = Task::with('vendors')->findOrFail($taskId);
    
    $isAssigned = TaskAssignment::where('task_id', $taskId)
        ->where('assistant_id', auth()->id())
        ->exists();
    
    abort_if(!$isAssigned, 403);
    
    return view('assistant.task-vendors', compact('task'));
}

public function vendorShow($vendorId)
{
    $vendor = \App\Models\Vendor::findOrFail($vendorId);
    return view('assistant.vendor-details', compact('vendor'));
}
public function orderForm($taskId, $vendorId)
{
    $task = Task::findOrFail($taskId);
    $vendor = \App\Models\Vendor::findOrFail($vendorId);
    
    // Verify assistant is assigned to this task
    $isAssigned = TaskAssignment::where('task_id', $taskId)
        ->where('assistant_id', auth()->id())
        ->exists();
    
    abort_if(!$isAssigned, 403);
    
    // Check if order already exists
    $existingOrder = \App\Models\VendorOrder::where('task_id', $taskId)
        ->where('vendor_id', $vendorId)
        ->where('assistant_id', auth()->id())
        ->first();
    
    return view('assistant.place-order', compact('task', 'vendor', 'existingOrder'));
}
public function submitOrder(Request $request, $taskId, $vendorId)
{
    $validated = $request->validate([
        'price' => 'required|numeric|min:0',
        'notes' => 'nullable|string|max:500',
    ]);
    
    $task = Task::findOrFail($taskId);
    $vendor = \App\Models\Vendor::findOrFail($vendorId);
    
    // Verify assistant is assigned
    $isAssigned = TaskAssignment::where('task_id', $taskId)
        ->where('assistant_id', auth()->id())
        ->exists();
    
    abort_if(!$isAssigned, 403);
    
    // Update or create order
    \App\Models\VendorOrder::updateOrCreate(
        [
            'task_id' => $taskId,
            'vendor_id' => $vendorId,
            'assistant_id' => auth()->id(),
        ],
        [
            'price' => $validated['price'],
            'notes' => $validated['notes'],
        ]
    );
    
    return redirect()->route('assistant.tasks.vendors', $taskId)
        ->with('success', 'Order placed successfully!');
}

public function myOrders()
{
    $orders = \App\Models\VendorOrder::where('assistant_id', auth()->id())
        ->with(['task.event', 'vendor'])
        ->orderBy('created_at', 'desc')
        ->get();
    
    return view('assistant.my-orders', compact('orders'));
}

public function deleteOrder($orderId)
{
    $order = \App\Models\VendorOrder::where('id', $orderId)
        ->where('assistant_id', auth()->id())
        ->firstOrFail();
    
    $order->delete();
    
    return redirect()->back()->with('success', 'Order deleted successfully!');
}
}