<?php

namespace App\Http\Controllers;

use App\Models\System;
use App\Models\SystemResponsible;
use Illuminate\Http\Request;

class SystemResponsibleController extends Controller
{
    private const LEVELS = 'lider_proyecto,desarrollador,mantenimiento,administrador,analista,soporte,supervision';

    public function store(Request $request, System $system)
    {
        $data = $request->validate([
            'persona_id'  => 'required|exists:personas,id',
            'level'       => 'required|in:' . self::LEVELS,
            'assigned_at' => 'required|date',
            'is_active'   => 'boolean',
        ]);

        $data['is_active'] = $request->boolean('is_active', true);

        $system->responsibles()->create($data);

        return redirect(route('systems.show', $system) . '#responsibles')->with('success', 'Responsable asignado correctamente.');
    }

    public function update(Request $request, System $system, SystemResponsible $responsible)
    {
        $data = $request->validate([
            'persona_id'  => 'required|exists:personas,id',
            'level'       => 'required|in:' . self::LEVELS,
            'assigned_at' => 'required|date',
            'is_active'   => 'boolean',
        ]);

        $data['is_active'] = $request->boolean('is_active', true);

        $responsible->update($data);

        return redirect(route('systems.show', $system) . '#responsibles')->with('success', 'Responsable actualizado correctamente.');
    }

    public function reactivate(Request $request, System $system, SystemResponsible $responsible)
    {
        $data = $request->validate([
            'assigned_at' => 'required|date',
        ]);

        $responsible->update([
            'is_active'     => true,
            'assigned_at'   => $data['assigned_at'],
            'unassigned_at' => null,
        ]);

        return redirect(route('systems.show', $system) . '#responsibles')->with('success', 'Responsable reactivado correctamente.');
    }

    public function deactivate(Request $request, System $system, SystemResponsible $responsible)
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

        return redirect(route('systems.show', $system) . '#responsibles')->with('success', 'Responsable dado de baja correctamente.');
    }

    public function destroy(System $system, SystemResponsible $responsible)
    {
        $responsible->delete();

        return redirect(route('systems.show', $system) . '#responsibles')->with('success', 'Responsable eliminado del historial.');
    }
}
