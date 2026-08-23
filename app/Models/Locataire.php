<?php

namespace App\Models;

use App\Models\Concerns\Auditable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Locataire extends Model
{
    use SoftDeletes;
    use Auditable;

    protected $fillable = [
        'numero',
        'nom',
        'prenom',
        'telephone',
        'whatsapp',
        'email',
        'adresse',
        'type_locataire',
        'piece_identite_type',
        'piece_identite_numero',
        'notes',
        'statut',
        'supprime_par_id',
        'motif_suppression',
    ];

    public function locations()
    {
        return $this->hasMany(Location::class);
    }

    public function documents()
    {
        return $this->morphMany(Document::class, 'documentable');
    }

    public function supprimePar()
    {
        return $this->belongsTo(User::class, 'supprime_par_id');
    }

    public function getNomCompletAttribute(): string
    {
        return trim($this->prenom.' '.$this->nom);
    }
}
