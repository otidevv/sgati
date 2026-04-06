<?php

namespace App\Http\Controllers;

use App\Models\System;
use App\Models\SystemService;
use Illuminate\Http\Request;

class SystemServiceController extends Controller
{
    public function create(System $system)
    {
        return view('systems.services.create', compact('system'));
    }

    public function store(Request $request, System $system)
    {
        $data = $request->validate([
            'service_name' => 'required|string|max:100',
            'service_type' => 'required|in:rest_api,soap,sftp,smtp,ldap,database,other',
            'endpoint_url' => 'nullable|string|max:255',
            'direction'    => 'required|in:consumed,exposed',
            'auth_type'    => 'nullable|string|max:50',
            'description'  => 'nullable|string',
            'is_active'    => 'boolean',
        ]);

        $data['is_active'] = $request->boolean('is_active', true);

        $system->services()->create($data);

        return redirect()->route('systems.show', $system)
            ->with('success', 'Servicio/API registrado correctamente.');
    }

    public function edit(System $system, SystemService $service)
    {
        return view('systems.services.edit', compact('system', 'service'));
    }

    public function update(Request $request, System $system, SystemService $service)
    {
        $data = $request->validate([
            'service_name' => 'required|string|max:100',
            'service_type' => 'required|in:rest_api,soap,sftp,smtp,ldap,database,other',
            'endpoint_url' => 'nullable|string|max:255',
            'direction'    => 'required|in:consumed,exposed',
            'auth_type'    => 'nullable|string|max:50',
            'description'  => 'nullable|string',
            'is_active'    => 'boolean',
        ]);

        $data['is_active'] = $request->boolean('is_active');

        $service->update($data);

        return redirect()->route('systems.show', $system)
            ->with('success', 'Servicio/API actualizado.');
    }

    public function destroy(System $system, SystemService $service)
    {
        $service->delete();

        return redirect()->route('systems.show', $system)
            ->with('success', 'Servicio/API eliminado.');
    }
}
