<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('boards', function (Blueprint $table) {
            $table->index('visibility');
            $table->index('created_at');
        });

        Schema::table('board_members', function (Blueprint $table) {
            $table->index('membership_status');
        });

        Schema::table('tasks', function (Blueprint $table) {
            $table->index('status');
            $table->index('created_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('boards', function (Blueprint $table) {
            $table->dropIndex(['visibility']);
            $table->dropIndex(['created_at']);
        });

        Schema::table('board_members', function (Blueprint $table) {
            $table->dropIndex(['membership_status']);
        });

        Schema::table('tasks', function (Blueprint $table) {
            $table->dropIndex(['status']);
            $table->dropIndex(['created_at']);
        });
    }
};
