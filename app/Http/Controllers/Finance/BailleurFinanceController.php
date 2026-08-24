<?php

namespace App\Http\Controllers\Finance;

use App\Http\Controllers\Controller;
use App\Models\Bailleur;
use App\Models\ContratLocation;
use App\Models\DepenseLocation;
use App\Models\EcheanceLoyer;
use App\Models\Paiement;
use App\Models\ReversementBailleur;
use App\Services\Locative\ReversementService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class BailleurFinanceController extends Controller
{
    public function index(Request $request)
    {
        Gate::authorize('finance.consulter');

        $bailleurs = Bailleur::when($request->filled('q'), function ($q) use ($request) {
            $terme = $request->q;
            $q->where(fn ($q2) => $q2->where('nom', 'like', "%{$terme}%")->orWhere('numero', 'like', "%{$terme}%"));
        })->orderBy('nom')->get();

        $service = app(ReversementService::class);
        $moisCourant = collect($service->calculerPourPeriode(now()))->keyBy(fn ($ligne) => $ligne['bailleur']->id);

        $lignes = $bailleurs->map(fn (Bailleur $bailleur) => [
            'bailleur' => $bailleur,
            'net_a_reverser' => $moisCourant->get($bailleur->id)['net_a_reverser'] ?? 0,
        ]);

        return view('finance.bailleurs.index', compact('lignes'));
    }

    /**
     * Fiche bailleur — module financier : reprend la rubrique BAILLEUR (loyers encaissés /
     * commission agence / travaux / autres dépenses / reversements) de l'architecture cible,
     * avec un aperçu contextuel des frais d'agence générés via ce bailleur (rubrique AGENCE).
     */
    public function show(Bailleur $bailleur)
    {
        Gate::authorize('finance.consulter');

        $contratIds = ContratLocation::where('bailleur_id', $bailleur->id)->pluck('id');
        $echeanceIds = EcheanceLoyer::whereIn('contrat_location_id', $contratIds)->pluck('id');

        $paiementsLoyer = Paiement::where('statut', 'valide')
            ->where(fn ($q) => $q->whereIn('contrat_location_id', $contratIds)->orWhereIn('echeance_loyer_id', $echeanceIds))
            ->where('type', 'loyer')
            ->with('echeance.contratLocation.bien', 'contratLocation.bien')
            ->orderByDesc('date_paiement')
            ->get();

        $paiementsEntree = Paiement::where('statut', 'valide')
            ->whereIn('contrat_location_id', $contratIds)
            ->where('type', 'entree')
            ->get();

        $depenses = DepenseLocation::where('bailleur_id', $bailleur->id)
            ->with('bien', 'categorie')
            ->latest()
            ->get();

        $reversements = ReversementBailleur::where('bailleur_id', $bailleur->id)
            ->with('modePaiement')
            ->orderByDesc('periode_annee')
            ->orderByDesc('periode_mois')
            ->get();

        $compteBailleur = [
            'loyers_encaisses' => (float) $paiementsLoyer->sum('montant'),
            'commission_agence' => (float) $paiementsLoyer->sum('part_commission_agence'),
            'travaux_depenses' => (float) $depenses->whereIn('statut', DepenseLocation::STATUTS_PAYEES)->sum(fn ($d) => $d->montantImpute()),
            'depenses_en_attente' => (float) $depenses->whereNotIn('statut', array_merge(DepenseLocation::STATUTS_PAYEES, [DepenseLocation::STATUT_REFUSEE]))->sum(fn ($d) => $d->montantImpute()),
            'deja_reverse' => (float) $reversements->where('statut', 'verse')->sum('montant_net'),
        ];
        $compteBailleur['a_reverser'] = round($compteBailleur['loyers_encaisses'] - $compteBailleur['commission_agence'] - $compteBailleur['travaux_depenses'] - $compteBailleur['deja_reverse'], 2);

        $compteAgence = [
            'frais_entree' => (float) $paiementsEntree->sum('part_frais_agence'),
            'commissions' => $compteBailleur['commission_agence'],
        ];

        $service = app(ReversementService::class);
        $calculMoisCourant = collect($service->calculerPourPeriode(now()))->firstWhere(fn ($ligne) => $ligne['bailleur']->id === $bailleur->id);

        return view('finance.bailleurs.show', compact('bailleur', 'paiementsLoyer', 'depenses', 'reversements', 'compteBailleur', 'compteAgence', 'calculMoisCourant'));
    }
}
