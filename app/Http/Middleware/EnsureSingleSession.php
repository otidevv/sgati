<?php

namespace App\Http\Middleware;

use App\Models\Setting;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class EnsureSingleSession
{
    public function handle(Request $request, Closure $next): Response
    {
        if (Auth::check() && (bool) Setting::get('single_session_enabled', false)) {
            $user          = Auth::user();
            $sessionToken  = $request->session()->get('session_token');

            if ($sessionToken === null || $sessionToken !== $user->session_token) {
                Auth::logout();
                $request->session()->invalidate();
                $request->session()->regenerateToken();

                return redirect()->route('login')
                    ->withErrors(['email' => 'Tu sesión fue cerrada porque iniciaste sesión desde otro dispositivo o navegador.']);
            }
        }

        return $next($request);
    }
}
