<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EcheanceLoyer extends Model
{
    protected $table = 'echeances_loyer';

    protected $fillable = [
        'contrat_location_id',
        'annee',
        'mois',
        'date_echeance',
        'montant_attendu',
        'montant_paye',
        'statut',
        'numero_quittance',
    ];

    protected $casts = [
        'date_echeance' => 'date',
        'montant_attendu' => 'decimal:2',
        'montant_paye' => 'decimal:2',
    ];

    public function contratLocation()
    {
        return $this->belongsTo(ContratLocation::class, 'contrat_location_id');
    }

    public function paiements()
    {
        return $this->hasMany(Paiement::class);
    }

    /**
     * Recalcule montant_paye à partir des paiements réellement valides (non annulés),
     * puis met à jour le statut. À appeler après tout encaissement ou annulation.
     */
    public function recalculerMontantPaye(): void
    {
        $this->montant_paye = $this->paiements()->where('statut', 'valide')->sum('montant');
        $this->recalculerStatut();
    }

    public function recalculerStatut(): void
    {
        if ($this->montant_paye <= 0) {
            $this->statut = $this->date_echeance->isPast() ? 'en_retard' : 'a_venir';
        } elseif ($this->montant_paye < $this->montant_attendu) {
            $this->statut = 'partiellement_paye';
        } else {
            $this->statut = 'paye';
        }

        $this->save();
    }

    /**
     * Numéro de quittance persistant, du type RQL-2027-08-002 (préfixe-année-mois-séquence dans le mois).
     * Généré une seule fois puis réutilisé, pour rester stable même si la page est régénérée.
     */
    public function numeroQuittance(): string
    {
        if ($this->numero_quittance) {
            return $this->numero_quittance;
        }

        $prefixe = sprintf('RQL-%d-%02d', $this->annee, $this->mois);

        $sequence = static::where('annee', $this->annee)
            ->where('mois', $this->mois)
            ->whereNotNull('numero_quittance')
            ->count() + 1;

        $this->numero_quittance = sprintf('%s-%03d', $prefixe, $sequence);
        $this->save();

        return $this->numero_quittance;
    }
}
