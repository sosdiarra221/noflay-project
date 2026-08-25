<?php

namespace App\Models\Rh;

use Illuminate\Database\Eloquent\Model;

class Poste extends Model
{
    protected $fillable = [
        'nom',
        'fiche_poste',
        'actif',
    ];

    protected $casts = [
        'actif' => 'boolean',
    ];

    public function employes()
    {
        return $this->hasMany(Employe::class);
    }
}
