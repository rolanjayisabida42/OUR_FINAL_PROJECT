<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | GET TEACHER NOTIFICATIONS
    |--------------------------------------------------------------------------
    */

    public function index(Request $request): JsonResponse
    {
        $user = $request->user();

        $notifications = $user->notifications()
            ->latest()
            ->limit(20)
            ->get()
            ->map(function ($notification) {

                $data = $notification->data;

                return [
                    'id' => $notification->id,
                    'kind' => $data['kind'] ?? 'info',
                    'title' => $data['title'] ?? 'Notification',
                    'message' => $data['message'] ?? '',
                    'url' => $data['url'] ?? null,
                    'read' => $notification->read_at !== null,
                    'time' => $notification->created_at
                        ?->diffForHumans(),
                    'date' => $notification->created_at
                        ?->copy()
                        ->timezone('Asia/Manila')
                        ->format('M d, Y h:i A'),
                ];
            })
            ->values();

        return response()->json([
            'unread_count' => $user
                ->unreadNotifications()
                ->count(),

            'notifications' => $notifications,
        ]);
    }


    /*
    |--------------------------------------------------------------------------
    | MARK ONE NOTIFICATION AS READ
    |--------------------------------------------------------------------------
    */

    public function read(
        Request $request,
        string $notification
    ): JsonResponse {

        // Important: search only the logged-in user's notifications.
        $item = $request->user()
            ->notifications()
            ->whereKey($notification)
            ->firstOrFail();

        $item->markAsRead();

        return response()->json([
            'success' => true,
        ]);
    }


    /*
    |--------------------------------------------------------------------------
    | MARK ALL NOTIFICATIONS AS READ
    |--------------------------------------------------------------------------
    */

    public function readAll(Request $request): JsonResponse
    {
        $request->user()
            ->unreadNotifications()
            ->update([
                'read_at' => now(),
            ]);

        return response()->json([
            'success' => true,
            'unread_count' => 0,
        ]);
    }
}