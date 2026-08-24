<?php

namespace App\Http\Controllers\Administration;

use App\Http\Controllers\Controller;
use App\Models\Role;
use App\Models\User;
use Illuminate\Support\Facades\Gate;

class AdministrationDashboardController extends Controller
{
    public function index()
    {
        Gate::authorize('administration.gerer');

        $kpis = [
            'utilisateurs_actifs' => User::where('statut', 'actif')->count(),
            'utilisateurs_inactifs' => User::where('statut', 'inactif')->count(),
            'roles' => Role::count(),
        ];

        $derniersConnectes = User::whereNotNull('derniere_activite_at')
            ->with('role')
            ->orderByDesc('derniere_activite_at')
            ->take(8)
            ->get();

        $parRole = User::with('role')->get()->groupBy(fn (User $u) => $u->role->libelle ?? '—')->map->count();

        return view('administration.dashboard', compact('kpis', 'derniersConnectes', 'parRole'));
    }
}
