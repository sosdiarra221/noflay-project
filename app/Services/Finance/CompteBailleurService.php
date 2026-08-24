<?php

namespace App\Services\Finance;

use App\Models\Bailleur;
use App\Models\ContratLocation;
use App\Models\DepenseLocation;
use App\Models\EcheanceLoyer;
use App\Models\Paiement;
use App\Models\ReversementBailleur;
use App\Models\VersementBailleur;
use App\Services\Locative\ReversementService;

/**
 * Calcule le compte d'un bailleur (loyers encaissés, commission agence, travaux/dépenses,
 * déjà versé, solde restant à verser) — utilisé à la fois par le module Finance (fiche
 * financière détaillée), la fiche bailleur du module Locative (aperçu synthétique + historique)
 * et l'écran Versements (module Locative), pour éviter de dupliquer ce calcul.
 *
 * Le solde est toujours calculé sur toute la durée de la relation (pas seulement le mois en
 * cours) : loyers encaissés - commission - dépenses - somme de tous les versements déjà
 * effectués. Un montant non versé un mois donné reste donc automatiquement dans le solde les
 * mois suivants, sans logique de report à coder séparément — c'est l'« arriéré ».
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

        // Deux mécanismes coexistent pour enregistrer un versement effectif à un bailleur :
        // l'ancien flux mensuel du module Finance (ReversementBailleur, statut "verse") et le
        // nouveau journal libre du module Locative (VersementBailleur, avances comprises). Les
        // deux alimentent le même solde pour rester cohérents partout où ce compte est affiché.
        $reversements = ReversementBailleur::where('bailleur_id', $bailleur->id)
            ->with('modePaiement')
            ->orderByDesc('periode_annee')
            ->orderByDesc('periode_mois')
            ->get();

        $versements = VersementBailleur::where('bailleur_id', $bailleur->id)
            ->with('modePaiement', 'effectuePar')
            ->orderByDesc('date_versement')
            ->get();

        $resume = [
            'loyers_encaisses' => (float) $paiementsLoyer->sum('montant'),
            'commission_agence' => (float) $paiementsLoyer->sum('part_commission_agence'),
            'travaux_depenses' => (float) $depenses->whereIn('statut', DepenseLocation::STATUTS_PAYEES)->sum(fn ($d) => $d->montantImpute()),
            'depenses_en_attente' => (float) $depenses->whereNotIn('statut', array_merge(DepenseLocation::STATUTS_PAYEES, [DepenseLocation::STATUT_REFUSEE]))->sum(fn ($d) => $d->montantImpute()),
            'deja_reverse' => (float) $reversements->where('statut', 'verse')->sum('montant_net') + (float) $versements->sum('montant'),
            'frais_entree' => (float) $paiementsEntree->sum('part_frais_agence'),
        ];
        $resume['a_reverser'] = round($resume['loyers_encaisses'] - $resume['commission_agence'] - $resume['travaux_depenses'] - $resume['deja_reverse'], 2);

        // Répartition indicative entre ce qui est dû au titre du mois en cours et ce qui provient
        // de mois antérieurs jamais soldés (arriéré), à titre d'information seulement — le solde
        // global ci-dessus reste la seule source de vérité pour le montant réellement dû.
        $duMoisCourant = collect(app(ReversementService::class)->calculerPourPeriode(now()))
            ->firstWhere(fn ($ligne) => $ligne['bailleur']->id === $bailleur->id)['net_a_reverser'] ?? 0.0;
        $resume['du_mois_courant'] = round(min((float) $duMoisCourant, $resume['a_reverser']), 2);
        $resume['arriere_anterieur'] = round(max($resume['a_reverser'] - $resume['du_mois_courant'], 0), 2);

        return compact('resume', 'paiementsLoyer', 'depenses', 'reversements', 'versements');
    }
}
