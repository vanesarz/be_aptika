<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\Pegawai;
use App\Models\DetailPerjalanan;

class SpdPeserta extends Model
{
    protected $table = 'spd_peserta';

    protected $fillable = [
        'detail_perjalanan_id',
        'pegawai_id',
        'nomor_spd',
        'lama_hari',
        'total_uang',
    ];

    protected $casts = [
        'lama_hari' => 'integer',
        'total_uang' => 'decimal:2',
    ];

    public function pegawai(): BelongsTo
    {
        return $this->belongsTo(Pegawai::class);
    }

    public function perjalanan(): BelongsTo
    {
        return $this->belongsTo(DetailPerjalanan::class, 'detail_perjalanan_id');
    }
}
