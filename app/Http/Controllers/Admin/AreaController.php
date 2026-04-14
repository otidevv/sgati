<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Area;
use Illuminate\Http\Request;

class AreaController extends Controller
{
    public function index()
    {
        $this->authorize('areas.viewAny');

        $areas = Area::withCount('systems')->orderBy('name')->get();

        return view('admin.areas.index', compact('areas'));
    }

    public function create()
    {
        $this->authorize('areas.create');

        return view('admin.areas.form', ['area' => new Area]);
    }

    public function store(Request $request)
    {
        $this->authorize('areas.create');

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
        $this->authorize('areas.edit');

        return view('admin.areas.form', compact('area'));
    }

    public function update(Request $request, Area $area)
    {
        $this->authorize('areas.edit');

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
        $this->authorize('areas.delete');

        $count = $area->systems()->count();
        if ($count > 0) {
            return redirect()->route('admin.areas.index')
                ->with('error', "No se puede eliminar «{$area->name}»: tiene {$count} sistema(s) asociado(s). Reasigna o elimina los sistemas primero.");
        }

        $area->delete();

        return redirect()->route('admin.areas.index')
            ->with('success', 'Área eliminada correctamente.');
    }
}
