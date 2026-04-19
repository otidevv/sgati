<?php

namespace App\Http\Controllers;

use App\Models\System;
use App\Models\SystemVersion;
use App\Models\SystemVersionResponsible;
use Illuminate\Http\Request;

class SystemVersionResponsibleController extends Controller
{
    private const ROLES = 'lider_tecnico,desarrollador,analista,tester,despliegue,aprobador';

    public function store(Request $request, System $system, SystemVersion $version)
    {
        $data = $request->validate([
            'persona_id'  => 'required|exists:personas,id',
            'role'        => 'required|in:' . self::ROLES,
            'assigned_at' => 'required|date',
        ]);

        $data['is_active'] = true;

        $version->responsibles()->create($data);

        return redirect(route('systems.versions.show', [$system, $version]))
            ->with('success', 'Responsable asignado correctamente.');
    }

    public function update(Request $request, System $system, SystemVersion $version, SystemVersionResponsible $responsible)
    {
        $data = $request->validate([
            'persona_id'  => 'required|exists:personas,id',
            'role'        => 'required|in:' . self::ROLES,
            'assigned_at' => 'required|date',
        ]);

        $responsible->update($data);

        return redirect(route('systems.versions.show', [$system, $version]))
            ->with('success', 'Responsable actualizado correctamente.');
    }

    public function deactivate(Request $request, System $system, SystemVersion $version, SystemVersionResponsible $responsible)
    {
        $data = $request->validate([
            'unassigned_at'    => 'required|date|after_or_equal:' . $responsible->assigned_at->format('Y-m-d'),
            'deactivate_notes' => 'nullable|string|max:500',
        ]);

        $responsible->update([
            'is_active'      => false,
            'unassigned_at'  => $data['unassigned_at'],
            'document_notes' => $data['deactivate_notes'] ?: $responsible->document_notes,
        ]);

        return redirect(route('systems.versions.show', [$system, $version]))
            ->with('success', 'Responsable dado de baja correctamente.');
    }

    public function reactivate(Request $request, System $system, SystemVersion $version, SystemVersionResponsible $responsible)
    {
        $data = $request->validate([
            'assigned_at' => 'required|date',
        ]);

        $responsible->update([
            'is_active'     => true,
            'assigned_at'   => $data['assigned_at'],
            'unassigned_at' => null,
        ]);

        return redirect(route('systems.versions.show', [$system, $version]))
            ->with('success', 'Responsable reactivado correctamente.');
    }

    public function destroy(System $system, SystemVersion $version, SystemVersionResponsible $responsible)
    {
        $responsible->delete();

        return redirect(route('systems.versions.show', [$system, $version]))
            ->with('success', 'Responsable eliminado del historial.');
    }
}
