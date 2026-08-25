<?php

namespace App\Models\Facturation;

use App\Models\Concerns\Auditable;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Facture extends Model
{
    use SoftDeletes;
    use Auditable;

    protected string $moduleJournal = 'facturation';

    const STATUTS = [
        'emise' => 'Émise',
        'payee' => 'Payée',
        'annulee' => 'Annulée',
    ];

    protected $fillable = [
        'numero',
        'date_facture',
        'client_id',
        'devis_id',
        'source',
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
        'date_facture' => 'date',
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

    public function devisSource()
    {
        return $this->belongsTo(Devis::class, 'devis_id');
    }

    public function lignes()
    {
        return $this->hasMany(FactureLigne::class)->orderBy('ordre');
    }

    public function creePar()
    {
        return $this->belongsTo(User::class, 'cree_par_id');
    }

    public function libelleStatut(): string
    {
        return self::STATUTS[$this->statut] ?? $this->statut;
    }
}
