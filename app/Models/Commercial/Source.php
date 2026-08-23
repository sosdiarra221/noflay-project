<?php

namespace App\Models\Commercial;

use Illuminate\Database\Eloquent\Model;

class Source extends Model
{
    protected $table = 'commercial_sources';

    protected $fillable = [
        'nom',
        'actif',
    ];

    protected $casts = [
        'actif' => 'boolean',
    ];

    public function prospects()
    {
        return $this->hasMany(Prospect::class, 'source_id');
    }
}
