<?php

namespace App\Models\Rh;

use Illuminate\Database\Eloquent\Model;

class EmployeEpouse extends Model
{
    protected $table = 'employe_epouses';

    protected $fillable = [
        'employe_id',
        'nom_complet',
        'telephone',
    ];

    public function employe()
    {
        return $this->belongsTo(Employe::class);
    }
}
