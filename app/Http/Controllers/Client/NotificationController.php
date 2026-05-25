<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\Notification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
    public function index()
    {
        $notifications = Notification::where('user_id', Auth::id())
            ->where('is_archived', false)
            ->orderBy('created_at', 'desc')
            ->limit(50)
            ->get();

        return response()->json([
            'notifications' => $notifications,
            'unread_count' => $notifications->where('is_read', false)->count(),
        ]);
    }

    public function markAsRead($id)
    {
        $notification = Notification::where('user_id', Auth::id())->findOrFail($id);
        $notification->update([
            'is_read' => true,
            'read_at' => now(),
        ]);

        return response()->json(['success' => true]);
    }

    public function archive($id)
    {
        $notification = Notification::where('user_id', Auth::id())->findOrFail($id);
        $notification->update(['is_archived' => true]);

        return response()->json(['success' => true]);
    }

    public function markAllAsRead()
    {
        Notification::where('user_id', Auth::id())
            ->where('is_read', false)
            ->update([
                'is_read' => true,
                'read_at' => now(),
            ]);

        return response()->json(['success' => true]);
    }

    public function archiveAll()
    {
        Notification::where('user_id', Auth::id())
            ->where('is_archived', false)
            ->update(['is_archived' => true]);

        return response()->json(['success' => true]);
    }

    public function stats()
    {
        $userId = Auth::id();
        
        $stats = [
            'total_today' => Notification::where('user_id', $userId)
                ->whereDate('created_at', today())
                ->count(),
            'unread' => Notification::where('user_id', $userId)
                ->where('is_read', false)
                ->count(),
            'urgent' => Notification::where('user_id', $userId)
                ->where('is_read', false)
                ->whereIn('priority', ['high', 'urgent'])
                ->count(),
        ];

        return response()->json($stats);
    }
}