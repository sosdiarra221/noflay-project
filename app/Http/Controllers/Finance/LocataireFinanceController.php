<?php

namespace App\Http\Controllers\Finance;

use App\Http\Controllers\Controller;
use App\Models\EcheanceLoyer;
use App\Models\Locataire;
use App\Models\Paiement;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class LocataireFinanceController extends Controller
{
    public function index(Request $request)
    {
        Gate::authorize('finance.consulter');

        $locataires = Locataire::with('locations.contrats.echeances')
            ->when($request->filled('q'), function ($q) use ($request) {
                $terme = $request->q;
                $q->where(fn ($q2) => $q2->where('nom', 'like', "%{$terme}%")->orWhere('prenom', 'like', "%{$terme}%")->orWhere('numero', 'like', "%{$terme}%"));
            })
            ->orderBy('nom')
            ->get()
            ->map(function (Locataire $locataire) {
                $echeances = $locataire->locations->flatMap->contrats->flatMap->echeances;

                return [
                    'locataire' => $locataire,
                    'du' => (float) $echeances->where('statut', '!=', 'annule')->sum('montant_attendu'),
                    'paye' => (float) $echeances->sum('montant_paye'),
                ];
            });

        return view('finance.locataires.index', compact('locataires'));
    }

    /**
     * Fiche locataire — module financier : reprend les rubriques LOCATAIRE (loyers dus /
     * paiements / paiements partiels / arriérés / solde locatif) et GARANTIE-CAUTION (montant
     * reçu / retenues / motif / justificatifs / montant à restituer) de l'architecture cible.
     */
    public function show(Locataire $locataire)
    {
        Gate::authorize('finance.consulter');

        $locataire->load(['locations.contrats.bien', 'locations.contrats.bailleur', 'locations.contrats.caution']);

        $contrats = $locataire->locations->flatMap->contrats;
        $contratIds = $contrats->pluck('id');

        $echeances = EcheanceLoyer::whereIn('contrat_location_id', $contratIds)
            ->with('contratLocation.bien')
            ->orderByDesc('date_echeance')
            ->get()
            ->map(fn (EcheanceLoyer $e) => [
                'echeance' => $e,
                'du' => (float) $e->montant_attendu,
                'paye' => (float) $e->montant_paye,
                'arriere' => max((float) $e->montant_attendu - (float) $e->montant_paye, 0),
                'partiel' => $e->montant_paye > 0 && $e->montant_paye < $e->montant_attendu,
            ]);

        $compteLocataire = [
            'loyers_dus' => (float) $echeances->where('echeance.statut', '!=', 'annule')->sum('du'),
            'loyers_payes' => (float) $echeances->sum('paye'),
            'arrieres' => (float) $echeances->sum('arriere'),
            'nb_partiels' => $echeances->where('partiel', true)->count(),
        ];
        $compteLocataire['solde'] = round($compteLocataire['loyers_dus'] - $compteLocataire['loyers_payes'], 2);

        $paiements = Paiement::whereIn('contrat_location_id', $contratIds)
            ->orWhereIn('echeance_loyer_id', EcheanceLoyer::whereIn('contrat_location_id', $contratIds)->pluck('id'))
            ->with('contratLocation.bien', 'echeance.contratLocation.bien', 'modePaiement')
            ->orderByDesc('date_paiement')
            ->get();

        $cautions = $contrats->filter(fn ($c) => $c->caution)->map(fn ($c) => $c->caution);

        return view('finance.locataires.show', compact('locataire', 'echeances', 'compteLocataire', 'paiements', 'cautions'));
    }
}
