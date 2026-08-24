<?php

namespace App\Events;

use App\Contracts\EvenementMetier;
use App\Models\DepenseLocation;
use App\Models\User;
use Illuminate\Support\Collection;

class DepenseCreee implements EvenementMetier
{
    public function __construct(public DepenseLocation $depense) {}

    public function payload(): array
    {
        return [
            'type' => 'depense_creee',
            'titre' => 'Nouvelle dépense',
            'message' => 'Dépense '.$this->depense->numero.' ('.($this->depense->categorie->nom ?? '').') sur '.($this->depense->bien->titre ?? 'un bien').'.',
            'icone' => 'bi-tools',
            'couleur' => 'warning',
            'lien' => route('finance.depenses.show', $this->depense),
        ];
    }

    public function destinataires(): Collection
    {
        return User::direction();
    }
}
