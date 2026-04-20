<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserLoginLog extends Model
{
    protected $fillable = [
        'user_id', 'ip_address', 'user_agent',
        'session_id', 'logged_in_at', 'logged_out_at',
    ];

    protected $casts = [
        'logged_in_at'  => 'datetime',
        'logged_out_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function getDurationAttribute(): ?string
    {
        $end = $this->logged_out_at ?? now();
        $seconds = $this->logged_in_at->diffInSeconds($end);

        if ($seconds < 60) return "{$seconds}s";
        if ($seconds < 3600) return floor($seconds / 60) . 'm ' . ($seconds % 60) . 's';

        $h = floor($seconds / 3600);
        $m = floor(($seconds % 3600) / 60);
        return "{$h}h {$m}m";
    }

    public function getOsAttribute(): string
    {
        $ua = $this->user_agent ?? '';

        return match (true) {
            str_contains($ua, 'Windows NT 10')  => 'Windows 10/11',
            str_contains($ua, 'Windows NT 6.3') => 'Windows 8.1',
            str_contains($ua, 'Windows NT 6.1') => 'Windows 7',
            str_contains($ua, 'Windows')        => 'Windows',
            str_contains($ua, 'iPhone')         => 'iOS (iPhone)',
            str_contains($ua, 'iPad')           => 'iOS (iPad)',
            str_contains($ua, 'Mac OS X')       => 'macOS',
            str_contains($ua, 'Android')        => 'Android',
            str_contains($ua, 'Linux')          => 'Linux',
            default                             => 'Desconocido',
        };
    }

    public function getBrowserAttribute(): string
    {
        $ua = $this->user_agent ?? '';

        return match (true) {
            str_contains($ua, 'Edg/')       => 'Microsoft Edge',
            str_contains($ua, 'OPR/')       => 'Opera',
            str_contains($ua, 'Opera/')     => 'Opera',
            str_contains($ua, 'Chrome/')    => 'Chrome',
            str_contains($ua, 'Firefox/')   => 'Firefox',
            str_contains($ua, 'Safari/')    => 'Safari',
            str_contains($ua, 'MSIE')       => 'Internet Explorer',
            str_contains($ua, 'Trident/')   => 'Internet Explorer 11',
            default                         => 'Desconocido',
        };
    }

    public function getIsExpiredAttribute(): bool
    {
        if ($this->logged_out_at !== null) return false;
        $lifetimeMinutes = config('session.lifetime', 120);
        return $this->logged_in_at->diffInMinutes(now()) > $lifetimeMinutes;
    }

    public static function record(\Illuminate\Http\Request $request, int $userId): self
    {
        // Cerrar sesiones abiertas anteriores del usuario (expiraron o fueron abandonadas)
        self::where('user_id', $userId)
            ->whereNull('logged_out_at')
            ->update(['logged_out_at' => now()]);

        return self::create([
            'user_id'      => $userId,
            'ip_address'   => self::resolveIp($request),
            'user_agent'   => $request->userAgent(),
            'session_id'   => $request->session()->getId(),
            'logged_in_at' => now(),
        ]);
    }

    private static function resolveIp(\Illuminate\Http\Request $request): string
    {
        // Revisar headers de proxy/balanceador en orden de prioridad
        foreach (['HTTP_X_FORWARDED_FOR', 'HTTP_CLIENT_IP', 'HTTP_X_REAL_IP'] as $header) {
            $value = $_SERVER[$header] ?? null;
            if ($value) {
                $ip = trim(explode(',', $value)[0]);
                if (filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE)) {
                    return $ip;
                }
            }
        }

        $ip = $request->ip();

        // Si es localhost, obtener la IP real de la máquina (como ipconfig)
        if (in_array($ip, ['127.0.0.1', '::1'])) {
            $localIp = gethostbyname(gethostname());
            if ($localIp && $localIp !== gethostname()) {
                return $localIp;
            }
        }

        return $ip;
    }

    public static function closeSession(string $sessionId): void
    {
        self::where('session_id', $sessionId)
            ->whereNull('logged_out_at')
            ->latest('logged_in_at')
            ->first()
            ?->update(['logged_out_at' => now()]);
    }
}
