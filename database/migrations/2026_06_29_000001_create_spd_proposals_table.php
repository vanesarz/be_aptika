<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('spd_proposals', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('orderer_name');
            $table->string('orderer_nip')->nullable();
            $table->string('orderer_position')->nullable();
            $table->string('employee_name');
            $table->string('employee_nip')->nullable();
            $table->string('employee_rank')->nullable();
            $table->string('employee_position')->nullable();
            $table->text('purpose');
            $table->string('transportation')->nullable();
            $table->string('departure_place')->nullable();
            $table->string('destination')->nullable();
            $table->date('start_date');
            $table->date('end_date');
            $table->unsignedBigInteger('budget_estimate')->nullable();
            $table->json('followers')->nullable();
            $table->string('status')->default('draft');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('spd_proposals');
    }
};
