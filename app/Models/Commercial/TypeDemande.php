<?php

namespace App\Models\Commercial;

use Illuminate\Database\Eloquent\Model;

class TypeDemande extends Model
{
    protected $table = 'commercial_types_demande';

    protected $fillable = [
        'nom',
        'actif',
    ];

    protected $casts = [
        'actif' => 'boolean',
    ];
}
