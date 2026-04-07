<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;

class SettingController extends Controller
{
    public function index()
    {
        $mailDb = Setting::getGroup('mail');
        $secDb  = Setting::getGroup('security');

        $settings = [
            'app_name'         => config('app.name', 'SGATI'),
            'app_env'          => config('app.env', 'production'),
            'app_debug'        => config('app.debug', false),
            'app_url'          => config('app.url', ''),
            'app_timezone'     => config('app.timezone', 'UTC'),
            'app_locale'       => config('app.locale', 'es'),
            'db_connection'    => config('database.default', 'mysql'),
            'cache_driver'     => config('cache.default', 'file'),
            'session_driver'   => config('session.driver', 'file'),
            // Correo: primero BD, luego .env
            'mail_mailer'      => $mailDb['mail_mailer']      ?? config('mail.default', 'smtp'),
            'mail_host'        => $mailDb['mail_host']         ?? config('mail.mailers.smtp.host', ''),
            'mail_port'        => $mailDb['mail_port']         ?? config('mail.mailers.smtp.port', 587),
            'mail_username'    => $mailDb['mail_username']     ?? config('mail.mailers.smtp.username', ''),
            'mail_encryption'  => $mailDb['mail_encryption']  ?? config('mail.mailers.smtp.encryption', 'tls'),
            'mail_from'        => $mailDb['mail_from']         ?? config('mail.from.address', ''),
            'mail_from_name'   => $mailDb['mail_from_name']    ?? config('mail.from.name', ''),
            'mail_configured'  => !empty($mailDb),
            // Seguridad: primero BD, luego defaults
            'session_lifetime'        => (int) ($secDb['session_lifetime']        ?? config('session.lifetime', 120)),
            'session_inactivity'      => (int) ($secDb['session_inactivity']      ?? 0),
            'session_expire_on_close' => (bool) ($secDb['session_expire_on_close'] ?? config('session.expire_on_close', false)),
            'max_login_attempts'      => (int) ($secDb['max_login_attempts']      ?? 5),
            'lockout_duration'        => (int) ($secDb['lockout_duration']        ?? 1),
            'security_configured'     => !empty($secDb),
        ];

        $phpVersion     = PHP_VERSION;
        $laravelVersion = app()->version();

        try {
            $dbVersion = DB::select('SELECT VERSION() as version')[0]->version ?? '—';
        } catch (\Throwable) {
            $dbVersion = '—';
        }

        return view('admin.settings.index', compact('settings', 'phpVersion', 'laravelVersion', 'dbVersion'));
    }

    public function updateMail(Request $request)
    {
        $data = $request->validate([
            'mail_mailer'    => ['required', 'in:smtp,sendmail,mailgun,ses,log'],
            'mail_host'      => ['required', 'string', 'max:255'],
            'mail_port'      => ['required', 'integer', 'min:1', 'max:65535'],
            'mail_username'  => ['nullable', 'string', 'max:255'],
            'mail_password'  => ['nullable', 'string', 'max:255'],
            'mail_encryption'=> ['nullable', 'in:tls,ssl,starttls,'],
            'mail_from'      => ['required', 'email', 'max:255'],
            'mail_from_name' => ['required', 'string', 'max:255'],
        ]);

        // Si la contraseña viene vacía, no sobreescribir la guardada
        if (empty($data['mail_password'])) {
            unset($data['mail_password']);
        }

        foreach ($data as $key => $value) {
            Setting::set($key, $value, 'mail');
        }

        return back()->with('success', 'Configuración de correo guardada correctamente.');
    }

    public function updateSecurity(Request $request)
    {
        $data = $request->validate([
            'session_lifetime'       => ['required', 'integer', 'min:5', 'max:1440'],
            'session_inactivity'     => ['required', 'integer', 'min:0', 'max:480'],
            'session_expire_on_close'=> ['nullable', 'boolean'],
            'max_login_attempts'     => ['required', 'integer', 'min:1', 'max:20'],
            'lockout_duration'       => ['required', 'integer', 'min:1', 'max:60'],
        ]);

        $data['session_expire_on_close'] = $request->boolean('session_expire_on_close') ? '1' : '0';

        foreach ($data as $key => $value) {
            Setting::set($key, $value, 'security');
        }

        return back()->with('success', 'Configuración de seguridad guardada. Los cambios aplican en la próxima solicitud.');
    }

    public function clearCache()
    {
        Artisan::call('cache:clear');
        Artisan::call('config:clear');
        Artisan::call('view:clear');
        Artisan::call('route:clear');

        return back()->with('success', 'Cache limpiada correctamente.');
    }
}
