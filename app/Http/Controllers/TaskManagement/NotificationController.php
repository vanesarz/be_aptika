<?php

namespace App\Http\Controllers\TaskManagement;

use App\Http\Controllers\Controller;
use App\Http\Resources\TaskManagement\NotificationResource;
use App\Services\TaskManagement\NotificationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
    public function __construct(protected NotificationService $service)
    {
    }

    public function index(Request $request)
    {
        $userId = Auth::id();
        $limit = (int) $request->query('limit', 15);
        $offset = (int) $request->query('offset', 0);

        $notifications = $this->service->getForUser($userId, max(1, min($limit, 50)), max(0, $offset));

        return response()->json([
            'success' => true,
            'data' => NotificationResource::collection($notifications),
        ]);
    }

    public function unreadCount()
    {
        return response()->json([
            'count' => $this->service->unreadCount(Auth::id()),
        ]);
    }

    public function markAsRead(string $id)
    {
        $updated = $this->service->markAsRead((int) $id, Auth::id());

        return response()->json([
            'success' => $updated,
            'message' => $updated ? 'Notifikasi ditandai dibaca.' : 'Notifikasi tidak ditemukan.',
        ]);
    }

    public function markAllAsRead()
    {
        $count = $this->service->markAllAsRead(Auth::id());

        return response()->json([
            'success' => true,
            'message' => sprintf('%d notifikasi ditandai dibaca.', $count),
        ]);
    }

    public function destroy(string $id)
    {
        $deleted = $this->service->delete((int) $id, Auth::id());

        return response()->json([
            'success' => $deleted,
            'message' => $deleted ? 'Notifikasi dihapus.' : 'Notifikasi tidak ditemukan.',
        ]);
    }
}
