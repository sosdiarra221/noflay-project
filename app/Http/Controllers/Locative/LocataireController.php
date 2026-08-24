<?php

namespace App\Http\Controllers\Locative;

use App\Http\Controllers\Controller;
use App\Models\ContratLocation;
use App\Models\Locataire;
use App\Models\Reglage;
use App\Services\Locative\NumeroService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class LocataireController extends Controller
{
    public function index(Request $request)
    {
        $locataires = Locataire::withCount('locations')
            ->when($request->filled('recherche'), function ($q) use ($request) {
                $terme = $request->recherche;
                $q->where(fn ($q2) => $q2->where('nom', 'like', "%{$terme}%")
                    ->orWhere('prenom', 'like', "%{$terme}%")
                    ->orWhere('telephone', 'like', "%{$terme}%")
                    ->orWhere('email', 'like', "%{$terme}%"));
            })
            ->when($request->filled('type_locataire'), fn ($q) => $q->where('type_locataire', $request->type_locataire))
            ->when($request->filled('statut'), fn ($q) => $q->where('statut', $request->statut))
            ->orderBy('nom')
            ->get();

        return view('locative.locataires.index', compact('locataires'));
    }

    public function show(Locataire $locataire)
    {
        $locataire->load([
            'locations.contrats.bien',
            'locations.contrats.echeances.paiements',
            'locations.contrats.fichesLocatives',
        ]);

        $contrats = $locataire->locations->flatMap->contrats;
        $echeances = $contrats->flatMap->echeances->sortByDesc('date_echeance')->values();
        $paiements = $echeances->flatMap->paiements->sortByDesc('date_paiement')->values();
        $fiches = $contrats->flatMap->fichesLocatives->sortByDesc(fn ($f) => sprintf('%04d%02d', $f->annee, $f->mois))->values();

        $maintenant = now();
        $arrieres = $echeances
            ->filter(fn ($e) => $e->date_echeance->lt($maintenant) && ! in_array($e->statut, ['paye', 'annule']))
            ->sum(fn ($e) => max($e->montant_attendu - $e->montant_paye, 0));

        $echeancesEchues = $echeances->filter(fn ($e) => $e->date_echeance->lt($maintenant) && $e->statut !== 'annule');
        $echeancesEchuesPayees = $echeancesEchues->where('statut', 'paye')->count();
        $tauxPaiement = $echeancesEchues->count() > 0 ? round($echeancesEchuesPayees / $echeancesEchues->count() * 100, 1) : 100;

        $stats = [
            'total_du' => $echeances->where('statut', '!=', 'annule')->sum('montant_attendu'),
            'total_paye' => $echeances->sum('montant_paye'),
            'arrieres' => $arrieres,
            'taux_paiement' => $tauxPaiement,
            'profil' => $arrieres <= 0 ? 'bon' : ($arrieres <= ($contrats->max('loyer_mensuel') ?? 0) ? 'a_surveiller' : 'mauvais'),
        ];

        $tendance = collect(range(11, 0))->map(function ($moisAvant) use ($echeances) {
            $date = now()->subMonths($moisAvant);
            $duMois = $echeances->filter(fn ($e) => $e->annee == $date->year && $e->mois == $date->month);

            return [
                'libelle' => ucfirst($date->translatedFormat('M Y')),
                'attendu' => $duMois->sum('montant_attendu'),
                'paye' => $duMois->sum('montant_paye'),
            ];
        });

        $contratsActifs = ContratLocation::whereIn('id', $contrats->pluck('id'))->where('statut', 'actif')->get();

        $reglage = Reglage::courant();

        return view('locative.locataires.show', compact('locataire', 'contrats', 'echeances', 'paiements', 'fiches', 'stats', 'tendance', 'contratsActifs', 'reglage'));
    }

    public function store(Request $request)
    {
        $data = $this->valider($request);

        $data['numero'] = NumeroService::genererNumero(Locataire::class, 'LOCT');

        $locataire = Locataire::create($data);

        return back()->with('success', 'Locataire créé avec succès.')->with('locataire_cree_id', $locataire->id);
    }

    public function update(Request $request, Locataire $locataire)
    {
        $data = $this->valider($request);

        $locataire->update($data);

        return back()->with('success', 'Locataire mis à jour avec succès.');
    }

    public function destroy(Request $request, Locataire $locataire)
    {
        Gate::authorize('locative.operations-sensibles');

        $request->validate([
            'motif_suppression' => ['required', 'string', 'max:255'],
        ]);

        $locataire->motif_suppression = $request->motif_suppression;
        $locataire->supprime_par_id = auth()->id();
        $locataire->save();
        $locataire->delete();

        return redirect()->route('locative.locataires.index')->with('success', 'Locataire supprimé avec succès.');
    }

    protected function valider(Request $request): array
    {
        return $request->validate([
            'nom' => ['required', 'string', 'max:255'],
            'prenom' => ['nullable', 'string', 'max:255'],
            'telephone' => ['nullable', 'string', 'max:50'],
            'whatsapp' => ['nullable', 'string', 'max:50'],
            'email' => ['nullable', 'email', 'max:255'],
            'adresse' => ['nullable', 'string', 'max:255'],
            'type_locataire' => ['nullable', 'string', 'in:particulier,entreprise'],
            'ninea' => ['nullable', 'string', 'max:100'],
            'rc' => ['nullable', 'string', 'max:100'],
            'piece_identite_type' => ['nullable', 'string', 'max:100'],
            'piece_identite_numero' => ['nullable', 'string', 'max:100'],
            'notes' => ['nullable', 'string'],
            'statut' => ['required', 'string', 'in:actif,inactif'],
        ]);
    }
}
