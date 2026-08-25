<?php

namespace App\Models\Rh;

use App\Models\Concerns\Auditable;
use App\Models\Departement;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Employe extends Model
{
    use SoftDeletes;
    use Auditable;

    protected string $moduleJournal = 'rh';

    const CATEGORIES_FONCTION = [
        'agent_terrain' => 'Agent terrain',
        'superviseur' => 'Superviseur',
        'staff' => 'Staff',
    ];

    const SITUATIONS_MATRIMONIALES = [
        'celibataire' => 'Célibataire',
        'marie' => 'Marié(e)',
        'divorce' => 'Divorcé(e)',
        'veuf' => 'Veuf/Veuve',
    ];

    const PIECES_IDENTITE = [
        'cni' => 'CNI',
        'passeport' => 'Passeport',
        'permis_conduire' => 'Permis de Conduire',
        'carte_sejour' => 'Carte de séjour',
    ];

    protected $fillable = [
        'matricule',
        'photo',
        'nom',
        'prenom',
        'sexe',
        'date_naissance',
        'lieu_naissance',
        'situation_matrimoniale',
        'piece_identite_type',
        'piece_identite_numero',
        'permis_conduire',
        'arts_martiaux',
        'service_militaire',
        'telephone',
        'whatsapp',
        'email',
        'adresse',
        'categorie_fonction',
        'poste_id',
        'departement_id',
        'langues_parlees',
        'langues_lues',
        'banque',
        'compte_bancaire',
        'personne_urgence_nom',
        'personne_urgence_telephone',
        'personne_urgence_lien',
        'solde_conges',
        'date_embauche',
        'statut',
        'date_sortie',
        'motif_sortie',
        'notes',
        'cree_par_id',
        'user_id',
        'supprime_par_id',
        'motif_suppression',
    ];

    protected $casts = [
        'date_naissance' => 'date',
        'date_embauche' => 'date',
        'date_sortie' => 'date',
        'solde_conges' => 'decimal:2',
        'permis_conduire' => 'boolean',
        'arts_martiaux' => 'boolean',
        'service_militaire' => 'boolean',
    ];

    public function departement()
    {
        return $this->belongsTo(Departement::class);
    }

    public function poste()
    {
        return $this->belongsTo(Poste::class);
    }

    public function sites()
    {
        return $this->belongsToMany(Site::class, 'employe_site');
    }

    public function epouses()
    {
        return $this->hasMany(EmployeEpouse::class);
    }

    public function enfants()
    {
        return $this->hasMany(EmployeEnfant::class);
    }

    public function diplomes()
    {
        return $this->hasMany(EmployeDiplome::class);
    }

    public function documents()
    {
        return $this->hasMany(EmployeDocument::class);
    }

    public function affectations()
    {
        return $this->hasMany(EmployeAffectation::class)->orderByDesc('date_affectation');
    }

    public function contrats()
    {
        return $this->hasMany(ContratTravail::class)->orderByDesc('date_debut');
    }

    public function contratActif()
    {
        return $this->hasOne(ContratTravail::class)->where('etat', 'actif')->orderByDesc('date_debut');
    }

    public function creePar()
    {
        return $this->belongsTo(User::class, 'cree_par_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function supprimePar()
    {
        return $this->belongsTo(User::class, 'supprime_par_id');
    }

    public function getNomCompletAttribute(): string
    {
        return trim($this->prenom.' '.$this->nom);
    }

    public function libelleCategorieFonction(): string
    {
        return self::CATEGORIES_FONCTION[$this->categorie_fonction] ?? $this->categorie_fonction;
    }

    public function libellePieceIdentite(): ?string
    {
        return self::PIECES_IDENTITE[$this->piece_identite_type] ?? $this->piece_identite_type;
    }

    public function aUnContratActif(): bool
    {
        return $this->contrats()->where('etat', 'actif')->exists();
    }

    public function scopeActifs($query)
    {
        return $query->where('statut', 'actif');
    }
}
