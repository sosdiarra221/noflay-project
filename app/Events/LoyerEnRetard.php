<?php

namespace App\Events;

use App\Contracts\EvenementMetier;
use App\Models\EcheanceLoyer;
use App\Models\User;
use Illuminate\Support\Collection;

class LoyerEnRetard implements EvenementMetier
{
    public function __construct(public EcheanceLoyer $echeance) {}

    public function payload(): array
    {
        $locataire = $this->echeance->contratLocation?->location?->locataire?->nom_complet ?? 'Un locataire';

        return [
            'type' => 'loyer_en_retard',
            'titre' => 'Loyer impayé',
            'message' => 'Le loyer de '.$locataire.' est en retard.',
            'icone' => 'bi-exclamation-triangle',
            'couleur' => 'danger',
            'lien' => route('locative.echeances.index', ['loyer' => $this->echeance->id]),
        ];
    }

    public function destinataires(): Collection
    {
        return User::direction();
    }
}
