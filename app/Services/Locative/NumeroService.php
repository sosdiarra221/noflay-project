<?php

namespace App\Services\Locative;

class NumeroService
{
    /**
     * Génère un numéro séquentiel du type PREFIXE-ANNEE-000001, unique par préfixe et par année.
     * Doit être appelé à l'intérieur de la même transaction que la création de l'enregistrement.
     */
    public static function genererNumero(string $modelClass, string $prefixe, string $colonne = 'numero', ?int $annee = null): string
    {
        $annee ??= now()->year;

        $utiliseSoftDeletes = method_exists($modelClass, 'scopeWithTrashed');
        $requete = $utiliseSoftDeletes ? $modelClass::withTrashed() : $modelClass::query();

        $compteur = $requete->where($colonne, 'like', "{$prefixe}-{$annee}-%")->count();

        return sprintf('%s-%d-%06d', $prefixe, $annee, $compteur + 1);
    }

    /**
     * Génère le numéro de contrat de location propre à un bien à partir du numéro de la location parente.
     * Ex: LOC-2026-000045 + index 1 => BAIL-2026-000045-01
     */
    public static function genererNumeroBail(string $numeroLocation, int $index): string
    {
        return sprintf('%s-%02d', str_replace('LOC-', 'BAIL-', $numeroLocation), $index);
    }

    /**
     * Génère un numéro séquentiel court du type PREFIXE-001/26 (compteur sur 3 chiffres, année sur
     * 2 chiffres), unique par préfixe et par année — utilisé pour la Facturation (devis/factures) où
     * un numéro plus court que PREFIXE-ANNEE-000001 est préférable.
     */
    public static function genererNumeroCourt(string $modelClass, string $prefixe, string $colonne = 'numero', ?int $annee = null): string
    {
        $annee ??= now()->year;
        $anneeCourte = substr((string) $annee, -2);

        $utiliseSoftDeletes = method_exists($modelClass, 'scopeWithTrashed');
        $requete = $utiliseSoftDeletes ? $modelClass::withTrashed() : $modelClass::query();

        $compteur = $requete->where($colonne, 'like', "{$prefixe}-%/{$anneeCourte}")->count();

        return sprintf('%s-%03d/%s', $prefixe, $compteur + 1, $anneeCourte);
    }
}
