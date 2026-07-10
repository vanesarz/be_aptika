<?php

namespace App\Http\Controllers\TaskManagement;

use App\Http\Controllers\Controller;
use App\Models\TaskActivity;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TaskActivityController extends Controller
{
    /**
     * Menampilkan riwayat aktivitas task yang dapat diakses user.
     */
    public function index(Request $request)
    {
        if (!Auth::check()) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthenticated.',
                'errors' => 'User belum login.',
            ], 401);
        }

        try {
            $taskId = $request->query('task_id');
            $userId = Auth::id();

            $activities = TaskActivity::query()
                ->with(['task', 'user'])
                ->when($taskId, function ($query) use ($taskId) {
                    $query->where('task_id', $taskId);
                })
                ->whereHas('task.board', function ($query) use ($userId) {
                    $query->where('created_by', $userId)
                        ->orWhereHas('members', function ($memberQuery) use ($userId) {
                            $memberQuery->where('user_id', $userId)
                                ->where('membership_status', 'accepted');
                        });
                })
                ->orderByDesc('created_at')
                ->get();

            return response()->json([
                'success' => true,
                'message' => 'Riwayat aktivitas berhasil diambil.',
                'data' => $activities,
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil aktivitas task.',
                'errors' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Menampilkan form pembuatan aktivitas (tidak digunakan pada API).
     */
    public function create()
    {
        return response()->json([
            'success' => false,
            'message' => 'Method not supported.',
            'errors' => 'Aktivitas task bersifat read-only.',
        ], 405);
    }

    /**
     * Membuat aktivitas secara manual (tidak digunakan pada API).
     */
    public function store(Request $request)
    {
        return response()->json([
            'success' => false,
            'message' => 'Method not supported.',
            'errors' => 'Aktivitas task bersifat read-only.',
        ], 405);
    }

    /**
     * Menampilkan detail aktivitas (tidak digunakan pada API).
     */
    public function show(string $id)
    {
        return response()->json([
            'success' => false,
            'message' => 'Method not supported.',
            'errors' => 'Endpoint ini tidak menyediakan detail aktivitas.',
        ], 405);
    }

    /**
     * Menampilkan form edit aktivitas (tidak digunakan pada API).
     */
    public function edit(string $id)
    {
        return response()->json([
            'success' => false,
            'message' => 'Method not supported.',
            'errors' => 'Aktivitas task bersifat read-only.',
        ], 405);
    }

    /**
     * Memperbarui aktivitas secara manual (tidak digunakan pada API).
     */
    public function update(Request $request, string $id)
    {
        return response()->json([
            'success' => false,
            'message' => 'Method not supported.',
            'errors' => 'Aktivitas task bersifat read-only.',
        ], 405);
    }

    /**
     * Menghapus aktivitas secara manual (tidak digunakan pada API).
     */
    public function destroy(string $id)
    {
        return response()->json([
            'success' => false,
            'message' => 'Method not supported.',
            'errors' => 'Aktivitas task bersifat read-only.',
        ], 405);
    }
}
