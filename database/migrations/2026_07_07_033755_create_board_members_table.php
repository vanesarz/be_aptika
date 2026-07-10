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
        Schema::create('board_members', function (Blueprint $table) {
            $table->id();
            $table->foreignId('board_id')
                ->constrained()
                ->cascadeOnDelete();
            $table->foreignId('user_id')
                ->constrained()
                ->cascadeOnDelete();
            $table->enum('role',[
                'pm',
                'member'
            ])->default('member');
            $table->enum('membership_status',[
                'pending',
                'accepted',
                'rejected'
            ])->default('pending');
            $table->timestamp('joined_at')->nullable();
            $table->timestamps();
            $table->unique([
                'board_id',
                'user_id'
            ]);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('board_members');
    }
};
