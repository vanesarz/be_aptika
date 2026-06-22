<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('appman_inventory_stats', function (Blueprint $table) {
            $table->id();
            $table->foreignId('service_type_id')
                ->constrained('service_types')
                ->cascadeOnDelete()->cascadeOnUpdate();
            $table->tinyInteger('month');
            $table->smallInteger('year');
            $table->integer('total_apps');
            $table->integer('profile');
            $table->integer('repository');
            $table->integer('registered_pse');
            $table->timestamps();
        });
    }
    public function down(): void
    {
        Schema::dropIfExists('appman_inventory_stats');
    }
};
