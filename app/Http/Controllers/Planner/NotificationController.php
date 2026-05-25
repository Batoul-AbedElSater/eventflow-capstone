<?php

namespace App\Http\Controllers\Planner;

use App\Http\Controllers\Controller;
use App\Models\Notification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
    // Get all active notifications (for river)
    public function index()
    {
        $notifications = Notification::where('user_id', Auth::id())
            ->active()
            ->orderBy('created_at', 'desc')
            ->limit(50)
            ->get();

        return response()->json([
            'notifications' => $notifications,
            'unread_count' => $notifications->where('is_read', false)->count(),
        ]);
    }

    // Mark as read
    public function markAsRead($id)
    {
        $notification = Notification::where('user_id', Auth::id())->findOrFail($id);
        $notification->markAsRead();

        return response()->json(['success' => true]);
    }

    // Archive notification
    public function archive($id)
    {
        $notification = Notification::where('user_id', Auth::id())->findOrFail($id);
        $notification->archive();

        return response()->json(['success' => true]);
    }

    // Mark all as read
    public function markAllAsRead()
    {
        Notification::where('user_id', Auth::id())
            ->unread()
            ->update([
                'is_read' => true,
                'read_at' => now(),
            ]);

        return response()->json(['success' => true]);
    }

    // Clear all archived
    public function clearArchived()
    {
        Notification::where('user_id', Auth::id())
            ->where('is_archived', true)
            ->delete();

        return response()->json(['success' => true]);
    }

    // Get notification stats
    public function stats()
    {
        $userId = Auth::id();
        
        $stats = [
            'total_today' => Notification::where('user_id', $userId)
                ->whereDate('created_at', today())
                ->count(),
            'unread' => Notification::where('user_id', $userId)
                ->unread()
                ->count(),
            'urgent' => Notification::where('user_id', $userId)
                ->unread()
                ->priority('urgent')
                ->count(),
        ];

        return response()->json($stats);
    }
}