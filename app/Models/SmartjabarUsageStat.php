<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SmartjabarUsageStat extends Model
{
    protected $table    = 'smartjabar_usage_stats';
    protected $fillable = [
        'service_type_id',
        'opd_id',
        'month',
        'year',
        'total_asn',
        'active_users',
    ];

    public function serviceType(): BelongsTo
    {
        return $this->belongsTo(ServiceType::class);
    }

    public function opd(): BelongsTo
    {
        return $this->belongsTo(GeneralOpd::class, 'opd_id');
    }
}
