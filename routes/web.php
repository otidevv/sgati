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
use App\Http\Controllers\ReportController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\AreaController;
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
    });

    // ── Repositorio general de documentos ────────────────────────────────
    Route::get('/documents', [SystemDocumentController::class, 'repository'])
        ->name('documents.repository');

    // ── Reportes ──────────────────────────────────────────────────────────
    Route::get('/reports', [ReportController::class, 'index'])->name('reports.index');
    Route::get('/reports/export/excel', [ReportController::class, 'exportExcel'])->name('reports.excel');
    Route::get('/reports/export/pdf', [ReportController::class, 'exportPdf'])->name('reports.pdf');

    // ── Administración (solo admin) ───────────────────────────────────────
    Route::middleware('role:admin')->prefix('admin')->name('admin.')->group(function () {
        Route::resource('users', UserController::class);
        Route::resource('areas', AreaController::class);
    });
});

require __DIR__.'/auth.php';
