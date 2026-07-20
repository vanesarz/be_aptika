<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('nota_dinas', function (Blueprint $table) {
            $table->string('dari')->default('Biro Umum dan Administrasi')->after('tujuan');
            $table->string('tembusan')->nullable()->after('dari');
            $table->enum('sifat_surat', ['biasa', 'penting', 'rahasia'])->default('biasa')->after('tembusan');
            $table->text('isi_lampiran')->nullable()->after('isi_surat');
        });
    }

    public function down(): void
    {
        Schema::table('nota_dinas', function (Blueprint $table) {
            $table->dropColumn(['dari', 'tembusan', 'sifat_surat', 'isi_lampiran']);
        });
    }
};
