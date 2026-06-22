<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AppmanDevelopmentTarget extends Model
{
    protected $table = 'appman_development_targets';

    protected $fillable = [
        'service_type_id',
        'month',
        'year',
        'outside_dc_jabar',
        'manual_service',
    ];

    public function serviceType(): BelongsTo
    {
        return $this->belongsTo(ServiceType::class);
    }
}
