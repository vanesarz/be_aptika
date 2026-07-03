<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Models\DetailPerjalanan;

class Rekening extends Model
{
    protected $table = 'rekening';

    protected $fillable = [
        'kode_rekening',
        'nomor_rekening',
        'nama_rekening',
    ];

    public function detailPerjalanan(): HasMany
    {
        return $this->hasMany(DetailPerjalanan::class);
    }
}
