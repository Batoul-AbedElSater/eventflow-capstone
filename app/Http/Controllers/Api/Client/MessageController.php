<?php

namespace App\Http\Controllers\Api\Client;

use App\Http\Controllers\Controller;
use App\Models\Message;
use App\Models\Event;
use App\Models\Notification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class MessageController extends Controller
{
    /**
     * List all messages for an event
     * GET /api/client/events/{event}/messages
     */
    public function index(Request $request, $eventId)
    {
        try {
            $event = Event::where('client_id', $request->user()->id)
                ->findOrFail($eventId);
            
            $userId = $request->user()->id;

            $messages = Message::where('event_id', $eventId)
                ->where(function ($query) use ($userId) {
                    $query->where(function ($q) use ($userId) {
                        $q->where('sender_id', $userId)
                          ->where('deleted_by_sender', false);
                    })->orWhere(function ($q) use ($userId) {
                        $q->where('receiver_id', $userId)
                          ->where('deleted_by_receiver', false);
                    });
                })
                ->with(['sender:id,name'])
                ->orderBy('created_at', 'asc')
                ->get();

            return response()->json([
                'success' => true,
                'data' => [
                    'event' => [
                        'id' => $event->id,
                        'name' => $event->name,
                        'planner' => $event->planner ? [
                            'id' => $event->planner->id,
                            'name' => $event->planner->name,
                        ] : null,
                    ],
                    'messages' => $messages->map(function($msg) use ($userId) {
                        return [
                            'id' => $msg->id,
                            'sender_id' => $msg->sender_id,
                            'sender_name' => $msg->sender->name ?? 'Unknown',
                            'message' => $msg->message ?? $msg->body,
                            'created_at' => $msg->created_at->format('M d, Y h:i A'),
                            'timestamp' => $msg->created_at->toISOString(),
                            'is_mine' => $msg->sender_id === $userId,
                        ];
                    }),
                ],
            ]);

        } catch (\Exception $e) {
            Log::error('API Client messages index error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to load messages',
            ], 500);
        }
    }

    /**
     * Send a new message
     * POST /api/client/events/{event}/messages
     */
    public function store(Request $request, $eventId)
    {
        try {
            $validated = $request->validate([
                'message' => 'required|string|max:5000',
            ]);

            $event = Event::where('client_id', $request->user()->id)
                ->findOrFail($eventId);

            if (!$event->planner_id) {
                return response()->json([
                    'success' => false,
                    'message' => 'No planner assigned yet.',
                ], 400);
            }

            $message = Message::create([
                'event_id' => $eventId,
                'sender_id' => $request->user()->id,
                'receiver_id' => $event->planner_id,
                'body' => $validated['message'],
                'message' => $validated['message'],
                'is_read' => false,
                'sent_at' => now(),
            ]);

            // Create notification for planner
            try {
                Notification::create([
                    'user_id' => $event->planner_id,
                    'type' => 'message',
                    'priority' => 'medium',
                    'title' => 'New Message from ' . $request->user()->name,
                    'message' => "Regarding your event: {$event->name}",
                    'icon' => 'fas fa-envelope',
                    'action_url' => "/planner/events/{$eventId}?tab=messages",
                    'is_read' => false,
                    'is_archived' => false,
                ]);
            } catch (\Exception $e) {
                Log::warning('Notification creation failed: ' . $e->getMessage());
            }

            return response()->json([
                'success' => true,
                'message' => 'Message sent!',
                'data' => [
                    'id' => $message->id,
                    'sender_id' => $message->sender_id,
                    'sender_name' => $request->user()->name,
                    'message' => $message->message,
                    'created_at' => $message->created_at->format('M d, Y h:i A'),
                    'timestamp' => $message->created_at->toISOString(),
                    'is_mine' => true,
                ],
            ], 201);

        } catch (\Exception $e) {
            Log::error('API Client message store error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to send message',
            ], 500);
        }
    }

    /**
     * Delete a specific message
     * DELETE /api/client/messages/{message}
     */
    public function destroy(Request $request, $messageId)
    {
        try {
            $message = Message::where('sender_id', $request->user()->id)
                ->findOrFail($messageId);

            $message->update(['deleted_by_sender' => true]);

            return response()->json([
                'success' => true,
                'message' => 'Message deleted',
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete message',
            ], 500);
        }
    }

    /**
     * Clear all messages for an event
     * DELETE /api/client/events/{event}/messages
     */
    public function deleteAll(Request $request, $eventId)
    {
        try {
            $userId = $request->user()->id;
            
            Message::where('event_id', $eventId)
                ->where('sender_id', $userId)
                ->update(['deleted_by_sender' => true]);

            Message::where('event_id', $eventId)
                ->where('receiver_id', $userId)
                ->update(['deleted_by_receiver' => true]);

            return response()->json([
                'success' => true,
                'message' => 'Chat cleared',
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to clear chat',
            ], 500);
        }
    }

    /**
     * Get events with latest message preview
     * GET /api/client/messages/events
     */public function eventsWithMessages(Request $request)
{
    try {
        $userId = $request->user()->id;

        $events = Event::where('client_id', $userId)
            ->whereHas('planner')
            ->with(['planner:id,name,email'])
            ->withCount(['messages as unread_count' => function ($query) use ($userId) {
                $query->where('receiver_id', $userId)
                    ->where('is_read', false)
                    ->where('deleted_by_receiver', false);
            }])
            ->orderBy('start_date', 'desc')
            ->get()
            ->map(function ($event) use ($userId) {
                $lastMessage = Message::where('event_id', $event->id)
                    ->where(function ($query) use ($userId) {
                        $query->where(function ($q) use ($userId) {
                            $q->where('sender_id', $userId)
                                ->where('deleted_by_sender', false);
                        })->orWhere(function ($q) use ($userId) {
                            $q->where('receiver_id', $userId)
                                ->where('deleted_by_receiver', false);
                        });
                    })
                    ->orderBy('created_at', 'desc')
                    ->first();

                return [
                    'id' => $event->id,
                    'name' => $event->name,
                    'planner' => [
                        'id' => $event->planner->id,
                        'name' => $event->planner->name,
                    ],
                    'unread_count' => $event->unread_count ?? 0,
                    'last_message' => $lastMessage ? [
                        'message' => \Str::limit($lastMessage->message ?? $lastMessage->body, 50),
                        'created_at' => $lastMessage->created_at->diffForHumans(),
                    ] : null,
                    '_sort_time' => $lastMessage ? $lastMessage->created_at->timestamp : 0,
                ];
            })
            ->sortByDesc('_sort_time')
            ->values()
            ->map(function ($item) {
                unset($item['_sort_time']);
                return $item;
            });

        return response()->json([
            'success' => true,
            'data' => $events,
        ]);

    } catch (\Exception $e) {
        Log::error('API Events with messages error: ' . $e->getMessage());

        return response()->json([
            'success' => false,
            'message' => 'Failed to load events',
        ], 500);
    }
}



    public function markEventMessagesAsRead(Request $request, Event $event)
{
    $user = $request->user();

    if ($event->client_id !== $user->id) {
        return response()->json([
            'success' => false,
            'message' => 'Unauthorized',
        ], 403);
    }

    Message::where('event_id', $event->id)
        ->where('receiver_id', $user->id)
        ->where('is_read', false)
        ->update([
            'is_read' => true,
        ]);

    return response()->json([
        'success' => true,
    ]);
}
}