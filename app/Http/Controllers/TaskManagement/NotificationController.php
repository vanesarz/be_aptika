<?php

namespace App\Http\Controllers\TaskManagement;

use App\Http\Controllers\Controller;
use App\Models\Notification;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
    /**
     * GET /task-management/notifications
     * Mengambil daftar notifikasi milik user yang sedang login.
     */
    public function index(Request $request)
    {
        if (!Auth::check()) {
            return response()->json(['success' => false, 'message' => 'Unauthenticated.'], 401);
        }

        try {
            $limit  = max(1, min((int) $request->query('limit', 20), 100));
            $offset = max(0, (int) $request->query('offset', 0));

            $notifications = Notification::where('user_id', Auth::id())
                ->with(['board:id,name', 'task:id,title,status', 'createdByUser:id,name'])
                ->orderByDesc('created_at')
                ->skip($offset)
                ->take($limit)
                ->get()
                ->map(fn($n) => $this->formatNotification($n));

            return response()->json([
                'success' => true,
                'message' => 'Daftar notifikasi berhasil diambil.',
                'data'    => $notifications,
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil data notifikasi.',
                'errors'  => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * GET /task-management/notifications/unread-count
     * Mengembalikan jumlah notifikasi yang belum dibaca.
     */
    public function unreadCount()
    {
        if (!Auth::check()) {
            return response()->json(['success' => false, 'message' => 'Unauthenticated.'], 401);
        }

        try {
            $count = Notification::where('user_id', Auth::id())
                ->where('is_read', false)
                ->count();

            return response()->json(['count' => $count], 200);
        } catch (Exception $e) {
            return response()->json(['count' => 0], 200);
        }
    }

    /**
     * PATCH /task-management/notifications/{id}/read
     * Menandai satu notifikasi sebagai sudah dibaca.
     */
    public function markAsRead(string $id)
    {
        if (!Auth::check()) {
            return response()->json(['success' => false, 'message' => 'Unauthenticated.'], 401);
        }

        try {
            $notification = Notification::where('user_id', Auth::id())->findOrFail($id);
            $notification->update(['is_read' => true, 'read_at' => now()]);

            return response()->json([
                'success' => true,
                'message' => 'Notifikasi berhasil ditandai sebagai dibaca.',
                'data'    => $this->formatNotification($notification->fresh()),
            ], 200);
        } catch (ModelNotFoundException) {
            return response()->json(['success' => false, 'message' => 'Notifikasi tidak ditemukan.'], 404);
        } catch (Exception $e) {
            return response()->json(['success' => false, 'message' => 'Gagal memperbarui notifikasi.', 'errors' => $e->getMessage()], 500);
        }
    }

    /**
     * PATCH /task-management/notifications/read-all
     * Menandai semua notifikasi milik user sebagai sudah dibaca.
     */
    public function markAllAsRead()
    {
        if (!Auth::check()) {
            return response()->json(['success' => false, 'message' => 'Unauthenticated.'], 401);
        }

        try {
            Notification::where('user_id', Auth::id())
                ->where('is_read', false)
                ->update(['is_read' => true, 'read_at' => now()]);

            return response()->json([
                'success' => true,
                'message' => 'Semua notifikasi berhasil ditandai sebagai dibaca.',
            ], 200);
        } catch (Exception $e) {
            return response()->json(['success' => false, 'message' => 'Gagal memperbarui notifikasi.', 'errors' => $e->getMessage()], 500);
        }
    }

    /**
     * DELETE /task-management/notifications/{id}
     * Menghapus satu notifikasi milik user.
     */
    public function destroy(string $id)
    {
        if (!Auth::check()) {
            return response()->json(['success' => false, 'message' => 'Unauthenticated.'], 401);
        }

        try {
            $notification = Notification::where('user_id', Auth::id())->findOrFail($id);
            $notification->delete();

            return response()->json(['success' => true, 'message' => 'Notifikasi berhasil dihapus.', 'data' => null], 200);
        } catch (ModelNotFoundException) {
            return response()->json(['success' => false, 'message' => 'Notifikasi tidak ditemukan.'], 404);
        } catch (Exception $e) {
            return response()->json(['success' => false, 'message' => 'Gagal menghapus notifikasi.', 'errors' => $e->getMessage()], 500);
        }
    }

    /**
     * Format satu notifikasi sesuai kontrak frontend (NotificationItem).
     */
    private function formatNotification(Notification $n): array
    {
        return [
            'id'         => $n->id,
            'type'       => $n->type ?? 'SYSTEM',
            'title'      => $n->title,
            'message'    => $n->message,
            'is_read'    => (bool) $n->is_read,
            'read_at'    => $n->read_at?->toISOString(),
            'created_at' => $n->created_at?->toISOString(),
            'board'      => $n->board ? ['id' => $n->board->id, 'name' => $n->board->name] : null,
            'task'       => $n->task  ? ['id' => $n->task->id,  'title' => $n->task->title, 'status' => $n->task->status] : null,
            'created_by' => $n->createdByUser ? ['id' => $n->createdByUser->id, 'name' => $n->createdByUser->name] : null,
        ];
    }
}

