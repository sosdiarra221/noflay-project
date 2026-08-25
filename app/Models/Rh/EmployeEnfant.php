<?php

namespace App\Models\Rh;

use Illuminate\Database\Eloquent\Model;

class EmployeEnfant extends Model
{
    protected $table = 'employe_enfants';

    protected $fillable = [
        'employe_id',
        'nom_complet',
        'date_naissance',
        'telephone',
    ];

    protected $casts = [
        'date_naissance' => 'date',
    ];

    public function employe()
    {
        return $this->belongsTo(Employe::class);
    }

    public function getAgeAttribute(): ?int
    {
        return $this->date_naissance?->age;
    }
}
