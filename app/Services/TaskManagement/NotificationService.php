<?php

namespace App\Services\TaskManagement;

use App\Models\Board;
use App\Models\BoardMember;
use App\Models\Notification;
use App\Models\Task;
use App\Models\User;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class NotificationService
{
    public function create(array $payload): ?Notification
    {
        try {
            return DB::transaction(function () use ($payload) {
                return Notification::create([
                    'user_id' => $payload['user_id'],
                    'board_id' => $payload['board_id'] ?? null,
                    'task_id' => $payload['task_id'] ?? null,
                    'type' => $payload['type'],
                    'title' => $payload['title'],
                    'message' => $payload['message'],
                    'is_read' => false,
                    'created_by' => $payload['created_by'] ?? null,
                ]);
            });
        } catch (\Throwable $e) {
            Log::warning('Notification create failed', ['error' => $e->getMessage()]);

            return null;
        }
    }

    public function notifyBoardJoinRequest(Board $board, User $actor): void
    {
        $pm = $board->pm;
        if (!$pm || (int) $pm->id === (int) $actor->id) {
            return;
        }

        $this->create([
            'user_id' => $pm->id,
            'board_id' => $board->id,
            'type' => 'JOIN_REQUEST',
            'title' => 'Permintaan bergabung board',
            'message' => sprintf('%s mengirim permintaan untuk bergabung ke board %s.', $actor->name, $board->name),
            'created_by' => $actor->id,
        ]);
    }

    public function notifyBoardJoinApproved(Board $board, User $recipient, User $actor): void
    {
        $this->create([
            'user_id' => $recipient->id,
            'board_id' => $board->id,
            'type' => 'JOIN_APPROVED',
            'title' => 'Permintaan bergabung disetujui',
            'message' => sprintf('%s menyetujui permintaan bergabung Anda ke board %s.', $actor->name, $board->name),
            'created_by' => $actor->id,
        ]);
    }

    public function notifyBoardJoinRejected(Board $board, User $recipient, User $actor): void
    {
        $this->create([
            'user_id' => $recipient->id,
            'board_id' => $board->id,
            'type' => 'JOIN_REJECTED',
            'title' => 'Permintaan bergabung ditolak',
            'message' => sprintf('%s menolak permintaan bergabung Anda ke board %s.', $actor->name, $board->name),
            'created_by' => $actor->id,
        ]);
    }

    public function notifyTaskAssigned(Task $task, User $recipient): void
    {
        $actor = $task->creator ?? $task->board?->pm;
        $this->create([
            'user_id' => $recipient->id,
            'board_id' => $task->board_id,
            'task_id' => $task->id,
            'type' => 'TASK_ASSIGNED',
            'title' => 'Tugas baru ditugaskan',
            'message' => sprintf('%s menugaskan Anda untuk tugas %s.', $actor?->name ?? 'Sistem', $task->title),
            'created_by' => $actor?->id ?? null,
        ]);
    }

    public function notifyTaskUpdated(Task $task, User $actor, array $recipients): void
    {
        foreach ($recipients as $recipient) {
            if ((int) $recipient->id === (int) $actor->id) {
                continue;
            }

            $this->create([
                'user_id' => $recipient->id,
                'board_id' => $task->board_id,
                'task_id' => $task->id,
                'type' => 'TASK_UPDATED',
                'title' => 'Tugas diperbarui',
                'message' => sprintf('%s memperbarui tugas %s.', $actor->name, $task->title),
                'created_by' => $actor->id,
            ]);
        }
    }

    public function notifyTaskComment(Task $task, User $actor, array $recipients): void
    {
        foreach ($recipients as $recipient) {
            if ((int) $recipient->id === (int) $actor->id) {
                continue;
            }

            $this->create([
                'user_id' => $recipient->id,
                'board_id' => $task->board_id,
                'task_id' => $task->id,
                'type' => 'TASK_COMMENT',
                'title' => 'Komentar baru',
                'message' => sprintf('%s mengomentari tugas %s.', $actor->name, $task->title),
                'created_by' => $actor->id,
            ]);
        }
    }

    public function notifyStatusChanged(Task $task, array $recipients): void
    {
        foreach ($recipients as $recipient) {
            $this->create([
                'user_id' => $recipient->id,
                'board_id' => $task->board_id,
                'task_id' => $task->id,
                'type' => 'TASK_STATUS_CHANGED',
                'title' => 'Status tugas berubah',
                'message' => sprintf('Status tugas %s berubah.', $task->title),
                'created_by' => $task->created_by,
            ]);
        }
    }

    public function notifyBoardArchived(Board $board, array $recipients): void
    {
        foreach ($recipients as $recipient) {
            $this->create([
                'user_id' => $recipient->id,
                'board_id' => $board->id,
                'type' => 'BOARD_ARCHIVED',
                'title' => 'Board diarsipkan',
                'message' => sprintf('Board %s telah diarsipkan.', $board->name),
                'created_by' => $board->created_by,
            ]);
        }
    }

    public function notifyDueSoon(Task $task, User $recipient): void
    {
        $this->create([
            'user_id' => $recipient->id,
            'board_id' => $task->board_id,
            'task_id' => $task->id,
            'type' => 'TASK_DUE_SOON',
            'title' => 'Tugas akan jatuh tempo',
            'message' => sprintf('Tugas %s akan jatuh tempo besok.', $task->title),
            'created_by' => $task->created_by,
        ]);
    }

    public function getForUser(int $userId, int $limit = 15, int $offset = 0): Collection
    {
        return Notification::query()
            ->where('user_id', $userId)
            ->with(['board', 'task', 'createdBy', 'receiver'])
            ->orderByDesc('created_at')
            ->skip($offset)
            ->limit($limit)
            ->get();
    }

    public function unreadCount(int $userId): int
    {
        return Notification::query()
            ->where('user_id', $userId)
            ->where('is_read', false)
            ->count();
    }

    public function markAsRead(int $notificationId, int $userId): bool
    {
        $notification = Notification::where('id', $notificationId)
            ->where('user_id', $userId)
            ->first();

        if (!$notification) {
            return false;
        }

        $notification->update([
            'is_read' => true,
            'read_at' => now(),
        ]);

        return true;
    }

    public function markAllAsRead(int $userId): int
    {
        return Notification::where('user_id', $userId)
            ->where('is_read', false)
            ->update([
                'is_read' => true,
                'read_at' => now(),
            ]);
    }

    public function delete(int $notificationId, int $userId): bool
    {
        $notification = Notification::where('id', $notificationId)
            ->where('user_id', $userId)
            ->first();

        if (!$notification) {
            return false;
        }

        $notification->delete();

        return true;
    }
}
