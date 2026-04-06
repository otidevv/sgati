<?php

namespace App\Http\Controllers;

use App\Models\System;
use App\Models\SystemDatabase;
use Illuminate\Http\Request;

class SystemDatabaseController extends Controller
{
    public function create(System $system)
    {
        return view('systems.databases.create', compact('system'));
    }

    public function store(Request $request, System $system)
    {
        $data = $request->validate([
            'db_name'     => 'required|string|max:100',
            'engine'      => 'required|in:postgresql,mysql,oracle,sqlserver,sqlite,mongodb,other',
            'server_host' => 'nullable|string|max:100',
            'port'        => 'nullable|integer|min:1|max:65535',
            'schema_name' => 'nullable|string|max:100',
            'responsible' => 'nullable|string|max:100',
            'environment' => 'required|in:production,staging,development',
            'notes'       => 'nullable|string',
        ]);

        $system->databases()->create($data);

        return redirect()->route('systems.show', $system)
            ->with('success', 'Base de datos registrada correctamente.');
    }

    public function edit(System $system, SystemDatabase $database)
    {
        return view('systems.databases.edit', compact('system', 'database'));
    }

    public function update(Request $request, System $system, SystemDatabase $database)
    {
        $data = $request->validate([
            'db_name'     => 'required|string|max:100',
            'engine'      => 'required|in:postgresql,mysql,oracle,sqlserver,sqlite,mongodb,other',
            'server_host' => 'nullable|string|max:100',
            'port'        => 'nullable|integer|min:1|max:65535',
            'schema_name' => 'nullable|string|max:100',
            'responsible' => 'nullable|string|max:100',
            'environment' => 'required|in:production,staging,development',
            'notes'       => 'nullable|string',
        ]);

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
