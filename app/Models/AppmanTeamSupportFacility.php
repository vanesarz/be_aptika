<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AppmanTeamSupportFacility extends Model
{
    protected $table = 'appman_team_support_facilities';

    protected $fillable = [
        'service_type_id',
        'month',
        'year',
        'total_pd',
        'total_apps',
    ];

    public function serviceType(): BelongsTo
    {
        return $this->belongsTo(ServiceType::class);
    }
}
