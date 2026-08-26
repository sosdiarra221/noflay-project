<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Package extends Model
{
    protected $connection = 'central';

    protected $fillable = [
        'nom',
        'description',
        'modules',
        'actif',
    ];

    protected $casts = [
        'modules' => 'array',
        'actif' => 'boolean',
    ];

    public function licences()
    {
        return $this->hasMany(Licence::class);
    }
}
