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
            'name'                 => 'required|string|max:120',
            'consumer_type'        => 'required|in:internal,external,person',
            'requesting_system_id' => 'nullable|exists:systems,id',
            'consumer_organization'=> 'nullable|string|max:150',
            'consumer_persona_id'  => 'nullable|exists:personas,id',  // persona como consumidor
            'purpose'              => 'nullable|string|max:255',
            'auth_type'            => 'required|in:bearer,api_key,query_param,none',
            'rate_per_minute'      => 'nullable|integer|min:1|max:32767',
            'rate_per_day'         => 'nullable|integer|min:1|max:32767',
            'expires_at'           => 'nullable|date|after:today',
            'allowed_ips'          => 'nullable|string',
            'persona_id'           => 'nullable|exists:personas,id',  // persona de contacto
            'notes'                => 'nullable|string|max:500',
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

        // Si el consumidor es una persona, usar esa misma como contacto si no se especificó
        $contactPersonaId = $data['persona_id'] ?? null;
        if ($data['consumer_type'] === 'person' && !$contactPersonaId) {
            $contactPersonaId = $data['consumer_persona_id'] ?? null;
        }

        $key = $service->gatewayKeys()->create([
            'name'                  => $data['name'],
            'key_prefix'            => $prefix,
            'key_hash'              => $hash,
            'is_active'             => true,
            'auth_type'             => $data['auth_type'],
            'consumer_type'         => $data['consumer_type'],
            'requesting_system_id'  => $data['consumer_type'] === 'internal'  ? ($data['requesting_system_id'] ?? null)  : null,
            'consumer_organization' => $data['consumer_type'] === 'external'  ? ($data['consumer_organization'] ?? null) : null,
            'purpose'               => $data['purpose'] ?? null,
            'rate_per_minute'       => $data['rate_per_minute'] ?? null,
            'rate_per_day'          => $data['rate_per_day'] ?? null,
            'expires_at'            => $data['expires_at'] ?? null,
            'allowed_ips'           => $allowedIps,
            'persona_id'            => $contactPersonaId,
            'notes'                 => $data['notes'] ?? null,
        ]);

        // Generar URL de gateway único para este consumidor
        $key->generateGatewaySlug();
        $key->save();

        // ── Auto-activar el gateway si aún no está habilitado ────────────────
        if (!$service->gateway_enabled) {
            $service->gateway_enabled     = true;
            $service->gateway_require_key = true;  // por defecto, protegido con clave
            $service->save();
        }

        // Guardamos la raw key en la sesión para mostrarla UNA VEZ
        return back()
            ->with('success', 'Solicitante registrado. Copia la clave de acceso ahora — no se mostrará de nuevo.')
            ->with('new_raw_key', $rawKey)
            ->with('new_key_id', $key->id)
            ->with('new_gateway_url', $key->gatewayUrl());
    }

    /**
     * Regenera la API key del consumidor. La clave anterior deja de funcionar inmediatamente.
     * La nueva clave se muestra UNA SOLA VEZ en el banner de sesión.
     */
    public function regenerateKey(System $system, SystemService $service, ServiceGatewayKey $key)
    {
        abort_if($key->system_service_id !== $service->id, 403);

        $rawKey = Str::random(40);
        $prefix = substr($rawKey, 0, 8);
        $hash   = hash('sha256', $rawKey);

        $key->update([
            'key_prefix' => $prefix,
            'key_hash'   => $hash,
        ]);

        return back()
            ->with('success', "Clave de \"{$key->name}\" regenerada. Copia el nuevo token — no se mostrará de nuevo.")
            ->with('new_raw_key', $rawKey)
            ->with('new_key_id', $key->id)
            ->with('new_gateway_url', $key->gatewayUrl());
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
            'name'                 => 'required|string|max:120',
            'consumer_type'        => 'required|in:internal,external,person',
            'requesting_system_id' => 'nullable|exists:systems,id',
            'consumer_organization'=> 'nullable|string|max:150',
            'consumer_persona_id'  => 'nullable|exists:personas,id',
            'purpose'              => 'nullable|string|max:255',
            'auth_type'            => 'required|in:bearer,api_key,query_param,none',
            'rate_per_minute'      => 'nullable|integer|min:1|max:32767',
            'rate_per_day'         => 'nullable|integer|min:1|max:32767',
            'expires_at'           => 'nullable|date',
            'allowed_ips'          => 'nullable|string',
            'persona_id'           => 'nullable|exists:personas,id',
            'notes'                => 'nullable|string|max:500',
        ]);

        $allowedIps = null;
        if (!empty($data['allowed_ips'])) {
            $allowedIps = array_values(array_filter(
                array_map('trim', preg_split('/[\s,]+/', $data['allowed_ips']))
            )) ?: null;
        }

        $contactPersonaId = $data['persona_id'] ?? null;
        if ($data['consumer_type'] === 'person' && !$contactPersonaId) {
            $contactPersonaId = $data['consumer_persona_id'] ?? null;
        }

        $key->update([
            'name'                  => $data['name'],
            'auth_type'             => $data['auth_type'],
            'consumer_type'         => $data['consumer_type'],
            'requesting_system_id'  => $data['consumer_type'] === 'internal'  ? ($data['requesting_system_id'] ?? null)  : null,
            'consumer_organization' => $data['consumer_type'] === 'external'  ? ($data['consumer_organization'] ?? null) : null,
            'purpose'               => $data['purpose'] ?? null,
            'rate_per_minute'       => $data['rate_per_minute'] ?? null,
            'rate_per_day'          => $data['rate_per_day'] ?? null,
            'expires_at'            => $data['expires_at'] ?? null,
            'allowed_ips'           => $allowedIps,
            'persona_id'            => $contactPersonaId,
            'notes'                 => $data['notes'] ?? null,
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
