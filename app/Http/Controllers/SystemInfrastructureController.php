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
        $servers      = Server::orderBy('name')->get(['id', 'name', 'operating_system', 'function']);
        $sslCerts     = SslCertificate::orderBy('name')->get(['id', 'name', 'common_name', 'valid_until']);

        return view('systems.tabs.infrastructure_edit', compact('system', 'infra', 'servers', 'sslCerts'));
    }

    public function update(Request $request, System $system)
    {
        $data = $request->validate([
            'server_id'          => 'nullable|exists:servers,id',
            'system_url'         => 'nullable|url|max:255',
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

        return redirect()->route('systems.show', $system)
            ->with('success', 'Infraestructura actualizada correctamente.');
    }
}
