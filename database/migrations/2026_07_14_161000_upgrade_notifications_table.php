<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Mengupgrade tabel notifications agar mendukung tipe, link ke board/task,
     * pembuat notifikasi, dan kolom read yang sesuai dengan ekspektasi frontend.
     */
    public function up(): void
    {
        Schema::table('notifications', function (Blueprint $table) {
            // Tambah kolom type
            $table->string('type')->default('SYSTEM')->after('user_id');

            // Tambah kolom is_read sebagai alias yang sesuai frontend (gantikan 'read')
            $table->boolean('is_read')->default(false)->after('message');
            $table->timestamp('read_at')->nullable()->after('is_read');

            // Referensi opsional ke board dan task
            $table->unsignedBigInteger('board_id')->nullable()->after('read_at');
            $table->unsignedBigInteger('task_id')->nullable()->after('board_id');
            $table->unsignedBigInteger('created_by_user_id')->nullable()->after('task_id');

            // Foreign keys (soft — nullable jadi tidak butuh cascade wajib)
            $table->foreign('board_id')->references('id')->on('boards')->onDelete('set null');
            $table->foreign('task_id')->references('id')->on('tasks')->onDelete('set null');
            $table->foreign('created_by_user_id')->references('id')->on('users')->onDelete('set null');

            // Index untuk query cepat
            $table->index(['user_id', 'is_read']);
            $table->index('created_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('notifications', function (Blueprint $table) {
            $table->dropForeign(['board_id']);
            $table->dropForeign(['task_id']);
            $table->dropForeign(['created_by_user_id']);

            $table->dropIndex(['user_id', 'is_read']);
            $table->dropIndex(['created_at']);

            $table->dropColumn([
                'type',
                'is_read',
                'read_at',
                'board_id',
                'task_id',
                'created_by_user_id',
            ]);
        });
    }
};
