<?php

namespace App\Http\Controllers\Administration;

use App\Http\Controllers\Controller;
use App\Models\Permission;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Str;

class RoleController extends Controller
{
    public function index()
    {
        Gate::authorize('administration.gerer');

        $roles = Role::withCount('utilisateurs', 'permissions')->orderBy('libelle')->get();

        return view('administration.roles.index', compact('roles'));
    }

    public function store(Request $request)
    {
        Gate::authorize('administration.gerer');

        $data = $request->validate([
            'libelle' => ['required', 'string', 'max:255', 'unique:roles,libelle'],
        ]);

        Role::create([
            'nom' => Str::slug($data['libelle']),
            'libelle' => $data['libelle'],
        ]);

        return back()->with('success', 'Rôle créé avec succès.');
    }

    public function edit(Role $role)
    {
        Gate::authorize('administration.gerer');

        $permissionsParModule = Permission::orderBy('module')->orderBy('libelle')->get()->groupBy('module');
        $permissionsActives = $role->permissions->pluck('id')->all();

        return view('administration.roles.edit', compact('role', 'permissionsParModule', 'permissionsActives'));
    }

    public function update(Request $request, Role $role)
    {
        Gate::authorize('administration.gerer');

        $data = $request->validate([
            'libelle' => ['required', 'string', 'max:255', 'unique:roles,libelle,'.$role->id],
            'permissions' => ['nullable', 'array'],
            'permissions.*' => ['exists:permissions,id'],
        ]);

        $role->update(['libelle' => $data['libelle']]);
        $role->permissions()->sync($data['permissions'] ?? []);

        return redirect()->route('administration.roles.index')->with('success', 'Rôle et permissions mis à jour avec succès.');
    }

    public function destroy(Role $role)
    {
        Gate::authorize('administration.gerer');

        abort_if($role->estSysteme(), 403, 'Ce rôle système ne peut pas être supprimé.');
        abort_if($role->utilisateurs()->exists(), 422, 'Ce rôle est encore attribué à des utilisateurs.');

        $role->delete();

        return back()->with('success', 'Rôle supprimé avec succès.');
    }
}
