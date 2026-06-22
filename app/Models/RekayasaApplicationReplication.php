<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RekayasaApplicationReplication extends Model
{
    protected $table    = 'rekayasa_application_replications';
    protected $fillable = [
        'service_type_id',
        'institution_id',
        'year',
        'month',
        'total_replications',
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
