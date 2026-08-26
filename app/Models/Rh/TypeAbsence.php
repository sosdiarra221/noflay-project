<?php

namespace App\Models\Rh;

use Illuminate\Database\Eloquent\Model;

class TypeAbsence extends Model
{
    protected $table = 'types_absence';

    protected $fillable = [
        'nom',
        'est_urgence',
        'actif',
    ];

    protected $casts = [
        'est_urgence' => 'boolean',
        'actif' => 'boolean',
    ];

    public function absences()
    {
        return $this->hasMany(Absence::class);
    }
}
