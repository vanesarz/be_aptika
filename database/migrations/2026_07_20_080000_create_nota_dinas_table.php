<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('nota_dinas', function (Blueprint $table) {
            $table->id();

            $table->string('nomor_surat')->unique();
            $table->string('tujuan');
            $table->text('perihal');
            $table->text('isi_surat')->nullable();
            $table->date('tanggal_surat');

            $table->enum('status', [
                'draft',
                'terkirim',
                'menunggu_tte'
            ])->default('draft');

            $table->string('lampiran')->nullable();
            $table->text('catatan')->nullable();

            $table->foreignId('user_id')
                  ->constrained('users')
                  ->onDelete('cascade');

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('nota_dinas');
    }
};
