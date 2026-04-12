<?php

namespace App\Http\Controllers;

use App\Models\System;
use App\Models\SystemDatabase;
use App\Models\SystemDatabaseResponsible;
use Illuminate\Http\Request;

class SystemDatabaseResponsibleController extends Controller
{
    private const LEVELS = 'principal,soporte,supervision,operador';

    public function store(Request $request, System $system, SystemDatabase $database)
    {
        $data = $request->validate([
            'persona_id'  => 'required|exists:personas,id',
            'level'       => 'required|in:' . self::LEVELS,
            'assigned_at' => 'required|date',
        ]);

        $data['is_active'] = true;

        $database->responsibles()->create($data);

        return back()->with('success', 'Responsable asignado correctamente.');
    }

    public function update(Request $request, System $system, SystemDatabase $database, SystemDatabaseResponsible $responsible)
    {
        $data = $request->validate([
            'persona_id'  => 'required|exists:personas,id',
            'level'       => 'required|in:' . self::LEVELS,
            'assigned_at' => 'required|date',
        ]);

        $responsible->update($data);

        return back()->with('success', 'Responsable actualizado correctamente.');
    }

    public function deactivate(Request $request, System $system, SystemDatabase $database, SystemDatabaseResponsible $responsible)
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

        return back()->with('success', 'Responsable dado de baja correctamente.');
    }

    public function reactivate(Request $request, System $system, SystemDatabase $database, SystemDatabaseResponsible $responsible)
    {
        $data = $request->validate([
            'assigned_at' => 'required|date',
        ]);

        $responsible->update([
            'is_active'     => true,
            'assigned_at'   => $data['assigned_at'],
            'unassigned_at' => null,
        ]);

        return back()->with('success', 'Responsable reactivado correctamente.');
    }

    public function destroy(System $system, SystemDatabase $database, SystemDatabaseResponsible $responsible)
    {
        $responsible->delete();

        return back()->with('success', 'Responsable eliminado del historial.');
    }
}
