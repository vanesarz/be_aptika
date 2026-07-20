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

class BoardMemberController extends Controller
{
    public function __construct(protected NotificationService $notificationService)
    {
    }

    /**
     * Mengirim permintaan join ke board.
     */
    public function join(Request $request, $boardId = null)
    {
        if (!Auth::check()) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthenticated.',
                'errors' => 'User belum login.',
            ], 401);
        }

        $boardId = $boardId ?? $request->input('board_id');

        if (empty($boardId)) {
            return response()->json([
                'success' => false,
                'message' => 'Board ID wajib diisi.',
                'errors' => 'board_id tidak boleh kosong.',
            ], 422);
        }

        try {
            $board = Board::findOrFail($boardId);

            if ($board->visibility !== 'public') {
                return response()->json([
                    'success' => false,
                    'message' => 'Board ini tidak menerima permintaan join.',
                    'errors' => 'Board bersifat private.',
                ], 403);
            }

            $userId = Auth::id();
            $existingMember = BoardMember::where('board_id', $boardId)
                ->where('user_id', $userId)
                ->first();

            if ($existingMember) {
                return response()->json([
                    'success' => false,
                    'message' => 'Anda sudah pernah mengirim permintaan join atau sudah menjadi member.',
                    'errors' => 'Membership sudah ada.',
                ], 409);
            }

            $member = DB::transaction(function () use ($board, $boardId, $userId) {
                $member = BoardMember::create([
                    'board_id' => $boardId,
                    'user_id' => $userId,
                    'role' => 'member',
                    'membership_status' => 'pending',
                ]);

                $this->notificationService->notifyBoardJoinRequest($board, Auth::user());

                return $member;
            });

            return response()->json([
                'success' => true,
                'message' => 'Permintaan join berhasil dikirim.',
                'data' => $this->formatMember($member->load('user')),
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
                'message' => 'Gagal mengirim permintaan join.',
                'errors' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Menampilkan daftar member board.
     */
    public function members($boardId = null)
    {
        try {
            $board = Board::findOrFail($boardId);
            $members = BoardMember::where('board_id', $board->id)
                ->with('user')
                ->orderByDesc('created_at')
                ->get();

            return response()->json([
                'success' => true,
                'message' => 'Daftar member berhasil diambil.',
                'data' => $members->map(function ($member) {
                    return $this->formatMember($member);
                }),
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
                'message' => 'Gagal mengambil data member.',
                'errors' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Menampilkan daftar permintaan join yang masih pending.
     */
    public function joinRequests($boardId = null)
    {
        try {
            $board = Board::findOrFail($boardId);
            $requests = BoardMember::where('board_id', $board->id)
                ->where('membership_status', 'pending')
                ->with('user')
                ->orderByDesc('created_at')
                ->get();

            return response()->json([
                'success' => true,
                'message' => 'Daftar permintaan join berhasil diambil.',
                'data' => $requests->map(function ($member) {
                    return $this->formatMember($member);
                }),
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
                'message' => 'Gagal mengambil permintaan join.',
                'errors' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Menyetujui permintaan join member. Hanya PM yang boleh melakukan.
     */
    public function approve(Request $request, $boardId = null, $userId = null)
    {
        if (!Auth::check()) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthenticated.',
                'errors' => 'User belum login.',
            ], 401);
        }

        $boardId = $boardId ?? $request->input('board_id');
        $userId = $userId ?? $request->input('user_id');

        try {
            $board = Board::findOrFail($boardId);

            $isAdmin = Auth::user()->role === 'admin';
            if ((int) $board->created_by !== Auth::id() && !$isAdmin) {
                return response()->json([
                    'success' => false,
                    'message' => 'Anda tidak berwenang menyetujui permintaan join.',
                    'errors' => 'Hanya PM yang dapat menyetujui.',
                ], 403);
            }

            $member = BoardMember::where('board_id', $board->id)
                ->where('user_id', $userId)
                ->firstOrFail();

            DB::transaction(function () use ($board, $member) {
                $member->update([
                    'membership_status' => 'accepted',
                    'joined_at' => now(),
                ]);

                $this->notificationService->notifyBoardJoinApproved($board, $member->user()->firstOrFail(), Auth::user());
            });

            return response()->json([
                'success' => true,
                'message' => 'Permintaan join berhasil disetujui.',
                'data' => $this->formatMember($member->fresh()->load('user')),
            ], 200);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Data member atau board tidak ditemukan.',
                'errors' => $e->getMessage(),
            ], 404);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal menyetujui permintaan join.',
                'errors' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Menolak permintaan join member. Hanya PM yang boleh melakukan.
     */
    public function reject(Request $request, $boardId = null, $userId = null)
    {
        if (!Auth::check()) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthenticated.',
                'errors' => 'User belum login.',
            ], 401);
        }

        $boardId = $boardId ?? $request->input('board_id');
        $userId = $userId ?? $request->input('user_id');

        try {
            $board = Board::findOrFail($boardId);

            $isAdmin = Auth::user()->role === 'admin';
            if ((int) $board->created_by !== Auth::id() && !$isAdmin) {
                return response()->json([
                    'success' => false,
                    'message' => 'Anda tidak berwenang menolak permintaan join.',
                    'errors' => 'Hanya PM yang dapat menolak.',
                ], 403);
            }

            $member = BoardMember::where('board_id', $board->id)
                ->where('user_id', $userId)
                ->firstOrFail();

            DB::transaction(function () use ($board, $member) {
                $member->update([
                    'membership_status' => 'rejected',
                ]);

                $this->notificationService->notifyBoardJoinRejected($board, $member->user()->firstOrFail(), Auth::user());
            });

            return response()->json([
                'success' => true,
                'message' => 'Permintaan join berhasil ditolak.',
                'data' => $this->formatMember($member->fresh()->load('user')),
            ], 200);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Data member atau board tidak ditemukan.',
                'errors' => $e->getMessage(),
            ], 404);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal menolak permintaan join.',
                'errors' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Member keluar dari board. PM tidak boleh keluar.
     */
    public function leave(Request $request, $boardId = null)
    {
        if (!Auth::check()) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthenticated.',
                'errors' => 'User belum login.',
            ], 401);
        }

        $boardId = $boardId ?? $request->input('board_id');

        try {
            $member = BoardMember::where('board_id', $boardId)
                ->where('user_id', Auth::id())
                ->firstOrFail();

            if ($member->role === 'pm') {
                return response()->json([
                    'success' => false,
                    'message' => 'PM tidak dapat keluar dari board.',
                    'errors' => 'PM wajib tetap berada di board.',
                ], 403);
            }

            DB::transaction(function () use ($member) {
                $member->delete();
            });

            return response()->json([
                'success' => true,
                'message' => 'Anda berhasil keluar dari board.',
                'data' => null,
            ], 200);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Membership tidak ditemukan.',
                'errors' => $e->getMessage(),
            ], 404);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal keluar dari board.',
                'errors' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Menampilkan daftar semua resource (tidak digunakan pada API).
     */
    public function index()
    {
        return response()->json([
            'success' => false,
            'message' => 'Method not supported.',
            'errors' => 'Gunakan endpoint members atau joinRequests.',
        ], 405);
    }

    /**
     * Menampilkan form pembuatan member (tidak digunakan pada API).
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
     * Membuat member secara manual (tidak digunakan pada API).
     */
    public function store(Request $request)
    {
        return response()->json([
            'success' => false,
            'message' => 'Method not supported.',
            'errors' => 'Gunakan endpoint join.',
        ], 405);
    }

    /**
     * Menampilkan detail member (tidak digunakan pada API).
     */
    public function show(string $id)
    {
        return response()->json([
            'success' => false,
            'message' => 'Method not supported.',
            'errors' => 'Endpoint ini tidak menyediakan detail member.',
        ], 405);
    }

    /**
     * Menampilkan form edit member (tidak digunakan pada API).
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
     * Memperbarui member secara manual (tidak digunakan pada API).
     */
    public function update(Request $request, string $id)
    {
        return response()->json([
            'success' => false,
            'message' => 'Method not supported.',
            'errors' => 'Gunakan endpoint approve atau reject.',
        ], 405);
    }

    /**
     * Menghapus member secara manual (tidak digunakan pada API).
     */
    public function destroy(string $id)
    {
        return response()->json([
            'success' => false,
            'message' => 'Method not supported.',
            'errors' => 'Gunakan endpoint leave.',
        ], 405);
    }

    /**
     * Memformat data membership agar status API konsisten.
     */
    private function formatMember(BoardMember $member): array
    {
        return [
            'id' => $member->id,
            'board_id' => $member->board_id,
            'user_id' => $member->user_id,
            'role' => $member->role,
            'membership_status' => $member->membership_status === 'accepted' ? 'joined' : $member->membership_status,
            'joined_at' => $member->joined_at,
            'user' => $member->user,
        ];
    }
}
