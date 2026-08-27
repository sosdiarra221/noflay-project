<?php

namespace App\Http\Controllers\Administration;

use App\Http\Controllers\Controller;
use App\Models\Licence;
use App\Models\Module;
use App\Models\Role;
use App\Models\User;
use App\Services\Finance\ComptabiliteService;
use Illuminate\Support\Facades\Gate;

class AdministrationDashboardController extends Controller
{
    public function index(ComptabiliteService $comptabiliteService)
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

        $licence = tenant()
            ? Licence::with('package')->where('tenant_id', tenant('id'))->orderByDesc('date_fin')->first()
            : null;

        $modulesCatalogue = collect(config('modules'))->keys()->reject(fn ($cle) => $cle === 'administration');
        $modulesActifsCount = $modulesCatalogue->filter(fn ($cle) => Module::estActif($cle))->count();

        $soldeTresorerie = Gate::allows('finance.comptabilite') ? $comptabiliteService->soldeGlobal() : null;

        return view('administration.dashboard', compact('kpis', 'derniersConnectes', 'parRole', 'licence', 'modulesCatalogue', 'modulesActifsCount', 'soldeTresorerie'));
    }
}
