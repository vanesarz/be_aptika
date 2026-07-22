<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('nota_dinas')) {
            Schema::create('nota_dinas', function (Blueprint $table) {
                $table->id();
                $table->string('nomor_surat')->nullable()->unique();
                $table->string('tujuan')->nullable();
                $table->text('perihal')->nullable();
                $table->text('isi_surat')->nullable();
                $table->date('tanggal_surat')->nullable();
                $table->enum('status', ['draft', 'terkirim', 'menunggu_tte'])->default('draft');
                $table->string('lampiran')->nullable();
                $table->text('catatan')->nullable();
                $table->foreignId('user_id')->nullable()->constrained('users')->onDelete('cascade');
                $table->timestamps();
            });
        } else {
            Schema::table('nota_dinas', function (Blueprint $table) {
                if (!Schema::hasColumn('nota_dinas', 'isi_surat')) {
                    $table->text('isi_surat')->nullable()->after('perihal');
                }
                if (!Schema::hasColumn('nota_dinas', 'tanggal_surat')) {
                    $table->date('tanggal_surat')->nullable()->after('isi_surat');
                }
                if (!Schema::hasColumn('nota_dinas', 'lampiran')) {
                    $table->string('lampiran')->nullable()->after('status');
                }
                if (!Schema::hasColumn('nota_dinas', 'catatan')) {
                    $table->text('catatan')->nullable()->after('lampiran');
                }
                if (!Schema::hasColumn('nota_dinas', 'user_id')) {
                    $table->foreignId('user_id')->nullable()->after('catatan')->constrained('users')->onDelete('cascade');
                }
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('nota_dinas');
    }
};

