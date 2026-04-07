<?php

namespace App\Providers;

use App\Models\Setting;
use App\Models\User;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void {}

    public function boot(): void
    {
        $this->loadMailSettingsFromDatabase();
        $this->loadSecuritySettingsFromDatabase();

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

    private function loadSecuritySettingsFromDatabase(): void
    {
        try {
            $sec = Setting::getGroup('security');

            if (empty($sec)) {
                return;
            }

            // Sobreescribir lifetime de sesión
            if (isset($sec['session_lifetime'])) {
                config(['session.lifetime' => (int) $sec['session_lifetime']]);
            }

            // Cierre de sesión al cerrar el navegador
            if (isset($sec['session_expire_on_close'])) {
                config(['session.expire_on_close' => (bool) $sec['session_expire_on_close']]);
            }

            // Valores de seguridad disponibles en config('security.*')
            config([
                'security.session_inactivity' => (int) ($sec['session_inactivity'] ?? 0),
                'security.max_login_attempts' => (int) ($sec['max_login_attempts'] ?? 5),
                'security.lockout_duration'   => (int) ($sec['lockout_duration'] ?? 1),
            ]);
        } catch (\Throwable) {
            // Tabla aún no existe
        }
    }

    private function loadMailSettingsFromDatabase(): void
    {
        try {
            $mail = Setting::getGroup('mail');

            if (empty($mail)) {
                return;
            }

            config([
                'mail.default'                      => $mail['mail_mailer']    ?? config('mail.default'),
                'mail.mailers.smtp.host'            => $mail['mail_host']      ?? config('mail.mailers.smtp.host'),
                'mail.mailers.smtp.port'            => $mail['mail_port']      ?? config('mail.mailers.smtp.port'),
                'mail.mailers.smtp.username'        => $mail['mail_username']  ?? config('mail.mailers.smtp.username'),
                'mail.mailers.smtp.password'        => $mail['mail_password']  ?? config('mail.mailers.smtp.password'),
                'mail.mailers.smtp.encryption'      => $mail['mail_encryption']?? config('mail.mailers.smtp.encryption'),
                'mail.from.address'                 => $mail['mail_from']      ?? config('mail.from.address'),
                'mail.from.name'                    => $mail['mail_from_name'] ?? config('mail.from.name'),
            ]);
        } catch (\Throwable) {
            // La tabla aún no existe (primera migración)
        }
    }
}
