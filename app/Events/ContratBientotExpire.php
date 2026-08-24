<?php

namespace App\Events;

use App\Contracts\EvenementMetier;
use App\Models\ContratLocation;
use App\Models\User;
use Illuminate\Support\Collection;

class ContratBientotExpire implements EvenementMetier
{
    public function __construct(public ContratLocation $contrat) {}

    public function payload(): array
    {
        $locataire = $this->contrat->location?->locataire?->nom_complet ?? 'Un locataire';

        return [
            'type' => 'contrat_bientot_expire',
            'titre' => 'Contrat à renouveler',
            'message' => 'Le contrat de '.$locataire.' ('.($this->contrat->bien->titre ?? '').') expire le '.$this->contrat->date_fin->format('d/m/Y').'.',
            'icone' => 'bi-calendar-x',
            'couleur' => 'warning',
            'lien' => route('locative.contrats.show', $this->contrat),
        ];
    }

    public function destinataires(): Collection
    {
        return User::direction();
    }
}
