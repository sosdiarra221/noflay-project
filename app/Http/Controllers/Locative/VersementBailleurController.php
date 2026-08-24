<?php

namespace App\Http\Controllers\Locative;

use App\Http\Controllers\Controller;
use App\Models\Bailleur;
use App\Models\ChargeLocative;
use App\Models\ContratLocation;
use App\Models\ModePaiement;
use App\Models\Reglage;
use App\Models\VersementBailleur;
use App\Services\Finance\CompteBailleurService;
use App\Services\Locative\NumeroService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class VersementBailleurController extends Controller
{
    /**
     * Point d'entrée « bailleur d'abord » : on sélectionne un bailleur (recherche), et le
     * système affiche automatiquement sa fiche financière (loyers, dépenses, TVA/TOM
     * appliquées ou non, net à verser, arriéré éventuel) pour y enregistrer un versement.
     */
    public function index(Request $request, CompteBailleurService $compteBailleurService)
    {
        Gate::authorize('locative.finances');

        $bailleurs = Bailleur::where('statut', 'actif')->orderBy('nom')->get();
        $modesPaiement = ModePaiement::where('actif', true)->orderBy('nom')->get();

        $bailleur = null;
        $compte = null;
        $paiementsLoyer = collect();
        $depenses = collect();
        $versements = collect();
        $contratsAvecTaxes = collect();
        $suiviLoyers = collect();

        if ($request->filled('bailleur_id')) {
            $bailleur = Bailleur::findOrFail($request->bailleur_id);
            ['resume' => $compte, 'paiementsLoyer' => $paiementsLoyer, 'depenses' => $depenses, 'versements' => $versements] = $compteBailleurService->calculer($bailleur);

            $contratsAvecTaxes = ContratLocation::where('bailleur_id', $bailleur->id)
                ->with(['bien', 'caution', 'echeances' => fn ($q) => $q->orderBy('annee')->orderBy('mois')])
                ->get();

            $chargesParContrat = ChargeLocative::whereIn('contrat_location_id', $contratsAvecTaxes->pluck('id'))
                ->get()
                ->groupBy('contrat_location_id');

            // Regroupe, pour chaque contrat du bailleur, le statut mois par mois (payé / partiel /
            // en retard / à venir) — permet d'afficher en un coup d'œil quels loyers sont encaissés
            // et lesquels sont en arriéré, plutôt qu'un seul chiffre agrégé.
            $suiviLoyers = $contratsAvecTaxes->map(function (ContratLocation $contrat) use ($chargesParContrat) {
                $charges = $chargesParContrat->get($contrat->id, collect());

                return [
                    'contrat' => $contrat,
                    'echeances' => $contrat->echeances,
                    'nouvelle_location' => $contrat->date_debut && $contrat->date_debut->greaterThan(now()->subDays(45)),
                    'charges_total' => (float) $charges->sum('montant'),
                    'charges_a_payer' => $charges->where('statut', 'a_payer')->count(),
                ];
            });
        }

        $reglage = Reglage::courant();

        return view('locative.versements.index', compact('bailleurs', 'modesPaiement', 'bailleur', 'compte', 'paiementsLoyer', 'depenses', 'versements', 'contratsAvecTaxes', 'reglage', 'suiviLoyers'));
    }

    public function store(Request $request)
    {
        Gate::authorize('locative.finances');

        $data = $request->validate([
            'bailleur_id' => ['required', 'exists:bailleurs,id'],
            'montant' => ['required', 'numeric', 'min:0.01'],
            'type' => ['required', 'in:normal,avance'],
            'date_versement' => ['required', 'date'],
            'mode_paiement_id' => ['required', 'exists:modes_paiement,id'],
            'reference' => ['nullable', 'string', 'max:255'],
            'notes' => ['nullable', 'string'],
        ]);

        $data['numero'] = NumeroService::genererNumero(VersementBailleur::class, 'VER');
        $data['effectue_par_id'] = auth()->id();

        VersementBailleur::create($data);

        return redirect()->route('locative.versements.index', ['bailleur_id' => $data['bailleur_id']])
            ->with('success', 'Versement enregistré avec succès.');
    }

    public function destroy(Request $request, VersementBailleur $versement)
    {
        Gate::authorize('locative.operations-sensibles');

        $request->validate(['motif_suppression' => ['required', 'string', 'max:255']]);

        $versement->motif_suppression = $request->motif_suppression;
        $versement->supprime_par_id = auth()->id();
        $versement->save();
        $versement->delete();

        return redirect()->route('locative.versements.index', ['bailleur_id' => $versement->bailleur_id])
            ->with('success', 'Versement supprimé avec succès.');
    }
}
