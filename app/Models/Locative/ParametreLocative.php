<?php

namespace App\Models\Locative;

use Illuminate\Database\Eloquent\Model;

class ParametreLocative extends Model
{
    protected $table = 'locative_parametres';

    protected $fillable = [
        'nom_societe',
        'logo',
        'adresse',
        'telephone',
        'email',
        'site_web',
    ];

    public static function courant(): self
    {
        return static::first() ?? static::create([]);
    }
}
