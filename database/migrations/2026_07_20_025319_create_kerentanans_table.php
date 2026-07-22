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
        if (!Schema::hasTable('kerentanans')) {
            Schema::create('kerentanans', function (Blueprint $table) {
                $table->id();
                $table->string('nomor_surat')->nullable();
                $table->date('tanggal')->nullable();
                $table->string('aplikasi')->nullable();
                $table->string('url')->nullable();
                $table->string('tingkat_kerentanan')->nullable();
                $table->string('perihal')->nullable();
                $table->text('deskripsi')->nullable();
                $table->string('status')->default('DRAF');
                $table->timestamps();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('kerentanans');
    }
};
