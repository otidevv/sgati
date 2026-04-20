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
}
