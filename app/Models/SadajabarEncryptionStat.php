<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SadajabarEncryptionStat extends Model
{
    protected $table    = 'sadajabar_encryption_stats';
    protected $fillable = ['month', 'year', 'app_count', 'service_type_id'];

    public function serviceType(): BelongsTo
    {
        return $this->belongsTo(ServiceType::class);
    }
}
