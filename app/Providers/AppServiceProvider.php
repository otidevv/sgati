<?php

namespace App\Providers;

use App\Models\User;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void {}

    public function boot(): void
    {
        // Admin omite todas las verificaciones de Gate
        Gate::before(function (User $user, string $ability) {
            if ($user->isAdmin()) {
                return true;
            }
        });

        // Para cualquier habilidad no definida explícitamente,
        // consulta los permisos del rol del usuario
        Gate::after(function (User $user, string $ability, $result) {
            if ($result === null) {
                return $user->role?->load('permissions')->hasPermission($ability) ?? false;
            }
        });
    }
}
