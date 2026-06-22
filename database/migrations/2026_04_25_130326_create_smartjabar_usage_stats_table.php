<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('smartjabar_usage_stats', function (Blueprint $table) {
            $table->id();
            // $table->unsignedBigInteger('service_type_id');
            $table->foreignId('service_type_id')
                ->constrained('service_types')
                ->cascadeOnDelete()->cascadeOnUpdate();
            // $table->unsignedBigInteger('opd_id');
            $table->foreignId('opd_id')->constrained('general_opd')->cascadeOnDelete();
            $table->tinyInteger('month');
            $table->smallInteger('year');
            $table->integer('total_asn')->default(0);
            $table->integer('active_users')->default(0);
            $table->timestamps();
        });
    }
    public function down(): void
    {
        Schema::dropIfExists('smartjabar_usage_stats');
    }
};
