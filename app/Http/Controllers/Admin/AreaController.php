<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Area;
use Illuminate\Http\Request;

class AreaController extends Controller
{
    public function index()
    {
        $areas = Area::withCount('systems')->orderBy('name')->get();

        return view('admin.areas.index', compact('areas'));
    }

    public function create()
    {
        return view('admin.areas.form', ['area' => new Area]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name'        => 'required|string|max:150|unique:areas,name',
            'acronym'     => 'nullable|string|max:20',
            'description' => 'nullable|string',
        ]);

        Area::create($data);

        return redirect()->route('admin.areas.index')
            ->with('success', 'Área registrada correctamente.');
    }

    public function edit(Area $area)
    {
        return view('admin.areas.form', compact('area'));
    }

    public function update(Request $request, Area $area)
    {
        $data = $request->validate([
            'name'        => 'required|string|max:150|unique:areas,name,' . $area->id,
            'acronym'     => 'nullable|string|max:20',
            'description' => 'nullable|string',
        ]);

        $area->update($data);

        return redirect()->route('admin.areas.index')
            ->with('success', 'Área actualizada correctamente.');
    }

    public function destroy(Area $area)
    {
        $area->delete();

        return redirect()->route('admin.areas.index')
            ->with('success', 'Área eliminada.');
    }
}
