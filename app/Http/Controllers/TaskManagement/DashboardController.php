<?php

namespace App\Http\Controllers\TaskManagement;

use App\Http\Controllers\Controller;
use App\Models\Board;
use App\Models\BoardMember;
use App\Models\Task;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

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

            $totalBoard = Board::query()
                ->where(function ($query) use ($userId) {
                    $query->where('created_by', $userId)
                        ->orWhereHas('members', function ($subQuery) use ($userId) {
                            $subQuery->where('user_id', $userId)
                                ->where('membership_status', 'accepted');
                        });
                })
                ->count();

            $joinedBoard = BoardMember::where('user_id', $userId)
                ->where('membership_status', 'accepted')
                ->count();

            $pendingRequest = BoardMember::where('user_id', $userId)
                ->where('membership_status', 'pending')
                ->count();

            $myTask = Task::where(function ($query) use ($userId) {
                $query->where('assigned_to', $userId)
                    ->orWhere('created_by', $userId);
            })->count();

            $completedTask = Task::where('assigned_to', $userId)
                ->where('status', 'done')
                ->count();

            $progress = $myTask > 0 ? round(($completedTask / $myTask) * 100, 2) : 0;

            return response()->json([
                'success' => true,
                'message' => 'Dashboard berhasil diambil.',
                'data' => [
                    'total_board' => $totalBoard,
                    'joined_board' => $joinedBoard,
                    'pending_request' => $pendingRequest,
                    'my_task' => $myTask,
                    'completed_task' => $completedTask,
                    'progress' => $progress,
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
