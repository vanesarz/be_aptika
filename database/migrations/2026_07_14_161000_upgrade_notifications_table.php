<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('notifications', function (Blueprint $table) {

            // Tambah kolom type
            if (!Schema::hasColumn('notifications', 'type')) {
                $table->string('type')
                    ->default('SYSTEM')
                    ->after('user_id');
            }

            // Tambah kolom is_read
            if (!Schema::hasColumn('notifications', 'is_read')) {
                $table->boolean('is_read')
                    ->default(false)
                    ->after('message');
            }

            // Tambah kolom read_at
            if (!Schema::hasColumn('notifications', 'read_at')) {
                $table->timestamp('read_at')
                    ->nullable()
                    ->after('is_read');
            }

            // Tambah board_id
            if (!Schema::hasColumn('notifications', 'board_id')) {
                $table->unsignedBigInteger('board_id')
                    ->nullable()
                    ->after('read_at');
            }

            // Tambah task_id
            if (!Schema::hasColumn('notifications', 'task_id')) {
                $table->unsignedBigInteger('task_id')
                    ->nullable()
                    ->after('board_id');
            }

            // Tambah created_by_user_id
            if (!Schema::hasColumn('notifications', 'created_by_user_id')) {
                $table->unsignedBigInteger('created_by_user_id')
                    ->nullable()
                    ->after('task_id');
            }
        });

        // Foreign key board_id
        if (
            Schema::hasColumn('notifications', 'board_id') &&
            !$this->foreignKeyExists('notifications', 'notifications_board_id_foreign')
        ) {
            Schema::table('notifications', function (Blueprint $table) {
                $table->foreign('board_id')
                    ->references('id')
                    ->on('boards')
                    ->nullOnDelete();
            });
        }

        // Foreign key task_id
        if (
            Schema::hasColumn('notifications', 'task_id') &&
            !$this->foreignKeyExists('notifications', 'notifications_task_id_foreign')
        ) {
            Schema::table('notifications', function (Blueprint $table) {
                $table->foreign('task_id')
                    ->references('id')
                    ->on('tasks')
                    ->nullOnDelete();
            });
        }

        // Foreign key created_by_user_id
        if (
            Schema::hasColumn('notifications', 'created_by_user_id') &&
            !$this->foreignKeyExists('notifications', 'notifications_created_by_user_id_foreign')
        ) {
            Schema::table('notifications', function (Blueprint $table) {
                $table->foreign('created_by_user_id')
                    ->references('id')
                    ->on('users')
                    ->nullOnDelete();
            });
        }

        // Index user_id,is_read
        if (!$this->indexExists('notifications', 'notifications_user_id_is_read_index')) {
            Schema::table('notifications', function (Blueprint $table) {
                $table->index(['user_id', 'is_read']);
            });
        }

        // Index created_at
        if (!$this->indexExists('notifications', 'notifications_created_at_index')) {
            Schema::table('notifications', function (Blueprint $table) {
                $table->index('created_at');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('notifications', function (Blueprint $table) {

            if ($this->foreignKeyExists('notifications', 'notifications_board_id_foreign')) {
                $table->dropForeign(['board_id']);
            }

            if ($this->foreignKeyExists('notifications', 'notifications_task_id_foreign')) {
                $table->dropForeign(['task_id']);
            }

            if ($this->foreignKeyExists('notifications', 'notifications_created_by_user_id_foreign')) {
                $table->dropForeign(['created_by_user_id']);
            }

            if ($this->indexExists('notifications', 'notifications_user_id_is_read_index')) {
                $table->dropIndex(['user_id', 'is_read']);
            }

            if ($this->indexExists('notifications', 'notifications_created_at_index')) {
                $table->dropIndex(['created_at']);
            }

            $columns = [];

            foreach ([
                'type',
                'is_read',
                'read_at',
                'board_id',
                'task_id',
                'created_by_user_id'
            ] as $column) {
                if (Schema::hasColumn('notifications', $column)) {
                    $columns[] = $column;
                }
            }

            if (!empty($columns)) {
                $table->dropColumn($columns);
            }
        });
    }

    /**
     * Cek apakah foreign key sudah ada.
     */
    private function foreignKeyExists($table, $foreign)
    {
        $database = DB::getDatabaseName();

        return DB::table('information_schema.TABLE_CONSTRAINTS')
            ->where('TABLE_SCHEMA', $database)
            ->where('TABLE_NAME', $table)
            ->where('CONSTRAINT_NAME', $foreign)
            ->exists();
    }

    /**
     * Cek apakah index sudah ada.
     */
    private function indexExists($table, $index)
    {
        $database = DB::getDatabaseName();

        return DB::table('information_schema.STATISTICS')
            ->where('TABLE_SCHEMA', $database)
            ->where('TABLE_NAME', $table)
            ->where('INDEX_NAME', $index)
            ->exists();
    }
};