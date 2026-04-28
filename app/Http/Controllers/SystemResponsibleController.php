<?php

namespace App\Http\Controllers;

use App\Models\System;
use App\Models\SystemResponsible;
use Illuminate\Http\Request;

class SystemResponsibleController extends Controller
{
    private const LEVELS = ['lider_proyecto','desarrollador','mantenimiento','administrador','analista','soporte','supervision'];

    private function levelRules(): array
    {
        return [
            'persona_id'  => 'required|exists:personas,id',
            'level'       => 'required|array|min:1',
            'level.*'     => 'in:' . implode(',', self::LEVELS),
            'assigned_at' => 'required|date',
            'is_active'   => 'boolean',
        ];
    }

    public function store(Request $request, System $system)
    {
        $data = $request->validate($this->levelRules());

        $data['is_active'] = $request->boolean('is_active', true);

        $system->responsibles()->create($data);

        return redirect(route('systems.show', $system) . '#responsibles')->with('success', 'Responsable asignado correctamente.');
    }

    public function update(Request $request, System $system, SystemResponsible $responsible)
    {
        $data = $request->validate($this->levelRules());

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

    public function pdfData(System $system, SystemResponsible $responsible): \Illuminate\Http\JsonResponse
    {
        $responsible->load('persona');
        $system->load('area');

        $levels    = (array) $responsible->level;
        $roleLabel = implode(' · ', array_map(fn($l) => SystemResponsible::levelLabel($l), $levels));
        $roleKey   = $levels[0] ?? 'soporte';

        $fields = [
            ['label' => 'Nombre del sistema', 'value' => $system->name],
            ['label' => 'Acrónimo',            'value' => $system->acronym ?? '—'],
            ['label' => 'Estado',              'value' => $system->status?->label() ?? '—'],
        ];
        if ($system->area?->name) {
            $fields[] = ['label' => 'Área responsable', 'value' => $system->area->name];
        }
        if ($system->observations) {
            $fields[] = ['label' => 'Observaciones', 'value' => $system->observations];
        }

        return response()->json([
            'generated_at' => now()->format('d/m/Y H:i'),
            'generated_by' => auth()->user()?->persona?->nombre_completo
                           ?? auth()->user()?->name
                           ?? 'Sistema',
            'context' => [
                'subtitle'      => 'RESPONSABILIDAD DE SISTEMA',
                'section_title' => 'Datos del Sistema',
                'fields'        => $fields,
                'responsibilities' => [
                    'Velar por el correcto funcionamiento, disponibilidad y continuidad operativa del sistema asignado.',
                    'Coordinar y supervisar las actividades de desarrollo, mantenimiento y soporte del sistema.',
                    'Notificar oportunamente cualquier incidente, vulnerabilidad o anomalía que afecte al sistema.',
                    'Garantizar el cumplimiento de los estándares y políticas de seguridad definidos por la OTI.',
                    'Coordinar con la OTI antes de realizar actualizaciones, migraciones o cambios críticos en el sistema.',
                    'Asegurar la disponibilidad y actualización de la documentación técnica y funcional del sistema.',
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
                'role'       => $roleKey,
                'role_label' => $roleLabel,
                'assigned_at'=> $responsible->assigned_at?->format('d/m/Y'),
                'is_active'  => $responsible->is_active,
            ],
        ]);
    }
}
