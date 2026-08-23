<?php

namespace App\Models\Commercial;

use App\Models\Concerns\Auditable;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Prospect extends Model
{
    use SoftDeletes;
    use Auditable;

    const STATUTS = ['non_traite', 'en_cours', 'gagne', 'perdu', 'annule'];

    protected $fillable = [
        'numero',
        'nom',
        'prenom',
        'telephone',
        'email',
        'adresse',
        'type_demande_id',
        'besoin',
        'budget_min',
        'budget_max',
        'devise',
        'source_id',
        'statut',
        'commercial_id',
        'converti_en',
        'converti_le',
        'supprime_par_id',
        'motif_suppression',
    ];

    protected $casts = [
        'budget_min' => 'decimal:2',
        'budget_max' => 'decimal:2',
        'converti_le' => 'datetime',
    ];

    public function typeDemande()
    {
        return $this->belongsTo(TypeDemande::class, 'type_demande_id');
    }

    public function source()
    {
        return $this->belongsTo(Source::class, 'source_id');
    }

    public function commercial()
    {
        return $this->belongsTo(User::class, 'commercial_id');
    }

    public function activites()
    {
        return $this->hasMany(Activite::class)->orderByDesc('date_activite');
    }

    public function historiqueStatuts()
    {
        return $this->hasMany(StatusHistory::class)->orderByDesc('created_at');
    }

    public function getNomCompletAttribute(): string
    {
        return trim($this->prenom.' '.$this->nom);
    }

    public function derniereActivite()
    {
        return $this->activites()->first();
    }
}
