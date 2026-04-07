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
use App\Http\Controllers\Admin\DatabaseServerController;
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
        Route::resource('personas', PersonaController::class);
        Route::resource('users', UserController::class);
        Route::resource('areas', AreaController::class);
        Route::resource('servers', ServerController::class);

        Route::prefix('servers/{server}')->name('servers.')->group(function () {
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
