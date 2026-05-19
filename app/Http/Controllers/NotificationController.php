<?php

namespace App\Http\Controllers;

use App\Models\Notification;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;

class NotificationController extends Controller
{
    public function markRead(Notification $notification): RedirectResponse
    {
        /** @var \App\Models\User $user */
        $user = auth()->user();

        abort_unless($notification->user_id === $user->id, 403);

        $notification->update([
            'is_read' => true,
            'read_at' => now(),
        ]);

        if ($notification->document_id) {
            return redirect()->route('portal.documents.show', $notification->document_id);
        }

        return redirect()->back();
    }

    public function markAllRead(): RedirectResponse
    {
        /** @var \App\Models\User $user */
        $user = auth()->user();

        Notification::where('user_id', $user->id)
            ->where('is_read', false)
            ->update(['is_read' => true, 'read_at' => now()]);

        return redirect()->back()->with('success', 'Semua notifikasi telah ditandai dibaca.');
    }

    public function unreadCount(): JsonResponse
    {
        /** @var \App\Models\User $user */
        $user  = auth()->user();
        $count = Notification::where('user_id', $user->id)->where('is_read', false)->count();

        return response()->json(['count' => $count]);
    }

    public function list(): JsonResponse
    {
        /** @var \App\Models\User $user */
        $user = auth()->user();

        $notifications = Notification::where('user_id', $user->id)
            ->latest()
            ->limit(20)
            ->get()
            ->map(fn ($n) => [
                'id'         => $n->id,
                'title'      => $n->title,
                'message'    => $n->message,
                'type'       => $n->type,
                'is_read'    => $n->is_read,
                'created_at' => $n->created_at->diffForHumans(),
                'read_url'   => route('notifications.mark-read', $n->id),
            ]);

        return response()->json($notifications);
    }
}
