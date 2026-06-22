<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SmartjabarJoinedApp extends Model
{
    protected $table    = 'smartjabar_joined_apps';
    protected $fillable = ['service_type_id', 'year', 'month', 'total_apps'];

    public function serviceType(): BelongsTo
    {
        return $this->belongsTo(ServiceType::class);
    }
}
