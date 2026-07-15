<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Tambah composite index untuk mempercepat query JOIN pada fitur
     * Manajemen Tugas Digital. Single-column index sudah ada di migration
     * 2026_07_14_160000, di sini kita tambah composite index yang jauh
     * lebih efisien untuk query multi-kondisi.
     */
    public function up(): void
    {
        // board_members: composite index untuk query JOIN user + status
        Schema::table('board_members', function (Blueprint $table) {
            // Digunakan di: BoardController, TaskController (cek akses member)
            $table->index(['board_id', 'user_id', 'membership_status'], 'bm_board_user_status_idx');

            // Digunakan di: DashboardController (hitung joined/pending board)
            $table->index(['user_id', 'membership_status'], 'bm_user_status_idx');
        });

        // tasks: composite index untuk query filter user
        Schema::table('tasks', function (Blueprint $table) {
            // Digunakan di: TaskController::myTasks() & DashboardController
            $table->index(['assigned_to', 'status'], 'tasks_assigned_status_idx');
            $table->index(['created_by', 'board_id'], 'tasks_creator_board_idx');

            // Digunakan di: TaskController::index() filter per board
            $table->index(['board_id', 'created_at'], 'tasks_board_created_idx');
        });

        // boards: composite index untuk query filter created_by
        Schema::table('boards', function (Blueprint $table) {
            // Digunakan di: BoardController & DashboardController
            $table->index(['created_by', 'created_at'], 'boards_creator_created_idx');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('board_members', function (Blueprint $table) {
            $table->dropIndex('bm_board_user_status_idx');
            $table->dropIndex('bm_user_status_idx');
        });

        Schema::table('tasks', function (Blueprint $table) {
            $table->dropIndex('tasks_assigned_status_idx');
            $table->dropIndex('tasks_creator_board_idx');
            $table->dropIndex('tasks_board_created_idx');
        });

        Schema::table('boards', function (Blueprint $table) {
            $table->dropIndex('boards_creator_created_idx');
        });
    }
};
