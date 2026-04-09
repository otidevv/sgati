<?php

namespace App\Http\Controllers;

use App\Models\System;
use App\Models\SystemService;
use Illuminate\Http\Request;

class SystemServiceController extends Controller
{
    private const VALIDATION = [
        'service_name'            => 'required|string|max:100',
        'service_type'            => 'required|in:rest_api,soap,sftp,smtp,ldap,database,other',
        'endpoint_url'            => 'nullable|string|max:255',
        'direction'               => 'required|in:consumed,exposed',
        'auth_type'               => 'nullable|string|max:50',
        'description'             => 'nullable|string',
        'is_active'               => 'boolean',
        'environment'             => 'required|in:production,staging,development',
        'version'                 => 'nullable|string|max:20',
        'provider_type'           => 'nullable|in:internal,external',
        'provider_system_id'      => 'nullable|exists:systems,id',
        'provider_name'           => 'nullable|string|max:150',
        'valid_from'              => 'nullable|date',
        'valid_until'             => 'nullable|date|after_or_equal:valid_from',
        'api_key'                 => 'nullable|string',
        'api_secret'              => 'nullable|string',
        'token'                   => 'nullable|string',
        'token_expires_at'        => 'nullable|date',
        'requested_by_persona_id' => 'nullable|exists:personas,id',
    ];

    public function show(System $system, SystemService $service)
    {
        $service->load(['providerSystem', 'requestedBy', 'documents', 'fields']);
        $allSystems = System::where('id', '!=', $system->id)->orderBy('name')->get(['id', 'name', 'acronym']);
        return view('systems.services.show', compact('system', 'service', 'allSystems'));
    }

    public function create(System $system)
    {
        $allSystems = System::where('id', '!=', $system->id)->orderBy('name')->get(['id', 'name', 'acronym']);
        return view('systems.services.create', compact('system', 'allSystems'));
    }

    public function store(Request $request, System $system)
    {
        $data = $request->validate(self::VALIDATION);
        $data['is_active']      = $request->boolean('is_active', true);
        $data['provider_type']  = $data['provider_type'] ?: null;
        $data['provider_system_id'] = ($data['provider_type'] === 'internal') ? ($data['provider_system_id'] ?: null) : null;
        $data['provider_name']  = ($data['provider_type'] === 'external') ? ($data['provider_name'] ?: null) : null;

        // Sólo guardar credencial si se envió algo
        if (empty($data['api_key']))    unset($data['api_key']);
        if (empty($data['api_secret'])) unset($data['api_secret']);
        if (empty($data['token']))      unset($data['token']);

        $system->services()->create($data);

        return redirect()->route('systems.show', $system)
            ->with('success', 'Servicio/API registrado correctamente.');
    }

    public function edit(System $system, SystemService $service)
    {
        $allSystems = System::where('id', '!=', $system->id)->orderBy('name')->get(['id', 'name', 'acronym']);
        return view('systems.services.edit', compact('system', 'service', 'allSystems'));
    }

    public function update(Request $request, System $system, SystemService $service)
    {
        $data = $request->validate(self::VALIDATION);
        $data['is_active']     = $request->boolean('is_active');
        $data['provider_type'] = $data['provider_type'] ?: null;
        $data['provider_system_id'] = ($data['provider_type'] === 'internal') ? ($data['provider_system_id'] ?: null) : null;
        $data['provider_name']  = ($data['provider_type'] === 'external') ? ($data['provider_name'] ?: null) : null;

        if (empty($data['api_key']))    unset($data['api_key']);
        if (empty($data['api_secret'])) unset($data['api_secret']);
        if (empty($data['token']))      unset($data['token']);

        $service->update($data);

        return redirect()->route('systems.services.show', [$system, $service])
            ->with('success', 'Servicio/API actualizado.');
    }

    public function destroy(System $system, SystemService $service)
    {
        $service->delete();
        return redirect()->route('systems.show', $system)
            ->with('success', 'Servicio/API eliminado.');
    }
}
