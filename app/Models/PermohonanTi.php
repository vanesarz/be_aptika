<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PermohonanTi extends Model
{
    protected $table = 'permohonan_tis';

    protected $fillable = [
        'no_rfc',
        'nomor_surat',
        'tanggal',
        'pengirim',
        'instansi',
        'pemohon',
        'unit_kerja',
        'perangkat_daerah_id',
        'nama_perangkat_daerah',
        'nomor_kontak',
        'email_dinas',
        'jenis_perubahan',
        'jenis_permohonan',
        'nama_aplikasi',
        'deskripsi_aplikasi',
        'alamat_aplikasi',
        'alamat_repository',
        'latar_belakang',
        'rincian_perubahan',
        'risiko_tidak_dilakukan',
        'kriteria_risiko',
        'keterangan',
        'solusi_diharapkan',
        'risiko_perubahan',
        'alternatif_perubahan',
        'biaya_perubahan',
        'waktu_perubahan',
        'perihal',
        'deskripsi',
        'status',
        'tanggal_permohonan',
        'tanda_tangan_file',
        'dokumen_pendukung_file',
        'assigned_to',
    ];

    protected $casts = [
        'jenis_perubahan' => 'array',
        'jenis_permohonan' => 'array',
        'kriteria_risiko' => 'array',
        'dokumen_pendukung_file' => 'array',
        'tanggal_permohonan' => 'date',
        'tanggal' => 'date',
    ];

    protected $appends = ['tanda_tangan_url', 'dokumen_pendukung_urls'];

    public function getTandaTanganUrlAttribute()
    {
        if (!$this->tanda_tangan_file) {
            return null;
        }
        return asset('storage/' . $this->tanda_tangan_file);
    }

    public function getDokumenPendukungUrlsAttribute()
    {
        if (!$this->dokumen_pendukung_file || !is_array($this->dokumen_pendukung_file)) {
            return [];
        }
        return array_map(function ($path) {
            return asset('storage/' . $path);
        }, $this->dokumen_pendukung_file);
    }

    public static function generateNoRfc()
    {
        $year = date('Y');
        $count = self::whereYear('created_at', $year)->count() + 1;
        return sprintf('RFC-%s-%04d', $year, $count);
    }
}
