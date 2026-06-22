<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('smartjabar_joined_apps', function (Blueprint $table) {
            $table->id();
            // $table->unsignedBigInteger('service_type_id');
            $table->foreignId('service_type_id')
                ->constrained('service_types')
                ->cascadeOnDelete()->cascadeOnUpdate();
            $table->smallInteger('year');
            $table->tinyInteger('month');
            $table->integer('total_apps')->default(0);
            $table->timestamps();
        });
    }
    public function down(): void
    {
        Schema::dropIfExists('smartjabar_joined_apps');
    }
};
