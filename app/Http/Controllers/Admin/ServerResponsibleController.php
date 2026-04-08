<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Server;
use App\Models\ServerResponsible;
use Illuminate\Http\Request;

class ServerResponsibleController extends Controller
{
    private const LEVELS    = 'principal,soporte,supervision,operador';
    private const DOC_TYPES = 'resolucion_directoral,resolucion_jefatural,memorando,oficio,contrato,acta,otro';

    public function store(Request $request, Server $server)
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

        $data['is_active'] = $request->boolean('is_active', true);
        $data['document_type']   = $data['document_type']   ?: null;
        $data['document_number'] = $data['document_number'] ?: null;
        $data['document_date']   = $data['document_date']   ?: null;

        $server->responsibles()->create($data);

        return back()->with('success', 'Responsable asignado correctamente.');
    }

    public function update(Request $request, Server $server, ServerResponsible $responsible)
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

    public function destroy(Server $server, ServerResponsible $responsible)
    {
        $responsible->delete();

        return back()->with('success', 'Responsable eliminado.');
    }
}
