<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('nota_dinas', function (Blueprint $table) {
            if (!Schema::hasColumn('nota_dinas', 'dari')) {
                $table->string('dari')->default('Biro Umum dan Administrasi')->after('tujuan');
            }
            if (!Schema::hasColumn('nota_dinas', 'tembusan')) {
                $table->string('tembusan')->nullable()->after('dari');
            }
            if (!Schema::hasColumn('nota_dinas', 'sifat_surat')) {
                $table->enum('sifat_surat', ['biasa', 'penting', 'rahasia'])->default('biasa')->after('tembusan');
            }
            if (!Schema::hasColumn('nota_dinas', 'isi_lampiran')) {
                $table->text('isi_lampiran')->nullable()->after('isi_surat');
            }
        });
    }

    public function down(): void
    {
        Schema::table('nota_dinas', function (Blueprint $table) {
            $colsToDrop = array_filter(['dari', 'tembusan', 'sifat_surat', 'isi_lampiran'], function ($col) {
                return Schema::hasColumn('nota_dinas', $col);
            });
            if (!empty($colsToDrop)) {
                $table->dropColumn($colsToDrop);
            }
        });
    }
};

