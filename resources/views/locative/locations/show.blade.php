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

        <div class="mb-6">
            <ul class="nav nav-pills" id="locationTab" role="tablist">
                <li class="nav-item" role="presentation">
                    <button class="nav-link active" data-bs-toggle="tab" data-bs-target="#biens-tab-pane" type="button">Biens loués</button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" data-bs-toggle="tab" data-bs-target="#loyers-tab-pane" type="button">Loyers &amp; paiements</button>
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
@endsection
