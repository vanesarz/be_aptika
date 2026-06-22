<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('sidebar_opd_usage', function (Blueprint $table) {
            $table->id();
            $table->foreignId('service_type_id')
                ->constrained('service_types')
                ->cascadeOnDelete()->cascadeOnUpdate();
            $table->foreignId('opd_id')->constrained('general_opd')->cascadeOnDelete();
            $table->tinyInteger('month');
            $table->smallInteger('year');
            $table->integer('active_count');
            $table->timestamps();
        });
    }
    public function down(): void
    {
        Schema::dropIfExists('sidebar_opd_usage');
    }
};
