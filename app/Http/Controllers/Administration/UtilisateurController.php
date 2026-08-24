<?php

namespace App\Http\Controllers\Administration;

use App\Http\Controllers\Controller;
use App\Models\Departement;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class UtilisateurController extends Controller
{
    public function index(Request $request)
    {
        Gate::authorize('administration.gerer');

        $utilisateurs = User::with('role', 'departement')
            ->when($request->filled('role_id'), fn ($q) => $q->where('role_id', $request->role_id))
            ->when($request->filled('statut'), fn ($q) => $q->where('statut', $request->statut))
            ->orderBy('name')
            ->get();

        $roles = Role::orderBy('libelle')->get();

        return view('administration.utilisateurs.index', compact('utilisateurs', 'roles'));
    }

    public function create()
    {
        Gate::authorize('administration.gerer');

        $roles = Role::orderBy('libelle')->get();
        $departements = Departement::orderBy('nom')->get();

        return view('administration.utilisateurs.create', compact('roles', 'departements'));
    }

    public function store(Request $request)
    {
        Gate::authorize('administration.gerer');

        $data = $this->valider($request);

        $data['password'] = Hash::make($data['password']);
        if (! empty($data['code_pin'])) {
            $data['code_pin'] = Hash::make($data['code_pin']);
        }

        User::create($data);

        return redirect()->route('administration.utilisateurs.index')->with('success', 'Utilisateur créé avec succès.');
    }

    public function edit(User $utilisateur)
    {
        Gate::authorize('administration.gerer');

        $roles = Role::orderBy('libelle')->get();
        $departements = Departement::orderBy('nom')->get();

        return view('administration.utilisateurs.edit', compact('utilisateur', 'roles', 'departements'));
    }

    public function update(Request $request, User $utilisateur)
    {
        Gate::authorize('administration.gerer');

        $data = $this->valider($request, $utilisateur);

        if (! empty($data['password'])) {
            $data['password'] = Hash::make($data['password']);
        } else {
            unset($data['password']);
        }

        if (! empty($data['code_pin'])) {
            $data['code_pin'] = Hash::make($data['code_pin']);
        } else {
            unset($data['code_pin']);
        }

        $utilisateur->update($data);

        return redirect()->route('administration.utilisateurs.index')->with('success', 'Utilisateur mis à jour avec succès.');
    }

    public function toggleStatut(User $utilisateur)
    {
        Gate::authorize('administration.gerer');

        abort_if($utilisateur->id === auth()->id(), 403, 'Vous ne pouvez pas désactiver votre propre compte.');

        $utilisateur->update(['statut' => $utilisateur->statut === 'actif' ? 'inactif' : 'actif']);

        return back()->with('success', 'Statut du compte mis à jour.');
    }

    public function destroy(User $utilisateur)
    {
        Gate::authorize('administration.gerer');

        abort_if($utilisateur->id === auth()->id(), 403, 'Vous ne pouvez pas supprimer votre propre compte.');

        $utilisateur->delete();

        return redirect()->route('administration.utilisateurs.index')->with('success', 'Utilisateur supprimé avec succès.');
    }

    protected function valider(Request $request, ?User $utilisateur = null): array
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', Rule::unique('users', 'email')->ignore($utilisateur)],
            'identifiant' => ['nullable', 'string', 'max:255', 'alpha_dash', Rule::unique('users', 'identifiant')->ignore($utilisateur)],
            'password' => [$utilisateur ? 'nullable' : 'required', 'string', 'min:8'],
            'code_pin' => ['nullable', 'digits:4'],
            'role_id' => ['required', 'exists:roles,id'],
            'departement_id' => ['nullable', 'exists:departements,id'],
            'statut' => ['required', 'in:actif,inactif'],
        ]);

        if (empty($data['identifiant'])) {
            $data['identifiant'] = null;
        }

        return $data;
    }
}
