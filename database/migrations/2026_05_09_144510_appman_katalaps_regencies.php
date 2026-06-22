<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('appman_katalaps_regencies', function (Blueprint $table) {
            $table->id();
            $table->foreignId('service_type_id')
                ->constrained('service_types')
                ->cascadeOnDelete()->cascadeOnUpdate();
            $table->foreignId('regency_id')->constrained('regencies_name');
            $table->tinyInteger('month');
            $table->smallInteger('year');
            $table->integer('app_count');
            $table->timestamps();
        });
    }
    public function down(): void
    {
        Schema::dropIfExists('appman_katalaps_regencies');
    }
};
