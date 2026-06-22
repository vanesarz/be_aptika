<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AppmanInventoryStat extends Model
{
    protected $table = 'appman_inventory_stats';

    protected $fillable = [
        'service_type_id',
        'month',
        'year',
        'total_apps',
        'profile',
        'repository',
        'registered_pse',
    ];

    public function serviceType(): BelongsTo
    {
        return $this->belongsTo(ServiceType::class);
    }
}
