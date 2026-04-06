<?php

namespace Database\Seeders;

use App\Models\Permission;
use App\Models\Role;
use Illuminate\Database\Seeder;

class PermissionSeeder extends Seeder
{
    public function run(): void
    {
        $permissions = [
            // Sistemas
            ['name' => 'systems.viewAny',         'label' => 'Ver listado de sistemas',       'module' => 'systems'],
            ['name' => 'systems.create',           'label' => 'Crear sistemas',                'module' => 'systems'],
            ['name' => 'systems.edit',             'label' => 'Editar sistemas',               'module' => 'systems'],
            ['name' => 'systems.delete',           'label' => 'Eliminar sistemas',             'module' => 'systems'],
            // Infraestructura
            ['name' => 'infrastructure.edit',      'label' => 'Editar infraestructura',        'module' => 'infrastructure'],
            // Versiones
            ['name' => 'versions.create',          'label' => 'Registrar versiones',           'module' => 'versions'],
            ['name' => 'versions.edit',            'label' => 'Editar versiones',              'module' => 'versions'],
            ['name' => 'versions.delete',          'label' => 'Eliminar versiones',            'module' => 'versions'],
            // Bases de datos
            ['name' => 'databases.create',         'label' => 'Registrar bases de datos',      'module' => 'databases'],
            ['name' => 'databases.edit',           'label' => 'Editar bases de datos',         'module' => 'databases'],
            ['name' => 'databases.delete',         'label' => 'Eliminar bases de datos',       'module' => 'databases'],
            // Servicios / APIs
            ['name' => 'services.create',          'label' => 'Registrar servicios/APIs',      'module' => 'services'],
            ['name' => 'services.edit',            'label' => 'Editar servicios/APIs',         'module' => 'services'],
            ['name' => 'services.delete',          'label' => 'Eliminar servicios/APIs',       'module' => 'services'],
            // Integraciones
            ['name' => 'integrations.create',      'label' => 'Registrar integraciones',       'module' => 'integrations'],
            ['name' => 'integrations.edit',        'label' => 'Editar integraciones',          'module' => 'integrations'],
            ['name' => 'integrations.delete',      'label' => 'Eliminar integraciones',        'module' => 'integrations'],
            // Documentos
            ['name' => 'documents.download',       'label' => 'Descargar documentos',          'module' => 'documents'],
            ['name' => 'documents.upload',         'label' => 'Subir documentos',              'module' => 'documents'],
            ['name' => 'documents.delete',         'label' => 'Eliminar documentos',           'module' => 'documents'],
            // Reportes
            ['name' => 'reports.view',             'label' => 'Ver reportes',                  'module' => 'reports'],
            ['name' => 'reports.export',           'label' => 'Exportar reportes',             'module' => 'reports'],
            // Administración
            ['name' => 'admin.users',              'label' => 'Gestionar usuarios',            'module' => 'admin'],
            ['name' => 'admin.areas',              'label' => 'Gestionar áreas',               'module' => 'admin'],
            ['name' => 'admin.roles',              'label' => 'Gestionar roles y permisos',    'module' => 'admin'],
        ];

        foreach ($permissions as $perm) {
            Permission::firstOrCreate(['name' => $perm['name']], $perm);
        }

        $this->assignRolePermissions();
    }

    private function assignRolePermissions(): void
    {
        $all = Permission::pluck('id', 'name');

        $map = [
            'technician' => [
                'systems.viewAny', 'systems.create', 'systems.edit',
                'infrastructure.edit',
                'versions.create', 'versions.edit', 'versions.delete',
                'databases.create', 'databases.edit', 'databases.delete',
                'services.create', 'services.edit', 'services.delete',
                'integrations.create', 'integrations.edit', 'integrations.delete',
                'documents.download', 'documents.upload', 'documents.delete',
                'reports.view', 'reports.export',
            ],
            'documenter' => [
                'systems.viewAny',
                'documents.download', 'documents.upload', 'documents.delete',
                'reports.view',
            ],
            'viewer' => [
                'systems.viewAny',
                'documents.download',
                'reports.view',
            ],
        ];

        foreach ($map as $roleName => $permNames) {
            $role = Role::where('name', $roleName)->first();
            if (!$role) continue;

            $ids = collect($permNames)->map(fn($n) => $all[$n] ?? null)->filter();
            $role->permissions()->sync($ids);
        }

        // admin no necesita permisos en DB — Gate::before lo deja pasar
    }
}
