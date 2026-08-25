<?php

namespace App\Http\Controllers\Rh;

use App\Http\Controllers\Controller;
use App\Models\Departement;
use App\Models\Rh\Employe;
use App\Models\Rh\EmployeAffectation;
use App\Models\Rh\Poste;
use App\Models\Rh\Site;
use App\Services\Locative\NumeroService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;

class EmployeController extends Controller
{
    public function index(Request $request)
    {
        Gate::authorize('rh.consulter');

        $employes = Employe::with('departement', 'poste', 'sites', 'contratActif')
            ->when($request->filled('recherche'), function ($q) use ($request) {
                $terme = $request->recherche;
                $q->where(fn ($q2) => $q2->where('nom', 'like', "%{$terme}%")
                    ->orWhere('prenom', 'like', "%{$terme}%")
                    ->orWhere('matricule', 'like', "%{$terme}%")
                    ->orWhere('telephone', 'like', "%{$terme}%"));
            })
            ->when($request->filled('departement_id'), fn ($q) => $q->where('departement_id', $request->departement_id))
            ->when($request->filled('categorie_fonction'), fn ($q) => $q->where('categorie_fonction', $request->categorie_fonction))
            ->when($request->filled('statut'), fn ($q) => $q->where('statut', $request->statut), fn ($q) => $q->where('statut', 'actif'))
            ->orderBy('nom')
            ->get();

        $departements = Departement::orderBy('nom')->get();

        return view('rh.employes.index', compact('employes', 'departements'));
    }

    public function create()
    {
        Gate::authorize('rh.gerer');

        $departements = Departement::orderBy('nom')->get();
        $sites = Site::where('actif', true)->orderBy('nom')->get();
        $postes = Poste::where('actif', true)->orderBy('nom')->get();

        return view('rh.employes.create', compact('departements', 'sites', 'postes'));
    }

    public function show(Employe $employe)
    {
        Gate::authorize('rh.consulter');

        $employe->load('departement', 'poste', 'sites', 'epouses', 'enfants', 'documents.ajoutePar', 'contrats', 'affectations.ancienDepartement', 'affectations.nouveauDepartement');

        return view('rh.employes.show', compact('employe'));
    }

    public function edit(Employe $employe)
    {
        Gate::authorize('rh.gerer');

        $employe->load('epouses', 'enfants', 'sites');
        $departements = Departement::orderBy('nom')->get();
        $sites = Site::where('actif', true)->orderBy('nom')->get();
        $postes = Poste::where('actif', true)->orderBy('nom')->get();

        return view('rh.employes.edit', compact('employe', 'departements', 'sites', 'postes'));
    }

    public function store(Request $request)
    {
        Gate::authorize('rh.gerer');

        $data = $this->valider($request);

        $employe = DB::transaction(function () use ($data, $request) {
            $data['matricule'] = NumeroService::genererNumeroCourt(Employe::class, 'EMP', 'matricule');
            $data['solde_conges'] = 0;
            $data['statut'] = 'actif';
            $data['cree_par_id'] = auth()->id();

            if ($request->hasFile('photo')) {
                $data['photo'] = $request->file('photo')->store('employes', 'public');
            }

            $epouses = $data['epouses'] ?? [];
            $enfants = $data['enfants'] ?? [];
            $sites = $data['sites'] ?? [];
            unset($data['epouses'], $data['enfants'], $data['sites']);

            $employe = Employe::create($data);

            $employe->epouses()->createMany($epouses);
            $employe->enfants()->createMany($enfants);
            $employe->sites()->sync($sites);

            return $employe;
        });

        return redirect()->route('rh.employes.show', $employe)->with('success', 'Employé créé avec succès — matricule '.$employe->matricule.'.');
    }

    public function update(Request $request, Employe $employe)
    {
        Gate::authorize('rh.gerer');

        $data = $this->valider($request, $employe);

        DB::transaction(function () use ($data, $request, $employe) {
            if ($request->hasFile('photo')) {
                if ($employe->photo) {
                    Storage::disk('public')->delete($employe->photo);
                }
                $data['photo'] = $request->file('photo')->store('employes', 'public');
            }

            $epouses = $data['epouses'] ?? [];
            $enfants = $data['enfants'] ?? [];
            $sitesDemandes = $data['sites'] ?? [];
            unset($data['epouses'], $data['enfants'], $data['sites']);

            // Transfert de département/site : on trace le changement avant de l'appliquer, pour
            // garder un historique des affectations utile en cas de litige.
            $ancienDepartementId = $employe->departement_id;
            $anciensSitesLibelles = $employe->sites->pluck('nom')->implode(', ');

            $employe->update($data);

            $sitesChanges = $employe->sites()->sync($sitesDemandes);
            $nouveauxSitesLibelles = Site::whereIn('id', $sitesDemandes)->pluck('nom')->implode(', ');

            $departementChange = (int) $ancienDepartementId !== (int) $data['departement_id'];
            $sitesOntChange = ! empty($sitesChanges['attached']) || ! empty($sitesChanges['detached']);

            if ($departementChange || $sitesOntChange) {
                EmployeAffectation::create([
                    'employe_id' => $employe->id,
                    'ancien_departement_id' => $ancienDepartementId,
                    'nouveau_departement_id' => $data['departement_id'],
                    'anciens_sites' => $anciensSitesLibelles ?: null,
                    'nouveaux_sites' => $nouveauxSitesLibelles ?: null,
                    'date_affectation' => now(),
                    'motif' => $request->input('motif_affectation'),
                    'effectue_par_id' => auth()->id(),
                ]);
            }

            $employe->epouses()->delete();
            $employe->enfants()->delete();
            $employe->epouses()->createMany($epouses);
            $employe->enfants()->createMany($enfants);
        });

        return redirect()->route('rh.employes.show', $employe)->with('success', 'Employé mis à jour avec succès.');
    }

    public function archiver(Request $request, Employe $employe)
    {
        Gate::authorize('rh.gerer');

        $data = $request->validate([
            'date_sortie' => ['required', 'date'],
            'motif_sortie' => ['required', 'string', 'max:255'],
        ]);

        $employe->update([
            'statut' => 'sortie',
            'date_sortie' => $data['date_sortie'],
            'motif_sortie' => $data['motif_sortie'],
        ]);

        return redirect()->route('rh.employes.show', $employe)->with('success', 'Employé archivé (statut « sortie ») avec succès.');
    }

    public function reactiver(Employe $employe)
    {
        Gate::authorize('rh.gerer');

        $employe->update(['statut' => 'actif', 'date_sortie' => null, 'motif_sortie' => null]);

        return back()->with('success', 'Employé réactivé avec succès.');
    }

    protected function valider(Request $request, ?Employe $employe = null): array
    {
        return $request->validate([
            'photo' => ['nullable', 'image', 'max:2048'],
            'nom' => ['required', 'string', 'max:255'],
            'prenom' => ['required', 'string', 'max:255'],
            'sexe' => ['nullable', 'string', 'in:homme,femme'],
            'date_naissance' => ['nullable', 'date'],
            'lieu_naissance' => ['nullable', 'string', 'max:255'],
            'situation_matrimoniale' => ['nullable', 'string', 'in:'.implode(',', array_keys(Employe::SITUATIONS_MATRIMONIALES))],
            'piece_identite_type' => ['nullable', 'string', 'max:100'],
            'piece_identite_numero' => ['nullable', 'string', 'max:100'],
            'telephone' => ['nullable', 'string', 'max:50'],
            'whatsapp' => ['nullable', 'string', 'max:50'],
            'email' => ['nullable', 'email', 'max:255'],
            'adresse' => ['nullable', 'string', 'max:255'],
            'poste_id' => ['required', 'exists:postes,id'],
            'categorie_fonction' => ['required', 'string', 'in:'.implode(',', array_keys(Employe::CATEGORIES_FONCTION))],
            'departement_id' => ['required', 'exists:departements,id'],
            'sites' => ['nullable', 'array'],
            'sites.*' => ['exists:rh_sites,id'],
            'niveau_etude' => ['nullable', 'string', 'max:255'],
            'intitule_diplome' => ['nullable', 'string', 'max:255'],
            'langues_parlees' => ['nullable', 'string', 'max:255'],
            'langues_lues' => ['nullable', 'string', 'max:255'],
            'banque' => ['nullable', 'string', 'max:255'],
            'compte_bancaire' => ['nullable', 'string', 'max:255'],
            'personne_urgence_nom' => ['nullable', 'string', 'max:255'],
            'personne_urgence_telephone' => ['nullable', 'string', 'max:50'],
            'personne_urgence_lien' => ['nullable', 'string', 'max:100'],
            'date_embauche' => ['required', 'date'],
            'notes' => ['nullable', 'string'],
            'epouses' => ['nullable', 'array'],
            'epouses.*.nom_complet' => ['required', 'string', 'max:255'],
            'epouses.*.telephone' => ['nullable', 'string', 'max:50'],
            'enfants' => ['nullable', 'array'],
            'enfants.*.nom_complet' => ['required', 'string', 'max:255'],
            'enfants.*.date_naissance' => ['nullable', 'date'],
            'enfants.*.telephone' => ['nullable', 'string', 'max:50'],
        ]);
    }
}
