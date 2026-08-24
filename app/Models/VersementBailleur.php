<?php

namespace App\Models;

use App\Models\Concerns\Auditable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class VersementBailleur extends Model
{
    use SoftDeletes;
    use Auditable;

    protected $table = 'versements_bailleurs';

    protected string $moduleJournal = 'locative';

    protected $fillable = [
        'numero',
        'bailleur_id',
        'montant',
        'type',
        'date_versement',
        'mode_paiement_id',
        'reference',
        'notes',
        'effectue_par_id',
        'motif_suppression',
        'supprime_par_id',
    ];

    protected $casts = [
        'montant' => 'decimal:2',
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

    public function supprimePar()
    {
        return $this->belongsTo(User::class, 'supprime_par_id');
    }
}
