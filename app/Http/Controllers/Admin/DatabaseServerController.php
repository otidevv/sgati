<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Server;
use App\Models\DatabaseServer;
use Illuminate\Http\Request;

class DatabaseServerController extends Controller
{
    private const ENGINES = ['postgresql','mysql','mariadb','oracle','sqlserver','sqlite','mongodb','other'];

    public function store(Request $request, Server $server)
    {
        $data = $request->validate([
            'name'           => 'nullable|string|max:100',
            'engine'         => 'required|in:' . implode(',', self::ENGINES),
            'version'        => 'nullable|string|max:50',
            'host'           => 'nullable|string|max:150',
            'port'           => 'nullable|integer|min:1|max:65535',
            'admin_user'     => 'nullable|string|max:100',
            'admin_password' => 'nullable|string',
            'notes'          => 'nullable|string',
        ]);

        $server->databaseServers()->create($data);

        return back()->with('success', 'Motor de BD registrado.');
    }

    public function update(Request $request, Server $server, DatabaseServer $databaseServer)
    {
        $data = $request->validate([
            'name'           => 'nullable|string|max:100',
            'engine'         => 'required|in:' . implode(',', self::ENGINES),
            'version'        => 'nullable|string|max:50',
            'host'           => 'nullable|string|max:150',
            'port'           => 'nullable|integer|min:1|max:65535',
            'admin_user'     => 'nullable|string|max:100',
            'admin_password' => 'nullable|string',
            'notes'          => 'nullable|string',
        ]);

        if (empty($data['admin_password'])) {
            unset($data['admin_password']);
        }

        $databaseServer->update($data);

        return back()->with('success', 'Motor de BD actualizado.');
    }

    public function destroy(Server $server, DatabaseServer $databaseServer)
    {
        $databaseServer->delete();
        return back()->with('success', 'Motor de BD eliminado.');
    }
}
