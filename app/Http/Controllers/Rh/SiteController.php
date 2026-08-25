<?php

namespace App\Http\Controllers\Rh;

use App\Http\Controllers\Controller;
use App\Models\Facturation\Client;
use App\Models\Rh\EmployeAffectation;
use App\Models\Rh\Site;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;

class SiteController extends Controller
{
    public function index()
    {
        Gate::authorize('rh.gerer');

        $sites = Site::with('client')->withCount('employes')->orderBy('nom')->get();
        $clients = Client::orderBy('nom_complet')->get();

        return view('rh.sites.index', compact('sites', 'clients'));
    }

    public function show(Site $site)
    {
        Gate::authorize('rh.consulter');

        $site->load('client');

        $employesAffectes = $site->employes()->with('departement', 'poste', 'contratActif')->orderBy('nom')->get();

        $hommes = $employesAffectes->where('sexe', 'homme')->count();
        $femmes = $employesAffectes->where('sexe', 'femme')->count();
        $nonRenseigne = $employesAffectes->count() - $hommes - $femmes;

        $derniereLigne = DB::table('employe_site')->where('site_id', $site->id)->orderByDesc('created_at')->first();
        $dernierEmploye = $derniereLigne ? $employesAffectes->firstWhere('id', $derniereLigne->employe_id) : null;

        $historique = EmployeAffectation::with('employe')
            ->where(fn ($q) => $q->where('anciens_sites', 'like', "%{$site->nom}%")->orWhere('nouveaux_sites', 'like', "%{$site->nom}%"))
            ->latest('date_affectation')
            ->take(15)
            ->get();

        $rapport = $this->construireRapport($site, $employesAffectes, $hommes, $femmes, $derniereLigne, $dernierEmploye);

        return view('rh.sites.show', compact('site', 'employesAffectes', 'hommes', 'femmes', 'nonRenseigne', 'derniereLigne', 'dernierEmploye', 'historique', 'rapport'));
    }

    /**
     * Mini rapport narratif du site — synthétise l'effectif, la répartition H/F et la date du
     * dernier transfert en une phrase lisible, plutôt que de laisser l'utilisateur recouper
     * lui-même les chiffres des cartes.
     */
    protected function construireRapport(Site $site, $employesAffectes, int $hommes, int $femmes, $derniereLigne, $dernierEmploye): string
    {
        $total = $employesAffectes->count();

        if ($total === 0) {
            return "Le site {$site->nom} n'a actuellement aucun employé affecté.";
        }

        $texte = "Le site {$site->nom}".($site->client ? " (client : {$site->client->nom_complet})" : ' (site interne à l\'agence)').
            " compte actuellement {$total} agent".($total > 1 ? 's' : '')." affecté".($total > 1 ? 's' : '').
            ", dont {$hommes} homme".($hommes > 1 ? 's' : '')." et {$femmes} femme".($femmes > 1 ? 's' : '').".";

        if ($derniereLigne && $dernierEmploye) {
            $texte .= ' La dernière affectation sur ce site remonte au '.\Illuminate\Support\Carbon::parse($derniereLigne->created_at)->format('d/m/Y').
                " ({$dernierEmploye->nom_complet}).";
        }

        $sansContrat = $employesAffectes->filter(fn ($e) => ! $e->contratActif)->count();
        if ($sansContrat > 0) {
            $texte .= " Attention : {$sansContrat} agent".($sansContrat > 1 ? 's' : '')." affecté".($sansContrat > 1 ? 's' : '')." à ce site n'".($sansContrat > 1 ? 'ont' : 'a')." pas de contrat actif.";
        }

        return $texte;
    }

    public function store(Request $request)
    {
        Gate::authorize('rh.gerer');

        $data = $request->validate([
            'nom' => ['required', 'string', 'max:255'],
            'client_id' => ['nullable', 'exists:facturation_clients,id'],
            'adresse' => ['nullable', 'string', 'max:255'],
        ]);

        $data['actif'] = true;

        Site::create($data);

        return back()->with('success', 'Site créé avec succès.');
    }

    public function update(Request $request, Site $site)
    {
        Gate::authorize('rh.gerer');

        $data = $request->validate([
            'nom' => ['required', 'string', 'max:255'],
            'client_id' => ['nullable', 'exists:facturation_clients,id'],
            'adresse' => ['nullable', 'string', 'max:255'],
            'actif' => ['nullable'],
        ]);

        $data['actif'] = ! empty($data['actif']);

        $site->update($data);

        return back()->with('success', 'Site mis à jour avec succès.');
    }
}
