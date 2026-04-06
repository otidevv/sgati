# SGATI — Estado Actual del Proyecto
## Diseño de Base de Datos, Backend y Guía para Vistas

> Actualizado: 2026-04-06 | Laravel 12 + PostgreSQL 15 (Docker) + Blade + Tailwind CSS + Alpine.js

---

## 1. Stack confirmado

| Capa | Tecnología |
|---|---|
| Framework | Laravel 12 |
| Backend | PHP 8.3 |
| Frontend | Blade + Tailwind CSS 3 + Alpine.js |
| Base de datos | PostgreSQL 15 (Docker, puerto 5433) |
| Autenticación | Laravel Breeze (Blade) |
| Exportación Excel | Maatwebsite Excel 3.1 |
| Exportación PDF | barryvdh/laravel-dompdf 3.1 |
| Interactividad | Alpine.js (tabs, modales, dropdowns) |
| Servidor local | `php artisan serve` → localhost:8000 |

---

## 2. Esquema de Base de Datos

### Orden de migraciones (respeta claves foráneas)

```
0001  users                    ← tabla base de Breeze (sin FK aún)
133902  personas               ← datos civiles (DNI, nombres, apellidos)
133903  areas                  ← unidades organizativas
133903  permissions            ← catálogo de permisos (systems.create, etc.)
133903  roles                  ← roles dinámicos (admin, technician, etc.)
133904  add_role_area_to_users ← agrega persona_id, role_id, area_id, is_active a users
133904  role_permission        ← pivot roles ↔ permisos
133904  systems                ← sistemas de información registrados
133905  system_infrastructure  ← servidor, IP, SSL, URL (1:1 con system)
133905  system_versions        ← historial de versiones por sistema
133906  system_databases       ← bases de datos por sistema
133907  system_services        ← APIs/servicios por sistema
133907  system_integrations    ← integraciones entre sistemas
133908  system_documents       ← archivos adjuntos por sistema
133908  system_status_logs     ← historial de cambios de estado
```

### Diagrama de relaciones

```
personas ──────────────────────────────────────────┐
                                                   │ persona_id
roles ──── role_permission ──── permissions        │
  │                                               ↓
  │ role_id                               users (auth)
  └──────────────────────────────────────────────┤
                                         area_id  │
areas ───────────────────────────────────────────┘
                                                   │ responsible_id
                                                   ↓
                                               systems
                                              /   |   \  \   \    \
                              infrastructure  versions  databases  \
                                                         services   \
                                                       integrations  \
                                                         documents  status_logs
```

### Tablas detalladas

#### `personas`
| Campo | Tipo | Notas |
|---|---|---|
| id | bigint PK | |
| dni | varchar(8) | unique |
| nombres | varchar(150) | |
| apellido_paterno | varchar(100) | |
| apellido_materno | varchar(100) | |
| fecha_nacimiento | date | nullable |
| sexo | enum(M,F) | nullable |
| telefono | varchar(20) | nullable |
| email_personal | varchar(150) | nullable |

#### `roles`
| Campo | Tipo | Notas |
|---|---|---|
| id | bigint PK | |
| name | varchar(50) | unique — admin, technician, documenter, viewer |
| label | varchar(100) | Administrador, Técnico, etc. |
| description | text | nullable |

#### `permissions`
| Campo | Tipo | Notas |
|---|---|---|
| id | bigint PK | |
| name | varchar(100) | unique — ej: systems.create |
| label | varchar(150) | ej: Crear sistemas |
| module | varchar(50) | ej: systems |

#### `role_permission` (pivot)
| Campo | Tipo |
|---|---|
| role_id | FK → roles |
| permission_id | FK → permissions |

#### `users` (extendida)
| Campo | Tipo | Notas |
|---|---|---|
| id | bigint PK | |
| persona_id | FK → personas | nullable |
| name | varchar | nombre de acceso |
| email | varchar | unique |
| password | varchar | hashed |
| role_id | FK → roles | nullable |
| area_id | FK → areas | nullable |
| is_active | boolean | default true |

#### `systems`
| Campo | Tipo | Notas |
|---|---|---|
| name, slug, acronym | varchar | slug único |
| status | enum | active, inactive, development, maintenance |
| area_id | FK → areas | |
| responsible_id | FK → users | |
| tech_stack, repo_url | varchar | |
| observations | text | |

---

## 3. Modelos Eloquent

| Modelo | Relaciones clave |
|---|---|
| `User` | belongsTo: Role, Area, Persona — hasMany: Systems |
| `Persona` | hasOne: User |
| `Role` | hasMany: Users — belongsToMany: Permissions |
| `Permission` | belongsToMany: Roles |
| `Area` | hasMany: Users, Systems |
| `System` | hasOne: Infrastructure — hasMany: Versions, Databases, Services, Documents, StatusLogs — hasMany: IntegrationsFrom, IntegrationsTo |
| `SystemInfrastructure` | belongsTo: System |
| `SystemVersion` | belongsTo: System, User(deployed_by) |
| `SystemDatabase` | belongsTo: System |
| `SystemService` | belongsTo: System |
| `SystemIntegration` | belongsTo: System(source), System(target) |
| `SystemDocument` | belongsTo: System, User(uploaded_by) |
| `SystemStatusLog` | belongsTo: System, User(changed_by) |

---

## 4. Control de Acceso (RBAC)

### Cómo funciona
- `Gate::before()` → si el usuario es **admin**, pasa todo sin verificar
- `Gate::after()` → para cualquier otro rol, consulta `role_permission`
- En Blade: `@can('systems.create')` / `@cannot('systems.delete')`
- En controlador: `$this->authorize('systems.create')`
- En User: `$user->hasPermission('systems.edit')`

### Permisos por rol (default)

| Permiso | admin | technician | documenter | viewer |
|---|:---:|:---:|:---:|:---:|
| systems.viewAny | ✅ | ✅ | ✅ | ✅ |
| systems.create | ✅ | ✅ | — | — |
| systems.edit | ✅ | ✅ | — | — |
| systems.delete | ✅ | — | — | — |
| infrastructure.edit | ✅ | ✅ | — | — |
| versions.create/edit/delete | ✅ | ✅ | — | — |
| databases.create/edit/delete | ✅ | ✅ | — | — |
| services.create/edit/delete | ✅ | ✅ | — | — |
| integrations.create/edit/delete | ✅ | ✅ | — | — |
| documents.download | ✅ | ✅ | ✅ | ✅ |
| documents.upload | ✅ | ✅ | ✅ | — |
| documents.delete | ✅ | ✅ | ✅ | — |
| reports.view | ✅ | ✅ | ✅ | ✅ |
| reports.export | ✅ | ✅ | ✅ | — |
| admin.users | ✅ | — | — | — |
| admin.areas | ✅ | — | — | — |
| admin.roles | ✅ | — | — | — |

---

## 5. Controladores existentes

```
app/Http/Controllers/
├── DashboardController.php          GET /dashboard
├── SystemController.php             CRUD /systems
├── SystemVersionController.php      /systems/{system}/versions
├── SystemInfrastructureController   /systems/{system}/infrastructure
├── SystemDatabaseController.php     /systems/{system}/databases
├── SystemServiceController.php      /systems/{system}/services
├── SystemIntegrationController.php  /systems/{system}/integrations
├── SystemDocumentController.php     /systems/{system}/documents + /documents (repo)
├── ReportController.php             /reports (index, excel, pdf)
└── Admin/
    ├── UserController.php           /admin/users
    └── AreaController.php           /admin/areas
```

**Pendiente crear:**
- `Admin/PersonaController.php` — CRUD de personas
- `Admin/RoleController.php` — gestión de roles y asignación de permisos

---

## 6. Flujo para crear un usuario

```
1. Admin > Personas > Nueva persona
   → Ingresar: DNI, nombres, apellidos, fecha_nacimiento, sexo

2. Admin > Usuarios > Nuevo usuario
   → Seleccionar persona registrada
   → Asignar: email, contraseña, rol, área
```

---

## 7. Seeders (datos iniciales)

| Seeder | Qué carga |
|---|---|
| RoleSeeder | admin, technician, documenter, viewer |
| PermissionSeeder | 25 permisos + asignación a roles |
| AreaSeeder | 8 áreas de UNAMAD (OTI, DGA, VRA, etc.) |
| UserSeeder | admin@unamad.edu.pe / CambiarPassword123! |

---

## 8. Guía para empezar las Vistas

### Orden recomendado de desarrollo

#### Paso 1 — Layout base (desbloqueador de todo)
Crear primero:
```
resources/views/layouts/app.blade.php      ← layout principal
resources/views/components/sidebar.blade.php
resources/views/components/header.blade.php
resources/views/components/status-badge.blade.php
resources/views/components/stat-card.blade.php
```
El layout usa Alpine.js para el sidebar colapsable (`x-data="{ sidebarOpen: false }"`).

#### Paso 2 — Dashboard
```
resources/views/dashboard/index.blade.php
```
Muestra: contadores por estado, sistemas recientes, alertas SSL próximas a vencer.

#### Paso 3 — CRUD de Sistemas (el núcleo)
```
resources/views/systems/index.blade.php    ← tabla con filtros (status, área, búsqueda)
resources/views/systems/create.blade.php   ← formulario
resources/views/systems/edit.blade.php     ← formulario
resources/views/systems/show.blade.php     ← ficha con tabs Alpine.js
resources/views/systems/tabs/
    ├── general.blade.php
    ├── infrastructure.blade.php
    ├── versions.blade.php
    ├── databases.blade.php
    ├── services.blade.php
    ├── integrations.blade.php
    ├── documents.blade.php
    └── logs.blade.php
```

#### Paso 4 — Administración
```
resources/views/admin/
    ├── personas/index.blade.php + form.blade.php
    ├── users/index.blade.php + form.blade.php
    ├── areas/index.blade.php + form.blade.php
    └── roles/index.blade.php + form.blade.php (con checkboxes de permisos)
```

#### Paso 5 — Documentos y Reportes
```
resources/views/documents/index.blade.php
resources/views/reports/index.blade.php
resources/views/pdf/systems-report.blade.php
```

### Convenciones de vistas

```blade
{{-- Proteger botones según permiso --}}
@can('systems.create')
    <a href="{{ route('systems.create') }}" class="btn-primary">Nuevo sistema</a>
@endcan

@can('systems.delete')
    <form action="{{ route('systems.destroy', $system) }}" method="POST">
        @csrf @method('DELETE')
        <button x-on:click.prevent="if(confirm('¿Eliminar?')) $el.closest('form').submit()">
            Eliminar
        </button>
    </form>
@endcan

{{-- Badge de estado --}}
<x-status-badge :status="$system->status" />

{{-- Flash messages (ya en el layout) --}}
@if(session('success'))
    <div class="alert-success">{{ session('success') }}</div>
@endif
```

### Paleta de colores Tailwind para estados

| Estado | Color |
|---|---|
| active | `bg-green-100 text-green-800` |
| development | `bg-blue-100 text-blue-800` |
| maintenance | `bg-yellow-100 text-yellow-800` |
| inactive | `bg-red-100 text-red-800` |

---

## 9. Comando para levantar el entorno

```bash
# 1. Iniciar PostgreSQL (Docker)
docker-compose up -d

# 2. Levantar servidor Laravel
cd c:/xampp/htdocs/laravel/SGATI/sgati
php artisan serve

# 3. Compilar assets (si se modifican CSS/JS)
npm run dev   # desarrollo con hot reload
npm run build # producción

# 4. Reset completo de BD (solo desarrollo)
php artisan migrate:fresh --seed
```

**Credenciales de acceso inicial:**
- URL: http://localhost:8000
- Email: `admin@unamad.edu.pe`
- Password: `CambiarPassword123!`

---

*SGATI v1.0 — OTI UNAMAD | Generado: 2026-04-06*
