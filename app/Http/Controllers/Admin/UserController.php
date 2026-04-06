<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Area;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function index()
    {
        $users = User::with(['role', 'area', 'persona'])->orderBy('name')->get();

        return view('admin.users.index', compact('users'));
    }

    public function create()
    {
        $personas = \App\Models\Persona::whereDoesntHave('user')
            ->orderBy('apellido_paterno')
            ->get();
        $areas = Area::orderBy('name')->get();
        $roles = Role::orderBy('label')->get();

        return view('admin.users.form', ['user' => new User, 'personas' => $personas, 'areas' => $areas, 'roles' => $roles]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'persona_id'    => 'required|exists:personas,id|unique:users,persona_id',
            'name'          => 'required|string|max:255',
            'email'         => 'required|email|unique:users,email',
            'password'      => 'required|string|min:8|confirmed',
            'role_id'       => 'required|exists:roles,id',
            'area_id'       => 'nullable|exists:areas,id',
            'is_active'     => 'boolean',
        ]);

        $data['password']  = Hash::make($data['password']);
        $data['is_active'] = $request->boolean('is_active', true);

        User::create($data);

        return redirect()->route('admin.users.index')
            ->with('success', 'Usuario creado correctamente.');
    }

    public function edit(User $user)
    {
        $personas = \App\Models\Persona::where(function ($q) use ($user) {
            $q->whereDoesntHave('user')->orWhere('id', $user->persona_id);
        })->orderBy('apellido_paterno')->get();
        $areas = Area::orderBy('name')->get();
        $roles = Role::orderBy('label')->get();

        return view('admin.users.form', compact('user', 'personas', 'areas', 'roles'));
    }

    public function update(Request $request, User $user)
    {
        $data = $request->validate([
            'persona_id'    => 'required|exists:personas,id|unique:users,persona_id,' . $user->id,
            'name'          => 'required|string|max:255',
            'email'         => 'required|email|unique:users,email,' . $user->id,
            'password'      => 'nullable|string|min:8|confirmed',
            'role_id'       => 'required|exists:roles,id',
            'area_id'       => 'nullable|exists:areas,id',
            'is_active'     => 'boolean',
        ]);

        if (!empty($data['password'])) {
            $data['password'] = Hash::make($data['password']);
        } else {
            unset($data['password']);
        }

        $data['is_active'] = $request->boolean('is_active');

        $user->update($data);

        return redirect()->route('admin.users.index')
            ->with('success', 'Usuario actualizado correctamente.');
    }

    public function destroy(User $user)
    {
        abort_if($user->id === auth()->id(), 403, 'No puedes eliminar tu propia cuenta.');
        $user->delete();

        return redirect()->route('admin.users.index')
            ->with('success', 'Usuario eliminado.');
    }
}
