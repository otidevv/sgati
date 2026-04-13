<?php

namespace App\Http\Controllers;

use App\Models\ServiceGatewayLog;
use App\Models\System;
use App\Models\SystemService;
use Illuminate\Http\Request;

class SystemServiceGatewayController extends Controller
{
    /** Activa o desactiva el gateway. Si no tiene slug, lo genera. */
    public function toggle(Request $request, System $system, SystemService $service)
    {
        $this->authorizeExposed($service);

        if (!$service->gateway_slug) {
            $service->generateGatewaySlug();
        }

        $service->gateway_enabled = !$service->gateway_enabled;
        $service->save();

        $state = $service->gateway_enabled ? 'activado' : 'desactivado';

        return back()->with('success', "Gateway {$state} correctamente.");
    }

    /** Actualiza configuración del gateway (rate limits, require key). */
    public function updateSettings(Request $request, System $system, SystemService $service)
    {
        $this->authorizeExposed($service);

        $request->validate([
            'gateway_require_key'     => 'boolean',
            'gateway_rate_per_minute' => 'nullable|integer|min:1|max:32767',
            'gateway_rate_per_day'    => 'nullable|integer|min:1|max:32767',
            'gateway_active_from'     => 'nullable|date_format:H:i',
            'gateway_active_to'       => 'nullable|date_format:H:i',
        ]);

        $service->update([
            'gateway_require_key'     => $request->boolean('gateway_require_key'),
            'gateway_rate_per_minute' => $request->filled('gateway_rate_per_minute') ? (int) $request->gateway_rate_per_minute : null,
            'gateway_rate_per_day'    => $request->filled('gateway_rate_per_day')    ? (int) $request->gateway_rate_per_day    : null,
            'gateway_active_from'     => $request->filled('gateway_active_from') ? $request->gateway_active_from : null,
            'gateway_active_to'       => $request->filled('gateway_active_to')   ? $request->gateway_active_to   : null,
        ]);

        return back()->with('success', 'Configuración del gateway actualizada.');
    }

    /** Regenera el slug (cambia la URL pública). Invalida todos los accesos anteriores. */
    public function regenerateSlug(System $system, SystemService $service)
    {
        $this->authorizeExposed($service);

        $service->generateGatewaySlug();
        $service->save();

        return back()->with('success', 'URL del gateway regenerada. Los accesos anteriores ya no funcionarán.');
    }

    /** Devuelve logs paginados vía JSON (para carga dinámica). */
    public function logs(Request $request, System $system, SystemService $service)
    {
        $this->authorizeExposed($service);

        $logs = $service->gatewayLogs()
            ->with('gatewayKey:id,name,key_prefix')
            ->when($request->status, fn($q, $s) => $q->where('response_status', $s))
            ->when($request->key_id, fn($q, $id) => $q->where('gateway_key_id', $id))
            ->paginate(50);

        // Estadísticas rápidas
        $stats = [
            'total_today'   => ServiceGatewayLog::where('system_service_id', $service->id)
                ->whereDate('created_at', today())->count(),
            'total_week'    => ServiceGatewayLog::where('system_service_id', $service->id)
                ->where('created_at', '>=', now()->startOfWeek())->count(),
            'errors_today'  => ServiceGatewayLog::where('system_service_id', $service->id)
                ->whereDate('created_at', today())->where('response_status', '>=', 400)->count(),
            'avg_time_ms'   => (int) ServiceGatewayLog::where('system_service_id', $service->id)
                ->whereDate('created_at', today())->avg('response_time_ms'),
        ];

        return view('systems.services.gateway.logs', compact('system', 'service', 'logs', 'stats'));
    }

    // ── Helper ─────────────────────────────────────────────────────────────

    private function authorizeExposed(SystemService $service): void
    {
        abort_if($service->direction !== 'exposed', 403, 'El gateway solo aplica a servicios expuestos.');
    }
}
