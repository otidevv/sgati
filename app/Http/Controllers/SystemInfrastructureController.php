<?php

namespace App\Http\Controllers;

use App\Models\Server;
use App\Models\SslCertificate;
use App\Models\System;
use Illuminate\Http\Request;

class SystemInfrastructureController extends Controller
{
    public function edit(System $system)
    {
        $infra        = $system->infrastructure ?? $system->infrastructure()->create([]);
        $servers      = Server::with('ips')->orderBy('name')->get(['id', 'name', 'operating_system', 'function']);
        $sslCerts     = SslCertificate::orderBy('name')->get(['id', 'name', 'common_name', 'valid_until']);

        // Mapa server_id → lista de IPs para el select del formulario
        $serverIpsMap = $servers->mapWithKeys(fn($s) => [
            $s->id => $s->ips->map(fn($ip) => [
                'id'         => $ip->id,
                'ip_address' => $ip->ip_address,
                'type'       => $ip->type,
                'is_primary' => (bool) $ip->is_primary,
            ])->values(),
        ]);

        return view('systems.tabs.infrastructure_edit', compact('system', 'infra', 'servers', 'sslCerts', 'serverIpsMap'));
    }

    public function update(Request $request, System $system)
    {
        $data = $request->validate([
            'server_id'          => 'nullable|exists:servers,id',
            'server_ip_id'       => 'nullable|exists:server_ips,id',
            'public_ip'          => 'nullable|ip|max:45',
            'port'               => 'nullable|integer|min:1|max:65535',
            'system_url'         => ['nullable', 'string', 'max:255', 'regex:/^(https?:\/\/)?[\w\-\.]+(\:\d+)?(\/\S*)?$/i'],
            'web_server'         => 'nullable|string|max:50',
            'ssl_enabled'        => 'boolean',
            'ssl_type'           => 'nullable|in:institutional,custom',
            'ssl_certificate_id' => 'nullable|exists:ssl_certificates,id',
            'ssl_custom_expiry'  => 'nullable|date',
            'environment'        => 'required|in:production,staging,development',
            'notes'              => 'nullable|string',
        ]);

        $data['ssl_enabled'] = $request->boolean('ssl_enabled');

        // Resolver certificado según tipo seleccionado
        if (!$data['ssl_enabled']) {
            $data['ssl_certificate_id'] = null;
            $data['ssl_custom_expiry']  = null;
        } elseif (($data['ssl_type'] ?? null) === 'institutional') {
            $data['ssl_custom_expiry'] = null;
        } else {
            $data['ssl_certificate_id'] = null;
        }
        unset($data['ssl_type']);

        $system->infrastructure()->updateOrCreate(
            ['system_id' => $system->id],
            $data
        );

        return redirect(route('systems.show', $system) . '#infrastructure')
            ->with('success', 'Infraestructura actualizada correctamente.');
    }
}
