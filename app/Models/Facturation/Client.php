<?php

namespace App\Models\Facturation;

use Illuminate\Database\Eloquent\Model;

class Client extends Model
{
    protected $table = 'facturation_clients';

    protected $fillable = [
        'nom_complet',
        'telephone',
        'email',
    ];

    public function devis()
    {
        return $this->hasMany(Devis::class);
    }
}
