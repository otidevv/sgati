<?php

namespace App\Http\Controllers;

use App\Models\Repository;
use App\Models\RepositoryCollaborator;
use App\Models\System;
use Illuminate\Http\Request;

class SystemRepositoryCollaboratorController extends Controller
{
    private const ROLES = 'owner,maintainer,developer,reader,deployer';

    public function store(Request $request, System $system, Repository $repository)
    {
        $data = $request->validate([
            'persona_id'  => 'required|exists:personas,id',
            'role'        => 'required|in:' . self::ROLES,
            'assigned_at' => 'required|date',
        ]);

        $data['is_active'] = true;

        $repository->collaborators()->create($data);

        return redirect(route('systems.repositories.show', [$system, $repository]))
            ->with('success', 'Colaborador asignado correctamente.');
    }

    public function update(Request $request, System $system, Repository $repository, RepositoryCollaborator $collaborator)
    {
        $data = $request->validate([
            'persona_id'  => 'required|exists:personas,id',
            'role'        => 'required|in:' . self::ROLES,
            'assigned_at' => 'required|date',
        ]);

        $collaborator->update($data);

        return redirect(route('systems.repositories.show', [$system, $repository]))
            ->with('success', 'Colaborador actualizado correctamente.');
    }

    public function deactivate(Request $request, System $system, Repository $repository, RepositoryCollaborator $collaborator)
    {
        $data = $request->validate([
            'unassigned_at'    => 'required|date|after_or_equal:' . $collaborator->assigned_at->format('Y-m-d'),
            'deactivate_notes' => 'nullable|string|max:500',
        ]);

        $collaborator->update([
            'is_active'      => false,
            'unassigned_at'  => $data['unassigned_at'],
            'document_notes' => $data['deactivate_notes'] ?: $collaborator->document_notes,
        ]);

        return redirect(route('systems.repositories.show', [$system, $repository]))
            ->with('success', 'Colaborador dado de baja correctamente.');
    }

    public function reactivate(Request $request, System $system, Repository $repository, RepositoryCollaborator $collaborator)
    {
        $data = $request->validate([
            'assigned_at' => 'required|date',
        ]);

        $collaborator->update([
            'is_active'     => true,
            'assigned_at'   => $data['assigned_at'],
            'unassigned_at' => null,
        ]);

        return redirect(route('systems.repositories.show', [$system, $repository]))
            ->with('success', 'Colaborador reactivado correctamente.');
    }

    public function destroy(System $system, Repository $repository, RepositoryCollaborator $collaborator)
    {
        $collaborator->delete();

        return redirect(route('systems.repositories.show', [$system, $repository]))
            ->with('success', 'Colaborador eliminado del historial.');
    }

    public function pdfData(System $system, Repository $repository, RepositoryCollaborator $collaborator): \Illuminate\Http\JsonResponse
    {
        $collaborator->load('persona');

        $fields = [
            ['label' => 'Repositorio', 'value' => $repository->name],
            ['label' => 'Proveedor',   'value' => $repository->provider->label()],
            ['label' => 'Sistema',     'value' => $system->name],
        ];
        if ($repository->clean_url) {
            $fields[] = ['label' => 'URL', 'value' => $repository->clean_url];
        }
        if ($repository->default_branch) {
            $fields[] = ['label' => 'Rama principal', 'value' => $repository->default_branch];
        }
        if ($repository->notes) {
            $fields[] = ['label' => 'Notas', 'value' => $repository->notes];
        }

        return response()->json([
            'generated_at' => now()->format('d/m/Y H:i'),
            'generated_by' => auth()->user()?->persona?->nombre_completo
                           ?? auth()->user()?->name
                           ?? 'Sistema',
            'context' => [
                'subtitle'      => 'RESPONSABILIDAD DE REPOSITORIO',
                'section_title' => 'Datos del Repositorio',
                'fields'        => $fields,
                'responsibilities' => [
                    'Gestionar los accesos y permisos del repositorio conforme al nivel de colaboración asignado.',
                    'Velar por la integridad, consistencia y seguridad del código fuente alojado en el repositorio.',
                    'Cumplir con las políticas de control de versiones (branching, commits, merge requests) definidas por la OTI.',
                    'Notificar oportunamente cualquier incidente de seguridad o acceso no autorizado al repositorio.',
                    'Coordinar con la OTI antes de realizar cambios en la rama principal o modificaciones en la configuración del repositorio.',
                    'Custodiar las credenciales de acceso al repositorio con criterios de confidencialidad y seguridad.',
                ],
            ],
            'responsible' => [
                'apellido_pat'    => $collaborator->persona?->apellido_paterno,
                'nombre_completo' => $collaborator->persona
                    ? trim(($collaborator->persona->apellido_paterno ?? '') . ' ' . ($collaborator->persona->apellido_materno ?? '') . ', ' . ($collaborator->persona->nombres ?? ''))
                    : '—',
                'dni'        => $collaborator->persona?->dni,
                'email'      => $collaborator->persona?->email_personal,
                'telefono'   => $collaborator->persona?->telefono,
                'role'       => $collaborator->role,
                'role_label' => RepositoryCollaborator::roleLabel($collaborator->role),
                'assigned_at'=> $collaborator->assigned_at?->format('d/m/Y'),
                'is_active'  => $collaborator->is_active,
            ],
        ]);
    }
}
