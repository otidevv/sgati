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

    public function pdfData(System $system, SystemVersion $version, SystemVersionResponsible $responsible): \Illuminate\Http\JsonResponse
    {
        $responsible->load('persona');

        $fields = [
            ['label' => 'Número de versión', 'value' => 'v' . $version->version],
            ['label' => 'Sistema',            'value' => $system->name],
            ['label' => 'Ambiente',           'value' => $version->environment?->label() ?? '—'],
        ];
        if ($version->release_date) {
            $fields[] = ['label' => 'Fecha de despliegue', 'value' => $version->release_date->format('d/m/Y')];
        }
        if ($version->git_branch) {
            $fields[] = ['label' => 'Rama Git', 'value' => $version->git_branch];
        }
        if ($version->changes) {
            $fields[] = ['label' => 'Cambios incluidos', 'value' => \Illuminate\Support\Str::limit($version->changes, 200)];
        }

        return response()->json([
            'generated_at' => now()->format('d/m/Y H:i'),
            'generated_by' => auth()->user()?->persona?->nombre_completo
                           ?? auth()->user()?->name
                           ?? 'Sistema',
            'context' => [
                'subtitle'      => 'RESPONSABILIDAD DE VERSIÓN',
                'section_title' => 'Datos de la Versión',
                'fields'        => $fields,
                'responsibilities' => [
                    'Coordinar y supervisar el proceso de despliegue de la versión indicada en el ambiente correspondiente.',
                    'Verificar el correcto funcionamiento del sistema tras el despliegue y validar los criterios de aceptación.',
                    'Notificar oportunamente cualquier incidente o falla detectada durante o después del despliegue.',
                    'Coordinar con los equipos de desarrollo y operaciones para la resolución de incidencias post-despliegue.',
                    'Custodiar el registro de cambios (changelog) y la documentación técnica de la versión.',
                    'Asegurar el cumplimiento del proceso de control de cambios definido por la OTI.',
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
                'role'       => $responsible->role,
                'role_label' => SystemVersionResponsible::roleLabel($responsible->role),
                'assigned_at'=> $responsible->assigned_at?->format('d/m/Y'),
                'is_active'  => $responsible->is_active,
            ],
        ]);
    }
}
