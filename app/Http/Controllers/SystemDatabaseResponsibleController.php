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

        return redirect(route('systems.show', $system) . '#databases')->with('success', 'Responsable asignado correctamente.');
    }

    public function update(Request $request, System $system, SystemDatabase $database, SystemDatabaseResponsible $responsible)
    {
        $data = $request->validate([
            'persona_id'  => 'required|exists:personas,id',
            'level'       => 'required|in:' . self::LEVELS,
            'assigned_at' => 'required|date',
        ]);

        $responsible->update($data);

        return redirect(route('systems.show', $system) . '#databases')->with('success', 'Responsable actualizado correctamente.');
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

        return redirect(route('systems.show', $system) . '#databases')->with('success', 'Responsable dado de baja correctamente.');
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

        return redirect(route('systems.show', $system) . '#databases')->with('success', 'Responsable reactivado correctamente.');
    }

    public function destroy(System $system, SystemDatabase $database, SystemDatabaseResponsible $responsible)
    {
        $responsible->delete();

        return redirect(route('systems.show', $system) . '#databases')->with('success', 'Responsable eliminado del historial.');
    }

    public function pdfData(System $system, SystemDatabase $database, SystemDatabaseResponsible $responsible): \Illuminate\Http\JsonResponse
    {
        $responsible->load('persona');
        $database->load('databaseServer');

        $fields = [
            ['label' => 'Nombre de la BD', 'value' => $database->db_name],
            ['label' => 'Motor',            'value' => strtoupper($database->engine)],
            ['label' => 'Ambiente',         'value' => $database->environment?->label() ?? '—'],
            ['label' => 'Sistema',          'value' => $system->name],
        ];
        if ($database->schema_name) {
            $fields[] = ['label' => 'Schema', 'value' => $database->schema_name];
        }
        if ($database->databaseServer) {
            $fields[] = ['label' => 'Servidor de BD', 'value' => $database->databaseServer->connection_string];
        }
        if ($database->notes) {
            $fields[] = ['label' => 'Notas', 'value' => $database->notes];
        }

        return response()->json([
            'generated_at' => now()->format('d/m/Y H:i'),
            'generated_by' => auth()->user()?->persona?->nombre_completo
                           ?? auth()->user()?->name
                           ?? 'Sistema',
            'context' => [
                'subtitle'      => 'RESPONSABILIDAD DE BASE DE DATOS',
                'section_title' => 'Datos de la Base de Datos',
                'fields'        => $fields,
                'responsibilities' => [
                    'Velar por la integridad, disponibilidad y seguridad de la base de datos asignada.',
                    'Gestionar los accesos, permisos y usuarios de la base de datos con criterios de mínimo privilegio.',
                    'Implementar y verificar periódicamente las copias de seguridad (backup) de la base de datos.',
                    'Notificar oportunamente cualquier incidente, pérdida de datos o anomalía detectada.',
                    'Coordinar con la OTI antes de realizar cambios estructurales (schemas, migraciones, índices críticos).',
                    'Mantener actualizada la documentación técnica de la base de datos.',
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
                'role_label' => SystemDatabaseResponsible::levelLabel($responsible->level),
                'assigned_at'=> $responsible->assigned_at?->format('d/m/Y'),
                'is_active'  => $responsible->is_active,
            ],
        ]);
    }
}
