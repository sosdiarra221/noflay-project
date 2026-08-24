<?php

namespace App\Listeners;

use App\Contracts\EvenementMetier;
use App\Notifications\NotificationMetier;
use Illuminate\Support\Facades\Notification;

/**
 * Unique listener générique pour tous les événements métier de l'application : transforme
 * l'événement en notification et l'envoie à ses destinataires. Ajouter un nouvel événement
 * métier ne demande donc jamais de créer un nouveau listener — seulement une classe
 * implémentant App\Contracts\EvenementMetier.
 */
class EnvoyerNotificationMetier
{
    public function handle(EvenementMetier $evenement): void
    {
        $destinataires = $evenement->destinataires();

        if ($destinataires->isNotEmpty()) {
            Notification::send($destinataires, new NotificationMetier($evenement));
        }
    }
}
