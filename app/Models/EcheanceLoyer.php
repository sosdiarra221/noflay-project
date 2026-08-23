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
}
