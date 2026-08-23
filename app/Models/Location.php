<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Location extends Model
{
    protected $fillable = [
        'numero',
        'locataire_id',
        'notes',
    ];

    public function locataire()
    {
        return $this->belongsTo(Locataire::class);
    }

    public function contrats()
    {
        return $this->hasMany(ContratLocation::class);
    }
}
