# SGATI — Sistema de Gestión de Activos Tecnológicos e Informáticos
## Plan de Desarrollo — Laravel 11 Monolítico + Blade + Tailwind CSS

---

## 1. Stack Tecnológico

| Capa | Tecnología |
|---|---|
| Framework | Laravel 11 |
| Lenguaje backend | PHP 8.3 |
| Frontend | Blade + Tailwind CSS 3 |
| Interactividad UI | Alpine.js (sin build complejo) |
| ORM | Eloquent |
| Base de datos | PostgreSQL 15 |
| Autenticación | Laravel Breeze (Blade + Tailwind) |
| Tablas interactivas | Livewire 3 o Alpine.js + fetch |
| Upload de archivos | Laravel Storage (disco local) |
| Exportación Excel | Laravel Excel (Maatwebsite) |
| Exportación PDF | DomPDF (barryvdh/laravel-dompdf) |
| Tareas programadas | Laravel Scheduler |
| Servidor | Ubuntu 22.04 + Nginx + PHP-FPM 8.3 |
| Control de versiones | Git |

**¿Por qué Blade + Alpine.js y no React?**
Al ser monolítico, Blade renderiza todo en el servidor. Alpine.js agrega interactividad ligera (dropdowns, modales, tabs) sin necesidad de compilar un bundle separado. Para tablas con filtros complejos se usa Livewire 3, que también corre en el servidor. Cero API separada, cero CORS, un solo proyecto.

---

## 2. Inicialización del Proyecto

```bash
# Crear proyecto Laravel
composer create-project laravel/laravel sgati
cd sgati

# Instalar Breeze con Blade + Tailwind
composer require laravel/breeze --dev
php artisan breeze:install blade
npm install && npm run build

# Paquetes principales
composer require maatwebsite/excel
composer require barryvdh/laravel-dompdf
composer require livewire/livewire

# Tailwind ya viene con Breeze, solo verificar tailwind.config.js
```

Configurar `.env`:
```env
APP_NAME=SGATI
APP_URL=http://sgati.unamad.edu.pe
APP_LOCALE=es

DB_CONNECTION=pgsql
DB_HOST=127.0.0.1
DB_PORT=5432
DB_DATABASE=sgati_db
DB_USERNAME=sgati_user
DB_PASSWORD=password_seguro

FILESYSTEM_DISK=local
STORAGE_PATH=/var/www/sgati-storage
```

---

## 3. Estructura de Base de Datos (Migraciones)

Crear en este orden respetando las claves foráneas:

### 3.1 `create_areas_table`
```php
Schema::create('areas', function (Blueprint $table) {
    $table->id();
    $table->string('name', 150);
    $table->string('acronym', 20)->nullable();
    $table->text('description')->nullable();
    $table->timestamps();
});
```

### 3.2 Modificar `create_users_table` (extender la de Breeze)
```php
// Agregar a la migración de users existente:
$table->enum('role', ['admin', 'technician', 'documenter', 'viewer'])->default('viewer');
$table->foreignId('area_id')->nullable()->constrained('areas')->nullOnDelete();
$table->boolean('is_active')->default(true);
```

### 3.3 `create_systems_table`
```php
Schema::create('systems', function (Blueprint $table) {
    $table->id();
    $table->string('name', 100);
    $table->string('slug', 100)->unique();
    $table->string('acronym', 20)->nullable();
    $table->text('description')->nullable();
    $table->enum('status', ['active', 'inactive', 'development', 'maintenance'])->default('development');
    $table->foreignId('area_id')->nullable()->constrained('areas')->nullOnDelete();
    $table->foreignId('responsible_id')->nullable()->constrained('users')->nullOnDelete();
    $table->string('tech_stack', 255)->nullable();
    $table->string('repo_url', 255)->nullable();
    $table->text('observations')->nullable();
    $table->timestamps();
});
```

### 3.4 `create_system_infrastructure_table`
```php
Schema::create('system_infrastructure', function (Blueprint $table) {
    $table->id();
    $table->foreignId('system_id')->unique()->constrained('systems')->cascadeOnDelete();
    $table->string('server_name', 100)->nullable();
    $table->string('server_os', 100)->nullable();
    $table->string('server_ip', 45)->nullable();
    $table->string('public_ip', 45)->nullable();
    $table->string('system_url', 255)->nullable();
    $table->integer('port')->nullable();
    $table->string('web_server', 50)->nullable();
    $table->boolean('ssl_enabled')->default(false);
    $table->date('ssl_expiry')->nullable();
    $table->enum('environment', ['production', 'staging', 'development'])->default('production');
    $table->text('notes')->nullable();
    $table->timestamps();
});
```

### 3.5 `create_system_versions_table`
```php
Schema::create('system_versions', function (Blueprint $table) {
    $table->id();
    $table->foreignId('system_id')->constrained('systems')->cascadeOnDelete();
    $table->string('version', 20);
    $table->date('release_date');
    $table->enum('environment', ['production', 'staging', 'development'])->default('production');
    $table->text('changes')->nullable();
    $table->string('git_commit', 100)->nullable();
    $table->string('git_branch', 100)->nullable();
    $table->foreignId('deployed_by')->nullable()->constrained('users')->nullOnDelete();
    $table->text('notes')->nullable();
    $table->timestamps();
});
```

### 3.6 `create_system_databases_table`
```php
Schema::create('system_databases', function (Blueprint $table) {
    $table->id();
    $table->foreignId('system_id')->constrained('systems')->cascadeOnDelete();
    $table->string('db_name', 100);
    $table->enum('engine', ['postgresql', 'mysql', 'oracle', 'sqlserver', 'sqlite', 'mongodb', 'other']);
    $table->string('server_host', 100)->nullable();
    $table->integer('port')->nullable();
    $table->string('schema_name', 100)->nullable();
    $table->string('responsible', 100)->nullable();
    $table->enum('environment', ['production', 'staging', 'development'])->default('production');
    $table->text('notes')->nullable();
    $table->timestamps();
});
```

### 3.7 `create_system_services_table`
```php
Schema::create('system_services', function (Blueprint $table) {
    $table->id();
    $table->foreignId('system_id')->constrained('systems')->cascadeOnDelete();
    $table->string('service_name', 100);
    $table->enum('service_type', ['rest_api', 'soap', 'sftp', 'smtp', 'ldap', 'database', 'other']);
    $table->string('endpoint_url', 255)->nullable();
    $table->enum('direction', ['consumed', 'exposed']);
    $table->string('auth_type', 50)->nullable();
    $table->text('description')->nullable();
    $table->boolean('is_active')->default(true);
    $table->timestamps();
});
```

### 3.8 `create_system_integrations_table`
```php
Schema::create('system_integrations', function (Blueprint $table) {
    $table->id();
    $table->foreignId('source_system_id')->constrained('systems')->cascadeOnDelete();
    $table->foreignId('target_system_id')->constrained('systems')->cascadeOnDelete();
    $table->enum('connection_type', ['api', 'direct_db', 'file', 'sftp', 'other']);
    $table->text('description')->nullable();
    $table->boolean('is_active')->default(true);
    $table->text('notes')->nullable();
    $table->timestamps();
});
```

### 3.9 `create_system_documents_table`
```php
Schema::create('system_documents', function (Blueprint $table) {
    $table->id();
    $table->foreignId('system_id')->constrained('systems')->cascadeOnDelete();
    $table->enum('doc_type', ['manual_user', 'manual_technical', 'oficio', 'resolution', 'acta', 'contract', 'diagram', 'other']);
    $table->string('title', 255);
    $table->string('doc_number', 100)->nullable();
    $table->string('issuer', 150)->nullable();
    $table->date('issue_date')->nullable();
    $table->string('file_path', 500);
    $table->string('file_name', 255);
    $table->integer('file_size')->nullable();
    $table->string('mime_type', 100)->nullable();
    $table->foreignId('uploaded_by')->nullable()->constrained('users')->nullOnDelete();
    $table->text('notes')->nullable();
    $table->timestamps();
});
```

### 3.10 `create_system_status_logs_table`
```php
Schema::create('system_status_logs', function (Blueprint $table) {
    $table->id();
    $table->foreignId('system_id')->constrained('systems')->cascadeOnDelete();
    $table->string('old_status', 50)->nullable();
    $table->string('new_status', 50);
    $table->foreignId('changed_by')->nullable()->constrained('users')->nullOnDelete();
    $table->text('reason')->nullable();
    $table->timestamps();
});
```

Ejecutar:
```bash
php artisan migrate
```

---

## 4. Modelos Eloquent

### `app/Models/System.php`
```php
class System extends Model
{
    protected $fillable = [
        'name', 'slug', 'acronym', 'description', 'status',
        'area_id', 'responsible_id', 'tech_stack', 'repo_url', 'observations'
    ];

    protected $casts = ['status' => SystemStatus::class];

    public function area() { return $this->belongsTo(Area::class); }
    public function responsible() { return $this->belongsTo(User::class, 'responsible_id'); }
    public function infrastructure() { return $this->hasOne(SystemInfrastructure::class); }
    public function versions() { return $this->hasMany(SystemVersion::class)->orderByDesc('release_date'); }
    public function databases() { return $this->hasMany(SystemDatabase::class); }
    public function services() { return $this->hasMany(SystemService::class); }
    public function documents() { return $this->hasMany(SystemDocument::class)->orderByDesc('created_at'); }
    public function statusLogs() { return $this->hasMany(SystemStatusLog::class)->orderByDesc('created_at'); }
    public function integrationsFrom() { return $this->hasMany(SystemIntegration::class, 'source_system_id'); }
    public function integrationsTo() { return $this->hasMany(SystemIntegration::class, 'target_system_id'); }

    protected static function booted()
    {
        static::creating(function ($system) {
            $system->slug = Str::slug($system->name);
        });
    }
}
```

Crear modelos similares para: `Area`, `SystemInfrastructure`, `SystemVersion`, `SystemDatabase`, `SystemService`, `SystemIntegration`, `SystemDocument`, `SystemStatusLog`.

---

## 5. Estructura de Carpetas del Proyecto

```
sgati/
├── app/
│   ├── Enums/
│   │   ├── SystemStatus.php        ← active, inactive, development, maintenance
│   │   ├── Environment.php
│   │   ├── DocType.php
│   │   └── UserRole.php
│   ├── Http/
│   │   ├── Controllers/
│   │   │   ├── DashboardController.php
│   │   │   ├── SystemController.php
│   │   │   ├── SystemVersionController.php
│   │   │   ├── SystemInfrastructureController.php
│   │   │   ├── SystemDatabaseController.php
│   │   │   ├── SystemServiceController.php
│   │   │   ├── SystemIntegrationController.php
│   │   │   ├── SystemDocumentController.php
│   │   │   ├── ReportController.php
│   │   │   ├── Admin/
│   │   │   │   ├── UserController.php
│   │   │   │   └── AreaController.php
│   │   │   └── Auth/               ← generado por Breeze
│   │   ├── Middleware/
│   │   │   └── CheckRole.php       ← verificar rol del usuario
│   │   └── Requests/               ← Form Requests con validación
│   │       ├── StoreSystemRequest.php
│   │       ├── UpdateSystemRequest.php
│   │       ├── StoreVersionRequest.php
│   │       └── StoreDocumentRequest.php
│   ├── Models/
│   │   ├── System.php
│   │   ├── SystemInfrastructure.php
│   │   ├── SystemVersion.php
│   │   ├── SystemDatabase.php
│   │   ├── SystemService.php
│   │   ├── SystemIntegration.php
│   │   ├── SystemDocument.php
│   │   ├── SystemStatusLog.php
│   │   └── Area.php
│   ├── Policies/
│   │   └── SystemPolicy.php        ← quién puede crear/editar/eliminar
│   ├── Console/
│   │   └── Commands/
│   │       └── CheckSslExpiry.php  ← comando para alertas SSL
│   └── Exports/
│       └── SystemsExport.php       ← Laravel Excel
├── resources/
│   ├── views/
│   │   ├── layouts/
│   │   │   ├── app.blade.php       ← layout principal con sidebar
│   │   │   └── guest.blade.php     ← layout login
│   │   ├── components/
│   │   │   ├── sidebar.blade.php
│   │   │   ├── header.blade.php
│   │   │   ├── status-badge.blade.php
│   │   │   ├── stat-card.blade.php
│   │   │   └── modal.blade.php
│   │   ├── dashboard/
│   │   │   └── index.blade.php
│   │   ├── systems/
│   │   │   ├── index.blade.php     ← listado con filtros
│   │   │   ├── create.blade.php
│   │   │   ├── edit.blade.php
│   │   │   └── show.blade.php      ← ficha con tabs (Alpine.js)
│   │   ├── documents/
│   │   │   └── index.blade.php     ← repositorio general
│   │   ├── reports/
│   │   │   └── index.blade.php
│   │   ├── admin/
│   │   │   ├── users/
│   │   │   │   ├── index.blade.php
│   │   │   │   └── form.blade.php
│   │   │   └── areas/
│   │   │       ├── index.blade.php
│   │   │       └── form.blade.php
│   │   └── pdf/
│   │       └── systems-report.blade.php  ← template para DomPDF
│   ├── css/
│   │   └── app.css                 ← Tailwind directives
│   └── js/
│       └── app.js                  ← Alpine.js import
├── routes/
│   └── web.php                     ← todas las rutas (no api.php)
├── database/
│   ├── migrations/
│   └── seeders/
│       ├── DatabaseSeeder.php
│       ├── AreaSeeder.php
│       └── UserSeeder.php          ← usuario admin inicial
└── storage/
    └── app/
        └── documents/              ← archivos subidos (enlazado a /var/www/sgati-storage)
```

---

## 6. Rutas (`routes/web.php`)

```php
<?php
use App\Http\Controllers\{
    DashboardController, SystemController,
    SystemVersionController, SystemInfrastructureController,
    SystemDatabaseController, SystemServiceController,
    SystemIntegrationController, SystemDocumentController,
    ReportController
};
use App\Http\Controllers\Admin\{UserController, AreaController};

// Auth (generado por Breeze)
require __DIR__.'/auth.php';

// Rutas protegidas
Route::middleware(['auth'])->group(function () {

    Route::get('/', fn() => redirect()->route('dashboard'));
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // ── Sistemas ─────────────────────────────────────────────────────────
    Route::resource('systems', SystemController::class);

    // Sub-recursos anidados bajo un sistema
    Route::prefix('systems/{system}')->name('systems.')->group(function () {

        // Versiones
        Route::resource('versions', SystemVersionController::class)
            ->except(['index', 'show']);

        // Infraestructura (solo create/store/edit/update — es única por sistema)
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

    // ── Repositorio de documentos general ────────────────────────────────
    Route::get('/documents', [SystemDocumentController::class, 'repository'])
        ->name('documents.repository');

    // ── Reportes ──────────────────────────────────────────────────────────
    Route::get('/reports', [ReportController::class, 'index'])->name('reports.index');
    Route::get('/reports/export/excel', [ReportController::class, 'exportExcel'])->name('reports.excel');
    Route::get('/reports/export/pdf', [ReportController::class, 'exportPdf'])->name('reports.pdf');

    // ── Administración ────────────────────────────────────────────────────
    Route::middleware('role:admin')->prefix('admin')->name('admin.')->group(function () {
        Route::resource('users', UserController::class);
        Route::resource('areas', AreaController::class);
    });
});
```

---

## 7. Middleware de Roles (`app/Http/Middleware/CheckRole.php`)

```php
<?php
namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class CheckRole
{
    public function handle(Request $request, Closure $next, string ...$roles): mixed
    {
        if (!$request->user() || !in_array($request->user()->role->value, $roles)) {
            abort(403, 'No tienes permisos para acceder a esta sección.');
        }
        return $next($request);
    }
}
```

Registrar en `bootstrap/app.php`:
```php
->withMiddleware(function (Middleware $middleware) {
    $middleware->alias(['role' => CheckRole::class]);
})
```

---

## 8. Ejemplo de Controlador (`SystemController.php`)

```php
<?php
namespace App\Http\Controllers;

use App\Models\System;
use App\Models\Area;
use App\Models\User;
use App\Http\Requests\StoreSystemRequest;
use App\Enums\SystemStatus;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class SystemController extends Controller
{
    public function index(Request $request)
    {
        $systems = System::with(['area', 'responsible', 'infrastructure'])
            ->when($request->status, fn($q) => $q->where('status', $request->status))
            ->when($request->area_id, fn($q) => $q->where('area_id', $request->area_id))
            ->when($request->search, fn($q) => $q->where('name', 'ilike', "%{$request->search}%"))
            ->orderByDesc('updated_at')
            ->paginate(20)
            ->withQueryString();

        $areas = Area::orderBy('name')->get();
        $statuses = SystemStatus::cases();

        return view('systems.index', compact('systems', 'areas', 'statuses'));
    }

    public function show(System $system)
    {
        $system->load([
            'area', 'responsible', 'infrastructure',
            'versions', 'databases', 'services',
            'integrationsFrom.targetSystem',
            'integrationsTo.sourceSystem',
            'documents', 'statusLogs.changedBy'
        ]);

        $allSystems = System::where('id', '!=', $system->id)->get(['id', 'name', 'acronym']);

        return view('systems.show', compact('system', 'allSystems'));
    }

    public function store(StoreSystemRequest $request)
    {
        $system = System::create($request->validated());

        // Crear infraestructura vacía asociada
        $system->infrastructure()->create([]);

        return redirect()->route('systems.show', $system)
            ->with('success', 'Sistema registrado correctamente.');
    }

    public function update(StoreSystemRequest $request, System $system)
    {
        $oldStatus = $system->status->value;
        $system->update($request->validated());

        // Registrar cambio de estado si hubo
        if ($oldStatus !== $request->status) {
            $system->statusLogs()->create([
                'old_status' => $oldStatus,
                'new_status' => $request->status,
                'changed_by' => auth()->id(),
                'reason'     => $request->status_reason,
            ]);
        }

        return redirect()->route('systems.show', $system)
            ->with('success', 'Sistema actualizado correctamente.');
    }

    public function destroy(System $system)
    {
        $this->authorize('delete', $system);
        $system->delete();
        return redirect()->route('systems.index')
            ->with('success', 'Sistema eliminado.');
    }
}
```

---

## 9. Upload de Documentos (`SystemDocumentController.php`)

```php
public function store(Request $request, System $system)
{
    $request->validate([
        'title'      => 'required|string|max:255',
        'doc_type'   => 'required|in:manual_user,manual_technical,oficio,resolution,acta,contract,diagram,other',
        'file'       => 'required|file|max:51200|mimes:pdf,doc,docx,xls,xlsx,png,jpg,jpeg',
        'doc_number' => 'nullable|string|max:100',
        'issuer'     => 'nullable|string|max:150',
        'issue_date' => 'nullable|date',
        'notes'      => 'nullable|string',
    ]);

    $file = $request->file('file');
    $year = now()->format('Y');
    $month = now()->format('m');

    // Guardar en storage/app/documents/YYYY/MM/
    $path = $file->store("documents/{$year}/{$month}", 'local');

    $system->documents()->create([
        'title'        => $request->title,
        'doc_type'     => $request->doc_type,
        'doc_number'   => $request->doc_number,
        'issuer'       => $request->issuer,
        'issue_date'   => $request->issue_date,
        'file_path'    => $path,
        'file_name'    => $file->getClientOriginalName(),
        'file_size'    => $file->getSize(),
        'mime_type'    => $file->getMimeType(),
        'uploaded_by'  => auth()->id(),
        'notes'        => $request->notes,
    ]);

    return back()->with('success', 'Documento cargado correctamente.');
}

public function download(System $system, SystemDocument $document)
{
    // Verificar que el documento pertenece al sistema
    abort_if($document->system_id !== $system->id, 404);

    return Storage::disk('local')->download(
        $document->file_path,
        $document->file_name
    );
}
```

---

## 10. Tareas Programadas (`app/Console/Commands/CheckSslExpiry.php`)

```php
<?php
namespace App\Console\Commands;

use App\Models\SystemInfrastructure;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class CheckSslExpiry extends Command
{
    protected $signature   = 'sgati:check-ssl';
    protected $description = 'Verifica certificados SSL próximos a vencer';

    public function handle()
    {
        $expiring = SystemInfrastructure::with('system')
            ->where('ssl_enabled', true)
            ->whereNotNull('ssl_expiry')
            ->whereDate('ssl_expiry', '<=', now()->addDays(30))
            ->get();

        foreach ($expiring as $infra) {
            $days = now()->diffInDays($infra->ssl_expiry, false);
            Log::warning("SSL por vencer: {$infra->system->name} — {$days} días");
            // Aquí puedes agregar envío de correo con Mail::to(...)
        }

        $this->info("Revisados: {$expiring->count()} certificados próximos a vencer.");
    }
}
```

Registrar en `routes/console.php`:
```php
Schedule::command('sgati:check-ssl')->dailyAt('08:00');
```

Activar el scheduler en crontab del servidor:
```bash
* * * * * cd /var/www/sgati && php artisan schedule:run >> /dev/null 2>&1
```

---

## 11. Exportación Excel (`app/Exports/SystemsExport.php`)

```php
<?php
namespace App\Exports;

use App\Models\System;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class SystemsExport implements FromCollection, WithHeadings, WithStyles
{
    public function collection()
    {
        return System::with(['area', 'responsible', 'infrastructure'])
            ->get()
            ->map(fn($s) => [
                'Nombre'       => $s->name,
                'Sigla'        => $s->acronym,
                'Estado'       => $s->status->label(),
                'Area'         => $s->area?->name,
                'Responsable'  => $s->responsible?->name,
                'Tecnologia'   => $s->tech_stack,
                'URL'          => $s->infrastructure?->system_url,
                'IP Servidor'  => $s->infrastructure?->server_ip,
                'IP Publica'   => $s->infrastructure?->public_ip,
                'SSL Vence'    => $s->infrastructure?->ssl_expiry?->format('d/m/Y'),
                'Actualizado'  => $s->updated_at->format('d/m/Y'),
            ]);
    }

    public function headings(): array
    {
        return ['Nombre', 'Sigla', 'Estado', 'Area', 'Responsable',
                'Tecnologia', 'URL', 'IP Servidor', 'IP Publica', 'SSL Vence', 'Actualizado'];
    }

    public function styles(Worksheet $sheet)
    {
        return [1 => ['font' => ['bold' => true]]];
    }
}
```

---

## 12. Layout Principal (`resources/views/layouts/app.blade.php`)

```blade
<!DOCTYPE html>
<html lang="es" class="h-full bg-gray-50">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SGATI — @yield('title', 'OTI UNAMAD')</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="h-full" x-data="{ sidebarOpen: false }">

    <div class="flex h-full">
        {{-- Sidebar --}}
        @include('components.sidebar')

        {{-- Contenido principal --}}
        <div class="flex-1 flex flex-col overflow-hidden">
            @include('components.header')

            <main class="flex-1 overflow-y-auto p-6">
                {{-- Flash messages --}}
                @if(session('success'))
                    <div class="mb-4 p-4 bg-green-50 border border-green-200 text-green-800 rounded-lg">
                        {{ session('success') }}
                    </div>
                @endif
                @if(session('error'))
                    <div class="mb-4 p-4 bg-red-50 border border-red-200 text-red-800 rounded-lg">
                        {{ session('error') }}
                    </div>
                @endif

                @yield('content')
            </main>
        </div>
    </div>

</body>
</html>
```

---

## 13. Tabs con Alpine.js en la Ficha del Sistema

```blade
{{-- resources/views/systems/show.blade.php --}}
@extends('layouts.app')
@section('title', $system->name)

@section('content')
<div x-data="{ tab: 'general' }">

    {{-- Tab Navigation --}}
    <div class="border-b border-gray-200 mb-6">
        <nav class="flex space-x-1 -mb-px">
            @foreach([
                'general'        => 'General',
                'infrastructure' => 'Infraestructura',
                'versions'       => 'Versiones',
                'databases'      => 'Bases de Datos',
                'services'       => 'APIs / Servicios',
                'integrations'   => 'Integraciones',
                'documents'      => 'Documentos',
                'logs'           => 'Historial',
            ] as $key => $label)
            <button
                @click="tab = '{{ $key }}'"
                :class="tab === '{{ $key }}'
                    ? 'border-blue-600 text-blue-600'
                    : 'border-transparent text-gray-500 hover:text-gray-700'"
                class="px-4 py-2 border-b-2 font-medium text-sm transition-colors">
                {{ $label }}
            </button>
            @endforeach
        </nav>
    </div>

    {{-- Tab Content --}}
    <div x-show="tab === 'general'">
        @include('systems.tabs.general')
    </div>
    <div x-show="tab === 'infrastructure'">
        @include('systems.tabs.infrastructure')
    </div>
    <div x-show="tab === 'versions'">
        @include('systems.tabs.versions')
    </div>
    <div x-show="tab === 'databases'">
        @include('systems.tabs.databases')
    </div>
    <div x-show="tab === 'services'">
        @include('systems.tabs.services')
    </div>
    <div x-show="tab === 'integrations'">
        @include('systems.tabs.integrations')
    </div>
    <div x-show="tab === 'documents'">
        @include('systems.tabs.documents')
    </div>
    <div x-show="tab === 'logs'">
        @include('systems.tabs.logs')
    </div>

</div>
@endsection
```

---

## 14. Configuración Nginx

```nginx
server {
    listen 80;
    server_name sgati.unamad.edu.pe;
    return 301 https://$host$request_uri;
}

server {
    listen 443 ssl;
    server_name sgati.unamad.edu.pe;

    root /var/www/sgati/public;
    index index.php;

    ssl_certificate     /etc/ssl/certs/unamad.crt;
    ssl_certificate_key /etc/ssl/private/unamad.key;

    client_max_body_size 50M;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.3-fpm.sock;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
    }

    location ~ /\.(?!well-known).* {
        deny all;
    }
}
```

Deploy:
```bash
cd /var/www/sgati
composer install --no-dev --optimize-autoloader
php artisan migrate --force
php artisan config:cache
php artisan route:cache
php artisan view:cache
npm run build
php artisan storage:link
```

---

## 15. Seeders Iniciales

```bash
php artisan make:seeder AreaSeeder
php artisan make:seeder UserSeeder
```

`UserSeeder.php` — crear usuario admin inicial:
```php
User::create([
    'name'     => 'Administrador OTI',
    'email'    => 'admin@unamad.edu.pe',
    'password' => Hash::make('CambiarPassword123!'),
    'role'     => 'admin',
]);
```

```bash
php artisan db:seed
```

---

## 16. Fases de Desarrollo

### Fase 1 — MVP (semanas 1–4)
- [ ] Crear proyecto con Breeze (Blade + Tailwind)
- [ ] Ejecutar todas las migraciones
- [ ] Crear Enums: SystemStatus, Environment, DocType, UserRole
- [ ] Crear todos los modelos con relaciones Eloquent
- [ ] Middleware de roles + Policies
- [ ] Layout principal con sidebar y header
- [ ] CRUD de Áreas (admin)
- [ ] CRUD de Usuarios con roles (admin)
- [ ] CRUD de Sistemas (ficha general)
- [ ] Módulo de Infraestructura
- [ ] Módulo de Versiones
- [ ] Upload y descarga de Documentos
- [ ] Dashboard: contadores, actividad reciente, alertas SSL
- [ ] Seeders: área y usuario admin inicial

### Fase 2 — Gestión técnica (semanas 5–7)
- [ ] Módulo de Bases de Datos por sistema
- [ ] Módulo de APIs / Servicios
- [ ] Módulo de Integraciones
- [ ] Vista de historial de cambios de estado
- [ ] Repositorio general de documentos con filtros

### Fase 3 — Reportes y automatización (semanas 8–10)
- [ ] Exportación a Excel con Laravel Excel
- [ ] Exportación a PDF con DomPDF
- [ ] Comando Artisan `sgati:check-ssl` + Scheduler
- [ ] Comando para ping/verificación de disponibilidad de sistemas

### Fase 4 — Mejoras (continuo)
- [ ] Notificaciones por correo (Laravel Mail + Mailables)
- [ ] Búsqueda global en el header
- [ ] Gráfico visual de integraciones (usando Chart.js o vis.js vía CDN)
- [ ] Seed masivo de los sistemas existentes de la OTI

---

## 17. Convenciones para el Agente

- **Todo en español** en la interfaz (labels, mensajes, validaciones)
- **Rutas web únicamente** — no usar `api.php`, todo por `web.php` con sesión
- **Form Requests** para toda validación de formularios
- **Policies** para autorización (quién puede crear, editar, eliminar)
- **Blade Components** para elementos repetidos (badge de estado, tarjetas, modales)
- **Alpine.js** para interactividad ligera: tabs, dropdowns, confirmaciones, toggles
- **Paginación Laravel** nativa con `->paginate(20)` y `{{ $items->links() }}` en vistas
- **Flash messages** con `session('success')` y `session('error')` en el layout
- **Storage disk `local`** para archivos — nunca `public` disk para documentos sensibles
- **Tailwind clases** directamente en Blade, sin CSS personalizado salvo casos excepcionales
- **Nunca exponer rutas de archivos** — siempre descargar a través del controlador autenticado

---

*Documento generado para la Oficina de Tecnologías de la Información — UNAMAD*
*Sistema: SGATI v1.0 — Plan Laravel 11 Monolítico + Blade + Tailwind CSS*
