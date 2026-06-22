<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('intop_service_catalogs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('service_type_id')
                ->constrained('service_types')
                ->cascadeOnDelete()->cascadeOnUpdate();
            $table->tinyInteger('month')->unsigned();
            $table->smallInteger('year')->unsigned();
            $table->integer('adm_service_count')->default(0)
                ->comment('Jumlah layanan administrasi pemerintahan');
            $table->integer('public_service_count')->default(0)
                ->comment('Jumlah layanan publik');
            $table->decimal('target_abs', 8, 2)->default(0)
                ->comment('Target absolut');
            $table->decimal('achievement_abs', 8, 2)->default(0)
                ->comment('Capaian absolut');
            $table->timestamps();
        });
        //
    }

    public function down(): void
    {
        Schema::dropIfExists('intop_service_catalogs');
    }
};
