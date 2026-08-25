<?php

namespace App\Models\Rh;

use Illuminate\Database\Eloquent\Model;

class Site extends Model
{
    protected $table = 'rh_sites';

    protected $fillable = [
        'nom',
        'adresse',
        'actif',
    ];

    protected $casts = [
        'actif' => 'boolean',
    ];

    public function employes()
    {
        return $this->belongsToMany(Employe::class, 'employe_site');
    }
}
