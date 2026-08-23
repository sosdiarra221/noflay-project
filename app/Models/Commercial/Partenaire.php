<?php

namespace App\Models\Commercial;

use App\Models\Concerns\Auditable;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Partenaire extends Model
{
    use SoftDeletes;
    use Auditable;

    protected $table = 'commercial_partenaires';

    protected string $moduleJournal = 'commercial';

    const TYPES = ['agence_immobiliere', 'notaire', 'banque', 'apporteur_affaires', 'autre'];

    protected $fillable = [
        'numero',
        'type',
        'nom',
        'contact_nom',
        'telephone',
        'email',
        'adresse',
        'commission_pourcentage',
        'statut',
        'notes',
        'supprime_par_id',
        'motif_suppression',
    ];

    protected $casts = [
        'commission_pourcentage' => 'decimal:2',
    ];

    public function prospects()
    {
        return $this->hasMany(Prospect::class);
    }

    public function supprimePar()
    {
        return $this->belongsTo(User::class, 'supprime_par_id');
    }
}
