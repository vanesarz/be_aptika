<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Spd extends Model
{
    use HasFactory;

    protected $fillable = [
        'no_spd',
        'pejabat_pemberi',
        'nama',
        'nip',
        'pangkat',
        'jabatan',
        'maksud',
        'angkutan',
        'tempat_berangkat',
        'tempat_tujuan',
        'tgl_mulai',
        'tgl_selesai',
        'durasi',
        'pengikut',
        'anggaran',
        'uang_harian',
        'uang_transport',
        'uang_hotel',
        'status',
    ];

    protected $casts = [
        'pengikut' => 'array',
        'tgl_mulai' => 'date',
        'tgl_selesai' => 'date',
    ];
}
