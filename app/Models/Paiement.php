<?php

namespace App\Models;

use App\Models\Concerns\Auditable;
use Illuminate\Database\Eloquent\Model;

class Paiement extends Model
{
    use Auditable;

    protected $fillable = [
        'numero',
        'echeance_loyer_id',
        'montant',
        'mode_paiement_id',
        'date_paiement',
        'reference',
        'note',
        'enregistre_par_id',
    ];

    protected $casts = [
        'montant' => 'decimal:2',
        'date_paiement' => 'date',
    ];

    public function echeance()
    {
        return $this->belongsTo(EcheanceLoyer::class, 'echeance_loyer_id');
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
