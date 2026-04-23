<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    /**
     * Display a listing of the notifications for the authenticated user.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {
        $notifications = auth()->user()->notifications;

        return response()->json([
            'success' => true,
            'data' => $notifications
        ]);
    }

    /**
     * Mark all unread notifications as read for the authenticated user.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function markAsRead()
    {
        auth()->user()->unreadNotifications->markAsRead();

        return response()->json([
            'success' => true,
            'message' => 'Notifications marked as read'
        ]);
    }
}
