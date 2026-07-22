<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Kerentanan extends Model
{
    protected $table = 'kerentanans';

    protected $fillable = [
        'nomor_surat',
        'tanggal',
        'aplikasi',
        'url',
        'tingkat_kerentanan',
        'perihal',
        'deskripsi',
        'status',
    ];

    protected $casts = [
        'tanggal' => 'date',
    ];

    public static function generateNomorSurat(): string
    {
        $year = now()->year;
        $prefix = "VULN-{$year}-";

        $last = static::where('nomor_surat', 'like', "{$prefix}%")
            ->orderByRaw('CAST(SUBSTRING(nomor_surat, ' . (strlen($prefix) + 1) . ') AS UNSIGNED) DESC')
            ->first();

        $nextNumber = $last ? ((int) substr($last->nomor_surat, strlen($prefix))) + 1 : 1;

        return $prefix . str_pad($nextNumber, 3, '0', STR_PAD_LEFT);
    }
}

