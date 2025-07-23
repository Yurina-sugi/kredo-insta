<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Notification;
use App\Models\User;

class NotificationController extends Controller
{
    /**
     * Display notification list
     */
    public function index()
    {
        $notifications = Auth::user()->notifications()
            ->with(['sender', 'notifiable'])
            ->paginate(20);

        return view('users.notifications.index', compact('notifications'));
    }

    /**
     * Mark notification as read
     */
    public function markAsRead($id)
    {
        $notification = Notification::findOrFail($id);

        // Check if user owns the notification
        if ($notification->recipient_id !== Auth::id()) {
            abort(403);
        }

        $notification->markAsRead();

        return response()->json([
            'success' => true,
            'unread_count' => Auth::user()->unreadNotificationsCount()
        ]);
    }

    /**
     * Mark all notifications as read
     */
    public function markAllAsRead()
    {
        Auth::user()->markAllNotificationsAsRead();

        return response()->json([
            'success' => true,
            'unread_count' => 0
        ]);
    }

    /**
     * Get unread notification count (for AJAX)
     */
    public function getUnreadCount()
    {
        // Check if user is authenticated
        if (!Auth::check()) {
            return response()->json(['count' => 0]);
        }

        $count = Auth::user()->unreadNotificationsCount();

        return response()->json([
            'count' => $count
        ]);
    }

    /**
     * Delete notification
     */
    public function destroy($id)
    {
        $notification = Notification::findOrFail($id);

        // Check if user owns the notification
        if ($notification->recipient_id !== Auth::id()) {
            abort(403);
        }

        $notification->delete();

        return response()->json([
            'success' => true
        ]);
    }

    /**
     * Get notification details (for AJAX)
     */
    public function show($id)
    {
        $notification = Notification::with(['sender', 'notifiable'])
            ->findOrFail($id);

        // Check if user owns the notification
        if ($notification->recipient_id !== Auth::id()) {
            abort(403);
        }

        return response()->json([
            'notification' => $notification,
            'message' => $notification->getMessage(),
            'icon' => $notification->getIcon()
        ]);
    }
}
