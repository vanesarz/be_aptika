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
        Schema::create('nota_dinas', function (Blueprint $table) {
            $table->id();
            $table->string('nomor_surat')->nullable();
            $table->date('tanggal')->nullable();
            $table->string('pengirim')->nullable();
            $table->string('tujuan')->nullable();
            $table->string('perihal')->nullable();
            $table->text('isi')->nullable();
            $table->string('status')->default('DRAF');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('nota_dinas');
    }
};
