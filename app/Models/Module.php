<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Module extends Model
{
    protected $fillable = [
        'cle',
        'nom',
        'description',
        'icone',
        'actif',
        'ordre',
    ];

    protected $casts = [
        'actif' => 'boolean',
    ];

    public static function estActif(string $cle): bool
    {
        $valeur = static::where('cle', $cle)->value('actif');

        return $valeur === null ? true : (bool) $valeur;
    }
}
