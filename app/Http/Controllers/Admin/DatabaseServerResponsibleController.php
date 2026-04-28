<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Server;
use App\Models\DatabaseServer;
use App\Models\DatabaseServerResponsible;
use Illuminate\Http\Request;

class DatabaseServerResponsibleController extends Controller
{
    private const LEVELS = 'principal,soporte,supervision,operador';

    public function store(Request $request, Server $server, DatabaseServer $databaseServer)
    {
        $data = $request->validate([
            'persona_id'  => 'required|exists:personas,id',
            'level'       => 'required|in:' . self::LEVELS,
            'assigned_at' => 'required|date',
            'is_active'   => 'boolean',
        ]);

        $data['is_active'] = $request->boolean('is_active', true);

        $databaseServer->responsibles()->create($data);

        return back()->with('success', 'Responsable asignado correctamente.');
    }

    public function update(Request $request, Server $server, DatabaseServer $databaseServer, DatabaseServerResponsible $responsible)
    {
        $data = $request->validate([
            'persona_id'  => 'required|exists:personas,id',
            'level'       => 'required|in:' . self::LEVELS,
            'assigned_at' => 'required|date',
            'is_active'   => 'boolean',
        ]);

        $data['is_active'] = $request->boolean('is_active', true);

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

    public function pdfData(Server $server, DatabaseServer $databaseServer, DatabaseServerResponsible $responsible): \Illuminate\Http\JsonResponse
    {
        $responsible->load('persona');

        $fields = [
            ['label' => 'Motor de BD',   'value' => $databaseServer->engine_label],
            ['label' => 'Host / Puerto', 'value' => $databaseServer->connection_string],
            ['label' => 'Servidor',      'value' => $server->name],
        ];
        if ($databaseServer->name) {
            $fields[] = ['label' => 'Nombre / Instancia', 'value' => $databaseServer->name];
        }
        if ($databaseServer->notes) {
            $fields[] = ['label' => 'Notas', 'value' => $databaseServer->notes];
        }

        return response()->json([
            'generated_at' => now()->format('d/m/Y H:i'),
            'generated_by' => auth()->user()?->persona?->nombre_completo
                           ?? auth()->user()?->name
                           ?? 'Sistema',
            'context' => [
                'subtitle'      => 'RESPONSABILIDAD DE SERVIDOR DE BD',
                'section_title' => 'Datos del Servidor de Base de Datos',
                'fields'        => $fields,
                'responsibilities' => [
                    'Velar por el correcto funcionamiento, disponibilidad y seguridad del servidor de base de datos asignado.',
                    'Gestionar las instancias, usuarios y configuraciones del servidor con criterios de mínimo privilegio y seguridad.',
                    'Implementar y verificar periódicamente las copias de seguridad y los planes de recuperación ante desastres.',
                    'Notificar oportunamente cualquier incidente, falla o anomalía que afecte al servidor de base de datos.',
                    'Mantener actualizados el motor de base de datos y los parches de seguridad conforme a las políticas de la OTI.',
                    'Coordinar con la OTI antes de realizar cambios estructurales, migraciones o actualizaciones del motor.',
                ],
            ],
            'responsible' => [
                'apellido_pat'    => $responsible->persona?->apellido_paterno,
                'nombre_completo' => $responsible->persona
                    ? trim($responsible->persona->apellido_paterno . ' ' . ($responsible->persona->apellido_materno ?? '') . ', ' . $responsible->persona->nombres)
                    : '—',
                'dni'        => $responsible->persona?->dni,
                'email'      => $responsible->persona?->email_personal,
                'telefono'   => $responsible->persona?->telefono,
                'role'       => $responsible->level,
                'role_label' => DatabaseServerResponsible::levelLabel($responsible->level),
                'assigned_at'=> $responsible->assigned_at?->format('d/m/Y'),
                'is_active'  => $responsible->is_active,
            ],
        ]);
    }
}
