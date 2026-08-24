<?php

namespace App\Models;

use App\Models\Concerns\Auditable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ContratLocation extends Model
{
    use SoftDeletes;
    use Auditable;

    protected $table = 'contrats_location';

    protected $fillable = [
        'numero',
        'location_id',
        'bien_id',
        'bailleur_id',
        'type_location',
        'date_debut',
        'date_fin',
        'loyer_mensuel',
        'depot_garantie',
        'depot_garantie_part_bailleur',
        'depot_garantie_part_agence',
        'jour_echeance',
        'charges',
        'appliquer_tva',
        'appliquer_tom',
        'periodicite',
        'mode_paiement_prefere_id',
        'statut',
        'notes',
        'supprime_par_id',
        'motif_suppression',
    ];

    protected $casts = [
        'date_debut' => 'date',
        'date_fin' => 'date',
        'loyer_mensuel' => 'decimal:2',
        'depot_garantie' => 'decimal:2',
        'depot_garantie_part_bailleur' => 'decimal:2',
        'depot_garantie_part_agence' => 'decimal:2',
        'charges' => 'decimal:2',
        'appliquer_tva' => 'boolean',
        'appliquer_tom' => 'boolean',
    ];

    public function location()
    {
        return $this->belongsTo(Location::class);
    }

    public function bien()
    {
        return $this->belongsTo(Bien::class);
    }

    public function bailleur()
    {
        return $this->belongsTo(Bailleur::class);
    }

    public function modePaiementPrefere()
    {
        return $this->belongsTo(ModePaiement::class, 'mode_paiement_prefere_id');
    }

    public function echeances()
    {
        return $this->hasMany(EcheanceLoyer::class);
    }

    public function fichesLocatives()
    {
        return $this->hasMany(FicheLocative::class);
    }

    public function caution()
    {
        return $this->hasOne(Caution::class);
    }

    public function paiements()
    {
        return $this->hasMany(Paiement::class);
    }

    public function typeLocationLabel(): string
    {
        return match ($this->type_location) {
            'commercial' => 'Usage commercial',
            'habitation' => 'Usage habitation',
            default => '—',
        };
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
}
