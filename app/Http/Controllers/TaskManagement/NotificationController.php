<?php

namespace App\Http\Controllers\TaskManagement;

use App\Http\Controllers\Controller;
use App\Models\Notification;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
    /**
     * Get all notifications for the authenticated user.
     */
    public function index()
    {
        if (!Auth::check()) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthenticated.',
                'errors' => 'User belum login.',
            ], 401);
        }

        try {
            $userId = Auth::id();
            $notifications = Notification::where('user_id', $userId)
                ->orderByDesc('created_at')
                ->get();

            return response()->json([
                'success' => true,
                'message' => 'Daftar notifikasi berhasil diambil.',
                'data' => $notifications,
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil data notifikasi.',
                'errors' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Mark a notification as read.
     */
    public function markAsRead(string $id)
    {
        if (!Auth::check()) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthenticated.',
                'errors' => 'User belum login.',
            ], 401);
        }

        try {
            $notification = Notification::where('user_id', Auth::id())
                ->findOrFail($id);

            $notification->update(['read' => true]);

            return response()->json([
                'success' => true,
                'message' => 'Notifikasi berhasil ditandai sebagai dibaca.',
                'data' => $notification,
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal memperbarui notifikasi.',
                'errors' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Delete all notifications for the user.
     */
    public function clearAll()
    {
        if (!Auth::check()) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthenticated.',
                'errors' => 'User belum login.',
            ], 401);
        }

        try {
            Notification::where('user_id', Auth::id())->delete();

            return response()->json([
                'success' => true,
                'message' => 'Semua notifikasi berhasil dihapus.',
                'data' => null,
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal menghapus notifikasi.',
                'errors' => $e->getMessage(),
            ], 500);
        }
    }
}
