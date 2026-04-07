<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Server;
use App\Models\ServerContainer;
use App\Models\System;
use Illuminate\Http\Request;

class ServerContainerController extends Controller
{
    private const TYPES = ['frontend','backend','database','cache','queue','proxy','storage','other'];

    public function store(Request $request, Server $server)
    {
        $data = $request->validate([
            'name'          => 'required|string|max:150',
            'image'         => 'nullable|string|max:150',
            'type'          => 'required|in:' . implode(',', self::TYPES),
            'system_id'     => 'nullable|exists:systems,id',
            'internal_port' => 'nullable|integer|min:1|max:65535',
            'external_port' => 'nullable|integer|min:1|max:65535',
            'volumes'       => 'nullable|string',
            'notes'         => 'nullable|string',
        ]);

        $data['volumes'] = $this->parseLines($request->input('volumes'));

        $server->containers()->create($data);

        return back()->with('success', 'Contenedor registrado.');
    }

    public function update(Request $request, Server $server, ServerContainer $container)
    {
        $data = $request->validate([
            'name'          => 'required|string|max:150',
            'image'         => 'nullable|string|max:150',
            'type'          => 'required|in:' . implode(',', self::TYPES),
            'system_id'     => 'nullable|exists:systems,id',
            'internal_port' => 'nullable|integer|min:1|max:65535',
            'external_port' => 'nullable|integer|min:1|max:65535',
            'volumes'       => 'nullable|string',
            'notes'         => 'nullable|string',
        ]);

        $data['volumes'] = $this->parseLines($request->input('volumes'));

        $container->update($data);

        return back()->with('success', 'Contenedor actualizado.');
    }

    public function destroy(Server $server, ServerContainer $container)
    {
        $container->delete();
        return back()->with('success', 'Contenedor eliminado.');
    }

    private function parseLines(?string $input): array
    {
        if (empty($input)) return [];
        return array_values(array_filter(array_map('trim', explode("\n", $input))));
    }
}
