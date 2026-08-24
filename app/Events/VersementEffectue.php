<?php

namespace App\Events;

use App\Contracts\EvenementMetier;
use App\Models\ReversementBailleur;
use App\Models\User;
use Illuminate\Support\Collection;

class VersementEffectue implements EvenementMetier
{
    public function __construct(public ReversementBailleur $reversement) {}

    public function payload(): array
    {
        return [
            'type' => 'versement_effectue',
            'titre' => 'Versement effectué',
            'message' => number_format((float) $this->reversement->montant_net, 0, ',', ' ').' FCFA reversés à '.($this->reversement->bailleur->nom_complet ?? 'un bailleur').'.',
            'icone' => 'bi-arrow-left-right',
            'couleur' => 'info',
            'lien' => route('finance.reversements.historique'),
        ];
    }

    public function destinataires(): Collection
    {
        return User::direction();
    }
}
