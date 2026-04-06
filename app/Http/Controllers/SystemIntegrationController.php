<?php

namespace App\Http\Controllers;

use App\Models\System;
use App\Models\SystemIntegration;
use Illuminate\Http\Request;

class SystemIntegrationController extends Controller
{
    public function create(System $system)
    {
        $otherSystems = System::where('id', '!=', $system->id)->orderBy('name')->get();

        return view('systems.integrations.create', compact('system', 'otherSystems'));
    }

    public function store(Request $request, System $system)
    {
        $data = $request->validate([
            'target_system_id' => 'required|exists:systems,id|different:source_system_id',
            'connection_type'  => 'required|in:api,direct_db,file,sftp,other',
            'description'      => 'nullable|string',
            'is_active'        => 'boolean',
            'notes'            => 'nullable|string',
        ]);

        $data['source_system_id'] = $system->id;
        $data['is_active']        = $request->boolean('is_active', true);

        SystemIntegration::create($data);

        return redirect()->route('systems.show', $system)
            ->with('success', 'Integración registrada correctamente.');
    }

    public function edit(System $system, SystemIntegration $integration)
    {
        $otherSystems = System::where('id', '!=', $system->id)->orderBy('name')->get();

        return view('systems.integrations.edit', compact('system', 'integration', 'otherSystems'));
    }

    public function update(Request $request, System $system, SystemIntegration $integration)
    {
        $data = $request->validate([
            'target_system_id' => 'required|exists:systems,id',
            'connection_type'  => 'required|in:api,direct_db,file,sftp,other',
            'description'      => 'nullable|string',
            'is_active'        => 'boolean',
            'notes'            => 'nullable|string',
        ]);

        $data['is_active'] = $request->boolean('is_active');

        $integration->update($data);

        return redirect()->route('systems.show', $system)
            ->with('success', 'Integración actualizada.');
    }

    public function destroy(System $system, SystemIntegration $integration)
    {
        $integration->delete();

        return redirect()->route('systems.show', $system)
            ->with('success', 'Integración eliminada.');
    }
}
