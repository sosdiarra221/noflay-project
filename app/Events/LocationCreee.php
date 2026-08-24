<?php

namespace App\Events;

use App\Contracts\EvenementMetier;
use App\Models\Location;
use App\Models\User;
use Illuminate\Support\Collection;

class LocationCreee implements EvenementMetier
{
    public function __construct(public Location $location) {}

    public function payload(): array
    {
        return [
            'type' => 'location_creee',
            'titre' => 'Contrat généré',
            'message' => 'Nouvelle location '.$this->location->numero.' créée pour '.($this->location->locataire->nom_complet ?? 'un locataire').'.',
            'icone' => 'bi-file-earmark-text',
            'couleur' => 'success',
            'lien' => route('locative.locations.show', $this->location),
        ];
    }

    public function destinataires(): Collection
    {
        return User::direction();
    }
}
