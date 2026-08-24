<?php

namespace App\Contracts;

use Illuminate\Support\Collection;

/**
 * Contrat commun à tous les événements métier de l'application (paiement reçu, nouveau
 * contrat, loyer en retard...). Le fait de dispatcher un event() implémentant cette interface
 * déclenche automatiquement une notification vers les bons destinataires, via l'unique
 * listener EnvoyerNotificationMetier — jamais de notification codée en dur dans un contrôleur.
 */
interface EvenementMetier
{
    /**
     * @return array{type: string, titre: string, message: string, icone: string, couleur: string, lien: string|null}
     */
    public function payload(): array;

    /**
     * @return Collection<int, \App\Models\User>
     */
    public function destinataires(): Collection;
}
