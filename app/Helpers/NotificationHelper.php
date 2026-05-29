<?php

namespace App\Helpers;

use App\Models\Notification;

class NotificationHelper
{
    public static function send($userId, $type, $priority, $title, $message, $icon = null, $actionUrl = null)
    {
        return Notification::create([
            'user_id' => $userId,
            'type' => $type,
            'priority' => $priority,
            'title' => $title,
            'message' => $message,
            'icon' => $icon,
            'action_url' => $actionUrl,
        ]);
    }

    // Quick methods for common notifications
    public static function taskCreated($userId, $taskTitle, $eventName, $taskId)
    {
        return self::send(
            $userId,
            'task',
            'medium',
            'New Task Created',
            "Task '{$taskTitle}' for {$eventName}",
            'fas fa-tasks',
            "/planner/tasks/{$taskId}"
        );
    }

    public static function eventRequest($userId, $eventName, $clientName, $eventId)
    {
        return self::send(
            $userId,
            'request',
            'high',
            'New Event Request',
            "{$clientName} requested: {$eventName}",
            'fas fa-inbox',
            "/planner/dashboard#requests"
        );
    }

    public static function weatherAlert($userId, $eventName, $rainChance)
    {
        return self::send(
            $userId,
            'weather',
            $rainChance > 30 ? 'urgent' : 'medium',
            'Weather Alert',
            "{$eventName}: {$rainChance}% rain chance",
            'fas fa-cloud-rain',
            "/planner/dashboard"
        );
    }

    public static function conflictDetected($userId, $event1Name, $event2Name)
    {
        return self::send(
            $userId,
            'conflict',
            'urgent',
            'Scheduling Conflict!',
            "Conflict between {$event1Name} and {$event2Name}",
            'fas fa-exclamation-triangle',
            "/planner/dashboard"
        );
    }

    public static function eventHealthWarning($userId, $eventName, $healthScore)
    {
        return self::send(
            $userId,
            'health',
            $healthScore < 50 ? 'urgent' : 'high',
            'Event Health Warning',
            "{$eventName} health at {$healthScore}%",
            'fas fa-heartbeat',
            "/planner/dashboard"
        );
    }

    public static function newMessage($userId, $clientName, $eventName, $messageId)
    {
        return self::send(
            $userId,
            'message',
            'medium',
            'New Message',
            "{$clientName} sent a message about {$eventName}",
            'fas fa-envelope',
            "/planner/messages/{$messageId}"
        );
    }
}
