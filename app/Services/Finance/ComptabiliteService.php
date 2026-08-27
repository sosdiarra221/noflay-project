<?php

namespace App\Services\Finance;

use App\Models\Caution;
use App\Models\DepenseLocation;
use App\Models\Facturation\Facture;
use App\Models\Paiement;
use App\Models\ReversementBailleur;
use App\Models\VersementBailleur;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;

/**
 * Consolide en un seul flux normalisé tous les encaissements et décaissements de l'application,
 * quel que soit le module d'origine. Calcule tout à la volée à partir des tables existantes —
 * pas de table de mouvements séparée à synchroniser, le solde reste donc toujours exact.
 */
class ComptabiliteService
{
    /**
     * @return Collection<int, object{type: string, source: string, module: string, montant: float, date: Carbon, reference: string, lien: array}>
     */
    public function mouvements(?Carbon $debut = null, ?Carbon $fin = null): Collection
    {
        return $this->encaissements($debut, $fin)
            ->concat($this->decaissements($debut, $fin))
            ->sortByDesc('date')
            ->values();
    }

    public function encaissements(?Carbon $debut = null, ?Carbon $fin = null): Collection
    {
        $paiements = Paiement::with('contratLocation')
            ->where('statut', 'valide')
            ->when($debut, fn ($q) => $q->whereDate('date_paiement', '>=', $debut))
            ->when($fin, fn ($q) => $q->whereDate('date_paiement', '<=', $fin))
            ->get()
            ->map(fn (Paiement $p) => (object) [
                'type' => 'encaissement',
                'source' => $p->type === 'entree' ? 'Paiement d\'entrée' : 'Paiement de loyer',
                'module' => 'Locative',
                'montant' => (float) $p->montant,
                'date' => Carbon::parse($p->date_paiement),
                'reference' => $p->numero,
                'lien' => ['type' => 'paiement', 'id' => $p->id],
            ]);

        $factures = Facture::where('statut', 'payee')
            ->when($debut, fn ($q) => $q->whereDate('updated_at', '>=', $debut))
            ->when($fin, fn ($q) => $q->whereDate('updated_at', '<=', $fin))
            ->get()
            ->map(fn (Facture $f) => (object) [
                'type' => 'encaissement',
                'source' => 'Paiement de facture',
                'module' => 'Facturation',
                'montant' => (float) $f->total_ttc,
                'date' => Carbon::parse($f->updated_at),
                'reference' => $f->numero,
                'lien' => ['type' => 'facture', 'id' => $f->id],
            ]);

        return $paiements->concat($factures);
    }

    public function decaissements(?Carbon $debut = null, ?Carbon $fin = null): Collection
    {
        $depenses = DepenseLocation::whereIn('statut', DepenseLocation::STATUTS_PAYEES)
            ->when($debut, fn ($q) => $q->whereDate('date_paiement', '>=', $debut))
            ->when($fin, fn ($q) => $q->whereDate('date_paiement', '<=', $fin))
            ->get()
            ->map(fn (DepenseLocation $d) => (object) [
                'type' => 'decaissement',
                'source' => 'Dépense ('.$d->qui_supporte.')',
                'module' => 'Finance',
                'montant' => $d->montantImpute(),
                'date' => $d->date_paiement ? Carbon::parse($d->date_paiement) : Carbon::parse($d->updated_at),
                'reference' => $d->numero,
                'lien' => ['type' => 'depense', 'id' => $d->id],
            ]);

        $cautions = Caution::whereIn('statut', ['restituee', 'partiellement_restituee'])
            ->whereNotNull('date_restitution')
            ->when($debut, fn ($q) => $q->whereDate('date_restitution', '>=', $debut))
            ->when($fin, fn ($q) => $q->whereDate('date_restitution', '<=', $fin))
            ->get()
            ->map(fn (Caution $c) => (object) [
                'type' => 'decaissement',
                'source' => 'Restitution de caution',
                'module' => 'Locative',
                'montant' => (float) $c->montant_restitue,
                'date' => Carbon::parse($c->date_restitution),
                'reference' => 'CAUTION-'.$c->id,
                'lien' => ['type' => 'caution', 'id' => $c->id],
            ]);

        $versements = VersementBailleur::when($debut, fn ($q) => $q->whereDate('date_versement', '>=', $debut))
            ->when($fin, fn ($q) => $q->whereDate('date_versement', '<=', $fin))
            ->get()
            ->map(fn (VersementBailleur $v) => (object) [
                'type' => 'decaissement',
                'source' => 'Versement bailleur',
                'module' => 'Locative',
                'montant' => (float) $v->montant,
                'date' => Carbon::parse($v->date_versement),
                'reference' => $v->numero,
                'lien' => ['type' => 'versement_bailleur', 'id' => $v->id],
            ]);

        $reversements = ReversementBailleur::where('statut', 'verse')
            ->when($debut, fn ($q) => $q->whereDate('date_versement', '>=', $debut))
            ->when($fin, fn ($q) => $q->whereDate('date_versement', '<=', $fin))
            ->get()
            ->map(fn (ReversementBailleur $r) => (object) [
                'type' => 'decaissement',
                'source' => 'Reversement bailleur (mensuel)',
                'module' => 'Finance',
                'montant' => (float) $r->montant_net,
                'date' => Carbon::parse($r->date_versement),
                'reference' => $r->numero,
                'lien' => ['type' => 'reversement_bailleur', 'id' => $r->id],
            ]);

        return $depenses->concat($cautions)->concat($versements)->concat($reversements);
    }

    public function totalEncaisse(?Carbon $debut = null, ?Carbon $fin = null): float
    {
        return round($this->encaissements($debut, $fin)->sum('montant'), 2);
    }

    public function totalDecaisse(?Carbon $debut = null, ?Carbon $fin = null): float
    {
        return round($this->decaissements($debut, $fin)->sum('montant'), 2);
    }

    /**
     * Solde de trésorerie global, tout historique confondu (pas de filtre de période).
     */
    public function soldeGlobal(): float
    {
        return round($this->totalEncaisse() - $this->totalDecaisse(), 2);
    }
}
