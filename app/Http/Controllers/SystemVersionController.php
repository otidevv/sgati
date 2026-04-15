<?php

namespace App\Http\Controllers;

use App\Models\System;
use App\Models\SystemVersion;
use Illuminate\Http\Request;

class SystemVersionController extends Controller
{
    public function create(System $system)
    {
        return view('systems.versions.create', compact('system'));
    }

    public function store(Request $request, System $system)
    {
        $data = $request->validate([
            'version'      => 'required|string|max:20',
            'release_date' => 'required|date',
            'environment'  => 'required|in:production,staging,development',
            'changes'      => 'nullable|string',
            'git_commit'   => 'nullable|string|max:100',
            'git_branch'   => 'nullable|string|max:100',
            'notes'        => 'nullable|string',
        ]);

        $data['deployed_by'] = auth()->id();

        $system->versions()->create($data);

        return redirect(route('systems.show', $system) . '#versions')
            ->with('success', 'Versión registrada correctamente.');
    }

    public function edit(System $system, SystemVersion $version)
    {
        return view('systems.versions.edit', compact('system', 'version'));
    }

    public function update(Request $request, System $system, SystemVersion $version)
    {
        $data = $request->validate([
            'version'      => 'required|string|max:20',
            'release_date' => 'required|date',
            'environment'  => 'required|in:production,staging,development',
            'changes'      => 'nullable|string',
            'git_commit'   => 'nullable|string|max:100',
            'git_branch'   => 'nullable|string|max:100',
            'notes'        => 'nullable|string',
        ]);

        $version->update($data);

        return redirect(route('systems.show', $system) . '#versions')
            ->with('success', 'Versión actualizada correctamente.');
    }

    public function destroy(System $system, SystemVersion $version)
    {
        $version->delete();

        return redirect(route('systems.show', $system) . '#versions')
            ->with('success', 'Versión eliminada.');
    }
}
