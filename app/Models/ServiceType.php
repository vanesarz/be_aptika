<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ServiceType extends Model
{
    protected $fillable = ['name'];

    public function appmanAppVulnerabilities(): HasMany
    {
        return $this->hasMany(AppmanAppVulnerability::class);
    }

    public function appmanDevelopmentTargets(): HasMany
    {
        return $this->hasMany(AppmanDevelopmentTarget::class);
    }

    public function appmanDriveJabarStats(): HasMany
    {
        return $this->hasMany(AppmanDriveJabarStat::class);
    }

    public function appmanEmailManagementStats(): HasMany
    {
        return $this->hasMany(AppmanEmailManagementStat::class);
    }

    public function appmanIntegrationMappings(): HasMany
    {
        return $this->hasMany(AppmanIntegrationMapping::class);
    }

    public function appmanInventoryStats(): HasMany
    {
        return $this->hasMany(AppmanInventoryStat::class);
    }

    public function appmanKatalapsRegencies(): HasMany
    {
        return $this->hasMany(AppmanKatalapsRegency::class);
    }

    public function appmanTeamSupportFacilities(): HasMany
    {
        return $this->hasMany(AppmanTeamSupportFacility::class);
    }

    public function intopIntegrationSummaries(): HasMany
    {
        return $this->hasMany(IntopIntegrationSummary::class);
    }

    public function intopServiceCatalogs(): HasMany
    {
        return $this->hasMany(IntopServiceCatalog::class);
    }

    public function sadajabarEncryptionStats(): HasMany
    {
        return $this->hasMany(SadajabarEncryptionStat::class);
    }

    public function sadajabarAppIntegrations(): HasMany
    {
        return $this->hasMany(SadajabarAppIntegration::class);
    }

    public function rekayasaApplicationReplications(): HasMany
    {
        return $this->hasMany(RekayasaApplicationReplication::class);
    }

    public function rekayasaMentoringPerformances(): HasMany
    {
        return $this->hasMany(RekayasaMentoringPerformance::class);
    }

    public function smartjabarJoinedApps(): HasMany
    {
        return $this->hasMany(SmartjabarJoinedApp::class);
    }

    public function smartjabarUsageStats(): HasMany
    {
        return $this->hasMany(SmartjabarUsageStat::class);
    }

    public function sidebarDocumentStats(): HasMany
    {
        return $this->hasMany(SidebarDocumentStat::class);
    }

    public function sidebarMetrics(): HasMany
    {
        return $this->hasMany(SidebarMetric::class);
    }

    public function sidebarOpdUsages(): HasMany
    {
        return $this->hasMany(SidebarOpdUsage::class);
    }
}
