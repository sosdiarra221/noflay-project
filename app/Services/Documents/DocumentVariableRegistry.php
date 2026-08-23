<?php

namespace App\Services\Documents;

use App\Models\Bien;
use App\Models\ContratGerance;
use App\Models\ContratLocation;
use App\Models\Documents\Document;
use App\Models\Reglage;

/**
 * Registre CENTRALISÉ des variables disponibles dans les modèles de documents ({{ locataire.nom }}...).
 *
 * C'est la SEULE source de vérité pour :
 *  - la liste des chemins connus (utilisée par DocumentRenderer pour restreindre la substitution) ;
 *  - le regroupement par entité affiché dans la barre latérale "Variables" de l'éditeur ;
 *  - la résolution réelle d'un chemin vers une valeur, à partir des modèles Eloquent du projet.
 *
 * Aucune autre classe ne doit dupliquer cette logique de correspondance variable → donnée.
 */
class DocumentVariableRegistry
{
    /**
     * Définition groupée par entité, pour la barre latérale de l'éditeur.
     * Chaque entrée : chemin => libellé humain.
     */
    public static function groupes(): array
    {
        return [
            'LOCATAIRE' => [
                'locataire.nom' => 'Nom',
                'locataire.prenom' => 'Prénom',
                'locataire.nom_complet' => 'Nom complet',
                'locataire.telephone' => 'Téléphone',
                'locataire.email' => 'Email',
                'locataire.adresse' => 'Adresse',
                'locataire.piece_identite_type' => "Type de pièce d'identité",
                'locataire.piece_identite_numero' => "Numéro de pièce d'identité",
            ],
            'BAILLEUR' => [
                'bailleur.nom' => 'Nom',
                'bailleur.nom_complet' => 'Nom complet / raison sociale',
                'bailleur.telephone' => 'Téléphone',
                'bailleur.email' => 'Email',
                'bailleur.adresse' => 'Adresse',
                'bailleur.ninea' => 'NINEA',
                'bailleur.rc' => 'RC',
                'bailleur.piece_identite_numero' => "Numéro de pièce d'identité",
            ],
            'AGENCE' => [
                'agence.nom_societe' => 'Nom de la société',
                'agence.adresse' => 'Adresse',
                'agence.telephone' => 'Téléphone',
                'agence.email' => 'Email',
                'agence.ninea' => 'NINEA',
            ],
            'BIEN' => [
                'bien.nom' => 'Désignation',
                'bien.adresse' => 'Adresse',
                'bien.type' => 'Type / catégorie',
                'bien.superficie' => 'Superficie',
            ],
            'LOCATION' => [
                'location.reference' => 'Référence du contrat de location',
                'location.loyer' => 'Loyer mensuel',
                'location.charges' => 'Charges',
                'location.date_debut' => 'Date de début',
                'location.date_fin' => 'Date de fin',
            ],
            'CONTRAT' => [
                'contrat.reference' => 'Référence du document généré',
                'contrat.date' => 'Date de génération',
            ],
            'GERANCE' => [
                'gerance.reference' => 'Référence du mandat de gérance',
                'gerance.frais_gestion_mode' => 'Mode des frais de gestion',
                'gerance.frais_gestion_valeur' => 'Valeur des frais de gestion',
                'gerance.tva_charge' => 'TVA à la charge de',
                'gerance.taxe_charge' => 'Taxe à la charge de',
                'gerance.tom_charge' => 'TOM à la charge de',
                'gerance.date_debut' => 'Date de début',
                'gerance.date_fin' => 'Date de fin',
            ],
        ];
    }

    /**
     * Liste à plat de tous les chemins connus, ex: ['locataire.nom', 'bailleur.nom', ...].
     */
    public static function cheminsConnus(): array
    {
        $chemins = [];
        foreach (self::groupes() as $variables) {
            $chemins = array_merge($chemins, array_keys($variables));
        }

        return $chemins;
    }

    /**
     * Construit le contexte de substitution (chemin => valeur texte) pour un "documentable"
     * (ContratLocation ou ContratGerance à ce stade) et, si disponible, le Document généré
     * lui-même (pour les variables contrat.*).
     *
     * Toutes les clés connues sont TOUJOURS présentes dans le tableau retourné (avec une valeur
     * vide '' si la donnée n'est pas applicable au contexte), afin que DocumentRenderer distingue
     * "variable connue mais vide" (remplacée par du vide) de "chemin inconnu" (laissé tel quel).
     */
    public function contexte($documentable, ?Document $document = null): array
    {
        // On initialise tout à vide, puis on remplit selon le type de $documentable disponible.
        $contexte = array_fill_keys(self::cheminsConnus(), '');

        $agence = Reglage::courant();
        $this->remplirAgence($contexte, $agence);

        if ($documentable instanceof ContratLocation) {
            $this->remplirDepuisContratLocation($contexte, $documentable);
        } elseif ($documentable instanceof ContratGerance) {
            $this->remplirDepuisContratGerance($contexte, $documentable);
        }

        if ($document) {
            $contexte['contrat.reference'] = (string) $document->reference;
            $contexte['contrat.date'] = optional($document->generated_at ?? $document->created_at)->format('d/m/Y') ?? '';
        }

        return $contexte;
    }

    protected function remplirAgence(array &$contexte, Reglage $agence): void
    {
        $contexte['agence.nom_societe'] = (string) ($agence->nom_societe ?? '');
        $contexte['agence.adresse'] = (string) ($agence->adresse ?? '');
        $contexte['agence.telephone'] = (string) ($agence->telephone ?? '');
        $contexte['agence.email'] = (string) ($agence->email ?? '');
        $contexte['agence.ninea'] = (string) ($agence->ninea ?? '');
    }

    protected function remplirDepuisContratLocation(array &$contexte, ContratLocation $contrat): void
    {
        $contrat->loadMissing(['location.locataire', 'bailleur', 'bien.categorie', 'bien.gerance']);

        $locataire = $contrat->location?->locataire;
        if ($locataire) {
            $contexte['locataire.nom'] = (string) $locataire->nom;
            $contexte['locataire.prenom'] = (string) ($locataire->prenom ?? '');
            $contexte['locataire.nom_complet'] = (string) $locataire->nom_complet;
            $contexte['locataire.telephone'] = (string) ($locataire->telephone ?? '');
            $contexte['locataire.email'] = (string) ($locataire->email ?? '');
            $contexte['locataire.adresse'] = (string) ($locataire->adresse ?? '');
            $contexte['locataire.piece_identite_type'] = (string) ($locataire->piece_identite_type ?? '');
            $contexte['locataire.piece_identite_numero'] = (string) ($locataire->piece_identite_numero ?? '');
        }

        $this->remplirBailleur($contexte, $contrat->bailleur);
        $this->remplirBien($contexte, $contrat->bien);

        $contexte['location.reference'] = (string) $contrat->numero;
        $contexte['location.loyer'] = $contrat->loyer_mensuel !== null ? number_format((float) $contrat->loyer_mensuel, 0, ',', ' ').' FCFA' : '';
        $contexte['location.charges'] = $contrat->charges !== null ? number_format((float) $contrat->charges, 0, ',', ' ').' FCFA' : '';
        $contexte['location.date_debut'] = $contrat->date_debut?->format('d/m/Y') ?? '';
        $contexte['location.date_fin'] = $contrat->date_fin?->format('d/m/Y') ?? '';

        if ($contrat->bien && $contrat->bien->gerance) {
            $this->remplirGerance($contexte, $contrat->bien->gerance);
        }
    }

    protected function remplirDepuisContratGerance(array &$contexte, ContratGerance $gerance): void
    {
        $gerance->loadMissing(['bailleur', 'biens.categorie']);

        $this->remplirBailleur($contexte, $gerance->bailleur);
        $this->remplirGerance($contexte, $gerance);

        $premierBien = $gerance->biens->first();
        if ($premierBien) {
            $this->remplirBien($contexte, $premierBien);
        }
    }

    protected function remplirBailleur(array &$contexte, $bailleur): void
    {
        if (! $bailleur) {
            return;
        }

        $contexte['bailleur.nom'] = (string) $bailleur->nom;
        $contexte['bailleur.nom_complet'] = (string) $bailleur->nom_complet;
        $contexte['bailleur.telephone'] = (string) ($bailleur->telephone ?? '');
        $contexte['bailleur.email'] = (string) ($bailleur->email ?? '');
        $contexte['bailleur.adresse'] = (string) ($bailleur->adresse ?? '');
        $contexte['bailleur.ninea'] = (string) ($bailleur->ninea ?? '');
        $contexte['bailleur.rc'] = (string) ($bailleur->rc ?? '');
        $contexte['bailleur.piece_identite_numero'] = (string) ($bailleur->piece_identite_numero ?? '');
    }

    protected function remplirBien(array &$contexte, ?Bien $bien): void
    {
        if (! $bien) {
            return;
        }

        $contexte['bien.nom'] = (string) $bien->titre;
        $contexte['bien.adresse'] = (string) ($bien->adresse ?? '');
        $contexte['bien.type'] = (string) ($bien->categorie->nom ?? '');
        // Aucune colonne "superficie" n'existe sur Bien à ce jour : la variable reste déclarée
        // (disponible dans la barre latérale) mais résolue à vide plutôt que d'inventer une colonne.
        $contexte['bien.superficie'] = '';
    }

    protected function remplirGerance(array &$contexte, ContratGerance $gerance): void
    {
        $contexte['gerance.reference'] = (string) $gerance->numero;
        $contexte['gerance.frais_gestion_mode'] = (string) ($gerance->frais_gestion_mode ?? '');
        $contexte['gerance.frais_gestion_valeur'] = $gerance->frais_gestion_valeur !== null ? (string) $gerance->frais_gestion_valeur : '';
        $contexte['gerance.tva_charge'] = (string) ($gerance->tva_charge ?? '');
        $contexte['gerance.taxe_charge'] = (string) ($gerance->taxe_charge ?? '');
        $contexte['gerance.tom_charge'] = (string) ($gerance->tom_charge ?? '');
        $contexte['gerance.date_debut'] = $gerance->date_debut?->format('d/m/Y') ?? '';
        $contexte['gerance.date_fin'] = $gerance->date_fin?->format('d/m/Y') ?? '';
    }
}
