<?php

namespace App\Http\Controllers\TaskManagement;

use App\Http\Controllers\Controller;
use App\Models\Task;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MyTaskController extends Controller
{
    /**
     * Menampilkan task milik user login.
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
            $tasks = Task::with(['board', 'creator', 'assignee'])
                ->where(function ($query) {
                    $query->where('assigned_to', Auth::id())
                        ->orWhere('created_by', Auth::id());
                })
                ->orderByDesc('created_at')
                ->get();

            return response()->json([
                'success' => true,
                'message' => 'Task milik user berhasil diambil.',
                'data' => $tasks,
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil task milik user.',
                'errors' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Menampilkan form pembuatan task (tidak digunakan pada API).
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
     * Menyimpan task milik user (tidak digunakan pada API).
     */
    public function store(Request $request)
    {
        return response()->json([
            'success' => false,
            'message' => 'Method not supported.',
            'errors' => 'Gunakan endpoint TaskController::store.',
        ], 405);
    }

    /**
     * Menampilkan detail task milik user (tidak digunakan pada API).
     */
    public function show(string $id)
    {
        return response()->json([
            'success' => false,
            'message' => 'Method not supported.',
            'errors' => 'Endpoint ini tidak menyediakan detail task.',
        ], 405);
    }

    /**
     * Menampilkan form edit task (tidak digunakan pada API).
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
     * Memperbarui task milik user (tidak digunakan pada API).
     */
    public function update(Request $request, string $id)
    {
        return response()->json([
            'success' => false,
            'message' => 'Method not supported.',
            'errors' => 'Gunakan endpoint TaskController::update.',
        ], 405);
    }

    /**
     * Menghapus task milik user (tidak digunakan pada API).
     */
    public function destroy(string $id)
    {
        return response()->json([
            'success' => false,
            'message' => 'Method not supported.',
            'errors' => 'Gunakan endpoint TaskController::destroy.',
        ], 405);
    }
}
