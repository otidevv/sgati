<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Persona;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class PersonaController extends Controller
{
    public function index()
    {
        $personas = Persona::withCount('user')
            ->orderBy('apellido_paterno')
            ->get();

        return view('admin.personas.index', compact('personas'));
    }

    public function create()
    {
        return view('admin.personas.form', ['persona' => new Persona]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'dni'              => 'required|string|size:8|unique:personas,dni',
            'nombres'          => 'required|string|max:150',
            'apellido_paterno' => 'required|string|max:100',
            'apellido_materno' => 'required|string|max:100',
            'fecha_nacimiento' => 'nullable|date|before:today',
            'sexo'             => 'nullable|in:M,F',
            'telefono'         => 'nullable|string|max:20',
            'email_personal'   => 'nullable|email|max:150|unique:personas,email_personal',
        ]);

        Persona::create($data);

        return redirect()->route('admin.personas.index')
            ->with('success', 'Persona registrada correctamente.');
    }

    public function edit(Persona $persona)
    {
        return view('admin.personas.form', compact('persona'));
    }

    public function update(Request $request, Persona $persona)
    {
        $data = $request->validate([
            'dni'              => 'required|string|size:8|unique:personas,dni,' . $persona->id,
            'nombres'          => 'required|string|max:150',
            'apellido_paterno' => 'required|string|max:100',
            'apellido_materno' => 'required|string|max:100',
            'fecha_nacimiento' => 'nullable|date|before:today',
            'sexo'             => 'nullable|in:M,F',
            'telefono'         => 'nullable|string|max:20',
            'email_personal'   => 'nullable|email|max:150|unique:personas,email_personal,' . $persona->id,
        ]);

        $persona->update($data);

        return redirect()->route('admin.personas.index')
            ->with('success', 'Persona actualizada correctamente.');
    }

    public function search(Request $request)
    {
        $q = trim($request->get('q', ''));

        if (mb_strlen($q) < 4) {
            return response()->json([]);
        }

        $personas = Persona::where('dni', 'like', $q . '%')
            ->orWhere('apellido_paterno', 'like', '%' . $q . '%')
            ->orWhere('apellido_materno', 'like', '%' . $q . '%')
            ->orWhere('nombres', 'like', '%' . $q . '%')
            ->orderBy('apellido_paterno')
            ->orderBy('apellido_materno')
            ->limit(10)
            ->get(['id', 'dni', 'nombres', 'apellido_paterno', 'apellido_materno']);

        return response()->json($personas);
    }

    public function dniLookup(string $dni)
    {
        if (!preg_match('/^\d{8}$/', $dni)) {
            return response()->json(['error' => 'DNI inválido.'], 422);
        }

        try {
            $response = Http::timeout(8)
                ->get(config('services.dni_api.url') . '/' . $dni);

            if ($response->failed() || $response->json() === null) {
                return response()->json(['error' => 'No se encontraron datos para este DNI.'], 404);
            }

            $data = $response->json();

            return response()->json([
                'nombres'          => ucwords(strtolower($data['NOMBRES'] ?? '')),
                'apellido_paterno' => ucwords(strtolower($data['AP_PAT'] ?? '')),
                'apellido_materno' => ucwords(strtolower($data['AP_MAT'] ?? '')),
                'fecha_nacimiento' => $data['FECHA_NAC'] ?? null,
                'sexo'             => match($data['SEXO'] ?? '') { '1' => 'M', '2' => 'F', default => '' },
            ]);
        } catch (\Throwable) {
            return response()->json(['error' => 'No se pudo conectar con el servicio de consulta.'], 503);
        }
    }

    public function destroy(Persona $persona)
    {
        abort_if($persona->user()->exists(), 403, 'No se puede eliminar una persona vinculada a un usuario.');
        $persona->delete();

        return redirect()->route('admin.personas.index')
            ->with('success', 'Persona eliminada.');
    }
}
