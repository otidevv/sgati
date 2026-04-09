<?php

namespace App\Http\Controllers;

use App\Models\System;
use App\Models\SystemService;
use App\Models\SystemServiceField;
use Illuminate\Http\Request;

class SystemServiceFieldController extends Controller
{
    private const TYPES = 'string,integer,boolean,number,array,object,date,datetime,uuid,other';

    public function store(Request $request, System $system, SystemService $service)
    {
        $data = $request->validate([
            'direction'     => 'required|in:request,response',
            'field_name'    => 'required|string|max:100',
            'field_type'    => 'required|in:' . self::TYPES,
            'is_required'   => 'boolean',
            'description'   => 'nullable|string|max:255',
            'example_value' => 'nullable|string|max:255',
        ]);

        $data['is_required'] = $request->boolean('is_required');
        $data['sort_order']  = $service->fields()->where('direction', $data['direction'])->max('sort_order') + 1;

        $service->fields()->create($data);

        return back()->with('success', 'Campo registrado.');
    }

    public function update(Request $request, System $system, SystemService $service, SystemServiceField $field)
    {
        $data = $request->validate([
            'direction'     => 'required|in:request,response',
            'field_name'    => 'required|string|max:100',
            'field_type'    => 'required|in:' . self::TYPES,
            'is_required'   => 'boolean',
            'description'   => 'nullable|string|max:255',
            'example_value' => 'nullable|string|max:255',
        ]);

        $data['is_required'] = $request->boolean('is_required');

        $field->update($data);

        return back()->with('success', 'Campo actualizado.');
    }

    public function destroy(System $system, SystemService $service, SystemServiceField $field)
    {
        $field->delete();
        return back()->with('success', 'Campo eliminado.');
    }
}
