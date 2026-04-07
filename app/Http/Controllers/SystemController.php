<?php

namespace App\Http\Controllers;

use App\Models\System;
use App\Models\Area;
use App\Models\User;
use App\Enums\SystemStatus;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class SystemController extends Controller
{
    public function index(Request $request)
    {
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
        $areas    = Area::orderBy('name')->get();
        $users    = User::where('is_active', true)->orderBy('name')->get();
        $statuses = SystemStatus::cases();

        return view('systems.create', compact('areas', 'users', 'statuses'));
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
            'tech_stack'     => 'nullable|string|max:255',
            'repo_url'       => 'nullable|url|max:255',
            'observations'   => 'nullable|string',
        ]);

        $data['slug'] = Str::slug($data['name']);

        $system = System::create($data);
        $system->infrastructure()->create([]);

        return redirect()->route('systems.show', $system)
            ->with('success', 'Sistema registrado correctamente.');
    }

    public function show(System $system)
    {
        $system->load([
            'area', 'responsible',
            'infrastructure.server.ips',
            'versions',
            'databases.databaseServer',
            'services',
            'repositories',
            'integrationsFrom.targetSystem',
            'integrationsTo.sourceSystem',
            'documents.uploadedBy', 'statusLogs.changedBy',
        ]);

        $allSystems = System::where('id', '!=', $system->id)->get(['id', 'name', 'acronym']);

        return view('systems.show', compact('system', 'allSystems'));
    }

    public function edit(System $system)
    {
        $areas    = Area::orderBy('name')->get();
        $users    = User::where('is_active', true)->orderBy('name')->get();
        $statuses = SystemStatus::cases();

        return view('systems.edit', compact('system', 'areas', 'users', 'statuses'));
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
            'tech_stack'     => 'nullable|string|max:255',
            'repo_url'       => 'nullable|url|max:255',
            'observations'   => 'nullable|string',
            'status_reason'  => 'nullable|string',
        ]);

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

        return redirect()->route('systems.show', $system)
            ->with('success', 'Sistema actualizado correctamente.');
    }

    public function destroy(System $system)
    {
        $system->delete();

        return redirect()->route('systems.index')
            ->with('success', 'Sistema eliminado.');
    }
}
