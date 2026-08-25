<?php

namespace App\Models\Rh;

use Illuminate\Database\Eloquent\Model;

class EmployeDiplome extends Model
{
    protected $fillable = [
        'employe_id',
        'intitule',
        'niveau',
        'annee_obtention',
    ];

    public function employe()
    {
        return $this->belongsTo(Employe::class);
    }
}
