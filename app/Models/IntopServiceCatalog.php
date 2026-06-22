<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class IntopServiceCatalog extends Model
{
    protected $table = 'intop_service_catalogs';

    protected $fillable = [
        'service_type_id',
        'month',
        'year',
        'adm_service_count',
        'public_service_count',
        'target_abs',
        'achievement_abs',
    ];
    public function serviceType(): BelongsTo
    {
        return $this->belongsTo(ServiceType::class);
    }
}
