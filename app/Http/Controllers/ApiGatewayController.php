<?php

namespace App\Http\Controllers;

use App\Models\ServiceGatewayKey;
use App\Models\ServiceGatewayLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\RateLimiter;

class ApiGatewayController extends Controller
{
    /**
     * Recibe cualquier petición HTTP al gateway y la redirige al backend real.
     * Ruta: ANY /api/gw/{slug}/{path?}
     *
     * El slug pertenece al CONSUMIDOR (ServiceGatewayKey), no al servicio.
     * Cada consumidor tiene su propia URL de gateway.
     */
    public function handle(Request $request, string $slug, string $path = '')
    {
        // ── 1. Encontrar la clave del consumidor por su slug ─────────────────
        $gatewayKey = ServiceGatewayKey::with('service')
            ->where('gateway_slug', $slug)
            ->first();

        if (!$gatewayKey || !$gatewayKey->service) {
            return response()->json(['error' => 'Gateway no encontrado.'], 404);
        }

        $service = $gatewayKey->service;

        // El servicio debe estar activo y ser expuesto
        if (!$service->is_active || $service->direction !== 'exposed') {
            return response()->json(['error' => 'El servicio no está disponible.'], 503);
        }

        // ── 2. Validar que la clave del consumidor esté activa ───────────────
        if (!$gatewayKey->is_active) {
            $this->log($service, $gatewayKey, $request, $path, 403, 0, 'Clave desactivada');
            return response()->json(['error' => 'Esta clave de acceso ha sido desactivada.'], 403);
        }

        if ($gatewayKey->isExpired()) {
            $this->log($service, $gatewayKey, $request, $path, 403, 0, 'Clave expirada');
            return response()->json(['error' => 'Esta clave de acceso ha expirado.'], 403);
        }

        // ── 3. Validar autenticación según el tipo del consumidor ────────────
        $authType = $gatewayKey->auth_type ?? 'bearer';

        if ($authType !== 'none') {
            $rawKey = match ($authType) {
                'bearer'      => $this->extractBearer($request),
                'api_key'     => $request->header('X-API-Key'),
                'query_param' => $request->query('api_key'),
                default       => null,
            };

            $hint = match ($authType) {
                'bearer'      => 'Envía el header: Authorization: Bearer <token>',
                'api_key'     => 'Envía el header: X-API-Key: <token>',
                'query_param' => 'Agrega el parámetro: ?api_key=<token>',
                default       => 'Token requerido',
            };

            if (!$rawKey) {
                $this->log($service, $gatewayKey, $request, $path, 401, 0, "Token requerido ({$authType})");
                return response()->json(['error' => 'Autenticación requerida.', 'hint' => $hint], 401);
            }

            if (!$gatewayKey->matchesRawKey($rawKey)) {
                $this->log($service, $gatewayKey, $request, $path, 401, 0, "Token inválido ({$authType})");
                return response()->json(['error' => 'Token inválido.'], 401);
            }
        }

        // ── 4. Whitelist de IPs ──────────────────────────────────────────────
        if (!empty($gatewayKey->allowed_ips) && !in_array($request->ip(), $gatewayKey->allowed_ips)) {
            $this->log($service, $gatewayKey, $request, $path, 403, 0, 'IP no permitida: ' . $request->ip());
            return response()->json(['error' => 'Tu IP no está autorizada para esta clave.'], 403);
        }

        // ── 5. Verificar horario de operación ────────────────────────────────
        if ($service->gateway_active_from && $service->gateway_active_to) {
            $now  = now()->format('H:i');
            $from = $service->gateway_active_from;
            $to   = $service->gateway_active_to;
            $inRange = ($from <= $to)
                ? ($now >= $from && $now <= $to)
                : ($now >= $from || $now <= $to);

            if (!$inRange) {
                $this->log($service, $gatewayKey, $request, $path, 503, 0, 'Fuera de horario de operación');
                return response()->json([
                    'error' => "Servicio no disponible en este horario. Operativo de {$from} a {$to}.",
                ], 503);
            }
        }

        // ── 6. Rate limiting (límite por consumidor, fallback al servicio) ───
        $baseKey   = "gw:{$service->id}:key:{$gatewayKey->id}";
        $perMinute = $gatewayKey->rate_per_minute ?? $service->gateway_rate_per_minute;
        $perDay    = $gatewayKey->rate_per_day    ?? $service->gateway_rate_per_day;

        if ($perMinute && !RateLimiter::attempt("{$baseKey}:min", $perMinute, fn() => true, 60)) {
            $this->log($service, $gatewayKey, $request, $path, 429, 0, 'Rate limit por minuto excedido');
            return response()->json([
                'error'       => 'Límite de solicitudes por minuto excedido.',
                'retry_after' => RateLimiter::availableIn("{$baseKey}:min"),
            ], 429);
        }

        if ($perDay && !RateLimiter::attempt("{$baseKey}:day", $perDay, fn() => true, 86400)) {
            $this->log($service, $gatewayKey, $request, $path, 429, 0, 'Rate limit diario excedido');
            return response()->json([
                'error'       => 'Límite de solicitudes diario excedido.',
                'retry_after' => RateLimiter::availableIn("{$baseKey}:day"),
            ], 429);
        }

        // ── 7. Construir URL destino ─────────────────────────────────────────
        $backendBase = rtrim($service->endpoint_url, '/');
        $targetUrl   = $path ? $backendBase . '/' . ltrim($path, '/') : $backendBase;
        $queryString = $request->getQueryString();
        if ($queryString) {
            parse_str($queryString, $params);
            unset($params['api_key']);
            $cleanQuery = http_build_query($params);
            if ($cleanQuery) {
                $targetUrl .= '?' . $cleanQuery;
            }
        }

        // ── 8. Preparar headers a reenviar ───────────────────────────────────
        $skipHeaders    = ['host', 'x-api-key', 'transfer-encoding', 'content-length', 'cookie', 'accept-encoding'];
        $forwardHeaders = [];
        foreach ($request->headers->all() as $name => $values) {
            if (!in_array(strtolower($name), $skipHeaders)) {
                $forwardHeaders[$name] = $values[0] ?? '';
            }
        }

        // ── 9. Proxiar la petición ───────────────────────────────────────────
        $startTime = microtime(true);

        try {
            $client = Http::withHeaders($forwardHeaders)->timeout(30)->withOptions(['verify' => false]);
            $method = strtolower($request->method());

            if (in_array($method, ['post', 'put', 'patch'])) {
                $contentType = strtolower($request->header('Content-Type', ''));
                if (str_contains($contentType, 'multipart/form-data')) {
                    $proxyResponse = $client->asMultipart()->$method($targetUrl, $request->all());
                } elseif (str_contains($contentType, 'application/x-www-form-urlencoded')) {
                    $proxyResponse = $client->asForm()->$method($targetUrl, $request->all());
                } else {
                    $proxyResponse = $client->$method($targetUrl, $request->all());
                }
            } else {
                $proxyResponse = $client->$method($targetUrl);
            }

            $responseStatus = $proxyResponse->status();
            $responseTimeMs = (int) ((microtime(true) - $startTime) * 1000);

        } catch (\Exception $e) {
            $responseTimeMs = (int) ((microtime(true) - $startTime) * 1000);
            $this->log($service, $gatewayKey, $request, $path, 502, $responseTimeMs, $e->getMessage());
            return response()->json(['error' => 'El backend no está disponible.', 'detail' => $e->getMessage()], 502);
        }

        // ── 10. Registrar y actualizar contadores ────────────────────────────
        $this->log($service, $gatewayKey, $request, $path, $responseStatus, $responseTimeMs);

        $gatewayKey->increment('total_requests');
        $gatewayKey->update(['last_used_at' => now()]);

        // ── 11. Devolver respuesta del backend ───────────────────────────────
        $skipResponseHeaders = ['transfer-encoding', 'content-encoding', 'set-cookie'];
        $responseHeaders     = [];
        foreach ($proxyResponse->headers() as $name => $values) {
            if (!in_array(strtolower($name), $skipResponseHeaders)) {
                $responseHeaders[$name] = is_array($values) ? $values[0] : $values;
            }
        }

        return response($proxyResponse->body(), $responseStatus)
            ->withHeaders($responseHeaders);
    }

    // ── Helpers ──────────────────────────────────────────────────────────────

    private function extractBearer(Request $request): ?string
    {
        $header = $request->header('Authorization', '');
        if (str_starts_with($header, 'Bearer ')) {
            return substr($header, 7);
        }
        return null;
    }

    private function log(
        $service,
        ?ServiceGatewayKey $key,
        Request $request,
        string $path,
        ?int $status,
        int $timeMs,
        ?string $error = null
    ): void {
        ServiceGatewayLog::create([
            'system_service_id' => $service->id,
            'gateway_key_id'    => $key?->id,
            'method'            => $request->method(),
            'path_info'         => $path ?: null,
            'query_string'      => $request->getQueryString() ?: null,
            'ip_address'        => $request->ip(),
            'response_status'   => $status,
            'response_time_ms'  => $timeMs,
            'error_message'     => $error,
            'created_at'        => now(),
        ]);
    }
}
