<?php

namespace App\Http\Controllers\Finance;

use App\Http\Controllers\Controller;
use App\Models\Bailleur;
use App\Services\Finance\CompteBailleurService;
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
    public function show(Bailleur $bailleur, CompteBailleurService $compteBailleurService)
    {
        Gate::authorize('finance.consulter');

        ['resume' => $compteBailleur, 'paiementsLoyer' => $paiementsLoyer, 'depenses' => $depenses, 'reversements' => $reversements] = $compteBailleurService->calculer($bailleur);

        $compteAgence = [
            'frais_entree' => $compteBailleur['frais_entree'],
            'commissions' => $compteBailleur['commission_agence'],
        ];

        $service = app(ReversementService::class);
        $calculMoisCourant = collect($service->calculerPourPeriode(now()))->firstWhere(fn ($ligne) => $ligne['bailleur']->id === $bailleur->id);

        return view('finance.bailleurs.show', compact('bailleur', 'paiementsLoyer', 'depenses', 'reversements', 'compteBailleur', 'compteAgence', 'calculMoisCourant'));
    }
}
