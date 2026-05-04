<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Server;
use App\Models\DatabaseServer;
use Illuminate\Http\Request;

class DatabaseServerController extends Controller
{
    private const ENGINES    = ['postgresql','mysql','mariadb','oracle','sqlserver','sqlite','mongodb','other'];
    private const AUTH_TYPES = ['credentials','windows','kerberos','iam','trusted'];

    public function show(Server $server, DatabaseServer $databaseServer)
    {
        $databaseServer->load([
            'databases.system',
            'responsibles.persona',
            'responsibles.documents',
            'server',
        ]);

        return view('admin.database-servers.show', compact('server', 'databaseServer'));
    }

    public function store(Request $request, Server $server)
    {
        $data = $request->validate([
            'name'           => 'nullable|string|max:100',
            'engine'         => 'required|in:' . implode(',', self::ENGINES),
            'version'        => 'nullable|string|max:50',
            'host'           => 'nullable|string|max:150',
            'port'           => 'nullable|integer|min:1|max:65535',
            'auth_type'      => 'nullable|in:' . implode(',', self::AUTH_TYPES),
            'admin_user'     => 'nullable|string|max:100',
            'admin_password' => 'nullable|string',
            'notes'          => 'nullable|string',
        ]);

        $data['auth_type'] ??= 'credentials';
        self::applyAuthTypeCredentials($data);

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
            'auth_type'      => 'nullable|in:' . implode(',', self::AUTH_TYPES),
            'admin_user'     => 'nullable|string|max:100',
            'admin_password' => 'nullable|string',
            'notes'          => 'nullable|string',
        ]);

        $data['auth_type'] ??= 'credentials';
        self::applyAuthTypeCredentials($data);

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

    private static function applyAuthTypeCredentials(array &$data): void
    {
        match ($data['auth_type']) {
            'windows', 'trusted' => $data['admin_user'] = $data['admin_password'] = null,
            'kerberos', 'iam'    => $data['admin_password'] = null,
            default              => null,
        };
    }
}
