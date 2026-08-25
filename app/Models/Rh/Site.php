<?php

namespace App\Models\Rh;

use App\Models\Facturation\Client;
use Illuminate\Database\Eloquent\Model;

class Site extends Model
{
    protected $table = 'rh_sites';

    protected $fillable = [
        'nom',
        'client_id',
        'adresse',
        'actif',
    ];

    protected $casts = [
        'actif' => 'boolean',
    ];

    public function client()
    {
        return $this->belongsTo(Client::class);
    }

    public function employes()
    {
        return $this->belongsToMany(Employe::class, 'employe_site')->withTimestamps();
    }
}
