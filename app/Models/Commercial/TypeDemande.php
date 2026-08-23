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

    public function prospects()
    {
        return $this->hasMany(Prospect::class, 'type_demande_id');
    }
}
