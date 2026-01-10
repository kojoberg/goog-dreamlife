<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
    // Fetch latest unread notifications (for dropdown via AJAX or Page Load)
    public function index()
    {
        $notifications = Auth::user()->unreadNotifications()->latest()->take(10)->get();
        return response()->json($notifications);
    }

    // Mark a specific notification as read
    public function markAsRead($id)
    {
        $notification = Auth::user()->notifications()->where('id', $id)->first();

        if ($notification) {
            $notification->markAsRead();

            return response()->json([
                'status' => 'success',
                'url' => $notification->data['action_url'] ?? $notification->data['url'] ?? null
            ]);
        }

        return response()->json(['status' => 'error', 'message' => 'Notification not found'], 404);
    }

    // Mark all as read
    public function markAllRead()
    {
        Auth::user()->unreadNotifications->markAsRead();
        return back()->with('success', 'All notifications marked as read.');
    }
}
