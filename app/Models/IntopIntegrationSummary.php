<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class IntopIntegrationSummary extends Model
{
    protected $table = 'intop_integration_summaries';

    protected $fillable = [
        'service_type_id',
        'institution_id',
        'month',
        'year',
        'app_count',
    ];

    public function serviceType(): BelongsTo
    {
        return $this->belongsTo(ServiceType::class);
    }

    public function institutionCategory(): BelongsTo
    {
        return $this->belongsTo(GeneralInstitutionCategory::class, 'institution_id');
    }
}
