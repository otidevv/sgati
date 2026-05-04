<?php

namespace App\Http\Controllers;

use App\Models\System;
use App\Models\Area;
use App\Models\User;
use App\Enums\SystemStatus;
use App\Enums\SystemOriginType;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class SystemController extends Controller
{
    public function index(Request $request)
    {
        $this->authorize('systems.viewAny');

        $systems = System::with(['area', 'responsible', 'infrastructure'])
            ->when($request->status, fn($q) => $q->where('status', $request->status))
            ->when($request->area_id, fn($q) => $q->where('area_id', $request->area_id))
            ->when($request->search, fn($q) => $q->where('name', 'ilike', "%{$request->search}%"))
            ->orderByDesc('updated_at')
            ->paginate(20)
            ->withQueryString();

        $areas    = Area::orderBy('name')->get();
        $statuses = SystemStatus::cases();

        return view('systems.index', compact('systems', 'areas', 'statuses'));
    }

    public function create()
    {
        $areas       = Area::orderBy('name')->get();
        $users       = User::where('is_active', true)->orderBy('name')->get();
        $statuses    = SystemStatus::cases();
        $originTypes = SystemOriginType::cases();

        return view('systems.create', compact('areas', 'users', 'statuses', 'originTypes'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name'           => 'required|string|max:100',
            'acronym'        => 'nullable|string|max:20',
            'description'    => 'nullable|string',
            'status'         => 'required|in:active,inactive,development,maintenance',
            'area_id'        => 'nullable|exists:areas,id',
            'responsible_id' => 'nullable|exists:users,id',
            'tech_stack'     => 'nullable|string',
            'repo_url'       => 'nullable|url|max:255',
            'observations'   => 'nullable|string',
        ]);

        $data['tech_stack'] = $data['tech_stack']
            ? (json_decode($data['tech_stack'], true) ?: null)
            : null;

        $data['slug'] = Str::slug($data['name']);

        $system = System::create($data);
        $system->infrastructure()->create([]);

        if ($request->filled('origin_type')) {
            $system->origin()->create($this->extractOriginData($request));
        }

        return redirect()->route('systems.show', $system)
            ->with('success', 'Sistema registrado correctamente.');
    }

    public function show(System $system)
    {
        $system->load([
            'area', 'responsible',
            'origin',
            'infrastructure.server.ips',
            'versions.responsibles.persona',
            'databases.databaseServer', 'databases.responsibles.persona',
            'services.fields', 'services.documents',
            'repositories.collaborators.persona',
            'integrationsFrom.targetSystem',
            'integrationsTo.sourceSystem',
            'documents.uploadedBy', 'statusLogs.changedBy',
            'activityLogs.causer',
            'responsibles.persona', 'responsibles.documents',
        ]);

        $allSystems = System::where('id', '!=', $system->id)->get(['id', 'name', 'acronym']);

        return view('systems.show', compact('system', 'allSystems'));
    }

    public function edit(System $system)
    {
        $system->load('origin');

        $areas       = Area::orderBy('name')->get();
        $users       = User::where('is_active', true)->orderBy('name')->get();
        $statuses    = SystemStatus::cases();
        $originTypes = SystemOriginType::cases();

        return view('systems.edit', compact('system', 'areas', 'users', 'statuses', 'originTypes'));
    }

    public function update(Request $request, System $system)
    {
        $data = $request->validate([
            'name'           => 'required|string|max:100',
            'acronym'        => 'nullable|string|max:20',
            'description'    => 'nullable|string',
            'status'         => 'required|in:active,inactive,development,maintenance',
            'area_id'        => 'nullable|exists:areas,id',
            'responsible_id' => 'nullable|exists:users,id',
            'tech_stack'     => 'nullable|string',
            'repo_url'       => 'nullable|url|max:255',
            'observations'   => 'nullable|string',
            'status_reason'  => 'nullable|string',
        ]);

        $data['tech_stack'] = $data['tech_stack']
            ? (json_decode($data['tech_stack'], true) ?: null)
            : null;

        $oldStatus = $system->status->value;
        $system->update($data);

        if ($oldStatus !== $request->status) {
            $system->statusLogs()->create([
                'old_status' => $oldStatus,
                'new_status' => $request->status,
                'changed_by' => auth()->id(),
                'reason'     => $request->status_reason,
            ]);
        }

        if ($request->filled('origin_type')) {
            $system->origin()->updateOrCreate(
                ['system_id' => $system->id],
                $this->extractOriginData($request)
            );
        } else {
            $system->origin()->delete();
        }

        return redirect()->route('systems.show', $system)
            ->with('success', 'Sistema actualizado correctamente.');
    }

    public function destroy(System $system)
    {
        $system->delete();

        return redirect()->route('systems.index')
            ->with('success', 'Sistema eliminado.');
    }

    private function extractOriginData(Request $request): array
    {
        $type = $request->input('origin_type');

        $common = [
            'origin_type'  => $type,
            'origin_notes' => $request->input('origin_notes'),
        ];

        return match($type) {
            'donated' => array_merge($common, $request->only([
                'donor_name', 'donor_institution', 'donation_type',
                'thesis_title', 'thesis_author', 'thesis_university',
                'donation_date', 'donation_document',
            ])),
            'third_party' => array_merge($common, $request->only([
                'company_name', 'contact_name', 'contact_email', 'contact_phone',
                'contract_number', 'contract_date', 'contract_value', 'warranty_expiry',
            ])),
            'internal' => array_merge($common, $request->only([
                'team_name', 'dev_start_date', 'dev_end_date', 'methodology', 'project_code',
            ])),
            'state' => array_merge($common, $request->only([
                'state_entity', 'state_entity_code', 'state_system_code',
                'state_official_url', 'legal_basis', 'state_implementation_date',
            ])),
            default => $common,
        };
    }
}
