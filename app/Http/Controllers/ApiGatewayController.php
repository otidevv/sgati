<?php

namespace App\Http\Controllers;

use App\Models\ServiceGatewayKey;
use App\Models\ServiceGatewayLog;
use App\Models\SystemService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\RateLimiter;

class ApiGatewayController extends Controller
{
    /**
     * Recibe cualquier petición HTTP al gateway y la redirige al backend real.
     * Ruta: ANY /api/gw/{slug}/{path?}
     */
    public function handle(Request $request, string $slug, string $path = '')
    {
        // ── 1. Encontrar servicio activo ─────────────────────────────────────
        $service = SystemService::where('gateway_slug', $slug)
            ->where('gateway_enabled', true)
            ->where('direction', 'exposed')
            ->first();

        if (!$service) {
            return response()->json(['error' => 'Gateway no encontrado o deshabilitado.'], 404);
        }

        // ── 2. Validar API Key ───────────────────────────────────────────────
        $gatewayKey = null;
        if ($service->gateway_require_key) {
            $rawKey = $request->header('X-API-Key') ?? $request->query('api_key');

            if (!$rawKey) {
                $this->log($service, null, $request, $path, 401, 0, 'API key requerida');
                return response()->json(['error' => 'API key requerida. Envía el header X-API-Key.'], 401);
            }

            $prefix = substr($rawKey, 0, 8);
            $hash   = hash('sha256', $rawKey);

            $gatewayKey = $service->gatewayKeys()
                ->where('key_prefix', $prefix)
                ->where('key_hash', $hash)
                ->where('is_active', true)
                ->where(fn($q) => $q->whereNull('expires_at')->orWhere('expires_at', '>', now()))
                ->first();

            if (!$gatewayKey) {
                $this->log($service, null, $request, $path, 401, 0, 'API key inválida o expirada');
                return response()->json(['error' => 'API key inválida o expirada.'], 401);
            }

            // Whitelist de IPs
            if (!empty($gatewayKey->allowed_ips) && !in_array($request->ip(), $gatewayKey->allowed_ips)) {
                $this->log($service, $gatewayKey, $request, $path, 403, 0, 'IP no permitida: ' . $request->ip());
                return response()->json(['error' => 'Tu IP no está autorizada para esta clave.'], 403);
            }
        }

        // ── 3. Verificar horario de operación ───────────────────────────────
        if ($service->gateway_active_from && $service->gateway_active_to) {
            $now  = now()->format('H:i');
            $from = $service->gateway_active_from;
            $to   = $service->gateway_active_to;
            // Soporta rangos que cruzan medianoche (ej: 22:00 – 06:00)
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

        // ── 4. Rate limiting ─────────────────────────────────────────────────
        $limitIdentifier = $gatewayKey ? "key:{$gatewayKey->id}" : "ip:{$request->ip()}";
        $baseKey         = "gw:{$service->id}:{$limitIdentifier}";

        $perMinute = $gatewayKey?->rate_per_minute ?? $service->gateway_rate_per_minute;
        $perDay    = $gatewayKey?->rate_per_day    ?? $service->gateway_rate_per_day;

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

        // ── 5. Construir URL destino ─────────────────────────────────────────
        $backendBase = rtrim($service->endpoint_url, '/');
        $targetUrl   = $path ? $backendBase . '/' . ltrim($path, '/') : $backendBase;
        $queryString = $request->getQueryString();
        // quitar api_key del query para no reenviarlo al backend
        if ($queryString) {
            parse_str($queryString, $params);
            unset($params['api_key']);
            $cleanQuery = http_build_query($params);
            if ($cleanQuery) {
                $targetUrl .= '?' . $cleanQuery;
            }
        }

        // ── 6. Preparar headers a reenviar ───────────────────────────────────
        $skipHeaders = ['host', 'x-api-key', 'transfer-encoding', 'content-length', 'cookie', 'accept-encoding'];
        $forwardHeaders = [];
        foreach ($request->headers->all() as $name => $values) {
            if (!in_array(strtolower($name), $skipHeaders)) {
                $forwardHeaders[$name] = $values[0] ?? '';
            }
        }

        // ── 7. Proxiar la petición ───────────────────────────────────────────
        $startTime = microtime(true);
        $responseStatus = null;

        try {
            $client  = Http::withHeaders($forwardHeaders)->timeout(30)->withOptions(['verify' => false]);
            $method  = strtolower($request->method());

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

            $responseStatus  = $proxyResponse->status();
            $responseTimeMs  = (int) ((microtime(true) - $startTime) * 1000);

        } catch (\Exception $e) {
            $responseTimeMs = (int) ((microtime(true) - $startTime) * 1000);
            $this->log($service, $gatewayKey, $request, $path, 502, $responseTimeMs, $e->getMessage());
            return response()->json(['error' => 'El backend no está disponible.', 'detail' => $e->getMessage()], 502);
        }

        // ── 8. Registrar en log y actualizar key ────────────────────────────
        $this->log($service, $gatewayKey, $request, $path, $responseStatus, $responseTimeMs);

        if ($gatewayKey) {
            $gatewayKey->increment('total_requests');
            $gatewayKey->update(['last_used_at' => now()]);
        }

        // ── 9. Devolver respuesta del backend ────────────────────────────────
        $skipResponseHeaders = ['transfer-encoding', 'content-encoding', 'set-cookie'];
        $responseHeaders = [];
        foreach ($proxyResponse->headers() as $name => $values) {
            if (!in_array(strtolower($name), $skipResponseHeaders)) {
                $responseHeaders[$name] = is_array($values) ? $values[0] : $values;
            }
        }

        return response($proxyResponse->body(), $responseStatus)
            ->withHeaders($responseHeaders);
    }

    // ── Helpers ─────────────────────────────────────────────────────────────

    private function log(
        SystemService    $service,
        ?ServiceGatewayKey $key,
        Request          $request,
        string           $path,
        ?int             $status,
        int              $timeMs,
        ?string          $error = null
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
