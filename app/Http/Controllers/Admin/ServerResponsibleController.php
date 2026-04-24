<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Server;
use App\Models\ServerResponsible;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ServerResponsibleController extends Controller
{
    private const LEVELS = 'principal,soporte,supervision,operador';

    public function store(Request $request, Server $server)
    {
        $data = $request->validate([
            'persona_id'  => 'required|exists:personas,id',
            'level'       => 'required|in:' . self::LEVELS,
            'assigned_at' => 'required|date',
            'is_active'   => 'boolean',
        ]);

        $data['is_active'] = $request->boolean('is_active', true);

        $server->responsibles()->create($data);

        return back()->with('success', 'Responsable asignado correctamente.');
    }

    public function update(Request $request, Server $server, ServerResponsible $responsible)
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

    public function reactivate(Request $request, Server $server, ServerResponsible $responsible)
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

    public function deactivate(Request $request, Server $server, ServerResponsible $responsible)
    {
        $data = $request->validate([
            'unassigned_at' => 'required|date|after_or_equal:' . $responsible->assigned_at->format('Y-m-d'),
            'deactivate_notes' => 'nullable|string|max:500',
        ]);

        $responsible->update([
            'is_active'        => false,
            'unassigned_at'    => $data['unassigned_at'],
            'document_notes'   => $data['deactivate_notes'] ?: $responsible->document_notes,
        ]);

        return back()->with('success', 'Responsable dado de baja correctamente.');
    }

    public function destroy(Server $server, ServerResponsible $responsible)
    {
        $responsible->delete();

        return back()->with('success', 'Responsable eliminado del historial.');
    }

    public function pdfData(Server $server, ServerResponsible $responsible): JsonResponse
    {
        $responsible->load(['persona', 'documents']);
        $server->load(['ips', 'responsibles.persona']);

        $primaryIp = $server->ips->where('is_primary', true)->first()?->ip_address
            ?? $server->ips->first()?->ip_address;

        $publicIps = $server->ips->where('type', 'public')->pluck('ip_address')->all();

        return response()->json([
            'generated_at'  => now()->format('d/m/Y H:i'),
            'generated_by'  => auth()->user()?->persona?->nombre_completo
                             ?? auth()->user()?->name
                             ?? 'Sistema',
            'server' => [
                'name'     => $server->name,
                'os'       => $server->operating_system,
                'function' => $server->function?->value ?? $server->function,
                'host_type'=> $server->host_type,
                'services' => $server->installed_services ?? [],
                'primary_ip'  => $primaryIp,
                'public_ips'  => $publicIps,
                'notes'    => $server->notes,
            ],
            'responsible' => [
                'id'           => $responsible->id,
                'nombres'      => $responsible->persona?->nombres,
                'apellido_pat' => $responsible->persona?->apellido_paterno,
                'apellido_mat' => $responsible->persona?->apellido_materno,
                'nombre_completo' => $responsible->persona
                    ? trim($responsible->persona->apellido_paterno . ' ' . $responsible->persona->apellido_materno . ', ' . $responsible->persona->nombres)
                    : '—',
                'dni'          => $responsible->persona?->dni,
                'email'        => $responsible->persona?->email_personal,
                'telefono'     => $responsible->persona?->telefono,
                'level'        => $responsible->level,
                'level_label'  => ServerResponsible::levelLabel($responsible->level),
                'assigned_at'  => $responsible->assigned_at?->format('d/m/Y'),
                'is_active'    => $responsible->is_active,
            ],
        ]);
    }
}
