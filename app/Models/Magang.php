<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Magang extends Model
{
    protected $fillable = [
        'nama',
        'nama_kampus',
        'tgl_mulai_magang',
        'tgl_selesai_magang',
        'cv_magang',
        'sertifikat',
        'keterangan'
    ];

    protected $casts = [
        'tgl_mulai_magang' => 'date',
        'tgl_selesai_magang' => 'date',
    ];

    protected $appends = [
        'status_magang'
    ];

    public function getStatusMagangAttribute()
    {
        $today = now('Asia/Jakarta')->format('Y-m-d');
        
        $start = $this->tgl_mulai_magang ? $this->tgl_mulai_magang->format('Y-m-d') : null;
        $end = $this->tgl_selesai_magang ? $this->tgl_selesai_magang->format('Y-m-d') : null;

        if (!$start || !$end) {
            return 'Belum mulai';
        }

        if ($today < $start) {
            return 'Belum mulai';
        }

        if ($today > $end) {
            return 'Selesai magang';
        }

        return 'Sedang magang';
    }
}