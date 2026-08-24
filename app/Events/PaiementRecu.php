<?php

namespace App\Events;

use App\Contracts\EvenementMetier;
use App\Models\Paiement;
use App\Models\User;
use Illuminate\Support\Collection;

class PaiementRecu implements EvenementMetier
{
    public function __construct(public Paiement $paiement) {}

    public function payload(): array
    {
        $contrat = $this->paiement->contratLocation ?? $this->paiement->echeance?->contratLocation;
        $locataire = $contrat?->location?->locataire?->nom_complet ?? 'un locataire';

        return [
            'type' => 'paiement_recu',
            'titre' => 'Paiement reçu',
            'message' => $locataire.' a payé '.number_format((float) $this->paiement->montant, 0, ',', ' ').' FCFA.',
            'icone' => 'bi-cash-coin',
            'couleur' => 'success',
            'lien' => route('finance.journal-caisse.index'),
        ];
    }

    public function destinataires(): Collection
    {
        return User::direction();
    }
}
