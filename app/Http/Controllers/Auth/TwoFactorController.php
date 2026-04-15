<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Mail\TwoFactorCode;
use App\Models\Setting;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\View\View;

class TwoFactorController extends Controller
{
    public function show(Request $request): View|RedirectResponse
    {
        if (! session('two_factor_user_id')) {
            return redirect()->route('login');
        }

        return view('auth.two-factor');
    }

    public function store(Request $request): RedirectResponse
    {
        $userId = session('two_factor_user_id');

        if (! $userId) {
            return redirect()->route('login');
        }

        $request->validate([
            'code' => ['required', 'string', 'size:6'],
        ]);

        $user = User::find($userId);

        if (! $user) {
            return redirect()->route('login');
        }

        if ($user->two_factor_code !== $request->code) {
            return back()->withErrors(['code' => 'El código ingresado es incorrecto.']);
        }

        if (! $user->two_factor_expires_at || $user->two_factor_expires_at->isPast()) {
            $request->session()->forget('two_factor_user_id');
            return redirect()->route('login')
                ->withErrors(['email' => 'El código ha expirado. Inicia sesión nuevamente.']);
        }

        // Código válido: limpiar y autenticar
        $user->update([
            'two_factor_code'       => null,
            'two_factor_expires_at' => null,
        ]);

        $request->session()->forget('two_factor_user_id');
        $request->session()->regenerate();

        Auth::login($user);

        return redirect()->intended(route('dashboard', absolute: false));
    }

    public function resend(Request $request): RedirectResponse
    {
        $userId = session('two_factor_user_id');

        if (! $userId) {
            return redirect()->route('login');
        }

        $user = User::find($userId);

        if (! $user) {
            return redirect()->route('login');
        }

        $code = str_pad((string) random_int(0, 999999), 6, '0', STR_PAD_LEFT);

        $user->update([
            'two_factor_code'       => $code,
            'two_factor_expires_at' => now()->addMinutes(10),
        ]);

        static::applyMailConfig();

        try {
            Mail::to($user->email)->send(new TwoFactorCode($user, $code));
        } catch (\Throwable $e) {
            return back()->withErrors(['code' => 'No se pudo enviar el correo. Verifica la configuración SMTP.']);
        }

        return back()->with('resent', true);
    }

    public static function applyMailConfig(): void
    {
        $m = Setting::getGroup('mail');
        if (empty($m)) return;

        config([
            'mail.default'                      => $m['mail_mailer']    ?? 'smtp',
            'mail.mailers.smtp.host'            => $m['mail_host']      ?? '',
            'mail.mailers.smtp.port'            => $m['mail_port']      ?? 587,
            'mail.mailers.smtp.username'        => $m['mail_username']  ?? '',
            'mail.mailers.smtp.password'        => $m['mail_password']  ?? '',
            'mail.mailers.smtp.encryption'      => $m['mail_encryption'] ?? 'tls',
            'mail.from.address'                 => $m['mail_from']      ?? '',
            'mail.from.name'                    => $m['mail_from_name'] ?? '',
        ]);
    }
}
