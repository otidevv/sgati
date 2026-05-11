<?php

namespace App\Http\Controllers;

use App\Models\System;
use Illuminate\Http\Request;

class SystemDecommissionController extends Controller
{
    public function create(System $system)
    {
        $this->authorize('systems.edit');

        $allSystems = System::where('id', '!=', $system->id)->get(['id', 'name', 'acronym']);

        return view('systems.decommission', compact('system', 'allSystems'));
    }

    public function store(Request $request, System $system)
    {
        $this->authorize('systems.edit');

        $data = $request->validate([
            'reason_type'                => 'required|in:obsolete,replaced,contract_expired,budget,merge,directive,other',
            'reason'                     => 'required|string|max:2000',
            'effective_date'             => 'required|date',
            'shutdown_date'              => 'nullable|date|after_or_equal:effective_date',
            'resolution_number'          => 'nullable|string|max:100',
            'resolution_date'            => 'nullable|date',
            'resolution_entity'          => 'nullable|string|max:200',
            'memo_number'                => 'nullable|string|max:100',
            'memo_date'                  => 'nullable|date',
            'authorized_by_name'         => 'nullable|string|max:200',
            'authorized_by_position'     => 'nullable|string|max:200',
            'successor_system_id'        => 'nullable|exists:systems,id',
            'successor_description'      => 'nullable|string|max:500',
            'data_migrated'              => 'boolean',
            'data_migration_destination' => 'nullable|string|max:500',
            'data_retention_until'       => 'nullable|date',
            'users_affected'             => 'nullable|integer|min:0',
            'audit_notes'                => 'nullable|string',
            'reactivation_procedure'     => 'nullable|string',
        ]);

        $data['registered_by_user_id'] = auth()->id();
        $data['data_migrated']         = $request->boolean('data_migrated');

        // Crear o actualizar la ficha de baja
        $system->decommission()->updateOrCreate(
            ['system_id' => $system->id],
            $data
        );

        $oldStatus = $system->status->value;
        $system->update(['status' => 'decommissioned']);

        if ($oldStatus !== 'decommissioned') {
            $system->statusLogs()->create([
                'old_status' => $oldStatus,
                'new_status' => 'decommissioned',
                'changed_by' => auth()->id(),
                'reason'     => $data['reason'],
            ]);
        }

        return redirect()->route('systems.show', $system)
            ->with('success', 'Sistema dado de baja correctamente. La ficha de baja ha sido registrada.');
    }
}
