<?php

namespace App\Services\Documents;

/**
 * Registre des types de documents pris en charge par le moteur de modèles.
 * Architecture volontairement extensible : ajouter un nouveau type ne demande que d'ajouter une
 * constante + une entrée dans tous(), sans toucher au reste du moteur (rendu, génération, PDF).
 */
class DocumentType
{
    const LOCATION_HABITATION = 'LOCATION_HABITATION';
    const LOCATION_COMMERCIAL = 'LOCATION_COMMERCIAL';
    const MANDAT_GERANCE = 'MANDAT_GERANCE';

    // Types déclarés pour usage futur — aucune logique de génération n'est requise pour ceux-ci
    // dans cette phase (cf. spécification, types placeholders).
    const ETAT_LIEUX = 'ETAT_LIEUX';
    const QUITTANCE = 'QUITTANCE';
    const RELANCE_LOYER = 'RELANCE_LOYER';

    public static function tous(): array
    {
        return [
            self::LOCATION_HABITATION => [
                'libelle' => "Contrat de location à usage d'habitation",
                'categorie' => 'LOCATION',
            ],
            self::LOCATION_COMMERCIAL => [
                'libelle' => 'Contrat de location à usage commercial',
                'categorie' => 'LOCATION',
            ],
            self::MANDAT_GERANCE => [
                'libelle' => 'Mandat de gérance',
                'categorie' => 'MANDAT',
            ],
            self::ETAT_LIEUX => [
                'libelle' => 'État des lieux',
                'categorie' => 'ETAT_LIEUX',
            ],
            self::QUITTANCE => [
                'libelle' => 'Quittance de loyer',
                'categorie' => 'QUITTANCE',
            ],
            self::RELANCE_LOYER => [
                'libelle' => 'Relance de loyer',
                'categorie' => 'RELANCE',
            ],
        ];
    }

    public static function libelle(string $code): string
    {
        return self::tous()[$code]['libelle'] ?? $code;
    }

    public static function categorie(string $code): ?string
    {
        return self::tous()[$code]['categorie'] ?? null;
    }

    public static function existe(string $code): bool
    {
        return array_key_exists($code, self::tous());
    }
}
