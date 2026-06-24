<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('form_perubahan_it', function (Blueprint $table) {
            $table->id();
            $table->string('no_rfc')->unique()->nullable();
            
            // Informasi Pemohon
            $table->string('pemohon');
            $table->string('unit_kerja')->nullable();
            $table->foreignId('perangkat_daerah_id')->nullable()->constrained('general_opd')->nullOnDelete(); // Relasi ke GeneralOpd
            $table->string('nomor_kontak');
            
            // Informasi Data (Disimpan dalam bentuk JSON array karena checkbox bisa pilih lebih dari 1)
            $table->json('jenis_perubahan')->nullable(); 
            $table->json('jenis_permohonan')->nullable();
            
            // Detail Aplikasi
            $table->string('nama_aplikasi')->nullable();
            $table->text('deskripsi_aplikasi')->nullable();
            $table->string('alamat_aplikasi')->nullable();
            $table->string('alamat_repository')->nullable();
            
            // Perubahan yang diharapkan
            $table->text('latar_belakang')->nullable();
            $table->text('rincian_perubahan')->nullable();
            $table->text('risiko_tidak_dilakukan')->nullable();
            
            // Kriteria & Keterangan
            $table->json('kriteria_risiko')->nullable(); // JSON Array
            $table->text('keterangan')->nullable();
            $table->text('solusi_diharapkan')->nullable();
            $table->text('risiko_perubahan')->nullable();
            $table->text('alternatif_perubahan')->nullable();
            
            // Informasi Tambahan
            $table->string('biaya_perubahan')->nullable();
            $table->string('waktu_perubahan')->nullable();
            $table->string('lampiran')->nullable(); // Bisa string path jika nanti upload file
            $table->date('tanggal_permohonan');
            
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('form_perubahan_it');
    }
};