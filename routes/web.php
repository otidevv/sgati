<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\SystemController;
use App\Http\Controllers\SystemVersionController;
use App\Http\Controllers\SystemInfrastructureController;
use App\Http\Controllers\SystemDatabaseController;
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
use App\Http\Controllers\Admin\SettingController;
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
            ->except(['index', 'show']);

        // Servicios / APIs
        Route::resource('services', SystemServiceController::class)
            ->except(['index', 'show']);

        // Integraciones
        Route::resource('integrations', SystemIntegrationController::class)
            ->except(['index', 'show']);

        // Documentos
        Route::resource('documents', SystemDocumentController::class)
            ->except(['index', 'show', 'edit', 'update']);
        Route::get('documents/{document}/download', [SystemDocumentController::class, 'download'])
            ->name('documents.download');

        // Repositorios
        Route::resource('repositories', SystemRepositoryController::class)
            ->except(['index', 'show']);
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

    // ── Administración (solo admin) ───────────────────────────────────────
    Route::middleware('role:admin')->prefix('admin')->name('admin.')->group(function () {
        Route::get('personas/dni-lookup/{dni}', [PersonaController::class, 'dniLookup'])->name('personas.dni-lookup');
        Route::resource('personas', PersonaController::class);
        Route::resource('users', UserController::class);
        Route::resource('areas', AreaController::class);
        Route::resource('servers', ServerController::class);

        // Configuración
        Route::get('settings', [SettingController::class, 'index'])->name('settings.index');
        Route::post('settings/cache', [SettingController::class, 'clearCache'])->name('settings.cache');
        Route::post('settings/mail', [SettingController::class, 'updateMail'])->name('settings.mail');
        Route::post('settings/security', [SettingController::class, 'updateSecurity'])->name('settings.security');

        Route::prefix('servers/{server}')->name('servers.')->group(function () {
            Route::get('connect',                        [ServerController::class, 'connect'])->name('connect');

            Route::post('responsibles',                                                          [ServerResponsibleController::class, 'store'])->name('responsibles.store');
            Route::put('responsibles/{responsible}',                                             [ServerResponsibleController::class, 'update'])->name('responsibles.update');
            Route::delete('responsibles/{responsible}',                                          [ServerResponsibleController::class, 'destroy'])->name('responsibles.destroy');

            Route::post('responsibles/{responsible}/documents',                                  [ServerResponsibleDocumentController::class, 'store'])->name('responsibles.documents.store');
            Route::get('responsibles/{responsible}/documents/{document}/download',               [ServerResponsibleDocumentController::class, 'download'])->name('responsibles.documents.download');
            Route::delete('responsibles/{responsible}/documents/{document}',                     [ServerResponsibleDocumentController::class, 'destroy'])->name('responsibles.documents.destroy');

            Route::post('containers',                    [ServerContainerController::class, 'store'])->name('containers.store');
            Route::put('containers/{container}',         [ServerContainerController::class, 'update'])->name('containers.update');
            Route::delete('containers/{container}',      [ServerContainerController::class, 'destroy'])->name('containers.destroy');

            Route::post('database-servers',              [DatabaseServerController::class, 'store'])->name('database-servers.store');
            Route::put('database-servers/{databaseServer}', [DatabaseServerController::class, 'update'])->name('database-servers.update');
            Route::delete('database-servers/{databaseServer}', [DatabaseServerController::class, 'destroy'])->name('database-servers.destroy');
        });
    });
});

require __DIR__.'/auth.php';
