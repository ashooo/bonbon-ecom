<?php

namespace App\Http\Controllers;

use App\Models\UserNotification;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\View\View;

class UserNotificationController extends Controller
{
    public function index(Request $request): View
    {
        $filter = $request->string('filter')->value();

        $query = match ($filter) {
            'unread' => $request->user()->activeUserNotifications()->whereNull('read_at'),
            'archived' => $request->user()->archivedUserNotifications(),
            default => $request->user()->activeUserNotifications(),
        };

        $notifications = $query->paginate(15)->withQueryString();

        $counts = [
            'all' => $request->user()->activeUserNotifications()->count(),
            'unread' => $request->user()->unreadUserNotifications()->count(),
            'archived' => $request->user()->archivedUserNotifications()->count(),
        ];

        return view('notifications.index', compact('notifications', 'filter', 'counts'));
    }

    public function readAll(Request $request): JsonResponse
    {
        $request->user()
            ->unreadUserNotifications()
            ->update(['read_at' => now()]);

        return response()->json(['ok' => true]);
    }

    public function open(Request $request, UserNotification $notification): RedirectResponse
    {
        abort_unless($notification->user_id === $request->user()->id, 403);

        $notification->markAsRead();

        return redirect()->to($notification->url ?: route('assistant'));
    }

    public function archive(Request $request, UserNotification $notification): RedirectResponse
    {
        abort_unless($notification->user_id === $request->user()->id, 403);

        $notification->archive();

        return back()->with('success', 'Notification archived.');
    }

    public function restore(Request $request, UserNotification $notification): RedirectResponse
    {
        abort_unless($notification->user_id === $request->user()->id, 403);

        $notification->unarchive();

        return back()->with('success', 'Notification moved back to active.');
    }

    public function destroy(Request $request, UserNotification $notification): RedirectResponse
    {
        abort_unless($notification->user_id === $request->user()->id, 403);

        $notification->delete();

        return back()->with('success', 'Notification deleted.');
    }
}
