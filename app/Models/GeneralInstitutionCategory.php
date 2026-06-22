<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class GeneralInstitutionCategory extends Model
{
    protected $table    = 'general_institution_categories';
    protected $fillable = ['name'];

    public function appIntegrations(): HasMany
    {
        return $this->hasMany(SadajabarAppIntegration::class, 'institution_id');
    }

    public function applicationReplications(): HasMany
    {
        return $this->hasMany(RekayasaApplicationReplication::class, 'institution_id');
    }

    public function integrationSummaries(): HasMany
    {
        return $this->hasMany(IntopIntegrationSummary::class, 'institution_id');
    }
}
