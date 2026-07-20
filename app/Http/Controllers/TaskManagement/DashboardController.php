<?php

namespace App\Http\Controllers\TaskManagement;

use App\Http\Controllers\Controller;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    /**
     * Menampilkan ringkasan dashboard task management untuk user login.
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

            // Query 1: statistik board — hitung semua dalam satu query
            $boardStats = DB::table('boards')
                ->leftJoin('board_members as bm', function ($join) use ($userId) {
                    $join->on('bm.board_id', '=', 'boards.id')
                        ->where('bm.user_id', '=', $userId);
                })
                ->where(function ($q) use ($userId) {
                    $q->where('boards.created_by', $userId)
                      ->orWhereNotNull('bm.id');
                })
                ->selectRaw('
                    COUNT(DISTINCT boards.id) as total_board,
                    COUNT(DISTINCT CASE WHEN bm.membership_status = ? THEN bm.id END) as joined_board,
                    COUNT(DISTINCT CASE WHEN bm.membership_status = ? THEN bm.id END) as pending_request
                ', ['accepted', 'pending'])
                ->first();

            // Query 2: statistik task — hitung semua dalam satu query
            $taskStats = DB::table('tasks')
                ->where(function ($q) use ($userId) {
                    $q->where('assigned_to', $userId)
                      ->orWhere('created_by', $userId);
                })
                ->selectRaw('
                    COUNT(*) as my_task,
                    SUM(CASE WHEN assigned_to = ? AND status = ? THEN 1 ELSE 0 END) as completed_task
                ', [$userId, 'done'])
                ->first();

            $myTask = (int) $taskStats->my_task;
            $completedTask = (int) $taskStats->completed_task;
            $progress = $myTask > 0 ? round(($completedTask / $myTask) * 100, 2) : 0;

            return response()->json([
                'success' => true,
                'message' => 'Dashboard berhasil diambil.',
                'data' => [
                    'total_board'     => (int) $boardStats->total_board,
                    'joined_board'    => (int) $boardStats->joined_board,
                    'pending_request' => (int) $boardStats->pending_request,
                    'my_task'         => $myTask,
                    'completed_task'  => $completedTask,
                    'progress'        => $progress,
                ],
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil dashboard.',
                'errors' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Menampilkan form pembuatan dashboard (tidak digunakan pada API).
     */
    public function create()
    {
        return response()->json([
            'success' => false,
            'message' => 'Method not supported.',
            'errors' => 'Endpoint ini tidak menyediakan form.',
        ], 405);
    }

    /**
     * Menyimpan data dashboard (tidak digunakan pada API).
     */
    public function store(Request $request)
    {
        return response()->json([
            'success' => false,
            'message' => 'Method not supported.',
            'errors' => 'Endpoint ini tidak menyediakan penyimpanan dashboard.',
        ], 405);
    }

    /**
     * Menampilkan detail dashboard (tidak digunakan pada API).
     */
    public function show(string $id)
    {
        return response()->json([
            'success' => false,
            'message' => 'Method not supported.',
            'errors' => 'Endpoint ini tidak menyediakan detail dashboard.',
        ], 405);
    }

    /**
     * Menampilkan form edit dashboard (tidak digunakan pada API).
     */
    public function edit(string $id)
    {
        return response()->json([
            'success' => false,
            'message' => 'Method not supported.',
            'errors' => 'Endpoint ini tidak menyediakan form edit.',
        ], 405);
    }

    /**
     * Memperbarui dashboard (tidak digunakan pada API).
     */
    public function update(Request $request, string $id)
    {
        return response()->json([
            'success' => false,
            'message' => 'Method not supported.',
            'errors' => 'Endpoint ini tidak menyediakan update dashboard.',
        ], 405);
    }

    /**
     * Menghapus dashboard (tidak digunakan pada API).
     */
    public function destroy(string $id)
    {
        return response()->json([
            'success' => false,
            'message' => 'Method not supported.',
            'errors' => 'Endpoint ini tidak menyediakan delete dashboard.',
        ], 405);
    }
}
