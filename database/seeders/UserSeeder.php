<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Area;
use App\Models\Role;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $oti   = Area::where('acronym', 'OTI')->first();
        $admin = Role::where('name', 'admin')->first();

        User::firstOrCreate(
            ['email' => 'dmamanic@unamad.edu.pe'],
            [
                'name'      => 'Administrador OTI',
                'password'  => Hash::make('123'),
                'role_id'   => $admin?->id,
                'area_id'   => $oti?->id,
                'is_active' => true,
            ]
        );
    }
}
