<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class SessionTimeout
{
    public function handle(Request $request, Closure $next): Response
    {
        $inactivityMinutes = (int) config('security.session_inactivity', 0);

        if ($inactivityMinutes > 0 && Auth::check()) {
            $lastActivity = session('last_activity_at');

            if ($lastActivity && now()->diffInMinutes($lastActivity) >= $inactivityMinutes) {
                Auth::logout();
                $request->session()->invalidate();
                $request->session()->regenerateToken();

                return redirect()->route('login')
                    ->with('status', 'Tu sesión expiró por inactividad. Por favor inicia sesión nuevamente.');
            }

            session(['last_activity_at' => now()]);
        }

        return $next($request);
    }
}
