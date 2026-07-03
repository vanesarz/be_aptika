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
        Schema::create('spd_peserta', function (Blueprint $table) {
            $table->id();
            $table->foreignId('detail_perjalanan_id')
                ->constrained('detail_perjalanan')
                ->cascadeOnDelete();
            $table->foreignId('pegawai_id')
                ->constrained('pegawai')
                ->cascadeOnDelete();
            $table->string('nomor_spd');
            $table->integer('lama_hari')->default(0);
            $table->decimal('total_uang',15,2)->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('spd_peserta');
    }
};
