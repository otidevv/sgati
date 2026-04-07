<?php

namespace App\Http\Controllers;

use App\Models\Server;
use App\Models\System;
use Illuminate\Http\Request;

class SystemInfrastructureController extends Controller
{
    public function edit(System $system)
    {
        $infra   = $system->infrastructure ?? $system->infrastructure()->create([]);
        $servers = Server::orderBy('name')->get(['id', 'name', 'operating_system', 'function']);

        return view('systems.tabs.infrastructure_edit', compact('system', 'infra', 'servers'));
    }

    public function update(Request $request, System $system)
    {
        $data = $request->validate([
            'server_id'  => 'nullable|exists:servers,id',
            'system_url' => 'nullable|url|max:255',
            'web_server' => 'nullable|string|max:50',
            'ssl_enabled'=> 'boolean',
            'ssl_expiry' => 'nullable|date',
            'environment'=> 'required|in:production,staging,development',
            'notes'      => 'nullable|string',
        ]);

        $data['ssl_enabled'] = $request->boolean('ssl_enabled');

        $system->infrastructure()->updateOrCreate(
            ['system_id' => $system->id],
            $data
        );

        return redirect()->route('systems.show', $system)
            ->with('success', 'Infraestructura actualizada correctamente.');
    }
}
