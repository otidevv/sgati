<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Persona;
use Illuminate\Http\Request;

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

    public function destroy(Persona $persona)
    {
        abort_if($persona->user()->exists(), 403, 'No se puede eliminar una persona vinculada a un usuario.');
        $persona->delete();

        return redirect()->route('admin.personas.index')
            ->with('success', 'Persona eliminada.');
    }
}
