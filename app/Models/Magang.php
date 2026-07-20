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
        'status_magang',
        'sertifikat',
        'keterangan'
    ];

    protected $casts = [
        'tgl_mulai_magang' => 'date',
        'tgl_selesai_magang' => 'date',
    ];
}