<?php

namespace App\Http\Controllers\TaskManagement;

use App\Http\Controllers\Controller;
use App\Models\BoardMember;
use App\Models\Task;
use App\Models\TaskActivity;
use App\Models\TaskComment;
use App\Services\TaskManagement\NotificationService;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class TaskCommentController extends Controller
{
    public function __construct(protected NotificationService $notificationService)
    {
    }

    /**
     * Menampilkan komentar berdasarkan task_id jika disediakan.
     */
    public function index(Request $request)
    {
        try {
            $taskId = $request->query('task_id');

            $comments = TaskComment::query()
                ->with(['task', 'user'])
                ->when($taskId, function ($query) use ($taskId) {
                    $query->where('task_id', $taskId);
                })
                ->orderByDesc('created_at')
                ->get();

            return response()->json([
                'success' => true,
                'message' => 'Daftar komentar berhasil diambil.',
                'data' => $comments,
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil komentar.',
                'errors' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Membuat komentar baru pada task. Hanya member board yang bisa berkomentar.
     */
    public function store(Request $request)
    {
        if (!Auth::check()) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthenticated.',
                'errors' => 'User belum login.',
            ], 401);
        }

        $validated = $request->validate([
            'task_id' => 'required|exists:tasks,id',
            'comment' => 'required|string',
        ]);

        try {
            $task = Task::findOrFail($validated['task_id']);
            $isMember = BoardMember::where('board_id', $task->board_id)
                ->where('user_id', Auth::id())
                ->where('membership_status', 'accepted')
                ->exists();

            if ((int) $task->board->created_by !== Auth::id() && !$isMember) {
                return response()->json([
                    'success' => false,
                    'message' => 'Anda tidak berwenang memberi komentar di board ini.',
                    'errors' => 'Hanya member board yang dapat berkomentar.',
                ], 403);
            }

            $comment = DB::transaction(function () use ($task, $validated) {
                $comment = TaskComment::create([
                    'task_id' => $task->id,
                    'user_id' => Auth::id(),
                    'comment' => $validated['comment'],
                ]);

                TaskActivity::create([
                    'task_id' => $task->id,
                    'user_id' => Auth::id(),
                    'activity' => 'User memberikan komentar',
                ]);

                $recipients = collect();
                if ($task->creator) {
                    $recipients->push($task->creator);
                }
                if ($task->assignee) {
                    $recipients->push($task->assignee);
                }

                $this->notificationService->notifyTaskComment($task, Auth::user(), $recipients->unique('id')->values()->all());

                return $comment;
            });

            return response()->json([
                'success' => true,
                'message' => 'Komentar berhasil ditambahkan.',
                'data' => $comment->load(['task', 'user']),
            ], 201);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Task tidak ditemukan.',
                'errors' => $e->getMessage(),
            ], 404);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal menambahkan komentar.',
                'errors' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Menghapus komentar. Hanya pemilik komentar atau PM yang bisa menghapus.
     */
    public function destroy(string $id)
    {
        if (!Auth::check()) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthenticated.',
                'errors' => 'User belum login.',
            ], 401);
        }

        try {
            $comment = TaskComment::findOrFail($id);
            $task = $comment->task;
            $board = $task->board;

            if ((int) $comment->user_id !== Auth::id() && (int) $board->created_by !== Auth::id()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Anda tidak berwenang menghapus komentar ini.',
                    'errors' => 'Hanya pemilik komentar atau PM yang dapat menghapus.',
                ], 403);
            }

            DB::transaction(function () use ($comment) {
                $comment->delete();
            });

            return response()->json([
                'success' => true,
                'message' => 'Komentar berhasil dihapus.',
                'data' => null,
            ], 200);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Komentar tidak ditemukan.',
                'errors' => $e->getMessage(),
            ], 404);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal menghapus komentar.',
                'errors' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Menampilkan form pembuatan komentar (tidak digunakan pada API).
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
     * Menampilkan detail komentar (tidak digunakan pada API).
     */
    public function show(string $id)
    {
        return response()->json([
            'success' => false,
            'message' => 'Method not supported.',
            'errors' => 'Endpoint ini tidak menyediakan detail komentar.',
        ], 405);
    }

    /**
     * Menampilkan form edit komentar (tidak digunakan pada API).
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
     * Memperbarui komentar secara manual (tidak digunakan pada API).
     */
    public function update(Request $request, string $id)
    {
        return response()->json([
            'success' => false,
            'message' => 'Method not supported.',
            'errors' => 'Komentar tidak mendukung update.',
        ], 405);
    }
}
