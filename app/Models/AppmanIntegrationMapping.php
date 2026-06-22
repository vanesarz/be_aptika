<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AppmanIntegrationMapping extends Model
{
    protected $table = 'appman_integration_mappings';

    protected $fillable = [
        'service_type_id',
        'month',
        'year',
        'total_apps',
        'integration_opportunity',
        'integrated',
    ];

    public function serviceType(): BelongsTo
    {
        return $this->belongsTo(ServiceType::class);
    }
}
