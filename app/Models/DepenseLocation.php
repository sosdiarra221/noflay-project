<?php

namespace App\Models;

use App\Models\Concerns\Auditable;
use Illuminate\Database\Eloquent\Model;

class DepenseLocation extends Model
{
    use Auditable;

    protected $table = 'depenses_location';

    protected string $moduleJournal = 'finance';

    const STATUT_BROUILLON = 'brouillon';
    const STATUT_EN_ATTENTE = 'en_attente_validation';
    const STATUT_APPROUVEE = 'approuvee';
    const STATUT_REFUSEE = 'refusee';
    const STATUT_INTERVENTION = 'intervention_en_cours';
    const STATUT_FACTURE_RECUE = 'facture_recue';
    const STATUT_A_PAYER = 'a_payer';
    const STATUT_PAYEE = 'payee';
    const STATUT_CLOTUREE = 'cloturee';

    const STATUTS_PAYEES = [self::STATUT_PAYEE, self::STATUT_CLOTUREE];

    protected $fillable = [
        'numero',
        'bien_id',
        'contrat_location_id',
        'bailleur_id',
        'locataire_id',
        'categorie_depense_id',
        'description',
        'montant_estime',
        'montant_final',
        'fournisseur',
        'urgence',
        'qui_supporte',
        'responsable_financier_id',
        'statut',
        'motif_refus',
        'date_validation',
        'date_paiement',
        'mode_paiement_id',
        'commentaire',
        'cree_par_id',
    ];

    protected $casts = [
        'montant_estime' => 'decimal:2',
        'montant_final' => 'decimal:2',
        'urgence' => 'boolean',
        'date_validation' => 'date',
        'date_paiement' => 'date',
    ];

    public function bien()
    {
        return $this->belongsTo(Bien::class);
    }

    public function contratLocation()
    {
        return $this->belongsTo(ContratLocation::class);
    }

    public function bailleur()
    {
        return $this->belongsTo(Bailleur::class);
    }

    public function locataire()
    {
        return $this->belongsTo(Locataire::class);
    }

    public function categorie()
    {
        return $this->belongsTo(CategorieDepense::class, 'categorie_depense_id');
    }

    public function responsableFinancier()
    {
        return $this->belongsTo(User::class, 'responsable_financier_id');
    }

    public function modePaiement()
    {
        return $this->belongsTo(ModePaiement::class);
    }

    public function creePar()
    {
        return $this->belongsTo(User::class, 'cree_par_id');
    }

    public function documents()
    {
        return $this->morphMany(Document::class, 'documentable');
    }

    public function montantImpute(): float
    {
        return (float) ($this->montant_final ?? $this->montant_estime);
    }

    public function libelleStatut(): string
    {
        return match ($this->statut) {
            self::STATUT_BROUILLON => 'Brouillon',
            self::STATUT_EN_ATTENTE => 'En attente de validation',
            self::STATUT_APPROUVEE => 'Approuvée',
            self::STATUT_REFUSEE => 'Refusée',
            self::STATUT_INTERVENTION => 'Intervention en cours',
            self::STATUT_FACTURE_RECUE => 'Facture reçue',
            self::STATUT_A_PAYER => 'À payer',
            self::STATUT_PAYEE => 'Payée',
            self::STATUT_CLOTUREE => 'Clôturée',
            default => $this->statut,
        };
    }
}
