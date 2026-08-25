<?php

namespace App\Models\Facturation;

use App\Models\Commercial\Prospect;
use Illuminate\Database\Eloquent\Model;

class Client extends Model
{
    protected $table = 'facturation_clients';

    protected $fillable = [
        'prospect_id',
        'nom_complet',
        'telephone',
        'email',
    ];

    public function devis()
    {
        return $this->hasMany(Devis::class);
    }

    public function factures()
    {
        return $this->hasMany(Facture::class);
    }

    public function prospect()
    {
        return $this->belongsTo(Prospect::class);
    }

    /**
     * Retrouve (ou crée) le client de facturation correspondant à un prospect, en le liant
     * durablement via prospect_id — évite de dupliquer une fiche client à chaque nouveau devis
     * émis pour le même prospect.
     */
    public static function depuisProspect(Prospect $prospect): self
    {
        return static::firstOrCreate(
            ['prospect_id' => $prospect->id],
            [
                'nom_complet' => $prospect->nom_complet,
                'telephone' => $prospect->telephone,
                'email' => $prospect->email,
            ]
        );
    }
}
