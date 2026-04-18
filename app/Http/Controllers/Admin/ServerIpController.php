<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Server;
use App\Models\ServerIp;
use App\Models\ServerIpPort;
use Illuminate\Http\Request;

class ServerIpController extends Controller
{
    public function store(Request $request, Server $server)
    {
        $this->authorize('servers.edit');

        $request->validate([
            'ip_address' => [
                'required', 'string', 'max:45',
                function ($attribute, $value, $fail) use ($server) {
                    if ($server->ips()->where('ip_address', $value)->exists()) {
                        $fail('La IP ' . $value . ' ya está registrada en este servidor.');
                    }
                },
            ],
            'type'       => 'required|in:private,public',
            'interface'  => 'nullable|string|max:50',
            'is_primary' => 'nullable|boolean',
        ]);

        $server->ips()->create([
            'ip_address' => $request->ip_address,
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

    public function storePort(Request $request, Server $server, ServerIp $ip)
    {
        $this->authorize('servers.edit');

        $request->validate([
            'port' => [
                'required', 'integer', 'min:1', 'max:65535',
                function ($attribute, $value, $fail) use ($ip) {
                    if ($ip->ports()->where('port', $value)->exists()) {
                        $fail('El puerto ' . $value . ' ya está registrado para esta IP.');
                    }
                },
            ],
            'protocol'    => 'required|in:tcp,udp,both',
            'description' => 'nullable|string|max:200',
        ]);

        $ip->ports()->create([
            'port'        => $request->port,
            'protocol'    => $request->protocol,
            'description' => $request->description ?: null,
            'is_active'   => true,
        ]);

        return back()->with('success', 'Puerto ' . $request->port . ' agregado.');
    }

    public function destroyPort(Server $server, ServerIp $ip, ServerIpPort $port)
    {
        $this->authorize('servers.edit');

        $port->delete();

        return back()->with('success', 'Puerto eliminado.');
    }
}
