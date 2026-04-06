<?php

namespace Database\Seeders;

use App\Models\Role;
use Illuminate\Database\Seeder;

class RoleSeeder extends Seeder
{
    public function run(): void
    {
        $roles = [
            ['name' => 'admin',      'label' => 'Administrador', 'description' => 'Acceso total al sistema'],
            ['name' => 'technician', 'label' => 'Técnico',       'description' => 'Gestiona sistemas, infraestructura y versiones'],
            ['name' => 'documenter', 'label' => 'Documentador',  'description' => 'Carga y gestiona documentos'],
            ['name' => 'viewer',     'label' => 'Visualizador',  'description' => 'Solo lectura'],
        ];

        foreach ($roles as $role) {
            Role::firstOrCreate(['name' => $role['name']], $role);
        }
    }
}
