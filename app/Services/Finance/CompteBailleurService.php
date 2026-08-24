<?php

namespace App\Services\Finance;

use App\Models\Bailleur;
use App\Models\ContratLocation;
use App\Models\DepenseLocation;
use App\Models\EcheanceLoyer;
use App\Models\Paiement;
use App\Models\ReversementBailleur;

/**
 * Calcule le compte d'un bailleur (loyers encaissés, commission agence, travaux/dépenses,
 * déjà reversé, solde restant à reverser) — utilisé à la fois par le module Finance
 * (fiche financière détaillée) et par la fiche bailleur du module Locative (aperçu synthétique
 * + historique des reversements), pour éviter de dupliquer ce calcul.
 */
class CompteBailleurService
{
    public function calculer(Bailleur $bailleur): array
    {
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

        $resume = [
            'loyers_encaisses' => (float) $paiementsLoyer->sum('montant'),
            'commission_agence' => (float) $paiementsLoyer->sum('part_commission_agence'),
            'travaux_depenses' => (float) $depenses->whereIn('statut', DepenseLocation::STATUTS_PAYEES)->sum(fn ($d) => $d->montantImpute()),
            'depenses_en_attente' => (float) $depenses->whereNotIn('statut', array_merge(DepenseLocation::STATUTS_PAYEES, [DepenseLocation::STATUT_REFUSEE]))->sum(fn ($d) => $d->montantImpute()),
            'deja_reverse' => (float) $reversements->where('statut', 'verse')->sum('montant_net'),
            'frais_entree' => (float) $paiementsEntree->sum('part_frais_agence'),
        ];
        $resume['a_reverser'] = round($resume['loyers_encaisses'] - $resume['commission_agence'] - $resume['travaux_depenses'] - $resume['deja_reverse'], 2);

        return compact('resume', 'paiementsLoyer', 'depenses', 'reversements');
    }
}
