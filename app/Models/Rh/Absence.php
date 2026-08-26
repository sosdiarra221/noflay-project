<?php

namespace App\Models\Rh;

use App\Models\Concerns\Auditable;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

class Absence extends Model
{
    use Auditable;

    protected string $moduleJournal = 'rh';

    protected $table = 'rh_absences';

    const STATUTS = [
        'en_attente' => 'En attente',
        'validee' => 'Validée',
        'refusee' => 'Refusée',
        'annulee' => 'Annulée',
    ];

    protected $fillable = [
        'numero',
        'employe_id',
        'type_absence_id',
        'date_debut',
        'date_retour',
        'nombre_jours',
        'motif',
        'document',
        'statut',
        'commentaire_statut',
        'cree_par_id',
        'valide_par_id',
        'date_validation',
    ];

    protected $casts = [
        'date_debut' => 'date',
        'date_retour' => 'date',
        'nombre_jours' => 'decimal:2',
        'date_validation' => 'datetime',
    ];

    public function employe()
    {
        return $this->belongsTo(Employe::class);
    }

    public function typeAbsence()
    {
        return $this->belongsTo(TypeAbsence::class);
    }

    public function creePar()
    {
        return $this->belongsTo(User::class, 'cree_par_id');
    }

    public function validePar()
    {
        return $this->belongsTo(User::class, 'valide_par_id');
    }

    public function libelleStatut(): string
    {
        return self::STATUTS[$this->statut] ?? $this->statut;
    }

    /**
     * "date_retour" est le jour où l'employé reprend le travail (donc exclu du décompte) — le
     * nombre de jours d'absence est le nombre de jours calendaires entre les deux bornes, moins
     * les jours fériés qui tombent dans la période (ils ne sont pas décomptés du solde de congé).
     */
    public static function calculerNombreJours(Carbon $debut, Carbon $retour): int
    {
        if ($retour->lte($debut)) {
            return 0;
        }

        $joursCalendaires = (int) $debut->diffInDays($retour);
        $joursFeries = count(JourFerie::datesFeriesEntre($debut, $retour->copy()->subDay()));

        return max(0, $joursCalendaires - $joursFeries);
    }

    public function scopeEnCours($query)
    {
        return $query->where('statut', 'validee')
            ->whereDate('date_debut', '<=', now())
            ->whereDate('date_retour', '>', now());
    }
}
