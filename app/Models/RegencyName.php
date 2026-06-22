<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class RegencyName extends Model
{
    protected $table = 'regencies_name';

    protected $fillable = [
        'name',
    ];

    public function appmanKatalapsRegencies(): HasMany
    {
        return $this->hasMany(AppmanKatalapsRegency::class, 'regency_id');
    }
}
