<?php

namespace App\Http\Controllers\TaskManagement;

use App\Http\Controllers\Controller;
use App\Models\Board;
use App\Models\BoardMember;
use App\Models\Task;
use App\Models\TaskActivity;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class TaskController extends Controller
{
    /**
     * Menampilkan daftar task yang dapat diakses user.
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
            $boardId = $request->query('board_id');
            $userId = Auth::id();

            $tasks = Task::query()
                ->with(['board', 'creator', 'assignee', 'comments', 'activities.user'])
                ->when($boardId, function ($query) use ($boardId) {
                    $query->where('board_id', $boardId);
                })
                ->where(function ($query) use ($userId) {
                    $query->whereHas('board', function ($subQuery) use ($userId) {
                        $subQuery->where('created_by', $userId)
                            ->orWhereHas('members', function ($memberQuery) use ($userId) {
                                $memberQuery->where('user_id', $userId)
                                    ->where('membership_status', 'accepted');
                            });
                    });
                })
                ->orderByDesc('created_at')
                ->get();

            return response()->json([
                'success' => true,
                'message' => 'Daftar task berhasil diambil.',
                'data' => $tasks,
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil data task.',
                'errors' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Menampilkan detail task berdasarkan ID.
     */
    public function show(string $id)
    {
        if (!Auth::check()) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthenticated.',
                'errors' => 'User belum login.',
            ], 401);
        }

        try {
            $task = Task::with(['board', 'creator', 'assignee', 'comments.user', 'activities.user'])
                ->findOrFail($id);

            $this->ensureBoardAccess($task->board_id);

            return response()->json([
                'success' => true,
                'message' => 'Task berhasil ditemukan.',
                'data' => $task,
            ], 200);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Task tidak ditemukan.',
                'errors' => $e->getMessage(),
            ], 404);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil detail task.',
                'errors' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Membuat task baru. Hanya PM yang boleh membuat task.
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
            'board_id' => 'required|exists:boards,id',
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'priority' => 'required|in:low,medium,high',
            'status' => 'required|in:todo,in_progress,in_review,done',
            'start_date' => 'nullable|date',
            'due_date' => 'nullable|date|after_or_equal:start_date',
            'assigned_to' => 'nullable|exists:users,id',
        ]);

        try {
            $board = Board::findOrFail($validated['board_id']);

            if ((int) $board->created_by !== Auth::id()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Anda tidak berwenang membuat task di board ini.',
                    'errors' => 'Hanya PM yang dapat membuat task.',
                ], 403);
            }

            if (!empty($validated['assigned_to'])) {
                $isValidAssignee = BoardMember::where('board_id', $board->id)
                    ->where('user_id', $validated['assigned_to'])
                    ->where('membership_status', 'accepted')
                    ->exists();

                if (!$isValidAssignee) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Assignee harus merupakan member board yang sudah diterima.',
                        'errors' => 'Assignee tidak valid.',
                    ], 422);
                }
            }

            $task = DB::transaction(function () use ($board, $validated) {
                $task = Task::create([
                    'board_id' => $validated['board_id'],
                    'created_by' => Auth::id(),
                    'assigned_to' => $validated['assigned_to'] ?? null,
                    'title' => $validated['title'],
                    'description' => $validated['description'] ?? null,
                    'priority' => $validated['priority'],
                    'status' => $validated['status'],
                    'start_date' => $validated['start_date'] ?? null,
                    'due_date' => $validated['due_date'] ?? null,
                    'completed_at' => $validated['status'] === 'done' ? now() : null,
                ]);

                $this->createTaskActivity($task, 'Task dibuat');

                if (!empty($validated['assigned_to'])) {
                    $this->createTaskActivity($task, 'Task diassign');
                }

                return $task;
            });

            return response()->json([
                'success' => true,
                'message' => 'Task berhasil dibuat.',
                'data' => $task->load(['board', 'creator', 'assignee']),
            ], 201);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Board tidak ditemukan.',
                'errors' => $e->getMessage(),
            ], 404);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal membuat task.',
                'errors' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Memperbarui task. PM dapat mengubah semua task, staff hanya dapat mengubah task yang ditugaskan kepadanya.
     */
    public function update(Request $request, string $id)
    {
        if (!Auth::check()) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthenticated.',
                'errors' => 'User belum login.',
            ], 401);
        }

        $validated = $request->validate([
            'title' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'priority' => 'nullable|in:low,medium,high',
            'status' => 'nullable|in:todo,in_progress,in_review,done',
            'start_date' => 'nullable|date',
            'due_date' => 'nullable|date|after_or_equal:start_date',
            'assigned_to' => 'nullable|exists:users,id',
        ]);

        try {
            $task = Task::findOrFail($id);
            $this->ensureBoardAccess($task->board_id);

            $isPm = (int) $task->board->created_by === Auth::id();
            if (!$isPm && (int) $task->assigned_to !== Auth::id()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Anda tidak berwenang mengubah task ini.',
                    'errors' => 'Staff hanya boleh mengedit task yang ditugaskan kepadanya.',
                ], 403);
            }

            DB::transaction(function () use ($task, $validated) {
                $originalStatus = $task->status;
                $originalDueDate = $task->due_date;
                $originalAssignee = $task->assigned_to;
                $originalPriority = $task->priority;

                $payload = [
                    'title' => array_key_exists('title', $validated) ? $validated['title'] : $task->title,
                    'description' => array_key_exists('description', $validated) ? $validated['description'] : $task->description,
                    'priority' => array_key_exists('priority', $validated) ? $validated['priority'] : $task->priority,
                    'status' => array_key_exists('status', $validated) ? $validated['status'] : $task->status,
                    'start_date' => array_key_exists('start_date', $validated) ? $validated['start_date'] : $task->start_date,
                    'due_date' => array_key_exists('due_date', $validated) ? $validated['due_date'] : $task->due_date,
                    'assigned_to' => array_key_exists('assigned_to', $validated) ? $validated['assigned_to'] : $task->assigned_to,
                    'completed_at' => array_key_exists('status', $validated) && $validated['status'] === 'done' ? now() : (array_key_exists('status', $validated) && $validated['status'] !== 'done' ? null : $task->completed_at),
                ];

                $task->update($payload);

                if ($originalStatus !== $task->status) {
                    $this->createTaskActivity($task, 'Status berubah');
                }

                if ($originalDueDate != $task->due_date) {
                    $this->createTaskActivity($task, 'Deadline berubah');
                }

                if ($originalPriority !== $task->priority) {
                    $this->createTaskActivity($task, 'Priority berubah');
                }

                if ($originalAssignee != $task->assigned_to) {
                    $this->createTaskActivity($task, 'Task diassign');
                }
            });

            return response()->json([
                'success' => true,
                'message' => 'Task berhasil diperbarui.',
                'data' => $task->fresh()->load(['board', 'creator', 'assignee']),
            ], 200);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Task tidak ditemukan.',
                'errors' => $e->getMessage(),
            ], 404);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal memperbarui task.',
                'errors' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Menghapus task. Hanya PM yang boleh menghapus task.
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
            $task = Task::findOrFail($id);
            $this->ensureBoardAccess($task->board_id);

            if ((int) $task->board->created_by !== Auth::id()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Anda tidak berwenang menghapus task ini.',
                    'errors' => 'Hanya PM yang dapat menghapus task.',
                ], 403);
            }

            DB::transaction(function () use ($task) {
                $this->createTaskActivity($task, 'Task dihapus');
                $task->delete();
            });

            return response()->json([
                'success' => true,
                'message' => 'Task berhasil dihapus.',
                'data' => null,
            ], 200);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Task tidak ditemukan.',
                'errors' => $e->getMessage(),
            ], 404);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal menghapus task.',
                'errors' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Mengubah status task untuk alur Kanban drag-and-drop.
     */
    public function updateStatus(Request $request, string $id)
    {
        if (!Auth::check()) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthenticated.',
                'errors' => 'User belum login.',
            ], 401);
        }

        $validated = $request->validate([
            'status' => 'required|in:todo,in_progress,in_review,done',
        ]);

        try {
            $task = Task::findOrFail($id);
            $this->ensureBoardAccess($task->board_id);

            if ((int) $task->board->created_by !== Auth::id() && (int) $task->assigned_to !== Auth::id()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Anda tidak berwenang mengubah status task ini.',
                    'errors' => 'Hanya PM atau assignee yang dapat mengubah status.',
                ], 403);
            }

            DB::transaction(function () use ($task, $validated) {
                $task->update([
                    'status' => $validated['status'],
                    'completed_at' => $validated['status'] === 'done' ? now() : null,
                ]);
                $this->createTaskActivity($task, 'Status berubah');
            });

            return response()->json([
                'success' => true,
                'message' => 'Status task berhasil diperbarui.',
                'data' => $task->fresh()->load(['board', 'creator', 'assignee']),
            ], 200);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Task tidak ditemukan.',
                'errors' => $e->getMessage(),
            ], 404);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengubah status task.',
                'errors' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Menampilkan task milik user yang sedang login.
     */
    public function myTasks(Request $request)
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
            $boardId = $request->query('board_id');

            $tasks = Task::query()
                ->with(['board', 'creator', 'assignee'])
                ->where(function ($query) use ($userId) {
                    $query->where('assigned_to', $userId)
                        ->orWhere('created_by', $userId);
                })
                ->when($boardId, function ($query) use ($boardId) {
                    $query->where('board_id', $boardId);
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
     * Mengembalikan form pembuatan task (tidak digunakan pada API).
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
     * Mengembalikan form edit task (tidak digunakan pada API).
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
     * Memastikan user memiliki akses ke board task.
     */
    private function ensureBoardAccess(int $boardId): void
    {
        $userId = Auth::id();
        $board = Board::findOrFail($boardId);

        $isMember = BoardMember::where('board_id', $board->id)
            ->where('user_id', $userId)
            ->where('membership_status', 'accepted')
            ->exists();

        if ((int) $board->created_by !== $userId && !$isMember) {
            throw new Exception('Anda tidak memiliki akses ke board ini.');
        }
    }

    /**
     * Membuat activity task otomatis.
     */
    private function createTaskActivity(Task $task, string $message): void
    {
        TaskActivity::create([
            'task_id' => $task->id,
            'user_id' => Auth::id(),
            'activity' => $message,
        ]);
    }
}
