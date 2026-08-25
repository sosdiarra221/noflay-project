<?php

namespace App\Models\Rh;

use App\Models\Concerns\Auditable;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;

class ContratTravail extends Model
{
    use Auditable;

    protected string $moduleJournal = 'rh';

    protected $table = 'contrats_travail';

    const TYPES = [
        'cdi' => 'CDI',
        'cdd' => 'CDD',
        'essai' => "Période d'essai",
        'stage' => 'Stage',
    ];

    protected $fillable = [
        'numero',
        'employe_id',
        'type_contrat',
        'date_debut',
        'date_prevu_fin',
        'date_fin',
        'motif',
        'montant',
        'document',
        'etat',
        'contrat_precedent_id',
        'cree_par_id',
    ];

    protected $casts = [
        'date_debut' => 'date',
        'date_prevu_fin' => 'date',
        'date_fin' => 'date',
        'montant' => 'decimal:2',
    ];

    public function employe()
    {
        return $this->belongsTo(Employe::class);
    }

    public function contratPrecedent()
    {
        return $this->belongsTo(ContratTravail::class, 'contrat_precedent_id');
    }

    public function renouvellement()
    {
        return $this->hasOne(ContratTravail::class, 'contrat_precedent_id');
    }

    public function creePar()
    {
        return $this->belongsTo(User::class, 'cree_par_id');
    }

    public function libelleType(): string
    {
        return self::TYPES[$this->type_contrat] ?? $this->type_contrat;
    }

    public function joursAvantEcheance(): ?int
    {
        if (! $this->date_prevu_fin || $this->etat !== 'actif') {
            return null;
        }

        return (int) now()->startOfDay()->diffInDays($this->date_prevu_fin->copy()->startOfDay(), false);
    }

    public function scopeActifs($query)
    {
        return $query->where('etat', 'actif');
    }

    public function scopeEcheanceSous($query, int $jours)
    {
        return $query->where('etat', 'actif')
            ->whereNotNull('date_prevu_fin')
            ->whereBetween('date_prevu_fin', [now()->startOfDay(), now()->addDays($jours)->endOfDay()]);
    }
}
