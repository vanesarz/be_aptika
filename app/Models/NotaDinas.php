<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class NotaDinas extends Model
{
    protected $table = 'nota_dinas';

    protected $fillable = [
        'nomor_surat',
        'tujuan',
        'dari',
        'tembusan',
        'sifat_surat',
        'perihal',
        'isi_surat',
        'isi_lampiran',
        'tanggal_surat',
        'status',
        'lampiran',
        'catatan',
        'user_id',
    ];

    protected $casts = [
        'tanggal_surat' => 'date',
    ];

    /**
     * Relasi ke user pembuat nota dinas.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Generate nomor surat otomatis.
     * Format: ND-{TAHUN}-{NOMOR URUT 3 DIGIT}
     * Contoh: ND-2024-001, ND-2024-012
     */
    public static function generateNomorSurat(): string
    {
        $year = now()->year;
        $prefix = "ND-{$year}-";

        $lastNota = static::where('nomor_surat', 'like', "{$prefix}%")
            ->orderByRaw('CAST(SUBSTRING(nomor_surat, ' . (strlen($prefix) + 1) . ') AS UNSIGNED) DESC')
            ->first();

        if ($lastNota) {
            $lastNumber = (int) substr($lastNota->nomor_surat, strlen($prefix));
            $nextNumber = $lastNumber + 1;
        } else {
            $nextNumber = 1;
        }

        return $prefix . str_pad($nextNumber, 3, '0', STR_PAD_LEFT);
    }

    /**
     * Label status untuk tampilan frontend.
     */
    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {
            'draft' => 'Draft',
            'terkirim' => 'Terkirim',
            'menunggu_tte' => 'Menunggu TTE',
            default => ucfirst((string) ($this->status ?? 'Draft')),
        };
    }
}

