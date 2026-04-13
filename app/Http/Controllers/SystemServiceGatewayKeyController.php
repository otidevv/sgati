<?php

namespace App\Http\Controllers;

use App\Models\ServiceGatewayKey;
use App\Models\System;
use App\Models\SystemService;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class SystemServiceGatewayKeyController extends Controller
{
    /** Genera una nueva API key y la muestra UNA SOLA VEZ. */
    public function store(Request $request, System $system, SystemService $service)
    {
        abort_if($service->direction !== 'exposed', 403);

        $data = $request->validate([
            'name'           => 'required|string|max:120',
            'rate_per_minute' => 'nullable|integer|min:1|max:32767',
            'rate_per_day'   => 'nullable|integer|min:1|max:32767',
            'expires_at'     => 'nullable|date|after:today',
            'allowed_ips'    => 'nullable|string',   // líneas separadas por coma/nueva línea
            'persona_id'     => 'nullable|exists:personas,id',
            'notes'          => 'nullable|string|max:500',
        ]);

        // Generar key segura
        $rawKey = Str::random(40);
        $prefix = substr($rawKey, 0, 8);
        $hash   = hash('sha256', $rawKey);

        // Parsear IPs permitidas
        $allowedIps = null;
        if (!empty($data['allowed_ips'])) {
            $allowedIps = array_filter(
                array_map('trim', preg_split('/[\s,]+/', $data['allowed_ips']))
            );
            $allowedIps = array_values($allowedIps) ?: null;
        }

        $key = $service->gatewayKeys()->create([
            'name'           => $data['name'],
            'key_prefix'     => $prefix,
            'key_hash'       => $hash,
            'is_active'      => true,
            'rate_per_minute' => $data['rate_per_minute'] ?? null,
            'rate_per_day'   => $data['rate_per_day'] ?? null,
            'expires_at'     => $data['expires_at'] ?? null,
            'allowed_ips'    => $allowedIps,
            'persona_id'     => $data['persona_id'] ?? null,
            'notes'          => $data['notes'] ?? null,
        ]);

        // Guardamos la raw key en la sesión para mostrarla UNA VEZ
        return back()
            ->with('success', 'API key generada. Cópiala ahora — no se mostrará de nuevo.')
            ->with('new_raw_key', $rawKey)
            ->with('new_key_id', $key->id);
    }

    /** Activa o desactiva una key. */
    public function toggle(Request $request, System $system, SystemService $service, ServiceGatewayKey $key)
    {
        abort_if($key->system_service_id !== $service->id, 403);

        $key->update(['is_active' => !$key->is_active]);
        $state = $key->is_active ? 'activada' : 'desactivada';

        return back()->with('success', "Clave \"{$key->name}\" {$state}.");
    }

    /** Actualiza nombre, límites, expiración y notas de una key existente. */
    public function update(Request $request, System $system, SystemService $service, ServiceGatewayKey $key)
    {
        abort_if($key->system_service_id !== $service->id, 403);

        $data = $request->validate([
            'name'           => 'required|string|max:120',
            'rate_per_minute' => 'nullable|integer|min:1|max:32767',
            'rate_per_day'   => 'nullable|integer|min:1|max:32767',
            'expires_at'     => 'nullable|date',
            'allowed_ips'    => 'nullable|string',
            'persona_id'     => 'nullable|exists:personas,id',
            'notes'          => 'nullable|string|max:500',
        ]);

        $allowedIps = null;
        if (!empty($data['allowed_ips'])) {
            $allowedIps = array_values(array_filter(
                array_map('trim', preg_split('/[\s,]+/', $data['allowed_ips']))
            )) ?: null;
        }

        $key->update([
            'name'           => $data['name'],
            'rate_per_minute' => $data['rate_per_minute'] ?? null,
            'rate_per_day'   => $data['rate_per_day'] ?? null,
            'expires_at'     => $data['expires_at'] ?? null,
            'allowed_ips'    => $allowedIps,
            'persona_id'     => $data['persona_id'] ?? null,
            'notes'          => $data['notes'] ?? null,
        ]);

        return back()->with('success', 'Clave actualizada correctamente.');
    }

    /** Elimina la key. Las entradas de log quedan con gateway_key_id = null. */
    public function destroy(System $system, SystemService $service, ServiceGatewayKey $key)
    {
        abort_if($key->system_service_id !== $service->id, 403);

        $key->delete();

        return back()->with('success', 'Clave eliminada.');
    }
}
