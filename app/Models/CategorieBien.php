<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CategorieBien extends Model
{
    protected $table = 'categories_biens';

    protected $fillable = [
        'nom',
        'actif',
    ];

    protected $casts = [
        'actif' => 'boolean',
    ];
}
