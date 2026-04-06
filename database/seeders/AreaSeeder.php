<?php

namespace Database\Seeders;

use App\Models\Area;
use Illuminate\Database\Seeder;

class AreaSeeder extends Seeder
{
    public function run(): void
    {
        $areas = [
            ['name' => 'Oficina de Tecnologías de la Información', 'acronym' => 'OTI', 'description' => 'Área responsable de la gestión de sistemas y tecnología informática'],
            ['name' => 'Dirección General de Administración',       'acronym' => 'DGA', 'description' => null],
            ['name' => 'Vicerrectorado Académico',                  'acronym' => 'VRA', 'description' => null],
            ['name' => 'Vicerrectorado de Investigación',           'acronym' => 'VRI', 'description' => null],
            ['name' => 'Oficina de Admisión',                       'acronym' => 'OA',  'description' => null],
            ['name' => 'Unidad de Recursos Humanos',                'acronym' => 'URH', 'description' => null],
            ['name' => 'Unidad de Economía',                        'acronym' => 'UE',  'description' => null],
            ['name' => 'Biblioteca Central',                        'acronym' => 'BC',  'description' => null],
        ];

        foreach ($areas as $area) {
            Area::firstOrCreate(['acronym' => $area['acronym']], $area);
        }
    }
}
