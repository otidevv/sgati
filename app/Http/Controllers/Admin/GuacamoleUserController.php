<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\GuacamoleService;
use Illuminate\Support\Str;

class GuacamoleUserController extends Controller
{
    /**
     * Crea o actualiza el usuario en Apache Guacamole.
     * Genera una contraseña segura aleatoria y la guarda encriptada.
     */
    public function sync(User $user)
    {
        $this->authorize('admin.users');

        try {
            $guac = new GuacamoleService();
            $auth = $guac->authenticate();

            // Reutiliza la contraseña existente o genera una nueva
            // getGuacamolePasswordDecrypted() retorna null si el APP_KEY cambió
            $guacPassword = $user->getGuacamolePasswordDecrypted() ?? Str::password(20, symbols: false);
            $username     = $user->guacamole_username ?? $user->email;
            $fullName     = $user->nombre_completo;

            if ($guac->guacamoleUserExists($username, $auth['authToken'], $auth['dataSource'])) {
                $guac->updateGuacamoleUser(
                    $username, $guacPassword, $fullName, $user->email,
                    $auth['authToken'], $auth['dataSource']
                );
                $msg = "Usuario [{$user->name}] actualizado en Guacamole.";
            } else {
                $guac->createGuacamoleUser(
                    $username, $guacPassword, $fullName, $user->email,
                    $auth['authToken'], $auth['dataSource']
                );
                $msg = "Usuario [{$user->name}] creado en Guacamole.";
            }

            $user->update([
                'guacamole_username'  => $username,
                'guacamole_password'  => $guacPassword,
                'guacamole_synced_at' => now(),
            ]);

            return back()->with('success', $msg);

        } catch (\Throwable $e) {
            return back()->with('error', "No se pudo sincronizar con Guacamole: {$e->getMessage()}");
        }
    }

    /**
     * Elimina el usuario de Apache Guacamole y limpia los campos locales.
     */
    public function destroy(User $user)
    {
        $this->authorize('admin.users');

        if (! $user->guacamole_username) {
            return back()->with('error', 'Este usuario no tiene cuenta en Guacamole.');
        }

        try {
            $guac = new GuacamoleService();
            $auth = $guac->authenticate();

            $guac->deleteGuacamoleUser($user->guacamole_username, $auth['authToken'], $auth['dataSource']);

            $user->update([
                'guacamole_username'  => null,
                'guacamole_password'  => null,
                'guacamole_synced_at' => null,
            ]);

            return back()->with('success', "Usuario [{$user->name}] eliminado de Guacamole.");

        } catch (\Throwable $e) {
            return back()->with('error', "No se pudo eliminar de Guacamole: {$e->getMessage()}");
        }
    }
}
