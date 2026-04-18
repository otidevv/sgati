<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Server;
use App\Models\ServerIp;
use Illuminate\Http\Request;

class ServerIpController extends Controller
{
    public function store(Request $request, Server $server)
    {
        $this->authorize('servers.edit');

        $port = $request->input('port') ? (int) $request->input('port') : null;

        $request->validate([
            'ip_address' => [
                'required', 'string', 'max:45',
                function ($attribute, $value, $fail) use ($server, $port) {
                    $exists = $server->ips()
                        ->where('ip_address', $value)
                        ->when(is_null($port),
                            fn ($q) => $q->whereNull('port'),
                            fn ($q) => $q->where('port', $port)
                        )
                        ->exists();
                    if ($exists) {
                        $fail('La IP' . ($port ? ':' . $port : '') . ' ya está registrada en este servidor.');
                    }
                },
            ],
            'port'       => 'nullable|integer|min:1|max:65535',
            'type'       => 'required|in:private,public',
            'interface'  => 'nullable|string|max:50',
            'is_primary' => 'nullable|boolean',
        ]);

        $server->ips()->create([
            'ip_address' => $request->ip_address,
            'port'       => $port,
            'type'       => $request->type,
            'interface'  => $request->interface ?: null,
            'is_primary' => $request->boolean('is_primary'),
        ]);

        return back()->with('success', 'Dirección IP agregada.');
    }

    public function destroy(Server $server, ServerIp $ip)
    {
        $this->authorize('servers.edit');

        $ip->delete();

        return back()->with('success', 'Dirección IP eliminada.');
    }
}
