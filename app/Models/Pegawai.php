<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Models\SpdPeserta;

class Pegawai extends Model
{
    protected $table = 'pegawai';

    protected $fillable = [
        'nama',
        'nip',
        'pangkat',
        'jabatan',
        'role',
    ];

    public function spdPeserta(): HasMany
    {
        return $this->hasMany(SpdPeserta::class);
    }
}
