<?php

namespace App\Services\Locative;

use App\Models\Bailleur;
use App\Models\Paiement;
use Carbon\Carbon;

class ReversementService
{
    /**
     * Calcule, pour chaque bailleur actif, le montant net à reverser sur une période donnée.
     * net_a_reverser = loyers encaissés - frais de gestion de la gérance concernée.
     * TVA/Taxe/TOM sont affichés selon la répartition définie sur le contrat de gérance,
     * mais aucun taux n'est configuré dans cette phase : seuls les frais de gestion sont déduits.
     *
     * @return array<int, array{bailleur: Bailleur, encaisse: float, frais_gestion: float, net_a_reverser: float}>
     */
    public function calculerPourPeriode(Carbon $periode): array
    {
        $debut = $periode->copy()->startOfMonth();
        $fin = $periode->copy()->endOfMonth();

        $bailleurs = Bailleur::where('statut', 'actif')->orderBy('nom')->get();

        $resultats = [];

        foreach ($bailleurs as $bailleur) {
            $paiements = Paiement::whereHas('echeance.contratLocation', function ($q) use ($bailleur) {
                $q->where('bailleur_id', $bailleur->id);
            })
                ->whereBetween('date_paiement', [$debut, $fin])
                ->with('echeance.contratLocation.bien.gerance')
                ->get();

            if ($paiements->isEmpty()) {
                continue;
            }

            $encaisse = (float) $paiements->sum('montant');

            $fraisGestion = 0.0;
            foreach ($paiements as $paiement) {
                $bien = $paiement->echeance->contratLocation->bien;
                $mode = $bien->frais_gestion_mode ?? $bien->gerance?->frais_gestion_mode ?? 'pourcentage';
                $valeur = $bien->frais_gestion_valeur ?? $bien->gerance?->frais_gestion_valeur ?? 0;

                $fraisGestion += $mode === 'pourcentage'
                    ? round((float) $paiement->montant * (float) $valeur / 100, 2)
                    : (float) $valeur;
            }

            $resultats[] = [
                'bailleur' => $bailleur,
                'encaisse' => $encaisse,
                'frais_gestion' => $fraisGestion,
                'net_a_reverser' => round($encaisse - $fraisGestion, 2),
            ];
        }

        return $resultats;
    }
}
