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
        Schema::table('permohonan_tis', function (Blueprint $table) {
            if (!Schema::hasColumn('permohonan_tis', 'no_rfc')) {
                $table->string('no_rfc')->nullable()->after('id');
            }
            if (!Schema::hasColumn('permohonan_tis', 'pemohon')) {
                $table->string('pemohon')->nullable()->after('no_rfc');
            }
            if (!Schema::hasColumn('permohonan_tis', 'unit_kerja')) {
                $table->string('unit_kerja')->nullable()->after('pemohon');
            }
            if (!Schema::hasColumn('permohonan_tis', 'perangkat_daerah_id')) {
                $table->unsignedBigInteger('perangkat_daerah_id')->nullable()->after('unit_kerja');
            }
            if (!Schema::hasColumn('permohonan_tis', 'nama_perangkat_daerah')) {
                $table->string('nama_perangkat_daerah')->nullable()->after('perangkat_daerah_id');
            }
            if (!Schema::hasColumn('permohonan_tis', 'nomor_kontak')) {
                $table->string('nomor_kontak')->nullable()->after('nama_perangkat_daerah');
            }
            if (!Schema::hasColumn('permohonan_tis', 'email_dinas')) {
                $table->string('email_dinas')->nullable()->after('nomor_kontak');
            }
            if (!Schema::hasColumn('permohonan_tis', 'jenis_perubahan')) {
                $table->json('jenis_perubahan')->nullable()->after('email_dinas');
            }
            if (!Schema::hasColumn('permohonan_tis', 'nama_aplikasi')) {
                $table->string('nama_aplikasi')->nullable()->after('jenis_permohonan');
            }
            if (!Schema::hasColumn('permohonan_tis', 'deskripsi_aplikasi')) {
                $table->text('deskripsi_aplikasi')->nullable()->after('nama_aplikasi');
            }
            if (!Schema::hasColumn('permohonan_tis', 'alamat_aplikasi')) {
                $table->string('alamat_aplikasi')->nullable()->after('deskripsi_aplikasi');
            }
            if (!Schema::hasColumn('permohonan_tis', 'alamat_repository')) {
                $table->string('alamat_repository')->nullable()->after('alamat_aplikasi');
            }
            if (!Schema::hasColumn('permohonan_tis', 'latar_belakang')) {
                $table->text('latar_belakang')->nullable()->after('alamat_repository');
            }
            if (!Schema::hasColumn('permohonan_tis', 'rincian_perubahan')) {
                $table->text('rincian_perubahan')->nullable()->after('latar_belakang');
            }
            if (!Schema::hasColumn('permohonan_tis', 'risiko_tidak_dilakukan')) {
                $table->text('risiko_tidak_dilakukan')->nullable()->after('rincian_perubahan');
            }
            if (!Schema::hasColumn('permohonan_tis', 'kriteria_risiko')) {
                $table->json('kriteria_risiko')->nullable()->after('risiko_tidak_dilakukan');
            }
            if (!Schema::hasColumn('permohonan_tis', 'keterangan')) {
                $table->text('keterangan')->nullable()->after('kriteria_risiko');
            }
            if (!Schema::hasColumn('permohonan_tis', 'solusi_diharapkan')) {
                $table->text('solusi_diharapkan')->nullable()->after('keterangan');
            }
            if (!Schema::hasColumn('permohonan_tis', 'risiko_perubahan')) {
                $table->text('risiko_perubahan')->nullable()->after('solusi_diharapkan');
            }
            if (!Schema::hasColumn('permohonan_tis', 'alternatif_perubahan')) {
                $table->text('alternatif_perubahan')->nullable()->after('risiko_perubahan');
            }
            if (!Schema::hasColumn('permohonan_tis', 'biaya_perubahan')) {
                $table->string('biaya_perubahan')->nullable()->after('alternatif_perubahan');
            }
            if (!Schema::hasColumn('permohonan_tis', 'waktu_perubahan')) {
                $table->string('waktu_perubahan')->nullable()->after('biaya_perubahan');
            }
            if (!Schema::hasColumn('permohonan_tis', 'tanggal_permohonan')) {
                $table->date('tanggal_permohonan')->nullable()->after('waktu_perubahan');
            }
            if (!Schema::hasColumn('permohonan_tis', 'tanda_tangan_file')) {
                $table->string('tanda_tangan_file')->nullable()->after('tanggal_permohonan');
            }
            if (!Schema::hasColumn('permohonan_tis', 'dokumen_pendukung_file')) {
                $table->json('dokumen_pendukung_file')->nullable()->after('tanda_tangan_file');
            }
            if (!Schema::hasColumn('permohonan_tis', 'assigned_to')) {
                $table->string('assigned_to')->nullable()->after('dokumen_pendukung_file');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('permohonan_tis', function (Blueprint $table) {
            $table->dropColumn([
                'no_rfc', 'pemohon', 'unit_kerja', 'perangkat_daerah_id',
                'nama_perangkat_daerah', 'nomor_kontak', 'email_dinas',
                'jenis_perubahan', 'nama_aplikasi', 'deskripsi_aplikasi',
                'alamat_aplikasi', 'alamat_repository', 'latar_belakang',
                'rincian_perubahan', 'risiko_tidak_dilakukan', 'kriteria_risiko',
                'keterangan', 'solusi_diharapkan', 'risiko_perubahan',
                'alternatif_perubahan', 'biaya_perubahan', 'waktu_perubahan',
                'tanggal_permohonan', 'tanda_tangan_file', 'dokumen_pendukung_file',
                'assigned_to'
            ]);
        });
    }
};
