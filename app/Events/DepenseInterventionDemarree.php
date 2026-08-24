<?php

namespace App\Events;

use App\Contracts\EvenementMetier;
use App\Models\DepenseLocation;
use App\Models\User;
use Illuminate\Support\Collection;

class DepenseInterventionDemarree implements EvenementMetier
{
    public function __construct(public DepenseLocation $depense) {}

    public function payload(): array
    {
        return [
            'type' => 'intervention_demarree',
            'titre' => 'Intervention nécessaire',
            'message' => 'Intervention démarrée pour la dépense '.$this->depense->numero.' sur '.($this->depense->bien->titre ?? 'un bien').'.',
            'icone' => 'bi-wrench-adjustable',
            'couleur' => 'info',
            'lien' => route('finance.depenses.show', $this->depense),
        ];
    }

    public function destinataires(): Collection
    {
        return User::direction();
    }
}
