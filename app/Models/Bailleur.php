<?php

namespace App\Models;

use App\Models\Concerns\Auditable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Bailleur extends Model
{
    use SoftDeletes;
    use Auditable;

    protected $fillable = [
        'numero',
        'type',
        'nom',
        'prenom',
        'telephone',
        'whatsapp',
        'email',
        'adresse',
        'piece_identite_type',
        'piece_identite_numero',
        'ninea',
        'coordonnees_paiement',
        'notes',
        'statut',
        'supprime_par_id',
        'motif_suppression',
    ];

    public function gerances()
    {
        return $this->hasMany(ContratGerance::class);
    }

    public function biens()
    {
        return $this->hasMany(Bien::class);
    }

    public function supprimePar()
    {
        return $this->belongsTo(User::class, 'supprime_par_id');
    }

    public function documents()
    {
        return $this->morphMany(Document::class, 'documentable');
    }

    public function getNomCompletAttribute(): string
    {
        if ($this->type === 'entreprise') {
            return $this->nom;
        }

        return trim($this->prenom.' '.$this->nom);
    }
}
