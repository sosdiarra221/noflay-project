<?php

namespace App\Models;

use App\Models\Concerns\Auditable;
use Illuminate\Database\Eloquent\Model;

class Paiement extends Model
{
    use Auditable;

    const TYPE_LOYER = 'loyer';
    const TYPE_ENTREE = 'entree';

    protected $fillable = [
        'numero',
        'echeance_loyer_id',
        'contrat_location_id',
        'type',
        'montant',
        'part_bailleur',
        'part_commission_agence',
        'part_caution',
        'part_frais_agence',
        'mode_paiement_id',
        'date_paiement',
        'reference',
        'note',
        'enregistre_par_id',
        'statut',
        'motif_annulation',
    ];

    protected $casts = [
        'montant' => 'decimal:2',
        'part_bailleur' => 'decimal:2',
        'part_commission_agence' => 'decimal:2',
        'part_caution' => 'decimal:2',
        'part_frais_agence' => 'decimal:2',
        'date_paiement' => 'date',
    ];

    public function echeance()
    {
        return $this->belongsTo(EcheanceLoyer::class, 'echeance_loyer_id');
    }

    public function contratLocation()
    {
        return $this->belongsTo(ContratLocation::class);
    }

    public function modePaiement()
    {
        return $this->belongsTo(ModePaiement::class, 'mode_paiement_id');
    }

    public function enregistrePar()
    {
        return $this->belongsTo(User::class, 'enregistre_par_id');
    }
}
