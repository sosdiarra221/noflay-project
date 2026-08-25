<?php

namespace App\Models\Facturation;

use App\Models\Concerns\Auditable;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Devis extends Model
{
    use SoftDeletes;
    use Auditable;

    protected string $moduleJournal = 'facturation';

    const STATUTS = [
        'brouillon' => 'Brouillon',
        'envoye' => 'Envoyé',
        'accepte' => 'Accepté',
        'refuse' => 'Refusé',
        'expire' => 'Expiré',
    ];

    protected $fillable = [
        'numero',
        'date_devis',
        'client_id',
        'statut',
        'appliquer_tva',
        'taux_tva',
        'sous_total_ht',
        'montant_tva',
        'total_ttc',
        'notes',
        'cree_par_id',
        'supprime_par_id',
        'motif_suppression',
    ];

    protected $casts = [
        'date_devis' => 'date',
        'appliquer_tva' => 'boolean',
        'taux_tva' => 'decimal:2',
        'sous_total_ht' => 'decimal:2',
        'montant_tva' => 'decimal:2',
        'total_ttc' => 'decimal:2',
    ];

    public function client()
    {
        return $this->belongsTo(Client::class);
    }

    public function lignes()
    {
        return $this->hasMany(DevisLigne::class)->orderBy('ordre');
    }

    public function creePar()
    {
        return $this->belongsTo(User::class, 'cree_par_id');
    }

    public function supprimePar()
    {
        return $this->belongsTo(User::class, 'supprime_par_id');
    }

    public function libelleStatut(): string
    {
        return self::STATUTS[$this->statut] ?? $this->statut;
    }
}
