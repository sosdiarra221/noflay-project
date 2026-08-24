<?php

namespace App\Models;

use App\Models\Concerns\Auditable;
use Illuminate\Database\Eloquent\Model;

class Caution extends Model
{
    use Auditable;

    protected $table = 'cautions';

    protected string $moduleJournal = 'finance';

    protected $fillable = [
        'contrat_location_id',
        'montant_total',
        'part_bailleur',
        'part_agence',
        'statut',
        'montant_retenu',
        'motif_retenue',
        'montant_restitue',
        'date_restitution',
        'restituee_par_id',
    ];

    protected $casts = [
        'montant_total' => 'decimal:2',
        'part_bailleur' => 'decimal:2',
        'part_agence' => 'decimal:2',
        'montant_retenu' => 'decimal:2',
        'montant_restitue' => 'decimal:2',
        'date_restitution' => 'date',
    ];

    public function contratLocation()
    {
        return $this->belongsTo(ContratLocation::class);
    }

    public function restituePar()
    {
        return $this->belongsTo(User::class, 'restituee_par_id');
    }

    /**
     * Seule la part bailleur (la vraie caution/garantie) est restituable : la part agence
     * correspond aux frais d'agence à l'entrée, déjà acquis à l'agence dès l'encaissement.
     */
    public function montantARestituer(): float
    {
        return round((float) $this->part_bailleur - (float) $this->montant_retenu, 2);
    }
}
