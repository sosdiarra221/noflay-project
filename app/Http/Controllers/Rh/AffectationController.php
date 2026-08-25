<?php

namespace App\Http\Controllers\Rh;

use App\Http\Controllers\Controller;
use App\Models\Facturation\Client;
use App\Models\Rh\Employe;
use App\Models\Rh\EmployeAffectation;
use App\Models\Rh\Site;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;

class AffectationController extends Controller
{
    public function index()
    {
        Gate::authorize('rh.gerer');

        $sites = Site::with('client')
            ->withCount('employes')
            ->orderBy('nom')
            ->get()
            ->map(function (Site $site) {
                $site->derniere_affectation = DB::table('employe_site')->where('site_id', $site->id)->max('created_at');

                return $site;
            });

        $employes = Employe::actifs()->with('departement', 'poste')->orderBy('nom')->get();
        $clients = Client::orderBy('nom_complet')->get();
        $historique = EmployeAffectation::with('employe')->latest('date_affectation')->take(30)->get();

        return view('rh.affectations.index', compact('sites', 'employes', 'clients', 'historique'));
    }

    public function store(Request $request)
    {
        Gate::authorize('rh.gerer');

        $data = $request->validate([
            'site_id' => ['required', 'exists:rh_sites,id'],
            'employes' => ['required', 'array', 'min:1'],
            'employes.*' => ['exists:employes,id'],
            'motif' => ['nullable', 'string', 'max:255'],
        ]);

        $site = Site::findOrFail($data['site_id']);
        $employes = Employe::whereIn('id', $data['employes'])->get();
        $nombreAffectes = 0;

        foreach ($employes as $employe) {
            if ($employe->sites->contains($site->id)) {
                continue;
            }

            $anciensSitesLibelles = $employe->sites->pluck('nom')->implode(', ');
            $employe->sites()->attach($site->id);
            $nombreAffectes++;

            EmployeAffectation::create([
                'employe_id' => $employe->id,
                'ancien_departement_id' => $employe->departement_id,
                'nouveau_departement_id' => $employe->departement_id,
                'anciens_sites' => $anciensSitesLibelles ?: null,
                'nouveaux_sites' => $employe->sites()->pluck('nom')->implode(', '),
                'date_affectation' => now(),
                'motif' => $data['motif'] ?? null,
                'effectue_par_id' => auth()->id(),
            ]);
        }

        $message = $nombreAffectes > 0
            ? $nombreAffectes.' employé(s) affecté(s) au site '.$site->nom.'.'
            : 'Les employés sélectionnés étaient déjà affectés à ce site.';

        return back()->with('success', $message);
    }
}
