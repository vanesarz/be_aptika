<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AppmanKatalapsRegency extends Model
{
    protected $table = 'appman_katalaps_regencies';

    protected $fillable = [
        'service_type_id',
        'regency_id',
        'month',
        'year',
        'app_count',
    ];

    public function serviceType(): BelongsTo
    {
        return $this->belongsTo(ServiceType::class);
    }

    public function regency(): BelongsTo
    {
        return $this->belongsTo(RegencyName::class, 'regency_id');
    }
}
