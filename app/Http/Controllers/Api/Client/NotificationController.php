<?php

namespace App\Http\Controllers\Api\Client;

use App\Http\Controllers\Controller;
use App\Models\Notification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
    /**
     * Get all notifications for the authenticated client
     * GET /api/client/notifications
     */
    public function index(Request $request)
    {
        $perPage = $request->input('per_page', 20);
        
        $notifications = Notification::where('user_id', $request->user()->id)
            ->where('is_archived', false)
            ->orderBy('created_at', 'desc')
            ->paginate($perPage);

        $unreadCount = Notification::where('user_id', $request->user()->id)
            ->where('is_read', false)
            ->where('is_archived', false)
            ->count();

        return response()->json([
            'success' => true,
            'data' => [
                'notifications' => $notifications->map(function ($notification) {
                    return [
                        'id' => $notification->id,
                        'type' => $notification->type,
                        'priority' => $notification->priority,
                        'title' => $notification->title,
                        'message' => $notification->message,
                        'icon' => $notification->icon,
                        'action_url' => $notification->action_url,
                        'is_read' => (bool) $notification->is_read,
                        'is_archived' => (bool) $notification->is_archived,
                        'created_at' => $notification->created_at->format('M d, Y h:i A'),
                        'timestamp' => $notification->created_at->toISOString(),
                        'time_ago' => $notification->created_at->diffForHumans(),
                    ];
                }),
                'unread_count' => $unreadCount,
                'pagination' => [
                    'current_page' => $notifications->currentPage(),
                    'per_page' => $notifications->perPage(),
                    'total' => $notifications->total(),
                    'last_page' => $notifications->lastPage(),
                ],
            ],
        ]);
    }

    /**
     * Mark a notification as read
     * POST /api/client/notifications/{id}/read
     */
    public function markAsRead(Request $request, $id)
    {
        $notification = Notification::where('user_id', $request->user()->id)
            ->findOrFail($id);
        
        $notification->update([
            'is_read' => true,
            'read_at' => now(),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Notification marked as read',
        ]);
    }

    /**
     * Archive a notification
     * POST /api/client/notifications/{id}/archive
     */
    public function archive(Request $request, $id)
    {
        $notification = Notification::where('user_id', $request->user()->id)
            ->findOrFail($id);
        
        $notification->update(['is_archived' => true]);

        return response()->json([
            'success' => true,
            'message' => 'Notification archived',
        ]);
    }

    /**
     * Mark all notifications as read
     * POST /api/client/notifications/read-all
     */
    public function markAllAsRead(Request $request)
    {
        Notification::where('user_id', $request->user()->id)
            ->where('is_read', false)
            ->update([
                'is_read' => true,
                'read_at' => now(),
            ]);

        return response()->json([
            'success' => true,
            'message' => 'All notifications marked as read',
        ]);
    }

    /**
     * Archive all notifications
     * POST /api/client/notifications/archive-all
     */
    public function archiveAll(Request $request)
    {
        Notification::where('user_id', $request->user()->id)
            ->where('is_archived', false)
            ->update(['is_archived' => true]);

        return response()->json([
            'success' => true,
            'message' => 'All notifications archived',
        ]);
    }

    /**
     * Get notification statistics
     * GET /api/client/notifications/stats
     */
    public function stats(Request $request)
    {
        $userId = $request->user()->id;
        
        return response()->json([
            'success' => true,
            'data' => [
                'total_today' => Notification::where('user_id', $userId)
                    ->whereDate('created_at', today())
                    ->count(),
                'unread' => Notification::where('user_id', $userId)
                    ->where('is_read', false)
                    ->where('is_archived', false)
                    ->count(),
                'urgent' => Notification::where('user_id', $userId)
                    ->where('is_read', false)
                    ->whereIn('priority', ['high', 'urgent'])
                    ->where('is_archived', false)
                    ->count(),
                'total' => Notification::where('user_id', $userId)
                    ->where('is_archived', false)
                    ->count(),
            ],
        ]);
    }

    /**
     * Get only unread notifications (for badge count)
     * GET /api/client/notifications/unread
     */
    public function unreadCount(Request $request)
    {
        $count = Notification::where('user_id', $request->user()->id)
            ->where('is_read', false)
            ->where('is_archived', false)
            ->count();

        return response()->json([
            'success' => true,
            'data' => [
                'unread_count' => $count,
            ],
        ]);
    }
}