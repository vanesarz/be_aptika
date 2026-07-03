<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\ProfileController;

use App\Http\Controllers\SPD\DetailPerjalananController;
use App\Http\Controllers\SPD\PegawaiController;
use App\Http\Controllers\SPD\RekeningController;
use App\Http\Controllers\SPD\SpdPesertaController;

use App\Http\Controllers\LaporanController;
use App\Http\Controllers\SpdProposalController;

use App\Http\Controllers\Rekayasa\ApplicationReplicationController;
use App\Http\Controllers\Rekayasa\MentoringPerformanceController;

use App\Http\Controllers\Intop\IntegrationSummaryController;
use App\Http\Controllers\Intop\ServiceCatalogController;
use App\Http\Controllers\Intop\IntopMandateServiceSummaryController;

use App\Http\Controllers\Sidebar\DocumentStatController;
use App\Http\Controllers\Sidebar\MetricController;
use App\Http\Controllers\Sidebar\OpdUsageController;

use App\Http\Controllers\SmartJabar\JoinedAppController;
use App\Http\Controllers\SmartJabar\UsageStatController;
use App\Http\Controllers\SadaJabar\AppIntegrationController;
use App\Http\Controllers\SadaJabar\EncryptionStatController;
use App\Http\Controllers\SpdController;

use App\Http\Controllers\Appman\AppVulnerabilityController;
use App\Http\Controllers\Appman\DevelopmentTargetController;
use App\Http\Controllers\Appman\DriveJabarStatController;
use App\Http\Controllers\Appman\EmailManagementStatController;
use App\Http\Controllers\Appman\IntegrationMappingController;
use App\Http\Controllers\Appman\InventoryStatController;
use App\Http\Controllers\Appman\KatalapsRegencyController;
use App\Http\Controllers\Appman\TeamSupportFacilityController;

// Route::post('/register', [RegisteredUserController::class, 'store']); dinonaktifkan karena bisa di akses oleh siapa saja dan gak harus login
Route::post('/login', [AuthenticatedSessionController::class, 'store']);

Route::middleware(['auth:sanctum', 'active'])->group(function () {

    Route::post('/logout', [AuthenticatedSessionController::class, 'destroy']);

    Route::get('/user', function (Request $request) {
        return $request->user();
    });

    Route::get('/profile', [ProfileController::class, 'edit']);
    Route::patch('/profile', [ProfileController::class, 'update']);
    Route::delete('/profile', [ProfileController::class, 'destroy']);

    // Admin user CRUD
    Route::middleware(['role:admin'])->prefix('admin')->group(function () {
        Route::apiResource('users', \App\Http\Controllers\Admin\UserController::class);
    });

    Route::prefix('spd')->group(function () {
        Route::apiResource('detail-perjalanan', DetailPerjalananController::class);
        Route::patch(
            'detail-perjalanan/{id}/status',
            [DetailPerjalananController::class, 'updateStatus']
        );
    
        Route::apiResource('rekening', RekeningController::class);
        Route::apiResource('pegawai', PegawaiController::class);
        Route::apiResource('spd-peserta', SpdPesertaController::class);
});

    Route::get('/spd/stats', [SpdProposalController::class, 'stats']);
    // Route::apiResource('/spd', SpdProposalController::class);

    Route::prefix('smartjabar')->group(function () {
        Route::get('/export', [LaporanController::class, 'smartjabarExport']);
        Route::get('/joined-apps', [JoinedAppController::class, 'index']);
        Route::get('/joined-apps/create', [JoinedAppController::class, 'create']);
        Route::post('/joined-apps', [JoinedAppController::class, 'store']);
        Route::get('/joined-apps/{id}/edit', [JoinedAppController::class, 'edit']);
        Route::put('/joined-apps/{id}', [JoinedAppController::class, 'update']);
        Route::delete('/joined-apps/{id}', [JoinedAppController::class, 'destroy']);

        Route::get('/stats', [UsageStatController::class, 'index']);
        Route::get('/stats/create', [UsageStatController::class, 'create']);
        Route::post('/stats', [UsageStatController::class, 'store']);
        Route::get('/stats/{id}/edit', [UsageStatController::class, 'edit']);
        Route::put('/stats/{id}', [UsageStatController::class, 'update']);
        Route::delete('/stats/{id}', [UsageStatController::class, 'destroy']);
    });

    Route::prefix('spd')->group(function () {
        Route::get('/', [SpdController::class, 'index']);
        Route::get('/{id}', [SpdController::class, 'show']);
        Route::post('/', [SpdController::class, 'store']);
        Route::put('/{id}', [SpdController::class, 'update']);
        Route::delete('/{id}', [SpdController::class, 'destroy']);
        Route::post('/laporan', [SpdController::class, 'submitLaporan']);
    });

    Route::prefix('sadajabar')->group(function () {
        Route::get('/export', [LaporanController::class, 'sadajabarExport']);
        Route::get('/integrasi', [AppIntegrationController::class, 'index']);
        Route::get('/integrasi/create', [AppIntegrationController::class, 'create']);
        Route::post('/integrasi', [AppIntegrationController::class, 'store']);
        Route::get('/integrasi/{id}/edit', [AppIntegrationController::class, 'edit']);
        Route::put('/integrasi/{id}', [AppIntegrationController::class, 'update']);
        Route::delete('/integrasi/{id}', [AppIntegrationController::class, 'destroy']);

        Route::get('/enkripsi', [EncryptionStatController::class, 'index']);
        Route::get('/enkripsi/create', [EncryptionStatController::class, 'create']);
        Route::post('/enkripsi', [EncryptionStatController::class, 'store']);
        Route::get('/enkripsi/{id}/edit', [EncryptionStatController::class, 'edit']);
        Route::put('/enkripsi/{id}', [EncryptionStatController::class, 'update']);
        Route::delete('/enkripsi/{id}', [EncryptionStatController::class, 'destroy']);
    });

    Route::prefix('rekayasa')->group(function () {
        Route::get('/export', [LaporanController::class, 'rekayasaExport']);
        Route::get('/application-replications/summary', [ApplicationReplicationController::class, 'summary']);
        Route::get('/application-replications', [ApplicationReplicationController::class, 'index']);
        Route::get('/application-replications/create', [ApplicationReplicationController::class, 'create']);
        Route::post('/application-replications', [ApplicationReplicationController::class, 'store']);
        Route::get('/application-replications/{id}/edit', [ApplicationReplicationController::class, 'edit']);
        Route::put('/application-replications/{id}', [ApplicationReplicationController::class, 'update']);
        Route::delete('/application-replications/{id}', [ApplicationReplicationController::class, 'destroy']);


        Route::get('/mentoring-performances', [MentoringPerformanceController::class, 'index']);
        Route::get('/mentoring-performances/create', [MentoringPerformanceController::class, 'create']);
        Route::post('/mentoring-performances', [MentoringPerformanceController::class, 'store']);
        Route::get('/mentoring-performances/{id}/edit', [MentoringPerformanceController::class, 'edit']);
        Route::put('/mentoring-performances/{id}', [MentoringPerformanceController::class, 'update']);
        Route::delete('/mentoring-performances/{id}', [MentoringPerformanceController::class, 'destroy']);
    });

    Route::prefix('intop')->group(function () {
        Route::get('/export', [LaporanController::class, 'intopExport']);
        Route::get('/integration-summaries', [IntegrationSummaryController::class, 'index']);
        Route::get('/integration-summaries/create', [IntegrationSummaryController::class, 'create']);
        Route::post('/integration-summaries', [IntegrationSummaryController::class, 'store']);
        Route::get('/integration-summaries/{id}/edit', [IntegrationSummaryController::class, 'edit']);
        Route::put('/integration-summaries/{id}', [IntegrationSummaryController::class, 'update']);
        Route::delete('/integration-summaries/{id}', [IntegrationSummaryController::class, 'destroy']);

        Route::get('/service-catalogs', [ServiceCatalogController::class, 'index']);
        Route::get('/service-catalogs/create', [ServiceCatalogController::class, 'create']);
        Route::post('/service-catalogs', [ServiceCatalogController::class, 'store']);
        Route::get('/service-catalogs/{id}/edit', [ServiceCatalogController::class, 'edit']);
        Route::put('/service-catalogs/{id}', [ServiceCatalogController::class, 'update']);
        Route::delete('/service-catalogs/{id}', [ServiceCatalogController::class, 'destroy']);

        Route::get('/intop-mandate-service-summaries', [IntopMandateServiceSummaryController::class, 'index']);
        Route::get('/intop-mandate-service-summaries/create', [IntopMandateServiceSummaryController::class, 'create']);
        Route::post('/intop-mandate-service-summaries', [IntopMandateServiceSummaryController::class, 'store']);
        Route::get('/intop-mandate-service-summaries/{id}/edit', [IntopMandateServiceSummaryController::class, 'edit']);
        Route::put('/intop-mandate-service-summaries/{id}', [IntopMandateServiceSummaryController::class, 'update']);
        Route::delete('/intop-mandate-service-summaries/{id}', [IntopMandateServiceSummaryController::class, 'destroy']);
    });

    Route::prefix('sidebar')->group(function () {
        Route::get('/export', [LaporanController::class, 'sidebarExport']);
        Route::get('/document-stats', [DocumentStatController::class, 'index']);
        Route::get('/document-stats/create', [DocumentStatController::class, 'create']);
        Route::post('/document-stats', [DocumentStatController::class, 'store']);
        Route::get('/document-stats/{id}/edit', [DocumentStatController::class, 'edit']);
        Route::put('/document-stats/{id}', [DocumentStatController::class, 'update']);
        Route::delete('/document-stats/{id}', [DocumentStatController::class, 'destroy']);

        Route::get('/metrics', [MetricController::class, 'index']);
        Route::get('/metrics/create', [MetricController::class, 'create']);
        Route::post('/metrics', [MetricController::class, 'store']);
        Route::get('/metrics/{id}/edit', [MetricController::class, 'edit']);
        Route::put('/metrics/{id}', [MetricController::class, 'update']);
        Route::delete('/metrics/{id}', [MetricController::class, 'destroy']);

        Route::get('/opd-usages', [OpdUsageController::class, 'index']);
        Route::get('/opd-usages/create', [OpdUsageController::class, 'create']);
        Route::post('/opd-usages', [OpdUsageController::class, 'store']);
        Route::get('/opd-usages/{id}/edit', [OpdUsageController::class, 'edit']);
        Route::put('/opd-usages/{id}', [OpdUsageController::class, 'update']);
        Route::delete('/opd-usages/{id}', [OpdUsageController::class, 'destroy']);
    });

    Route::prefix('appman')->group(function () {
        Route::get('/export', [LaporanController::class, 'appmanExport']);
        // KERENTAAN PADA APLIKASI PEMPROV JABAR
        Route::get('/app-vulnerabilities', [AppVulnerabilityController::class, 'index']);
        Route::get('/app-vulnerabilities/create', [AppVulnerabilityController::class, 'create']);
        Route::post('/app-vulnerabilities', [AppVulnerabilityController::class, 'store']);
        Route::get('/app-vulnerabilities/{id}/edit', [AppVulnerabilityController::class, 'edit']);
        Route::put('/app-vulnerabilities/{id}', [AppVulnerabilityController::class, 'update']);
        Route::delete('/app-vulnerabilities/{id}', [AppVulnerabilityController::class, 'destroy']);

        // APLIKASI/LAYANAN YANG MENJADI TARGET PENGEMBANGAN
        Route::get('/development-targets', [DevelopmentTargetController::class, 'index']);
        Route::get('/development-targets/create', [DevelopmentTargetController::class, 'create']);
        Route::post('/development-targets', [DevelopmentTargetController::class, 'store']);
        Route::get('/development-targets/{id}/edit', [DevelopmentTargetController::class, 'edit']);
        Route::put('/development-targets/{id}', [DevelopmentTargetController::class, 'update']);
        Route::delete('/development-targets/{id}', [DevelopmentTargetController::class, 'destroy']);

        // LAYANAN DRIVE JABAR
        Route::get('/drive-jabar-stats', [DriveJabarStatController::class, 'index']);
        Route::get('/drive-jabar-stats/create', [DriveJabarStatController::class, 'create']);
        Route::post('/drive-jabar-stats', [DriveJabarStatController::class, 'store']);
        Route::get('/drive-jabar-stats/{id}/edit', [DriveJabarStatController::class, 'edit']);
        Route::put('/drive-jabar-stats/{id}', [DriveJabarStatController::class, 'update']);
        Route::delete('/drive-jabar-stats/{id}', [DriveJabarStatController::class, 'destroy']);
        
        // LAYANAN PENGELOLAAN EMAIL
        Route::get('/email-management-stats', [EmailManagementStatController::class, 'index']);
        Route::get('/email-management-stats/create', [EmailManagementStatController::class, 'create']);
        Route::post('/email-management-stats', [EmailManagementStatController::class, 'store']);
        Route::get('/email-management-stats/{id}/edit', [EmailManagementStatController::class, 'edit']);
        Route::put('/email-management-stats/{id}', [EmailManagementStatController::class, 'update']);
        Route::delete('/email-management-stats/{id}', [EmailManagementStatController::class, 'destroy']);

        // PEMETAAN INTEGRASI APLIKASI
        Route::get('/integration-mappings', [IntegrationMappingController::class, 'index']);
        Route::get('/integration-mappings/create', [IntegrationMappingController::class, 'create']);
        Route::post('/integration-mappings', [IntegrationMappingController::class, 'store']);
        Route::get('/integration-mappings/{id}/edit', [IntegrationMappingController::class, 'edit']);
        Route::put('/integration-mappings/{id}', [IntegrationMappingController::class, 'update']);
        Route::delete('/integration-mappings/{id}', [IntegrationMappingController::class, 'destroy']);

        // PENDATAAN APLIKASI 2026 (BERDASARKAN KATALAPS)
        Route::get('/inventory-stats', [InventoryStatController::class, 'index']);
        Route::get('/inventory-stats/create', [InventoryStatController::class, 'create']);
        Route::post('/inventory-stats', [InventoryStatController::class, 'store']);
        Route::get('/inventory-stats/{id}/edit', [InventoryStatController::class, 'edit']);
        Route::put('/inventory-stats/{id}', [InventoryStatController::class, 'update']);
        Route::delete('/inventory-stats/{id}', [InventoryStatController::class, 'destroy']);

        // KATALAPS KABUPATEN KOTA
        Route::get('/katalaps-regencies', [KatalapsRegencyController::class, 'index']);
        Route::get('/katalaps-regencies/create', [KatalapsRegencyController::class, 'create']);
        Route::post('/katalaps-regencies', [KatalapsRegencyController::class, 'store']);
        Route::get('/katalaps-regencies/{id}/edit', [KatalapsRegencyController::class, 'edit']);
        Route::put('/katalaps-regencies/{id}', [KatalapsRegencyController::class, 'update']);
        Route::delete('/katalaps-regencies/{id}', [KatalapsRegencyController::class, 'destroy']);

        // FASILITASI DUKUNGAN TIM PADA PENGEMBANGAN APLIKASI PERANGKAT DAERAH (TOT) 
        Route::get('/team-support-facilities', [TeamSupportFacilityController::class, 'index']);
        Route::get('/team-support-facilities/create', [TeamSupportFacilityController::class, 'create']);
        Route::post('/team-support-facilities', [TeamSupportFacilityController::class, 'store']);
        Route::get('/team-support-facilities/{id}/edit', [TeamSupportFacilityController::class, 'edit']);
        Route::put('/team-support-facilities/{id}', [TeamSupportFacilityController::class, 'update']);
        Route::delete('/team-support-facilities/{id}', [TeamSupportFacilityController::class, 'destroy']);
    });
});
