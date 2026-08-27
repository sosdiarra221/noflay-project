@php
    $lignes = match ($type) {
        'paiement' => [
            'Numéro' => $enregistrement->numero,
            'Type' => $enregistrement->type === 'entree' ? "Paiement d'entrée" : 'Paiement de loyer',
            'Contrat' => $enregistrement->contratLocation?->numero ?? '—',
            'Bien' => $enregistrement->contratLocation?->bien?->titre ?? '—',
            'Bailleur' => $enregistrement->contratLocation?->bailleur?->nom_complet ?? '—',
            'Montant' => number_format($enregistrement->montant, 0, ',', ' ').' FCFA',
            'Mode de paiement' => $enregistrement->modePaiement?->nom ?? '—',
            'Date' => $enregistrement->date_paiement?->format('d/m/Y') ?? '—',
            'Référence' => $enregistrement->reference ?: '—',
        ],
        'facture' => [
            'Numéro' => $enregistrement->numero,
            'Client' => $enregistrement->client?->nom_complet ?? '—',
            'Montant TTC' => number_format($enregistrement->total_ttc, 0, ',', ' ').' FCFA',
            'Statut' => $enregistrement->libelleStatut(),
            'Date facture' => $enregistrement->date_facture?->format('d/m/Y') ?? '—',
        ],
        'depense' => [
            'Numéro' => $enregistrement->numero,
            'Description' => $enregistrement->description,
            'Bien' => $enregistrement->bien?->titre ?? '—',
            'Bailleur' => $enregistrement->bailleur?->nom_complet ?? '—',
            'Catégorie' => $enregistrement->categorie?->nom ?? '—',
            'Qui supporte' => ucfirst($enregistrement->qui_supporte),
            'Montant' => number_format($enregistrement->montantImpute(), 0, ',', ' ').' FCFA',
            'Mode de paiement' => $enregistrement->modePaiement?->nom ?? '—',
            'Date de paiement' => $enregistrement->date_paiement?->format('d/m/Y') ?? '—',
        ],
        'caution' => [
            'Contrat' => $enregistrement->contratLocation?->numero ?? '—',
            'Bien' => $enregistrement->contratLocation?->bien?->titre ?? '—',
            'Locataire' => $enregistrement->contratLocation?->location?->locataire?->nom_complet ?? '—',
            'Montant total' => number_format($enregistrement->montant_total, 0, ',', ' ').' FCFA',
            'Montant restitué' => number_format($enregistrement->montant_restitue, 0, ',', ' ').' FCFA',
            'Statut' => ucfirst(str_replace('_', ' ', $enregistrement->statut)),
            'Date de restitution' => $enregistrement->date_restitution?->format('d/m/Y') ?? '—',
        ],
        'versement_bailleur' => [
            'Numéro' => $enregistrement->numero,
            'Bailleur' => $enregistrement->bailleur?->nom_complet ?? '—',
            'Type' => ucfirst($enregistrement->type),
            'Montant' => number_format($enregistrement->montant, 0, ',', ' ').' FCFA',
            'Mode de paiement' => $enregistrement->modePaiement?->nom ?? '—',
            'Date' => $enregistrement->date_versement?->format('d/m/Y') ?? '—',
            'Référence' => $enregistrement->reference ?: '—',
        ],
        'reversement_bailleur' => [
            'Numéro' => $enregistrement->numero,
            'Bailleur' => $enregistrement->bailleur?->nom_complet ?? '—',
            'Période' => str_pad($enregistrement->periode_mois, 2, '0', STR_PAD_LEFT).'/'.$enregistrement->periode_annee,
            'Montant encaissé' => number_format($enregistrement->montant_encaisse, 0, ',', ' ').' FCFA',
            'Frais de gestion' => number_format($enregistrement->montant_frais_gestion, 0, ',', ' ').' FCFA',
            'Montant net versé' => number_format($enregistrement->montant_net, 0, ',', ' ').' FCFA',
            'Date de versement' => $enregistrement->date_versement?->format('d/m/Y') ?? '—',
        ],
        default => [],
    };
@endphp

<dl class="row mb-0 fs-14">
    @foreach ($lignes as $label => $valeur)
        <dt class="col-5 text-muted">{{ $label }}</dt>
        <dd class="col-7">{{ $valeur }}</dd>
    @endforeach
</dl>
