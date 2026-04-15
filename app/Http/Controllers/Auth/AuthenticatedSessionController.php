<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Http\Controllers\Auth\TwoFactorController;
use App\Mail\TwoFactorCode;
use App\Models\Setting;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\View\View;

class AuthenticatedSessionController extends Controller
{
    /**
     * Display the login view.
     */
    public function create(): View
    {
        return view('auth.login');
    }

    /**
     * Handle an incoming authentication request.
     */
    public function store(LoginRequest $request): RedirectResponse
    {
        $request->authenticate();

        $user = Auth::user();

        // Verificar si 2FA está habilitado en configuración
        if ((bool) Setting::get('two_factor_enabled', false)) {
            // Generar código de 6 dígitos
            $code = str_pad((string) random_int(0, 999999), 6, '0', STR_PAD_LEFT);

            $user->update([
                'two_factor_code'       => $code,
                'two_factor_expires_at' => now()->addMinutes(10),
            ]);

            // Cerrar sesión hasta que verifique el código
            Auth::logout();
            session(['two_factor_user_id' => $user->id]);

            // Aplicar config de correo desde BD y enviar
            TwoFactorController::applyMailConfig();

            try {
                Mail::to($user->email)->send(new TwoFactorCode($user, $code));
            } catch (\Throwable) {
                // Si falla el correo, limpiar y mostrar error
                $user->update(['two_factor_code' => null, 'two_factor_expires_at' => null]);
                session()->forget('two_factor_user_id');
                return back()->withErrors(['email' => 'No se pudo enviar el código de verificación. Verifica la configuración SMTP.']);
            }

            return redirect()->route('two-factor.show');
        }

        $request->session()->regenerate();

        return redirect()->intended(route('dashboard', absolute: false));
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/');
    }
}
