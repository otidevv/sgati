<?php

namespace App\Http\Controllers;

use App\Models\DatabaseServer;
use App\Models\System;
use App\Models\SystemDatabase;
use Illuminate\Http\Request;

class SystemDatabaseController extends Controller
{
    public function show(System $system, SystemDatabase $database)
    {
        $database->load(['responsibles.persona', 'responsibles.documents', 'databaseServer']);
        return view('systems.databases.show', compact('system', 'database'));
    }

    public function create(System $system)
    {
        $infraServerId = $system->infrastructure?->server_id;

        if ($infraServerId) {
            $systemDbServers = DatabaseServer::where('server_id', $infraServerId)
                ->orderBy('name')->orderBy('engine')
                ->get(['id', 'name', 'engine', 'version', 'host', 'server_id']);
            $otherDbServers = DatabaseServer::where(function ($q) use ($infraServerId) {
                $q->where('server_id', '!=', $infraServerId)->orWhereNull('server_id');
            })->orderBy('name')->orderBy('engine')
                ->get(['id', 'name', 'engine', 'version', 'host', 'server_id']);
        } else {
            $systemDbServers = collect();
            $otherDbServers  = DatabaseServer::orderBy('name')->orderBy('engine')
                ->get(['id', 'name', 'engine', 'version', 'host', 'server_id']);
        }

        return view('systems.databases.create', compact('system', 'systemDbServers', 'otherDbServers', 'infraServerId'));
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
            'environment'       => 'required|in:production,staging,development',
            'notes'             => 'nullable|string',
        ]);

        $system->databases()->create($data);

        return redirect(route('systems.show', $system) . '#databases')
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
            'environment'       => 'required|in:production,staging,development',
            'notes'             => 'nullable|string',
        ]);

        if (empty($data['db_password'])) {
            unset($data['db_password']);
        }

        $database->update($data);

        return redirect(route('systems.show', $system) . '#databases')
            ->with('success', 'Base de datos actualizada.');
    }

    public function destroy(System $system, SystemDatabase $database)
    {
        $database->delete();

        return redirect(route('systems.show', $system) . '#databases')
            ->with('success', 'Base de datos eliminada.');
    }
}
