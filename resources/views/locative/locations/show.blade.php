@extends('partials.layouts.master-locative')

@section('title', $location->numero.' | Locative')
@section('title-sub', 'Locations')
@section('pagetitle', $location->numero)

@section('content')
    <div id="layout-wrapper">

        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        @if (session('notice'))
            <div class="alert alert-warning alert-dismissible fade show" role="alert">
                <i class="bi bi-exclamation-triangle me-1"></i>{{ session('notice') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <div class="card overflow-hidden">
            <div class="card-body h-176px"
                style="background-image: url('{{ asset('assets/images/background.png') }}');background-repeat: no-repeat;background-position: right;">
            </div>
            <div class="mt-2">
                <div class="card-body p-5">
                    <div class="d-flex flex-wrap align-items-start gap-5">
                        <div class="mt-n12 flex-shrink-0">
                            <div class="h-128px w-128px border border-4 border-white shadow-lg bg-primary-subtle text-primary d-flex align-items-center justify-content-center fs-1">
                                <i class="bi bi-house-door"></i>
                            </div>
                        </div>
                        <div class="flex-grow-1">
                            <div class="mb-2">
                                <h5 class="mb-1">{{ $location->numero }}</h5>
                                <p class="text-muted fs-12 mb-0">
                                    Locataire : <a href="{{ route('locative.locataires.show', $location->locataire) }}" class="text-reset fw-medium">{{ $location->locataire->nom_complet }}</a>
                                    @if ($location->locataire->telephone)
                                        — <i class="bi bi-telephone me-1"></i>{{ $location->locataire->telephone }}
                                    @endif
                                    — Créée le {{ $location->created_at->format('d/m/Y') }}
                                </p>
                            </div>
                            <div class="d-flex flex-wrap gap-3">
                                <span class="badge bg-secondary-subtle text-secondary fs-13 px-3 py-2">{{ $location->contrats->count() }} Bien(s) loué(s)</span>
                                @if ($stats['echeances_en_retard'] > 0)
                                    <span class="badge bg-danger-subtle text-danger fs-13 px-3 py-2"><i class="bi bi-exclamation-triangle me-1"></i>{{ $stats['echeances_en_retard'] }} échéance(s) en retard</span>
                                @endif
                            </div>
                        </div>
                        <div class="flex-shrink-0">
                            <a href="{{ route('locative.locataires.show', $location->locataire) }}" class="btn btn-light-primary"><i class="bi bi-person me-1"></i>Dossier du locataire</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row g-3 mb-1">
            <div class="col-md-6 col-xl-3">
                <div class="card border">
                    <div class="card-body">
                        <div class="d-flex gap-4 border-bottom pb-5 mb-5">
                            <div class="h-50px w-50px bg-primary text-white d-flex align-items-center justify-content-center rounded fs-3">
                                <i class="bi bi-building"></i>
                            </div>
                            <div>
                                <h5 class="mb-2">{{ $location->contrats->count() }}</h5>
                                <p class="text-muted mb-0 fs-12">Biens loués</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-6 col-xl-3">
                <div class="card border">
                    <div class="card-body">
                        <div class="d-flex gap-4 border-bottom pb-5 mb-5">
                            <div class="h-50px w-50px bg-info text-white d-flex align-items-center justify-content-center rounded fs-3">
                                <i class="bi bi-cash-coin"></i>
                            </div>
                            <div>
                                <h5 class="mb-2">{{ number_format($stats['loyer_total'], 0, ',', ' ') }}</h5>
                                <p class="text-muted mb-0 fs-12">Loyer total mensuel (FCFA)</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-6 col-xl-3">
                <div class="card border">
                    <div class="card-body">
                        <div class="d-flex gap-4 border-bottom pb-5 mb-5">
                            <div class="h-50px w-50px bg-success text-white d-flex align-items-center justify-content-center rounded fs-3">
                                <i class="bi bi-check-circle"></i>
                            </div>
                            <div>
                                <h5 class="mb-2">{{ number_format($stats['total_paye'], 0, ',', ' ') }}</h5>
                                <p class="text-muted mb-0 fs-12">Total payé (FCFA)</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-6 col-xl-3">
                <div class="card border">
                    <div class="card-body">
                        <div class="d-flex gap-4 border-bottom pb-5 mb-5">
                            <div class="h-50px w-50px bg-secondary text-white d-flex align-items-center justify-content-center rounded fs-3">
                                <i class="bi bi-hourglass-split"></i>
                            </div>
                            <div>
                                <h5 class="mb-2">{{ number_format(max($stats['total_attendu'] - $stats['total_paye'], 0), 0, ',', ' ') }}</h5>
                                <p class="text-muted mb-0 fs-12">Solde restant dû (FCFA)</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-primary-subtle">
                <h6 class="card-action-title mb-0 text-primary"><i class="bi bi-file-earmark-text me-2"></i>Rapport de situation — {{ $location->numero }}</h6>
            </div>
            <div class="card-body" style="max-height: 260px; overflow-y: auto;">
                <p>
                    La location <strong>{{ $location->numero }}</strong> concerne <strong>{{ $location->locataire->nom_complet }}</strong>
                    sur <strong>{{ $location->contrats->count() }}</strong> bien{{ $location->contrats->count() > 1 ? 's' : '' }}
                    ({{ $location->contrats->pluck('bien.titre')->filter()->implode(', ') }})
                    @if ($rapport['date_debut_min'])
                        , loué{{ $location->contrats->count() > 1 ? 's' : '' }} depuis le <strong>{{ $rapport['date_debut_min']->format('d/m/Y') }}</strong>
                    @endif
                    , pour un loyer total de <strong>{{ number_format($stats['loyer_total'], 0, ',', ' ') }} FCFA</strong> par mois.
                </p>
                <p>
                    Sur l'ensemble des échéances émises, <strong>{{ number_format($stats['total_paye'], 0, ',', ' ') }} FCFA</strong> ont été encaissés
                    sur <strong>{{ number_format($stats['total_attendu'], 0, ',', ' ') }} FCFA</strong> attendus
                    @if ($rapport['taux_recouvrement'] !== null)
                        (taux de recouvrement d'environ <strong>{{ $rapport['taux_recouvrement'] }} %</strong>)
                    @endif
                    , soit <strong class="{{ $rapport['solde_restant'] > 0 ? 'text-danger' : 'text-success' }}">{{ number_format($rapport['solde_restant'], 0, ',', ' ') }} FCFA</strong> encore impayé{{ $rapport['solde_restant'] > 0 ? '' : ' (à jour)' }}.
                    Sur {{ $echeances->count() }} échéance{{ $echeances->count() > 1 ? 's' : '' }} au total, <strong>{{ $rapport['echeances_payees'] }}</strong> {{ $rapport['echeances_payees'] > 1 ? 'sont soldées' : 'est soldée' }},
                    <strong>{{ $rapport['echeances_partielles'] }}</strong> partiellement payée{{ $rapport['echeances_partielles'] > 1 ? 's' : '' }}
                    @if ($stats['echeances_en_retard'] > 0)
                        et <strong class="text-danger">{{ $stats['echeances_en_retard'] }}</strong> en retard de paiement
                    @endif
                    .
                </p>
                @if ($rapport['caution_totale_bailleur'] > 0 || $rapport['caution_totale_agence'] > 0)
                    <p>
                        Au titre de la caution / garantie versée à la signature, l'agence détient <strong>{{ number_format($rapport['caution_totale_bailleur'], 0, ',', ' ') }} FCFA</strong>
                        pour le compte du bailleur (restituable en fin de location) et a perçu <strong>{{ number_format($rapport['caution_totale_agence'], 0, ',', ' ') }} FCFA</strong> de frais d'agence à l'entrée
                        @if ($rapport['cautions_restituees'] > 0)
                            ({{ $rapport['cautions_restituees'] }} caution{{ $rapport['cautions_restituees'] > 1 ? 's' : '' }} déjà restituée{{ $rapport['cautions_restituees'] > 1 ? 's' : '' }})
                        @endif
                        .
                    </p>
                @endif
                @if ($rapport['charges_total'] > 0)
                    <p>
                        Les charges locatives (électricité, eau, wifi...) suivies pour cette location représentent <strong>{{ number_format($rapport['charges_total'], 0, ',', ' ') }} FCFA</strong>
                        @if ($rapport['charges_a_payer'] > 0)
                            , dont <strong class="text-warning-emphasis">{{ $rapport['charges_a_payer'] }}</strong> encore à payer
                        @endif
                        .
                    </p>
                @endif
                @can('locative.finances')
                    @if ($rapport['depenses_count'] > 0)
                        <p class="mb-0">
                            Par ailleurs, <strong>{{ $rapport['depenses_count'] }}</strong> dépense{{ $rapport['depenses_count'] > 1 ? 's ont' : ' a' }} été enregistrée{{ $rapport['depenses_count'] > 1 ? 's' : '' }} sur cette location,
                            pour un total payé de <strong>{{ number_format($rapport['depenses_total'], 0, ',', ' ') }} FCFA</strong>.
                        </p>
                    @endif
                @endcan
            </div>
        </div>

        <div class="mb-6">
            <ul class="nav nav-pills" id="locationTab" role="tablist">
                <li class="nav-item" role="presentation">
                    <button class="nav-link active" data-bs-toggle="tab" data-bs-target="#biens-tab-pane" type="button">Biens loués</button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" data-bs-toggle="tab" data-bs-target="#loyers-tab-pane" type="button">Loyers &amp; paiements</button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" data-bs-toggle="tab" data-bs-target="#charges-tab-pane" type="button">
                        Charges <span class="badge bg-secondary-subtle text-secondary ms-1">{{ $charges->count() }}</span>
                    </button>
                </li>
                @can('locative.finances')
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" data-bs-toggle="tab" data-bs-target="#depenses-tab-pane" type="button">
                            Dépenses <span class="badge bg-secondary-subtle text-secondary ms-1">{{ $depenses->count() }}</span>
                        </button>
                    </li>
                @endcan
            </ul>
        </div>

        <div class="tab-content">
        <div class="tab-pane fade show active" id="biens-tab-pane">
        <div class="row g-4">
            @foreach ($location->contrats as $contrat)
                <div class="col-md-6 col-xl-4">
                    <div class="card border h-100">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-start mb-3">
                                <div class="flex-shrink-0 h-40px w-40px bg-primary-subtle d-flex justify-content-center align-items-center rounded text-primary fs-5">
                                    <i class="bi bi-house-door"></i>
                                </div>
                                <span class="badge bg-success-subtle text-success text-capitalize">{{ str_replace('_', ' ', $contrat->statut) }}</span>
                            </div>
                            <h6 class="mb-1">{{ $contrat->bien->titre ?? '—' }}</h6>
                            <p class="text-muted fs-12 mb-2">{{ $contrat->bien->categorie->nom ?? '—' }} — {{ $contrat->numero }}</p>
                            <div class="d-flex flex-wrap gap-1 mb-3">
                                <span class="badge bg-light-subtle text-dark border">{{ $contrat->typeLocationLabel() }}</span>
                                @if ($contrat->appliquer_tva)<span class="badge bg-primary-subtle text-primary">TVA</span>@endif
                                @if ($contrat->appliquer_tom)<span class="badge bg-secondary-subtle text-secondary">TOM</span>@endif
                            </div>
                            <ul class="list-unstyled mb-3 fs-13">
                                <li class="d-flex justify-content-between border-bottom py-2">
                                    <span class="text-muted">Loyer mensuel</span>
                                    <span class="fw-medium">{{ number_format($contrat->loyer_mensuel, 0, ',', ' ') }} FCFA</span>
                                </li>
                                <li class="d-flex justify-content-between border-bottom py-2">
                                    <span class="text-muted">Période</span>
                                    <span class="fw-medium">{{ $contrat->date_debut->format('d/m/Y') }}@if ($contrat->date_fin) → {{ $contrat->date_fin->format('d/m/Y') }} @endif</span>
                                </li>
                                @if ($contrat->caution)
                                    <li class="d-flex justify-content-between border-bottom py-2">
                                        <span class="text-muted">Caution (bailleur)</span>
                                        <span class="fw-medium">{{ number_format($contrat->caution->part_bailleur, 0, ',', ' ') }} FCFA</span>
                                    </li>
                                    <li class="d-flex justify-content-between border-bottom py-2">
                                        <span class="text-muted">Frais d'agence (entrée)</span>
                                        <span class="fw-medium">{{ number_format($contrat->caution->part_agence, 0, ',', ' ') }} FCFA</span>
                                    </li>
                                @endif
                                <li class="d-flex justify-content-between py-2">
                                    <span class="text-muted">Échéances</span>
                                    <span class="fw-medium">{{ $contrat->echeances->count() }}</span>
                                </li>
                            </ul>
                            <div class="d-flex gap-2">
                                <a href="{{ route('locative.contrats.show', $contrat) }}" class="btn btn-light-success btn-sm flex-fill"><i class="bi bi-eye me-1"></i>Ouvrir</a>
                                <button type="button" class="btn btn-light-info btn-sm" data-bs-toggle="modal" data-bs-target="#bailContratModal{{ $contrat->id }}"><i class="bi bi-file-earmark-pdf"></i></button>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
        </div>

        {{-- Loyers & paiements --}}
        <div class="tab-pane fade" id="loyers-tab-pane">
            <div class="card">
                <div class="card-header"><h6 class="mb-0">Historique des loyers <span class="badge bg-secondary-subtle text-secondary ms-1">{{ $echeances->count() }}</span></h6></div>
                <div class="card-body p-0">
                    <div class="table-box table-responsive">
                        <table class="table text-nowrap align-middle mb-0">
                            <thead><tr><th>Période</th><th>Bien</th><th>Attendu</th><th>Payé</th><th>Statut</th></tr></thead>
                            <tbody>
                                @php $classesLoc = ['paye' => 'success', 'partiellement_paye' => 'warning', 'en_retard' => 'danger', 'a_venir' => 'secondary', 'echu' => 'danger', 'annule' => 'dark']; @endphp
                                @forelse ($echeances as $echeance)
                                    <tr>
                                        <td>{{ ucfirst(\Carbon\Carbon::createFromDate($echeance->annee, $echeance->mois, 1)->translatedFormat('F Y')) }}</td>
                                        <td>{{ $echeance->contratLocation->bien->titre ?? '—' }}</td>
                                        <td>{{ number_format($echeance->montant_attendu, 0, ',', ' ') }} FCFA</td>
                                        <td>{{ number_format($echeance->montant_paye, 0, ',', ' ') }} FCFA</td>
                                        <td><span class="badge bg-{{ $classesLoc[$echeance->statut] ?? 'secondary' }}-subtle text-{{ $classesLoc[$echeance->statut] ?? 'secondary' }} text-capitalize">{{ str_replace('_', ' ', $echeance->statut) }}</span></td>
                                    </tr>
                                @empty
                                    <tr><td colspan="5" class="text-center text-muted py-5">Aucune échéance pour cette location.</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <div class="card">
                <div class="card-header"><h6 class="mb-0">Paiements enregistrés <span class="badge bg-secondary-subtle text-secondary ms-1">{{ $paiements->count() }}</span></h6></div>
                <div class="card-body p-0">
                    <div class="table-box table-responsive">
                        <table class="table text-nowrap align-middle mb-0">
                            <thead><tr><th>Numéro</th><th>Date</th><th>Bien</th><th>Mode</th><th>Montant</th><th>Statut</th><th></th></tr></thead>
                            <tbody>
                                @forelse ($paiements as $paiement)
                                    <tr>
                                        <td class="fw-medium">{{ $paiement->numero }}</td>
                                        <td>{{ $paiement->date_paiement->format('d/m/Y') }}</td>
                                        <td>{{ $paiement->contratLocation->bien->titre ?? '—' }}</td>
                                        <td>{{ $paiement->modePaiement->nom ?? '—' }}</td>
                                        <td>{{ number_format($paiement->montant, 0, ',', ' ') }} FCFA</td>
                                        <td>
                                            @if ($paiement->statut === 'annule')
                                                <span class="badge bg-danger-subtle text-danger">Annulé</span>
                                            @else
                                                <span class="badge bg-success-subtle text-success">Valide</span>
                                            @endif
                                        </td>
                                        <td><a href="{{ route('locative.paiements.apercu', $paiement) }}" target="_blank" class="btn btn-light-warning icon-btn-sm" title="Voir le reçu"><i class="bi bi-receipt"></i></a></td>
                                    </tr>
                                @empty
                                    <tr><td colspan="7" class="text-center text-muted py-5">Aucun paiement pour cette location.</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        {{-- Charge location --}}
        <div class="tab-pane fade" id="charges-tab-pane">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="mb-0">Charge location</h6>
                        <p class="text-muted fs-11 mb-0">Électricité, eau, wifi... à la charge du locataire — distinctes du loyer, elles n'entrent jamais dans le calcul des échéances.</p>
                    </div>
                    <button type="button" class="btn btn-primary btn-sm flex-shrink-0" data-bs-toggle="modal" data-bs-target="#ajouterChargeModal">
                        <i class="bi bi-plus-lg me-1"></i>Ajouter une charge
                    </button>
                </div>
                <div class="card-body p-0">
                    <div class="table-box table-responsive">
                        <table class="table text-nowrap align-middle mb-0">
                            <thead><tr><th>Type</th><th>Titre</th><th>Bien</th><th>Montant</th><th>Fréquence</th><th>Réglée par</th><th>Statut</th><th>Actions</th></tr></thead>
                            <tbody>
                                @forelse ($charges as $charge)
                                    <tr>
                                        <td>{{ $charge->libelleType() }}</td>
                                        <td class="fw-medium">{{ $charge->titre }}</td>
                                        <td>{{ $charge->contratLocation->bien->titre ?? '—' }}</td>
                                        <td>{{ number_format($charge->montant, 0, ',', ' ') }} FCFA</td>
                                        <td>{{ $charge->libelleFrequence() }}</td>
                                        <td>
                                            @if ($charge->reglee_par_locataire)
                                                <span class="badge bg-secondary-subtle text-secondary">Locataire</span>
                                            @else
                                                <span class="badge bg-primary-subtle text-primary">Agence</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if ($charge->statut === 'payee')
                                                <span class="badge bg-success-subtle text-success">Payée</span>
                                            @else
                                                <span class="badge bg-warning-subtle text-warning">À payer</span>
                                            @endif
                                        </td>
                                        <td>
                                            <div class="hstack gap-2">
                                                @if ($charge->statut !== 'payee')
                                                    <form action="{{ route('locative.charges.update', $charge) }}" method="POST">
                                                        @csrf
                                                        @method('PUT')
                                                        <input type="hidden" name="statut" value="payee">
                                                        <button type="submit" class="btn btn-light-success icon-btn-sm" title="Marquer comme payée"><i class="bi bi-check-circle"></i></button>
                                                    </form>
                                                @endif
                                                <button type="button" class="btn btn-light-danger icon-btn-sm" data-bs-toggle="modal" data-bs-target="#supprimerChargeModal{{ $charge->id }}" title="Supprimer"><i class="ri-delete-bin-line"></i></button>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr><td colspan="8" class="text-center text-muted py-5">Aucune charge enregistrée pour cette location.</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        {{-- Dépenses --}}
        @can('locative.finances')
            <div class="tab-pane fade" id="depenses-tab-pane">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h6 class="mb-0">Dépenses liées à cette location</h6>
                        <span class="fw-medium text-danger">Total payé : {{ number_format($stats['total_depenses'], 0, ',', ' ') }} FCFA</span>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-box table-responsive">
                            <table class="table text-nowrap align-middle mb-0">
                                <thead><tr><th>Numéro</th><th>Bien</th><th>Catégorie</th><th>Montant</th><th>Qui supporte</th><th>Statut</th></tr></thead>
                                <tbody>
                                    @forelse ($depenses as $depense)
                                        <tr>
                                            <td class="fw-medium">
                                                @can('finance.consulter')
                                                    <a href="{{ route('finance.depenses.show', $depense) }}" class="text-reset">{{ $depense->numero }}</a>
                                                @else
                                                    {{ $depense->numero }}
                                                @endcan
                                            </td>
                                            <td>{{ $depense->bien->titre ?? '—' }}</td>
                                            <td>{{ $depense->categorie->nom ?? '—' }}</td>
                                            <td>{{ number_format($depense->montantImpute(), 0, ',', ' ') }} FCFA</td>
                                            <td><span class="badge bg-secondary-subtle text-secondary text-capitalize">{{ $depense->qui_supporte }}</span></td>
                                            <td>{{ $depense->libelleStatut() }}</td>
                                        </tr>
                                    @empty
                                        <tr><td colspan="6" class="text-center text-muted py-5">Aucune dépense enregistrée pour cette location.</td></tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        @endcan
        </div>

        {{-- Ajouter une charge --}}
        <div class="modal fade" id="ajouterChargeModal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
                <div class="modal-content">
                    <form action="{{ route('locative.charges.store') }}" method="POST">
                        @csrf
                        <div class="modal-header">
                            <h5 class="modal-title">Ajouter une charge</h5>
                            <button type="button" class="btn-close icon-btn-sm" data-bs-dismiss="modal" aria-label="Close"><i class="ri-close-large-line fw-semibold"></i></button>
                        </div>
                        <div class="modal-body">
                            <div class="row g-3">
                                @if ($location->contrats->count() > 1)
                                    <div class="col-12">
                                        <label class="form-label">Bien concerné<span class="text-danger ms-1">*</span></label>
                                        <select class="form-select" name="contrat_location_id" required>
                                            @foreach ($location->contrats as $contrat)
                                                <option value="{{ $contrat->id }}">{{ $contrat->bien->titre ?? $contrat->numero }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                @else
                                    <input type="hidden" name="contrat_location_id" value="{{ $location->contrats->first()->id ?? '' }}">
                                @endif
                                <div class="col-md-6">
                                    <label class="form-label">Type de charge<span class="text-danger ms-1">*</span></label>
                                    <select class="form-select" name="type_charge" required>
                                        @foreach (\App\Models\ChargeLocative::TYPES as $valeur => $libelle)
                                            <option value="{{ $valeur }}">{{ $libelle }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Titre<span class="text-danger ms-1">*</span></label>
                                    <input type="text" class="form-control" name="titre" placeholder="Ex: Facture SENELEC" required>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Montant (FCFA)<span class="text-danger ms-1">*</span></label>
                                    <input type="number" step="0.01" min="0" class="form-control" name="montant" required>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Date<span class="text-danger ms-1">*</span></label>
                                    <input type="date" class="form-control" name="date_charge" value="{{ now()->format('Y-m-d') }}" required>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Fréquence</label>
                                    <select class="form-select" id="selectFrequenceCharge">
                                        <option value="1">Chaque mois</option>
                                        <option value="2">Tous les 2 mois</option>
                                        <option value="3">Tous les 3 mois</option>
                                        <option value="4">Tous les 4 mois</option>
                                        <option value="5">Tous les 5 mois</option>
                                        <option value="personnalise">Période personnalisée...</option>
                                    </select>
                                    <input type="number" min="1" max="36" class="form-control mt-2 d-none" id="champFrequencePersonnalisee" placeholder="Tous les combien de mois ?">
                                    <input type="hidden" name="frequence_mois" id="champFrequenceMois" value="1">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label d-block">Statut initial</label>
                                    <select class="form-select" name="statut">
                                        <option value="a_payer">À payer</option>
                                        <option value="payee">Déjà payée</option>
                                    </select>
                                </div>
                                <div class="col-12">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="reglee_par_locataire" value="1" id="chargeRegleeParLocataire" checked>
                                        <label class="form-check-label" for="chargeRegleeParLocataire">Cette charge est réglée directement par le locataire (hors agence)</label>
                                    </div>
                                </div>
                                <div class="col-12">
                                    <label class="form-label">Description</label>
                                    <textarea class="form-control" name="description" rows="2"></textarea>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-light" data-bs-dismiss="modal">Annuler</button>
                            <button type="submit" class="btn btn-primary">Ajouter la charge</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        @foreach ($charges as $charge)
            <div class="modal fade" id="supprimerChargeModal{{ $charge->id }}" tabindex="-1" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered" role="document">
                    <div class="modal-content">
                        <form action="{{ route('locative.charges.destroy', $charge) }}" method="POST">
                            @csrf
                            @method('DELETE')
                            <div class="modal-header">
                                <h5 class="modal-title">Supprimer la charge « {{ $charge->titre }} »</h5>
                                <button type="button" class="btn-close icon-btn-sm" data-bs-dismiss="modal" aria-label="Close"><i class="ri-close-large-line fw-semibold"></i></button>
                            </div>
                            <div class="modal-body">
                                <label class="form-label">Motif de suppression<span class="text-danger ms-1">*</span></label>
                                <textarea class="form-control" name="motif_suppression" rows="2" required></textarea>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-light" data-bs-dismiss="modal">Annuler</button>
                                <button type="submit" class="btn btn-danger">Confirmer</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        @endforeach

        @foreach ($location->contrats as $contrat)
            <div class="modal fade" id="bailContratModal{{ $contrat->id }}" tabindex="-1" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title">Contrat de bail — {{ $contrat->numero }}</h5>
                            <button type="button" class="btn-close icon-btn-sm" data-bs-dismiss="modal" aria-label="Close"><i class="ri-close-large-line fw-semibold"></i></button>
                        </div>
                        <div class="modal-body p-0">
                            <iframe src="{{ route('locative.contrats.apercu', $contrat) }}" style="width: 100%; height: 70vh; border: 0;"></iframe>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-light" data-bs-dismiss="modal">Fermer</button>
                            <a href="{{ route('locative.contrats.pdf', $contrat) }}" class="btn btn-primary"><i class="bi bi-download me-1"></i>Télécharger en PDF</a>
                        </div>
                    </div>
                </div>
            </div>
        @endforeach

    </div>
    </main>
@endsection

@section('js')
    <script type="module" src="{{ asset('assets/js/app.js') }}"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const selectFrequence = document.getElementById('selectFrequenceCharge');
            const champPersonnalise = document.getElementById('champFrequencePersonnalisee');
            const champFrequenceMois = document.getElementById('champFrequenceMois');
            if (! selectFrequence) return;

            function synchroniser() {
                if (selectFrequence.value === 'personnalise') {
                    champPersonnalise.classList.remove('d-none');
                    champFrequenceMois.value = champPersonnalise.value || 1;
                } else {
                    champPersonnalise.classList.add('d-none');
                    champFrequenceMois.value = selectFrequence.value;
                }
            }

            selectFrequence.addEventListener('change', synchroniser);
            champPersonnalise.addEventListener('input', synchroniser);
            synchroniser();
        });
    </script>
@endsection
