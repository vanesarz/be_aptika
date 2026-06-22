<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RekayasaMentoringPerformance extends Model
{
    protected $table    = 'rekayasa_mentoring_performance';
    protected $fillable = [
        'service_type_id',
        'month',
        'year',
        'total_apps',
        'target',
        'realization',
    ];

    public function serviceType(): BelongsTo
    {
        return $this->belongsTo(ServiceType::class);
    }
}
