<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('spds', function (Blueprint $table) {
            $table->id();
            $table->string('no_spd')->nullable();
            $table->string('pejabat_pemberi')->nullable();
            $table->string('nama');
            $table->string('nip');
            $table->string('pangkat')->nullable();
            $table->string('jabatan')->nullable();
            $table->text('maksud')->nullable();
            $table->string('angkutan')->nullable();
            $table->string('tempat_berangkat')->nullable();
            $table->string('tempat_tujuan')->nullable();
            $table->date('tgl_mulai')->nullable();
            $table->date('tgl_selesai')->nullable();
            $table->integer('durasi')->nullable();
            $table->json('pengikut')->nullable();
            $table->bigInteger('anggaran')->nullable();
            $table->string('status')->default('DRAF');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('spds');
    }
};
