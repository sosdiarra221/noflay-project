<?php

namespace App\Models;

use App\Models\Concerns\Auditable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ReversementBailleur extends Model
{
    use SoftDeletes;
    use Auditable;

    protected $table = 'reversements_bailleurs';

    protected string $moduleJournal = 'finance';

    protected $fillable = [
        'numero',
        'bailleur_id',
        'periode_annee',
        'periode_mois',
        'montant_encaisse',
        'montant_frais_gestion',
        'montant_net',
        'statut',
        'date_versement',
        'mode_paiement_id',
        'reference',
        'notes',
        'effectue_par_id',
        'motif_suppression',
        'supprime_par_id',
    ];

    protected $casts = [
        'montant_encaisse' => 'decimal:2',
        'montant_frais_gestion' => 'decimal:2',
        'montant_net' => 'decimal:2',
        'date_versement' => 'date',
    ];

    public function bailleur()
    {
        return $this->belongsTo(Bailleur::class);
    }

    public function modePaiement()
    {
        return $this->belongsTo(ModePaiement::class);
    }

    public function effectuePar()
    {
        return $this->belongsTo(User::class, 'effectue_par_id');
    }
}
