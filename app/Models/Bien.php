<?php

namespace App\Models;

use App\Models\Concerns\Auditable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Bien extends Model
{
    use SoftDeletes;
    use Auditable;

    const STATUTS_LOCATION = ['disponible', 'reserve', 'en_attente_entree', 'occupe', 'maintenance', 'indisponible'];
    const STATUTS_VENTE = ['a_vendre', 'visite_programmee', 'offre_recue', 'compromis', 'vente_en_cours', 'vendu'];

    protected $fillable = [
        'numero',
        'titre',
        'categorie_bien_id',
        'adresse',
        'zone',
        'description',
        'bailleur_id',
        'gerance_id',
        'type_exploitation',
        'loyer_mensuel',
        'prix_vente',
        'frais_gestion_mode',
        'frais_gestion_valeur',
        'statut',
        'notes',
        'supprime_par_id',
        'motif_suppression',
    ];

    protected $casts = [
        'loyer_mensuel' => 'decimal:2',
        'prix_vente' => 'decimal:2',
        'frais_gestion_valeur' => 'decimal:2',
    ];

    public function bailleur()
    {
        return $this->belongsTo(Bailleur::class);
    }

    public function gerance()
    {
        return $this->belongsTo(ContratGerance::class, 'gerance_id');
    }

    public function categorie()
    {
        return $this->belongsTo(CategorieBien::class, 'categorie_bien_id');
    }

    public function contratsLocation()
    {
        return $this->hasMany(ContratLocation::class);
    }

    public function supprimePar()
    {
        return $this->belongsTo(User::class, 'supprime_par_id');
    }

    public function montantExploitation(): ?float
    {
        return $this->type_exploitation === 'vente' ? $this->prix_vente : $this->loyer_mensuel;
    }

    public function fraisGestionCalcules(): float
    {
        $montant = $this->montantExploitation() ?? 0;
        $mode = $this->frais_gestion_mode;
        $valeur = (float) $this->frais_gestion_valeur;

        if ($mode === 'pourcentage') {
            return round($montant * $valeur / 100, 2);
        }

        return $valeur;
    }
}
