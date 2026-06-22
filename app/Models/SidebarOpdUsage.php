<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SidebarOpdUsage extends Model
{
    protected $table = 'sidebar_opd_usage';

    protected $fillable = [
        'service_type_id',
        'opd_id',
        'month',
        'year',
        'active_count',
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
