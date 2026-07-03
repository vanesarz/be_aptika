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
        Schema::create('detail_perjalanan', function (Blueprint $table) {
            $table->id();
            $table->string('travel_code')->unique();
            $table->string('kegiatan');
            $table->string('sub_kegiatan');
            $table->string('tujuan');
            $table->date('tanggal_berangkat');
            $table->date('tanggal_kembali');
            $table->decimal('uang_harian',15,2);
            $table->foreignId('rekening_id')
                ->constrained('rekening')
                ->cascadeOnDelete();
            $table->string('alat_angkutan');
            $table->text('deskripsi')->nullable();
            $table->enum('status', [
                'belum_selesai',
                'selesai'
            ])->default('belum_selesai');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('detail_perjalanan');
    }
};
