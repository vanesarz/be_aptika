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
        Schema::table('detail_perjalanan', function (Blueprint $table) {
            $table->foreignId('rekening_id')->nullable()->change();
            $table->string('alat_angkutan')->nullable()->change();
            $table->decimal('uang_harian', 15, 2)->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('detail_perjalanan', function (Blueprint $table) {
            $table->foreignId('rekening_id')->nullable(false)->change();
            $table->string('alat_angkutan')->nullable(false)->change();
            $table->decimal('uang_harian', 15, 2)->nullable(false)->change();
        });
    }
};
