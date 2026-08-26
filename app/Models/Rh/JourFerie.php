<?php

namespace App\Models\Rh;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

class JourFerie extends Model
{
    protected $table = 'jours_feries';

    protected $fillable = [
        'nom',
        'date',
        'recurrent_annuel',
    ];

    protected $casts = [
        'date' => 'date',
        'recurrent_annuel' => 'boolean',
    ];

    /**
     * Un jour férié récurrent (ex: fête de l'Indépendance) tombe le même jour/mois chaque
     * année sans qu'il faille ressaisir une ligne par année — seule la date de création sert
     * de référence pour le jour/mois.
     */
    public static function estFerie(Carbon $date): bool
    {
        return static::query()
            ->where(function ($q) use ($date) {
                $q->whereDate('date', $date->toDateString())
                    ->orWhere(function ($q2) use ($date) {
                        $q2->where('recurrent_annuel', true)
                            ->whereMonth('date', $date->month)
                            ->whereDay('date', $date->day);
                    });
            })
            ->exists();
    }

    /**
     * Calcule en une seule requête l'ensemble des jours fériés (aaaa-mm-jj) tombant dans une
     * période, pour éviter une requête par jour lors du calcul du nombre de jours d'une absence.
     */
    public static function datesFeriesEntre(Carbon $debut, Carbon $fin): array
    {
        $dates = [];
        $periode = static::all();

        for ($jour = $debut->copy(); $jour->lte($fin); $jour->addDay()) {
            foreach ($periode as $ferie) {
                $memeJour = $ferie->recurrent_annuel
                    ? ($ferie->date->month === $jour->month && $ferie->date->day === $jour->day)
                    : $ferie->date->isSameDay($jour);

                if ($memeJour) {
                    $dates[] = $jour->toDateString();
                    break;
                }
            }
        }

        return $dates;
    }
}
