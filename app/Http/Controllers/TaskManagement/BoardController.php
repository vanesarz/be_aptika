<?php

namespace App\Http\Controllers\TaskManagement;

use App\Http\Controllers\Controller;
use App\Models\Board;
use App\Models\BoardMember;
use App\Services\TaskManagement\NotificationService;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class BoardController extends Controller
{
    public function __construct(protected NotificationService $notificationService)
    {
    }

    /**
     * Menampilkan daftar board yang dapat diakses user saat ini.
     */
    public function index(Request $request)
    {
        try {
            if (!Auth::check()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthenticated.',
                    'errors' => 'User belum login.',
                ], 401);
            }

            $userId = Auth::id();
            $isAdmin = Auth::user()->role === 'admin';


            $boards = Board::query()
                ->select('boards.*')
                ->when(!$isAdmin, function ($query) use ($userId) {
                    // Gunakan LEFT JOIN ke board_members agar lebih efisien daripada orWhereHas
                    $query->leftJoin('board_members as bm_access', function ($join) use ($userId) {
                        $join->on('bm_access.board_id', '=', 'boards.id')
                            ->where('bm_access.user_id', '=', $userId);
                    })
                    ->where(function ($q) use ($userId) {
                        $q->where('boards.created_by', $userId)
                          ->orWhere('boards.visibility', 'public')
                          ->orWhereNotNull('bm_access.id');
                    });
                })
                ->with([
                    'pm:id,name',
                    'members:id,board_id,user_id,role,membership_status',
                    'members.user:id,name',
                ])
                ->withCount('tasks')
                ->orderByDesc('boards.created_at')
                ->get();

            return response()->json([
                'success' => true,
                'message' => 'Daftar board berhasil diambil.',
                'data' => $boards,
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil data board.',
                'errors' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Menampilkan detail board berdasarkan ID.
     */
    public function show(string $id)
    {
        try {
            $board = Board::with([
                'pm:id,name',
                'members:id,board_id,user_id,membership_status',
                'members.user:id,name',
            ])
                ->withCount('tasks')
                ->findOrFail($id);

            return response()->json([
                'success' => true,
                'message' => 'Board berhasil ditemukan.',
                'data' => $board,
            ], 200);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Board tidak ditemukan.',
                'errors' => $e->getMessage(),
            ], 404);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil detail board.',
                'errors' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Membuat board baru dan otomatis menjadikan pembuat sebagai PM.
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
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'status' => 'nullable|in:active,completed,archived',
            'visibility' => 'nullable|in:private,public',
            'allow_join' => 'nullable|boolean',
        ]);

        try {
            $board = DB::transaction(function () use ($validated) {
                $userId = Auth::id();
                $visibility = $this->resolveVisibility($validated);

                $board = Board::create([
                    'name' => $validated['name'],
                    'description' => $validated['description'] ?? null,
                    'created_by' => $userId,
                    'start_date' => $validated['start_date'] ?? null,
                    'end_date' => $validated['end_date'] ?? null,
                    'status' => $validated['status'] ?? 'active',
                    'visibility' => $visibility,
                ]);

                BoardMember::create([
                    'board_id' => $board->id,
                    'user_id' => $userId,
                    'role' => 'pm',
                    'membership_status' => 'accepted',
                    'joined_at' => now(),
                ]);

                return $board;
            });

            return response()->json([
                'success' => true,
                'message' => 'Board berhasil dibuat.',
                'data' => $board->load([
                    'pm:id,name',
                    'members:id,board_id,user_id,membership_status',
                    'members.user:id,name',
                ]),
            ], 201);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal membuat board.',
                'errors' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Memperbarui board. Hanya PM yang boleh melakukan update.
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
            'name' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'status' => 'nullable|in:active,completed,archived',
            'visibility' => 'nullable|in:private,public',
            'allow_join' => 'nullable|boolean',
        ]);

        try {
            $board = Board::findOrFail($id);

            $isAdmin = Auth::user()->role === 'admin';
            if ((int) $board->created_by !== Auth::id() && !$isAdmin) {
                return response()->json([
                    'success' => false,
                    'message' => 'Anda tidak berwenang mengubah board ini.',
                    'errors' => 'Hanya PM yang dapat mengubah board.',
                ], 403);
            }

            DB::transaction(function () use ($board, $validated) {
                $payload = [
                    'name' => $validated['name'] ?? $board->name,
                    'description' => array_key_exists('description', $validated) ? $validated['description'] : $board->description,
                    'start_date' => array_key_exists('start_date', $validated) ? $validated['start_date'] : $board->start_date,
                    'end_date' => array_key_exists('end_date', $validated) ? $validated['end_date'] : $board->end_date,
                    'status' => array_key_exists('status', $validated) ? $validated['status'] : $board->status,
                    'visibility' => $this->resolveVisibility($validated, $board->visibility),
                ];

                $board->update($payload);

                if (isset($validated['status']) && $validated['status'] === 'archived') {
                    $recipients = BoardMember::where('board_id', $board->id)
                        ->where('membership_status', 'accepted')
                        ->with('user')
                        ->get()
                        ->map(fn ($member) => $member->user)
                        ->filter()
                        ->all();

                    $this->notificationService->notifyBoardArchived($board, $recipients);
                }
            });

            return response()->json([
                'success' => true,
                'message' => 'Board berhasil diperbarui.',
                'data' => $board->fresh()->load([
                    'pm:id,name',
                    'members:id,board_id,user_id,membership_status',
                    'members.user:id,name',
                ]),
            ], 200);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Board tidak ditemukan.',
                'errors' => $e->getMessage(),
            ], 404);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal memperbarui board.',
                'errors' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Menghapus board. Hanya PM yang boleh melakukan delete.
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
            $board = Board::findOrFail($id);

            $isAdmin = Auth::user()->role === 'admin';
            if ((int) $board->created_by !== Auth::id() && !$isAdmin) {
                return response()->json([
                    'success' => false,
                    'message' => 'Anda tidak berwenang menghapus board ini.',
                    'errors' => 'Hanya PM yang dapat menghapus board.',
                ], 403);
            }

            DB::transaction(function () use ($board) {
                $board->delete();
            });

            return response()->json([
                'success' => true,
                'message' => 'Board berhasil dihapus.',
                'data' => null,
            ], 200);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Board tidak ditemukan.',
                'errors' => $e->getMessage(),
            ], 404);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal menghapus board.',
                'errors' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Mengembalikan form pembuatan board (tidak digunakan pada API).
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
     * Mengembalikan form edit board (tidak digunakan pada API).
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
     * Menentukan visibilitas board berdasarkan allow_join atau visibility.
     */
    private function resolveVisibility(array $validated, ?string $fallback = null): string
    {
        if (array_key_exists('allow_join', $validated)) {
            return $validated['allow_join'] ? 'public' : 'private';
        }

        if (array_key_exists('visibility', $validated)) {
            return $validated['visibility'];
        }

        return $fallback ?? 'private';
    }
}
