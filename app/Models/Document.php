<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Document extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'documentable_type',
        'documentable_id',
        'nom_original',
        'chemin',
        'type_mime',
        'taille',
        'categorie',
        'uploaded_by_id',
    ];

    public function documentable()
    {
        return $this->morphTo();
    }

    public function uploadePar()
    {
        return $this->belongsTo(User::class, 'uploaded_by_id');
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
