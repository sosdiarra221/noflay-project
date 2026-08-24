<?php

namespace App\Events;

use App\Contracts\EvenementMetier;
use App\Models\Commercial\Prospect;
use App\Models\User;
use Illuminate\Support\Collection;

class ProspectCree implements EvenementMetier
{
    public function __construct(public Prospect $prospect) {}

    public function payload(): array
    {
        return [
            'type' => 'prospect_cree',
            'titre' => 'Nouveau prospect',
            'message' => $this->prospect->nom_complet.' a été ajouté comme prospect.',
            'icone' => 'bi-person-badge',
            'couleur' => 'primary',
            'lien' => route('commercial.prospects.show', $this->prospect),
        ];
    }

    public function destinataires(): Collection
    {
        return User::direction();
    }
}
