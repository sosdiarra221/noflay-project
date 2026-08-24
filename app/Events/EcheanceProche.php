<?php

namespace App\Events;

use App\Contracts\EvenementMetier;
use App\Models\EcheanceLoyer;
use App\Models\User;
use Illuminate\Support\Collection;

class EcheanceProche implements EvenementMetier
{
    public function __construct(public EcheanceLoyer $echeance) {}

    public function payload(): array
    {
        $locataire = $this->echeance->contratLocation?->location?->locataire?->nom_complet ?? 'Un locataire';

        return [
            'type' => 'echeance_proche',
            'titre' => 'Loyer à payer',
            'message' => "L'échéance de ".$locataire.' arrive le '.$this->echeance->date_echeance->format('d/m/Y').'.',
            'icone' => 'bi-alarm',
            'couleur' => 'warning',
            'lien' => route('locative.echeances.index', ['loyer' => $this->echeance->id]),
        ];
    }

    public function destinataires(): Collection
    {
        return User::direction();
    }
}
