<?php

namespace App\Http\Controllers\Finance;

use App\Http\Controllers\Controller;
use App\Models\Bailleur;
use App\Models\DepenseLocation;
use App\Models\FicheLocative;
use App\Models\Paiement;
use App\Models\ReversementBailleur;
use Carbon\Carbon;
use Illuminate\Support\Facades\Gate;

class FinanceDashboardController extends Controller
{
    public function index()
    {
        Gate::authorize('finance.consulter');

        $debutMois = now()->startOfMonth();
        $finMois = now()->endOfMonth();

        $paiementsMois = Paiement::where('statut', 'valide')
            ->whereBetween('date_paiement', [$debutMois, $finMois])
            ->with('echeance.contratLocation.bien.gerance')
            ->get();

        $totalEncaisse = (float) $paiementsMois->sum('montant');
        $totalCommissions = $this->calculerCommissions($paiementsMois);
        $netAReverser = round($totalEncaisse - $totalCommissions, 2);

        $totalVerse = (float) ReversementBailleur::where('periode_annee', now()->year)
            ->where('periode_mois', now()->month)
            ->where('statut', 'verse')
            ->sum('montant_net');

        $fichesMois = FicheLocative::where('annee', now()->year)->where('mois', now()->month)->get();
        $tvaCollectee = (float) $fichesMois->sum('montant_tva');
        $tomCollectee = (float) $fichesMois->sum('montant_tom');

        $depensesMois = DepenseLocation::whereIn('statut', DepenseLocation::STATUTS_PAYEES)
            ->whereMonth('date_paiement', now()->month)
            ->whereYear('date_paiement', now()->year)
            ->get();
        $depensesAgence = (float) $depensesMois->where('qui_supporte', 'agence')->sum(fn ($d) => $d->montantImpute());
        $depensesEnAttente = (float) DepenseLocation::where('statut', DepenseLocation::STATUT_A_PAYER)->get()->sum(fn ($d) => $d->montantImpute());

        $kpis = [
            'encaisse' => $totalEncaisse,
            'commissions' => $totalCommissions,
            'net_a_reverser' => $netAReverser,
            'deja_verse' => $totalVerse,
            'tva_collectee' => $tvaCollectee,
            'tom_collectee' => $tomCollectee,
            'depenses_agence' => $depensesAgence,
            'depenses_en_attente' => $depensesEnAttente,
        ];

        // Tendance des 12 derniers mois : encaissements bruts.
        $tendance = collect(range(11, 0))->map(function ($moisAvant) {
            $date = now()->subMonths($moisAvant);
            $total = Paiement::where('statut', 'valide')
                ->whereYear('date_paiement', $date->year)
                ->whereMonth('date_paiement', $date->month)
                ->sum('montant');

            return [
                'libelle' => ucfirst($date->translatedFormat('M Y')),
                'total' => (float) $total,
            ];
        });

        // Répartition des commissions par bailleur sur le mois courant.
        $parBailleur = [];
        foreach ($paiementsMois as $paiement) {
            $bien = $paiement->echeance->contratLocation->bien ?? null;
            $bailleurId = $bien->bailleur_id ?? null;
            if (! $bailleurId) {
                continue;
            }
            $parBailleur[$bailleurId] = ($parBailleur[$bailleurId] ?? 0) + (float) $paiement->montant;
        }
        arsort($parBailleur);
        $topBailleurs = collect($parBailleur)->take(6)->map(function ($montant, $bailleurId) {
            return ['nom' => Bailleur::find($bailleurId)?->nom_complet ?? '—', 'montant' => $montant];
        })->values();

        return view('finance.dashboard', compact('kpis', 'tendance', 'topBailleurs'));
    }

    protected function calculerCommissions($paiements): float
    {
        $total = 0.0;

        foreach ($paiements as $paiement) {
            $bien = $paiement->echeance->contratLocation->bien ?? null;
            if (! $bien) {
                continue;
            }

            $mode = $bien->frais_gestion_mode ?? $bien->gerance?->frais_gestion_mode ?? 'pourcentage';
            $valeur = $bien->frais_gestion_valeur ?? $bien->gerance?->frais_gestion_valeur ?? 0;

            $total += $mode === 'pourcentage'
                ? round((float) $paiement->montant * (float) $valeur / 100, 2)
                : (float) $valeur;
        }

        return round($total, 2);
    }
}
