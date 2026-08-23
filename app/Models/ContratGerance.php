<?php

namespace App\Models;

use App\Models\Concerns\Auditable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ContratGerance extends Model
{
    use SoftDeletes;
    use Auditable;

    protected $table = 'contrats_gerance';

    protected $fillable = [
        'numero',
        'bailleur_id',
        'date_debut',
        'date_fin',
        'type_gerance',
        'frais_gestion_mode',
        'frais_gestion_valeur',
        'tva_charge',
        'taxe_charge',
        'tom_charge',
        'statut',
        'notes',
        'supprime_par_id',
        'motif_suppression',
    ];

    protected $casts = [
        'date_debut' => 'date',
        'date_fin' => 'date',
        'frais_gestion_valeur' => 'decimal:2',
    ];

    public function bailleur()
    {
        return $this->belongsTo(Bailleur::class);
    }

    public function biens()
    {
        return $this->hasMany(Bien::class, 'gerance_id');
    }

    public function supprimePar()
    {
        return $this->belongsTo(User::class, 'supprime_par_id');
    }

    public function documents()
    {
        return $this->morphMany(Document::class, 'documentable');
    }

    /**
     * Documents générés par le nouveau moteur de modèles (module Gestion Document), distincts
     * des pièces jointes ci-dessus (App\Models\Document).
     */
    public function documentsGeneres()
    {
        return $this->morphMany(\App\Models\Documents\Document::class, 'documentable');
    }

    public function getDureeAttribute(): ?string
    {
        if (! $this->date_debut || ! $this->date_fin) {
            return null;
        }

        return $this->date_debut->diffForHumans($this->date_fin, ['parts' => 2, 'syntax' => \Carbon\CarbonInterface::DIFF_ABSOLUTE]);
    }
}
