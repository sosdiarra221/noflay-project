<?php

namespace App\Services\Finance;

use App\Models\ContratLocation;

/**
 * Ventile automatiquement chaque paiement encaissé, conformément à la règle fondamentale du
 * flux financier : un paiement de loyer ne doit jamais être enregistré comme un simple montant
 * en caisse, il doit toujours être décomposé en part bailleur / part commission agence.
 */
class VentilationService
{
    /**
     * @return array{part_bailleur: float, part_commission_agence: float}
     */
    public function ventilerLoyer(ContratLocation $contrat, float $montant): array
    {
        $bien = $contrat->bien;
        $mode = $bien?->frais_gestion_mode ?? $bien?->gerance?->frais_gestion_mode ?? 'pourcentage';
        $valeur = (float) ($bien?->frais_gestion_valeur ?? $bien?->gerance?->frais_gestion_valeur ?? 0);

        $commission = $mode === 'pourcentage'
            ? round($montant * $valeur / 100, 2)
            : min($valeur, $montant);

        return [
            'part_commission_agence' => $commission,
            'part_bailleur' => round($montant - $commission, 2),
        ];
    }
}
