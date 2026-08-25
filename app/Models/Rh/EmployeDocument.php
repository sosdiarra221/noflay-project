<?php

namespace App\Models\Rh;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;

class EmployeDocument extends Model
{
    protected $table = 'employe_documents';

    protected $fillable = [
        'employe_id',
        'type_document',
        'nom_fichier',
        'chemin_fichier',
        'type_mime',
        'taille',
        'ajoute_par_id',
    ];

    public function employe()
    {
        return $this->belongsTo(Employe::class);
    }

    public function ajoutePar()
    {
        return $this->belongsTo(User::class, 'ajoute_par_id');
    }

    public function estPrevisualisable(): bool
    {
        return str_starts_with((string) $this->type_mime, 'image/') || $this->type_mime === 'application/pdf';
    }

    public function tailleLisible(): string
    {
        $taille = $this->taille ?? 0;

        if ($taille < 1024) {
            return $taille.' o';
        }
        if ($taille < 1024 * 1024) {
            return round($taille / 1024, 1).' Ko';
        }

        return round($taille / (1024 * 1024), 1).' Mo';
    }
}
