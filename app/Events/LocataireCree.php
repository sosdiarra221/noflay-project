<?php

namespace App\Events;

use App\Contracts\EvenementMetier;
use App\Models\Locataire;
use App\Models\User;
use Illuminate\Support\Collection;

class LocataireCree implements EvenementMetier
{
    public function __construct(public Locataire $locataire) {}

    public function payload(): array
    {
        return [
            'type' => 'locataire_cree',
            'titre' => 'Nouveau locataire',
            'message' => $this->locataire->nom_complet.' a été ajouté comme locataire.',
            'icone' => 'bi-person-plus',
            'couleur' => 'primary',
            'lien' => route('locative.locataires.show', $this->locataire),
        ];
    }

    public function destinataires(): Collection
    {
        return User::direction();
    }
}
