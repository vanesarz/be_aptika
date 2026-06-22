<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class GeneralOpd extends Model
{
    protected $table    = 'general_opd';
    protected $fillable = ['name'];

    public function usageStats(): HasMany
    {
        return $this->hasMany(SmartjabarUsageStat::class, 'opd_id');
    }

    public function sidebarOpdUsages(): HasMany
    {
        return $this->hasMany(SidebarOpdUsage::class, 'opd_id');
    }
}
