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
        Schema::create('tasks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('board_id')
                ->constrained()
                ->cascadeOnDelete();
            $table->foreignId('created_by')
                ->constrained('users');
            $table->foreignId('assigned_to')
                ->nullable()
                ->constrained('users');
            $table->string('title');
            $table->text('description')->nullable();
            $table->enum('priority',[
                'low',
                'medium',
                'high'
            ])->default('medium');
            $table->enum('status',[
                'todo',
                'in_progress',
                'in_review',
                'done'
            ])->default('todo');
            $table->date('start_date')->nullable();
            $table->date('due_date')->nullable();
            $table->dateTime('completed_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tasks');
    }
};
