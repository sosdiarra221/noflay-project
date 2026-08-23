<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FicheLocative extends Model
{
    protected $table = 'fiches_locatives';

    protected $fillable = [
        'numero_reference',
        'contrat_location_id',
        'annee',
        'mois',
        'loyer_mensuel',
        'arrieres',
        'frais_agence',
        'taxe_tom',
        'taux_tva',
        'montant_tva',
        'montant_total',
        'date_limite_paiement',
        'genere_par_id',
    ];

    protected $casts = [
        'loyer_mensuel' => 'decimal:2',
        'arrieres' => 'decimal:2',
        'frais_agence' => 'decimal:2',
        'taxe_tom' => 'decimal:2',
        'taux_tva' => 'decimal:2',
        'montant_tva' => 'decimal:2',
        'montant_total' => 'decimal:2',
        'date_limite_paiement' => 'date',
    ];

    public function contratLocation()
    {
        return $this->belongsTo(ContratLocation::class);
    }

    public function generePar()
    {
        return $this->belongsTo(User::class, 'genere_par_id');
    }
}
