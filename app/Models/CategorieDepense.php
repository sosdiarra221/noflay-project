<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CategorieDepense extends Model
{
    protected $table = 'categories_depense';

    protected $fillable = [
        'nom',
        'imputation_defaut',
        'actif',
    ];

    protected $casts = [
        'actif' => 'boolean',
    ];
}
