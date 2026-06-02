<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\Message;
use App\Models\Event;
use App\Models\Notification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class MessageController extends Controller
{
    public function showPage()
    {
        $events = Event::where('client_id', Auth::id())
            ->with(['planner:id,name,email'])
            ->orderBy('start_date', 'desc')
            ->get();

        return view('client.messages', compact('events'));
    }

public function index($eventId)
{
    try {
        $event = Event::where('client_id', Auth::id())->findOrFail($eventId);
        $userId = Auth::id();

        $messages = Message::where('event_id', $eventId)
            ->where(function ($query) use ($userId) {
                // Messages where client is the sender → only show if not deleted_by_sender
                $query->where(function ($q) use ($userId) {
                    $q->where('sender_id', $userId)->where('deleted_by_sender', false);
                })->orWhere(function ($q) use ($userId) {
                    // Messages where client is the receiver → only show if not deleted_by_receiver
                    $q->where('receiver_id', $userId)->where('deleted_by_receiver', false);
                });
            })
            ->with(['sender:id,name'])
            ->orderBy('created_at', 'asc')
            ->get();

        return response()->json([
            'success' => true,
            'messages' => $messages->map(function($msg) {
                return [
                    'id' => $msg->id,
                    'sender_id' => $msg->sender_id,
                    'sender_name' => $msg->sender->name ?? 'Unknown',
                    'message' => $msg->message ?? $msg->body,
                    'created_at' => $msg->created_at->format('M d, Y h:i A'),
                    'is_mine' => $msg->sender_id === Auth::id(),
                ];
            })
        ]);

    } catch (\Exception $e) {
        Log::error('Client messages index error: ' . $e->getMessage());
        return response()->json(['success' => false], 500);
    }
}

    public function store(Request $request, $eventId)
        {
            try {
                $validated = $request->validate([
                    'message' => 'required|string|max:5000',
                ]);

                $event = Event::where('client_id', Auth::id())->findOrFail($eventId);

                // 🔥 CRITICAL: Check if a planner is assigned
                if (!$event->planner_id) {
                    return response()->json([
                        'success' => false,
                        'message' => 'No planner assigned yet. You can send messages after your request is accepted.'
                    ], 400);
                }

                $message = Message::create([
                    'event_id'    => $eventId,
                    'sender_id'   => Auth::id(),
                    'receiver_id' => $event->planner_id,
                    'body'        => $validated['message'],   // keep both if you want
                    'message'     => $validated['message'],
                    'is_read'     => false,
                    'sent_at'     => now(),
                ]);

                // Create notification for planner
                Notification::create([
                    'user_id'     => $event->planner_id,
                    'type'        => 'message',
                    'priority'    => 'medium',
                    'title'       => 'New Message from ' . Auth::user()->name,
                    'message'     => "Regarding your event: {$event->name}",
                    'icon'        => 'fas fa-envelope',
                    'action_url'  => "/planner/events/{$eventId}?tab=messages",
                    'is_read'     => false,
                    'is_archived' => false,
                ]);

                return response()->json([
                    'success' => true,
                    'message' => [
                        'id'          => $message->id,
                        'sender_id'   => $message->sender_id,
                        'sender_name' => Auth::user()->name,
                        'message'     => $message->message,
                        'created_at'  => $message->created_at->format('M d, Y h:i A'),
                        'is_mine'     => true,
                    ]
                ]);

            } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
                return response()->json(['success' => false, 'message' => 'Event not found'], 404);
            } catch (\Exception $e) {
                Log::error('Client message store error: ' . $e->getMessage(), [
                    'event_id' => $eventId,
                    'trace'    => $e->getTraceAsString()
                ]);
                return response()->json(['success' => false, 'message' => 'Server error: ' . $e->getMessage()], 500);
            }
        }

    public function destroy($eventId, $messageId)
    {
        try {
            $message = Message::where('event_id', $eventId)
                ->where('sender_id', Auth::id())
                ->findOrFail($messageId);

            $message->delete();

            return response()->json(['success' => true]);

        } catch (\Exception $e) {
            Log::error('Delete message error: ' . $e->getMessage());
            return response()->json(['success' => false], 500);
        }
    }

   public function deleteAll($eventId)
{
    try {
        $userId = Auth::id();
        $event = Event::where('client_id', $userId)->findOrFail($eventId);

        // Determine if client is the sender or receiver in each message
        // For simplicity: client is always the "sender" in messages they sent,
        // and "receiver" for messages the planner sent.
        // We'll mark both cases:

        // Messages sent by this client → mark deleted_by_sender = true
        Message::where('event_id', $eventId)
            ->where('sender_id', $userId)
            ->update(['deleted_by_sender' => true]);

        // Messages received by this client (planner sent) → mark deleted_by_receiver = true
        Message::where('event_id', $eventId)
            ->where('receiver_id', $userId)
            ->update(['deleted_by_receiver' => true]);

        return response()->json(['success' => true, 'message' => 'Chat cleared for you']);
    } catch (\Exception $e) {
        Log::error('Delete all messages error: ' . $e->getMessage());
        return response()->json(['success' => false, 'message' => 'Failed to clear chat'], 500);
    }
}
}
