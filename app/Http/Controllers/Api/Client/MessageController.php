<?php

namespace App\Http\Controllers\Api\Client;

use App\Http\Controllers\Controller;
use App\Models\Event;
use App\Models\Message;
use App\Models\Notification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MessageController extends Controller
{
    public function index($eventId)
    {
        $event = Event::where('client_id', Auth::id())->findOrFail($eventId);
        
        $messages = Message::where('event_id', $eventId)
            ->with('sender')
            ->orderBy('created_at', 'asc')
            ->get();

        return response()->json([
            'success' => true,
            'messages' => $messages->map(function($message) {
                return [
                    'id' => $message->id,
                    'message' => $message->message,
                    'sender_id' => $message->sender_id,
                    'sender_name' => $message->sender->name,
                    'created_at' => $message->created_at,
                ];
            })
        ]);
    }

    public function store(Request $request, $eventId)
    {
        $event = Event::where('client_id', Auth::id())->findOrFail($eventId);

        $request->validate([
            'message' => 'required|string|max:1000',
        ]);

        $message = Message::create([
            'event_id' => $eventId,
            'sender_id' => Auth::id(),
            'receiver_id' => $event->planner_id,
            'message' => $request->message,
        ]);

        // Notify planner
        if ($event->planner_id) {
            Notification::create([
                'user_id' => $event->planner_id,
                'type' => 'message',
                'priority' => 'medium',
                'title' => 'New Message from Client',
                'message' => Auth::user()->name . ' sent you a message about ' . $event->name,
                'icon' => 'fas fa-envelope',
                'action_url' => '/planner/messages',
            ]);
        }

        return response()->json([
            'success' => true,
            'message' => $message
        ]);
    }

    public function destroy($messageId)
    {
        $message = Message::whereHas('event', function($query) {
            $query->where('client_id', Auth::id());
        })->findOrFail($messageId);

        $message->delete();

        return response()->json([
            'success' => true
        ]);
    }
}