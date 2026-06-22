<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AppmanEmailManagementStat extends Model
{
    protected $table = 'appman_email_management_stats';

    protected $fillable = [
        'service_type_id',
        'month',
        'year',
        'user_asn',
        'user_others',
        'active_user',
    ];

    public function serviceType(): BelongsTo
    {
        return $this->belongsTo(ServiceType::class);
    }
}
