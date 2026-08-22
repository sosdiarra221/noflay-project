<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Devise extends Model
{
    protected $fillable = [
        'code',
        'nom',
        'symbole',
        'est_defaut',
    ];

    protected $casts = [
        'est_defaut' => 'boolean',
    ];
}
