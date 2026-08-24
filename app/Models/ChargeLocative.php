<?php

namespace App\Models;

use App\Models\Concerns\Auditable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ChargeLocative extends Model
{
    use SoftDeletes;
    use Auditable;

    protected $table = 'charges_locatives';

    protected string $moduleJournal = 'locative';

    const TYPES = [
        'electricite' => 'Électricité',
        'eau' => 'Eau',
        'wifi' => 'Wifi / Internet',
        'gaz' => 'Gaz',
        'ordures' => 'Ordures ménagères',
        'autre' => 'Autre',
    ];

    protected $fillable = [
        'numero',
        'contrat_location_id',
        'type_charge',
        'titre',
        'montant',
        'description',
        'reglee_par_locataire',
        'frequence_mois',
        'statut',
        'date_charge',
        'cree_par_id',
        'motif_suppression',
        'supprime_par_id',
    ];

    protected $casts = [
        'montant' => 'decimal:2',
        'reglee_par_locataire' => 'boolean',
        'date_charge' => 'date',
    ];

    public function contratLocation()
    {
        return $this->belongsTo(ContratLocation::class);
    }

    public function creePar()
    {
        return $this->belongsTo(User::class, 'cree_par_id');
    }

    public function supprimePar()
    {
        return $this->belongsTo(User::class, 'supprime_par_id');
    }

    public function libelleType(): string
    {
        return self::TYPES[$this->type_charge] ?? $this->type_charge;
    }

    public function libelleFrequence(): string
    {
        return match ($this->frequence_mois) {
            1 => 'Chaque mois',
            2 => 'Tous les 2 mois',
            3 => 'Tous les 3 mois',
            4 => 'Tous les 4 mois',
            5 => 'Tous les 5 mois',
            default => 'Tous les '.$this->frequence_mois.' mois',
        };
    }
}
