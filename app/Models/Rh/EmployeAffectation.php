<?php

namespace App\Models\Rh;

use App\Models\Departement;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;

class EmployeAffectation extends Model
{
    protected $table = 'employe_affectations';

    protected $fillable = [
        'employe_id',
        'ancien_departement_id',
        'nouveau_departement_id',
        'anciens_sites',
        'nouveaux_sites',
        'date_affectation',
        'motif',
        'effectue_par_id',
    ];

    protected $casts = [
        'date_affectation' => 'date',
    ];

    public function employe()
    {
        return $this->belongsTo(Employe::class);
    }

    public function ancienDepartement()
    {
        return $this->belongsTo(Departement::class, 'ancien_departement_id');
    }

    public function nouveauDepartement()
    {
        return $this->belongsTo(Departement::class, 'nouveau_departement_id');
    }

    public function effectuePar()
    {
        return $this->belongsTo(User::class, 'effectue_par_id');
    }
}
