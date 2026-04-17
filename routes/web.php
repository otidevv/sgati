<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\SystemController;
use App\Http\Controllers\SystemVersionController;
use App\Http\Controllers\SystemInfrastructureController;
use App\Http\Controllers\SystemDatabaseController;
use App\Http\Controllers\SystemDatabaseResponsibleController;
use App\Http\Controllers\SystemDatabaseResponsibleDocumentController;
use App\Http\Controllers\SystemServiceDocumentController;
use App\Http\Controllers\SystemServiceFieldController;
use App\Http\Controllers\SystemServiceController;
use App\Http\Controllers\SystemIntegrationController;
use App\Http\Controllers\SystemDocumentController;
use App\Http\Controllers\SystemRepositoryController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\AreaController;
use App\Http\Controllers\Admin\PersonaController;
use App\Http\Controllers\Admin\ServerController;
use App\Http\Controllers\Admin\ServerContainerController;
use App\Http\Controllers\Admin\ServerResponsibleController;
use App\Http\Controllers\Admin\ServerResponsibleDocumentController;
use App\Http\Controllers\Admin\DatabaseServerController;
use App\Http\Controllers\Admin\DatabaseServerResponsibleController;
use App\Http\Controllers\Admin\DatabaseServerResponsibleDocumentController;
use App\Http\Controllers\Admin\SettingController;
use App\Http\Controllers\Admin\RoleController;
use App\Http\Controllers\Admin\SslCertificateController;
use App\Http\Controllers\SystemResponsibleController;
use App\Http\Controllers\SystemResponsibleDocumentController;
use App\Http\Controllers\SystemServiceGatewayController;
use App\Http\Controllers\SystemServiceGatewayKeyController;
use App\Http\Controllers\SystemServiceGatewayKeyDocumentController;
use Illuminate\Support\Facades\Route;

Route::get('/', fn() => redirect()->route('dashboard'));

Route::middleware(['auth'])->group(function () {

    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Perfil (generado por Breeze)
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // ── Sistemas ──────────────────────────────────────────────────────────
    Route::resource('systems', SystemController::class);

    Route::prefix('systems/{system}')->name('systems.')->group(function () {

        // Versiones
        Route::resource('versions', SystemVersionController::class)
            ->except(['index', 'show']);

        // Infraestructura (única por sistema)
        Route::get('infrastructure/edit', [SystemInfrastructureController::class, 'edit'])
            ->name('infrastructure.edit');
        Route::put('infrastructure', [SystemInfrastructureController::class, 'update'])
            ->name('infrastructure.update');

        // Bases de datos
        Route::resource('databases', SystemDatabaseController::class)
            ->except(['index']);

        Route::prefix('databases/{database}')->name('databases.')->group(function () {
            Route::post('responsibles',                                                                  [SystemDatabaseResponsibleController::class, 'store'])->name('responsibles.store');
            Route::put('responsibles/{responsible}',                                                     [SystemDatabaseResponsibleController::class, 'update'])->name('responsibles.update');
            Route::patch('responsibles/{responsible}/deactivate',                                        [SystemDatabaseResponsibleController::class, 'deactivate'])->name('responsibles.deactivate');
            Route::patch('responsibles/{responsible}/reactivate',                                        [SystemDatabaseResponsibleController::class, 'reactivate'])->name('responsibles.reactivate');
            Route::delete('responsibles/{responsible}',                                                  [SystemDatabaseResponsibleController::class, 'destroy'])->name('responsibles.destroy');

            Route::post('responsibles/{responsible}/documents',                                          [SystemDatabaseResponsibleDocumentController::class, 'store'])->name('responsibles.documents.store');
            Route::get('responsibles/{responsible}/documents/{document}/download',                       [SystemDatabaseResponsibleDocumentController::class, 'download'])->name('responsibles.documents.download');
            Route::get('responsibles/{responsible}/documents/{document}/preview',                        [SystemDatabaseResponsibleDocumentController::class, 'preview'])->name('responsibles.documents.preview');
            Route::delete('responsibles/{responsible}/documents/{document}',                             [SystemDatabaseResponsibleDocumentController::class, 'destroy'])->name('responsibles.documents.destroy');
        });

        // Servicios / APIs
        Route::resource('services', SystemServiceController::class)
            ->except(['index']);

        Route::prefix('services/{service}')->name('services.')->group(function () {
            Route::post('documents',                              [SystemServiceDocumentController::class, 'store'])->name('documents.store');
            Route::get('documents/{document}/download',          [SystemServiceDocumentController::class, 'download'])->name('documents.download');
            Route::get('documents/{document}/preview',           [SystemServiceDocumentController::class, 'preview'])->name('documents.preview');
            Route::delete('documents/{document}',                [SystemServiceDocumentController::class, 'destroy'])->name('documents.destroy');

            Route::post('fields',                                [SystemServiceFieldController::class, 'store'])->name('fields.store');
            Route::put('fields/{field}',                         [SystemServiceFieldController::class, 'update'])->name('fields.update');
            Route::delete('fields/{field}',                      [SystemServiceFieldController::class, 'destroy'])->name('fields.destroy');

            // Gateway
            Route::post('gateway/toggle',                        [SystemServiceGatewayController::class, 'toggle'])->name('gateway.toggle');
            Route::put('gateway/settings',                       [SystemServiceGatewayController::class, 'updateSettings'])->name('gateway.settings');
            Route::post('gateway/regenerate-slug',               [SystemServiceGatewayController::class, 'regenerateSlug'])->name('gateway.regenerate-slug');
            Route::get('gateway/logs',                           [SystemServiceGatewayController::class, 'logs'])->name('gateway.logs');
            // Gateway keys
            Route::post('gateway/keys',                                        [SystemServiceGatewayKeyController::class, 'store'])->name('gateway.keys.store');
            Route::post('gateway/keys/{key}/toggle',                           [SystemServiceGatewayKeyController::class, 'toggle'])->name('gateway.keys.toggle');
            Route::post('gateway/keys/{key}/regenerate',                       [SystemServiceGatewayKeyController::class, 'regenerateKey'])->name('gateway.keys.regenerate');
            Route::put('gateway/keys/{key}',                                   [SystemServiceGatewayKeyController::class, 'update'])->name('gateway.keys.update');
            Route::delete('gateway/keys/{key}',                                [SystemServiceGatewayKeyController::class, 'destroy'])->name('gateway.keys.destroy');
            // Gateway key documents
            Route::post('gateway/keys/{key}/documents',                        [SystemServiceGatewayKeyDocumentController::class, 'store'])->name('gateway.keys.documents.store');
            Route::get('gateway/keys/{key}/documents/{document}/download',     [SystemServiceGatewayKeyDocumentController::class, 'download'])->name('gateway.keys.documents.download');
            Route::get('gateway/keys/{key}/documents/{document}/preview',      [SystemServiceGatewayKeyDocumentController::class, 'preview'])->name('gateway.keys.documents.preview');
            Route::delete('gateway/keys/{key}/documents/{document}',           [SystemServiceGatewayKeyDocumentController::class, 'destroy'])->name('gateway.keys.documents.destroy');
        });

        // Integraciones
        Route::resource('integrations', SystemIntegrationController::class)
            ->except(['index', 'show']);

        // Documentos
        Route::resource('documents', SystemDocumentController::class)
            ->except(['index', 'show', 'edit', 'update']);
        Route::get('documents/{document}/download', [SystemDocumentController::class, 'download'])
            ->name('documents.download');
        Route::get('documents/{document}/preview',  [SystemDocumentController::class, 'preview'])
            ->name('documents.preview');

        // Repositorios
        Route::resource('repositories', SystemRepositoryController::class)
            ->except(['index', 'show']);

        // Responsables del sistema
        Route::post('responsibles',                                                      [SystemResponsibleController::class, 'store'])->name('responsibles.store');
        Route::put('responsibles/{responsible}',                                         [SystemResponsibleController::class, 'update'])->name('responsibles.update');
        Route::patch('responsibles/{responsible}/deactivate',                            [SystemResponsibleController::class, 'deactivate'])->name('responsibles.deactivate');
        Route::patch('responsibles/{responsible}/reactivate',                            [SystemResponsibleController::class, 'reactivate'])->name('responsibles.reactivate');
        Route::delete('responsibles/{responsible}',                                      [SystemResponsibleController::class, 'destroy'])->name('responsibles.destroy');

        Route::post('responsibles/{responsible}/documents',                              [SystemResponsibleDocumentController::class, 'store'])->name('responsibles.documents.store');
        Route::get('responsibles/{responsible}/documents/{document}/download',           [SystemResponsibleDocumentController::class, 'download'])->name('responsibles.documents.download');
        Route::get('responsibles/{responsible}/documents/{document}/preview',            [SystemResponsibleDocumentController::class, 'preview'])->name('responsibles.documents.preview');
        Route::delete('responsibles/{responsible}/documents/{document}',                 [SystemResponsibleDocumentController::class, 'destroy'])->name('responsibles.documents.destroy');
    });

    // ── Repositorio general de documentos ────────────────────────────────
    Route::get('/documents', [SystemDocumentController::class, 'repository'])
        ->name('documents.repository');

    // ── Repositorios (vista global) ───────────────────────────────────────
    Route::get('/repositories', [SystemRepositoryController::class, 'index'])
        ->name('repositories.index');

    // ── Reportes ──────────────────────────────────────────────────────────
    Route::get('/reports', [ReportController::class, 'index'])->name('reports.index');
    Route::get('/reports/export/excel', [ReportController::class, 'exportExcel'])->name('reports.excel');
    Route::get('/reports/export/pdf', [ReportController::class, 'exportPdf'])->name('reports.pdf');
    Route::get('/systems/{system}/report/pdf', [ReportController::class, 'systemPdf'])->name('systems.report.pdf');
    Route::get('/systems/{system}/pdf-data',   [ReportController::class, 'systemPdfData'])->name('systems.pdf-data');

    // ── Administración (solo admin) ───────────────────────────────────────
    Route::middleware('role:admin')->prefix('admin')->name('admin.')->group(function () {
        Route::get('personas/search',            [PersonaController::class, 'search'])->name('personas.search');
        Route::get('personas/dni-lookup/{dni}', [PersonaController::class, 'dniLookup'])->name('personas.dni-lookup');
        Route::get('personas/check-email',       [PersonaController::class, 'checkEmail'])->name('personas.check-email');
        Route::resource('personas', PersonaController::class);
        Route::get('users/check-email', [UserController::class, 'checkEmail'])->name('users.check-email');
        Route::resource('users', UserController::class);
        Route::get('areas/check-name', [AreaController::class, 'checkName'])->name('areas.check-name');
        Route::resource('areas', AreaController::class);
        Route::get('roles',                              [RoleController::class, 'index'])->name('roles.index');
        Route::put('roles/{role}/permissions',           [RoleController::class, 'updatePermissions'])->name('roles.permissions');
        Route::resource('servers', ServerController::class);

        // Certificados SSL
        Route::post('ssl-certificates/parse-cert', [SslCertificateController::class, 'parseCert'])->name('ssl-certificates.parse-cert');
        Route::resource('ssl-certificates', SslCertificateController::class)
              ->parameters(['ssl-certificates' => 'sslCertificate']);
        Route::get('ssl-certificates/{sslCertificate}/download/cert',  [SslCertificateController::class, 'downloadCert'])->name('ssl-certificates.download.cert');
        Route::get('ssl-certificates/{sslCertificate}/download/key',   [SslCertificateController::class, 'downloadKey'])->name('ssl-certificates.download.key');
        Route::get('ssl-certificates/{sslCertificate}/download/chain', [SslCertificateController::class, 'downloadChain'])->name('ssl-certificates.download.chain');
        Route::get('ssl-certificates/{sslCertificate}/download/pfx',   [SslCertificateController::class, 'downloadPfx'])->name('ssl-certificates.download.pfx');
        Route::post('ssl-certificates/{sslCertificate}/extract-from-pfx', [SslCertificateController::class, 'extractFromPfx'])->name('ssl-certificates.extract-from-pfx');
        Route::post('ssl-certificates/{sslCertificate}/convert-to-pfx',   [SslCertificateController::class, 'convertToPfx'])->name('ssl-certificates.convert-to-pfx');

        // Configuración
        Route::get('settings', [SettingController::class, 'index'])->name('settings.index');
        Route::post('settings/cache', [SettingController::class, 'clearCache'])->name('settings.cache');
        Route::post('settings/mail', [SettingController::class, 'updateMail'])->name('settings.mail');
        Route::post('settings/security', [SettingController::class, 'updateSecurity'])->name('settings.security');

        Route::prefix('servers/{server}')->name('servers.')->group(function () {
            Route::get('connect',                        [ServerController::class, 'connect'])->name('connect');
            Route::post('reconnect',                     [ServerController::class, 'reconnect'])->name('reconnect');

            Route::post('responsibles',                                                          [ServerResponsibleController::class, 'store'])->name('responsibles.store');
            Route::put('responsibles/{responsible}',                                             [ServerResponsibleController::class, 'update'])->name('responsibles.update');
            Route::patch('responsibles/{responsible}/deactivate',                                [ServerResponsibleController::class, 'deactivate'])->name('responsibles.deactivate');
            Route::patch('responsibles/{responsible}/reactivate',                                [ServerResponsibleController::class, 'reactivate'])->name('responsibles.reactivate');
            Route::delete('responsibles/{responsible}',                                          [ServerResponsibleController::class, 'destroy'])->name('responsibles.destroy');

            Route::post('responsibles/{responsible}/documents',                                  [ServerResponsibleDocumentController::class, 'store'])->name('responsibles.documents.store');
            Route::get('responsibles/{responsible}/documents/{document}/download',               [ServerResponsibleDocumentController::class, 'download'])->name('responsibles.documents.download');
            Route::get('responsibles/{responsible}/documents/{document}/preview',               [ServerResponsibleDocumentController::class, 'preview'])->name('responsibles.documents.preview');
            Route::delete('responsibles/{responsible}/documents/{document}',                     [ServerResponsibleDocumentController::class, 'destroy'])->name('responsibles.documents.destroy');

            Route::post('containers',                    [ServerContainerController::class, 'store'])->name('containers.store');
            Route::put('containers/{container}',         [ServerContainerController::class, 'update'])->name('containers.update');
            Route::delete('containers/{container}',      [ServerContainerController::class, 'destroy'])->name('containers.destroy');

            Route::post('database-servers',                    [DatabaseServerController::class, 'store'])->name('database-servers.store');
            Route::get('database-servers/{databaseServer}',   [DatabaseServerController::class, 'show'])->name('database-servers.show');
            Route::put('database-servers/{databaseServer}',   [DatabaseServerController::class, 'update'])->name('database-servers.update');
            Route::delete('database-servers/{databaseServer}',[DatabaseServerController::class, 'destroy'])->name('database-servers.destroy');

            Route::prefix('database-servers/{databaseServer}')->name('database-servers.')->group(function () {
                Route::post('responsibles',                                     [DatabaseServerResponsibleController::class, 'store'])->name('responsibles.store');
                Route::put('responsibles/{responsible}',                        [DatabaseServerResponsibleController::class, 'update'])->name('responsibles.update');
                Route::patch('responsibles/{responsible}/deactivate',           [DatabaseServerResponsibleController::class, 'deactivate'])->name('responsibles.deactivate');
                Route::patch('responsibles/{responsible}/reactivate',           [DatabaseServerResponsibleController::class, 'reactivate'])->name('responsibles.reactivate');
                Route::delete('responsibles/{responsible}',                     [DatabaseServerResponsibleController::class, 'destroy'])->name('responsibles.destroy');

                Route::post('responsibles/{responsible}/documents',                                          [DatabaseServerResponsibleDocumentController::class, 'store'])->name('responsibles.documents.store');
                Route::get('responsibles/{responsible}/documents/{document}/download',                       [DatabaseServerResponsibleDocumentController::class, 'download'])->name('responsibles.documents.download');
                Route::get('responsibles/{responsible}/documents/{document}/preview',                        [DatabaseServerResponsibleDocumentController::class, 'preview'])->name('responsibles.documents.preview');
                Route::delete('responsibles/{responsible}/documents/{document}',                             [DatabaseServerResponsibleDocumentController::class, 'destroy'])->name('responsibles.documents.destroy');
            });
        });
    });
});

require __DIR__.'/auth.php';
