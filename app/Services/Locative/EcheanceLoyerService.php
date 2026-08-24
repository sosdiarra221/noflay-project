<?php

namespace App\Services\Locative;

use App\Models\ContratLocation;
use App\Models\EcheanceLoyer;
use Carbon\Carbon;

class EcheanceLoyerService
{
    /**
     * Génère les échéances mensuelles d'un contrat, de sa date de début à sa date de fin.
     * Si le contrat n'a pas de date de fin, génère 12 mois par défaut.
     */
    public function genererPourContrat(ContratLocation $contrat): void
    {
        $debut = $contrat->date_debut->copy()->startOfMonth();
        $fin = $contrat->date_fin ? $contrat->date_fin->copy() : $debut->copy()->addMonths(11);

        $mois = $debut->copy();
        while ($mois->lessThanOrEqualTo($fin)) {
            $this->creerEcheanceSiAbsente($contrat, $mois->year, $mois->month);
            $mois->addMonth();
        }
    }

    /**
     * Génération manuelle ("Générer les loyers") pour une liste de mois d'une année donnée.
     * Ne modifie jamais une échéance déjà partiellement ou totalement payée.
     *
     * @return EcheanceLoyer[] échéances créées
     */
    public function genererLoyersManuel(ContratLocation $contrat, int $annee, array $mois): array
    {
        $creees = [];

        foreach ($mois as $m) {
            $echeance = $this->creerEcheanceSiAbsente($contrat, $annee, (int) $m);
            if ($echeance) {
                $creees[] = $echeance;
            }
        }

        return $creees;
    }

    protected function creerEcheanceSiAbsente(ContratLocation $contrat, int $annee, int $mois): ?EcheanceLoyer
    {
        $existe = EcheanceLoyer::where('contrat_location_id', $contrat->id)
            ->where('annee', $annee)
            ->where('mois', $mois)
            ->exists();

        if ($existe) {
            return null;
        }

        $jour = min($contrat->jour_echeance, Carbon::createFromDate($annee, $mois, 1)->daysInMonth);

        // Les charges (électricité, eau, wifi...) sont propres au locataire et ne font pas
        // partie du loyer dû au titre du contrat de location — elles sont suivies séparément
        // (cf. App\Models\ChargeLocative), jamais ajoutées au montant de l'échéance.
        return EcheanceLoyer::create([
            'contrat_location_id' => $contrat->id,
            'annee' => $annee,
            'mois' => $mois,
            'date_echeance' => Carbon::createFromDate($annee, $mois, $jour),
            'montant_attendu' => $contrat->loyer_mensuel,
            'montant_paye' => 0,
            'statut' => 'a_venir',
        ]);
    }
}
