<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AppmanDriveJabarStat extends Model
{
    protected $table = 'appman_drive_jabar_stats';

    protected $fillable = [
        'service_type_id',
        'month',
        'year',
        'total_users',
    ];

    public function serviceType(): BelongsTo
    {
        return $this->belongsTo(ServiceType::class);
    }
}
