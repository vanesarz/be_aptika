<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SidebarMetric extends Model
{
    protected $table = 'sidebar_metrics';

    protected $fillable = [
        'service_type_id',
        'month',
        'year',
        'total_users',
        'active_users',
        'document_created',
    ];

    public function serviceType(): BelongsTo
    {
        return $this->belongsTo(ServiceType::class);
    }
}
