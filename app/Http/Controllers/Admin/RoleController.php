<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Permission;
use App\Models\Role;
use Illuminate\Http\Request;

class RoleController extends Controller
{
    public function index()
    {
        $roles       = Role::with('permissions')->orderBy('label')->get();
        $permissions = Permission::orderBy('module')->orderBy('label')->get()
                        ->groupBy('module');

        return view('admin.roles.index', compact('roles', 'permissions'));
    }

    public function updatePermissions(Request $request, Role $role)
    {
        abort_if($role->name === 'admin', 403, 'El rol administrador no necesita permisos explícitos.');

        $validated = $request->validate([
            'permissions'   => 'nullable|array',
            'permissions.*' => 'integer|exists:permissions,id',
        ]);

        $role->permissions()->sync($validated['permissions'] ?? []);

        return redirect()->route('admin.roles.index', ['tab' => $role->name])
            ->with('success', "Permisos del rol «{$role->label}» actualizados correctamente.");
    }
}
