<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Server;
use App\Models\DatabaseServer;
use App\Models\DatabaseServerResponsible;
use Illuminate\Http\Request;

class DatabaseServerResponsibleController extends Controller
{
    private const LEVELS    = 'principal,soporte,supervision,operador';
    private const DOC_TYPES = 'resolucion_directoral,resolucion_jefatural,memorando,oficio,contrato,acta,otro';

    public function store(Request $request, Server $server, DatabaseServer $databaseServer)
    {
        $data = $request->validate([
            'persona_id'      => 'required|exists:personas,id',
            'level'           => 'required|in:' . self::LEVELS,
            'assigned_at'     => 'required|date',
            'is_active'       => 'boolean',
            'document_type'   => 'nullable|in:' . self::DOC_TYPES,
            'document_number' => 'nullable|string|max:100',
            'document_date'   => 'nullable|date',
            'document_notes'  => 'nullable|string|max:500',
        ]);

        $data['is_active']       = $request->boolean('is_active', true);
        $data['document_type']   = $data['document_type']   ?: null;
        $data['document_number'] = $data['document_number'] ?: null;
        $data['document_date']   = $data['document_date']   ?: null;

        $databaseServer->responsibles()->create($data);

        return back()->with('success', 'Responsable asignado correctamente.');
    }

    public function update(Request $request, Server $server, DatabaseServer $databaseServer, DatabaseServerResponsible $responsible)
    {
        $data = $request->validate([
            'persona_id'      => 'required|exists:personas,id',
            'level'           => 'required|in:' . self::LEVELS,
            'assigned_at'     => 'required|date',
            'is_active'       => 'boolean',
            'document_type'   => 'nullable|in:' . self::DOC_TYPES,
            'document_number' => 'nullable|string|max:100',
            'document_date'   => 'nullable|date',
            'document_notes'  => 'nullable|string|max:500',
        ]);

        $data['is_active']       = $request->boolean('is_active', true);
        $data['document_type']   = $data['document_type']   ?: null;
        $data['document_number'] = $data['document_number'] ?: null;
        $data['document_date']   = $data['document_date']   ?: null;

        $responsible->update($data);

        return back()->with('success', 'Responsable actualizado correctamente.');
    }

    public function deactivate(Request $request, Server $server, DatabaseServer $databaseServer, DatabaseServerResponsible $responsible)
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

    public function reactivate(Request $request, Server $server, DatabaseServer $databaseServer, DatabaseServerResponsible $responsible)
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

    public function destroy(Server $server, DatabaseServer $databaseServer, DatabaseServerResponsible $responsible)
    {
        $responsible->delete();

        return back()->with('success', 'Responsable eliminado del historial.');
    }
}
