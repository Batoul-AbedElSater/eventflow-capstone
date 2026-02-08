<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\Event;
use App\Models\MessageThread;
use App\Models\Message;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MessageController extends Controller
{
    /**
     * Show all message threads for client
     */
    public function index()
    {
        $threads = MessageThread::where('client_id', Auth::id())
            ->with(['event', 'planner', 'messages' => function($query) {
                $query->latest()->limit(1);
            }])
            ->withCount(['messages as unread_count' => function($query) {
                $query->where('sender_id', '!=', Auth::id())
                      ->where('is_read', false);
            }])
            ->orderBy('updated_at', 'desc')
            ->get();
        
        return view('client.messages.index', compact('threads'));
    }

    /**
     * Show specific conversation thread
     */
    public function show($threadId)
    {
        $thread = MessageThread::with(['event', 'planner', 'messages.sender'])
            ->findOrFail($threadId);
        
        // Verify ownership
        if ($thread->client_id !== Auth::id()) {
            abort(403, 'Unauthorized access');
        }
        
        // Mark all messages as read
        $thread->messages()
            ->where('sender_id', '!=', Auth::id())
            ->where('is_read', false)
            ->update(['is_read' => true]);
        
        return view('client.messages.show', compact('thread'));
    }

    /**
     * Send a message
     */
    public function store(Request $request, $threadId)
    {
        $thread = MessageThread::findOrFail($threadId);
        
        // Verify ownership
        if ($thread->client_id !== Auth::id()) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized'
            ], 403);
        }
        
        $validated = $request->validate([
            'message' => 'required|string|max:5000',
        ]);
        
        $message = Message::create([
            'thread_id' => $threadId,
            'sender_id' => Auth::id(),
            'body' => $validated['message'],
            'is_read' => false,
        ]);
        
        // Update thread timestamp
        $thread->touch();
        
        return response()->json([
            'success' => true,
            'message' => $message->load('sender'),
        ]);
    }

    /**
     * Create new thread for an event
     */
    public function createThread($eventId)
    {
        $event = Event::findOrFail($eventId);
        
        // Verify ownership
        if ($event->client_id !== Auth::id()) {
            abort(403, 'Unauthorized access');
        }
        
        // Check if planner is assigned
        if (!$event->planner_id) {
            return redirect()->route('client.events.show', $eventId)
                ->with('error', 'No planner assigned yet. Please assign a planner first.');
        }
        
        // Check if thread already exists
        $existingThread = MessageThread::where('event_id', $eventId)
            ->where('client_id', Auth::id())
            ->first();
        
        if ($existingThread) {
            return redirect()->route('client.messages.show', $existingThread->id);
        }
        
        // Create new thread
        $thread = MessageThread::create([
            'event_id' => $eventId,
            'client_id' => Auth::id(),
            'planner_id' => $event->planner_id,
        ]);
        
        return redirect()->route('client.messages.show', $thread->id);
    }
}
