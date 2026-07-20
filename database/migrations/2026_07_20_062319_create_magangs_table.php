<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('magangs', function (Blueprint $table) {
            $table->id();

            $table->string('nama');
            $table->string('nama_kampus');

            $table->date('tgl_mulai_magang');
            $table->date('tgl_selesai_magang');

            $table->string('cv_magang');

            $table->enum('status_magang', [
                'Belum mulai',
                'Sedang magang',
                'Selesai magang'
            ])->default('Belum mulai');

            $table->enum('sertifikat', [
                'Sudah menerima',
                'Belum menerima'
            ])->default('Belum menerima');

            $table->text('keterangan')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('magangs');
    }
};