<?php

namespace App\Http\Controllers\Rh;

use App\Http\Controllers\Controller;
use App\Models\Facturation\Client;
use App\Models\Rh\Employe;
use App\Models\Rh\EmployeAffectation;
use App\Models\Rh\Site;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class AffectationController extends Controller
{
    public function index()
    {
        Gate::authorize('rh.gerer');

        $employes = Employe::actifs()->with('departement', 'poste', 'sites')->orderBy('nom')->get();
        $sites = Site::with('client')->where('actif', true)->orderBy('nom')->get();
        $clients = Client::orderBy('nom_complet')->get();
        $historique = EmployeAffectation::with('employe')->latest('date_affectation')->take(30)->get();

        return view('rh.affectations.index', compact('employes', 'sites', 'clients', 'historique'));
    }

    public function store(Request $request, Employe $employe)
    {
        Gate::authorize('rh.gerer');

        $data = $request->validate([
            'sites' => ['nullable', 'array'],
            'sites.*' => ['exists:rh_sites,id'],
            'motif' => ['nullable', 'string', 'max:255'],
        ]);

        $anciensSitesLibelles = $employe->sites->pluck('nom')->implode(', ');
        $sitesChanges = $employe->sites()->sync($data['sites'] ?? []);
        $nouveauxSitesLibelles = Site::whereIn('id', $data['sites'] ?? [])->pluck('nom')->implode(', ');

        if (! empty($sitesChanges['attached']) || ! empty($sitesChanges['detached'])) {
            EmployeAffectation::create([
                'employe_id' => $employe->id,
                'ancien_departement_id' => $employe->departement_id,
                'nouveau_departement_id' => $employe->departement_id,
                'anciens_sites' => $anciensSitesLibelles ?: null,
                'nouveaux_sites' => $nouveauxSitesLibelles ?: null,
                'date_affectation' => now(),
                'motif' => $data['motif'] ?? null,
                'effectue_par_id' => auth()->id(),
            ]);
        }

        return back()->with('success', 'Affectation mise à jour pour '.$employe->nom_complet.'.');
    }
}
