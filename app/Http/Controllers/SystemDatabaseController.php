<?php

namespace App\Http\Controllers;

use App\Models\DatabaseServer;
use App\Models\System;
use App\Models\SystemDatabase;
use Illuminate\Http\Request;

class SystemDatabaseController extends Controller
{
    public function create(System $system)
    {
        $databaseServers = DatabaseServer::orderBy('name')->orderBy('engine')->get(['id', 'name', 'engine', 'version', 'host']);
        return view('systems.databases.create', compact('system', 'databaseServers'));
    }

    public function store(Request $request, System $system)
    {
        $data = $request->validate([
            'db_name'           => 'required|string|max:100',
            'engine'            => 'required|in:postgresql,mysql,mariadb,oracle,sqlserver,sqlite,mongodb,other',
            'database_server_id'=> 'nullable|exists:database_servers,id',
            'db_user'           => 'nullable|string|max:100',
            'db_password'       => 'nullable|string',
            'schema_name'       => 'nullable|string|max:100',
            'responsible'       => 'nullable|string|max:100',
            'environment'       => 'required|in:production,staging,development',
            'notes'             => 'nullable|string',
        ]);

        $system->databases()->create($data);

        return redirect()->route('systems.show', $system)
            ->with('success', 'Base de datos registrada correctamente.');
    }

    public function edit(System $system, SystemDatabase $database)
    {
        $databaseServers = DatabaseServer::orderBy('name')->orderBy('engine')->get(['id', 'name', 'engine', 'version', 'host']);
        return view('systems.databases.edit', compact('system', 'database', 'databaseServers'));
    }

    public function update(Request $request, System $system, SystemDatabase $database)
    {
        $data = $request->validate([
            'db_name'           => 'required|string|max:100',
            'engine'            => 'required|in:postgresql,mysql,mariadb,oracle,sqlserver,sqlite,mongodb,other',
            'database_server_id'=> 'nullable|exists:database_servers,id',
            'db_user'           => 'nullable|string|max:100',
            'db_password'       => 'nullable|string',
            'schema_name'       => 'nullable|string|max:100',
            'responsible'       => 'nullable|string|max:100',
            'environment'       => 'required|in:production,staging,development',
            'notes'             => 'nullable|string',
        ]);

        if (empty($data['db_password'])) {
            unset($data['db_password']);
        }

        $database->update($data);

        return redirect()->route('systems.show', $system)
            ->with('success', 'Base de datos actualizada.');
    }

    public function destroy(System $system, SystemDatabase $database)
    {
        $database->delete();

        return redirect()->route('systems.show', $system)
            ->with('success', 'Base de datos eliminada.');
    }
}
