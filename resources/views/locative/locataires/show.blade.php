@extends('partials.layouts.master-locative')

@section('title', $locataire->nom_complet.' | Locative')
@section('title-sub', 'Locataires')
@section('pagetitle', $locataire->nom_complet)

@section('css')
    <link rel="stylesheet" href="{{ asset('assets/libs/choices.js/public/assets/styles/choices.min.css') }}">
@endsection

@section('content')
    <div id="layout-wrapper">

        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
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
                                <i class="bi bi-person"></i>
                            </div>
                        </div>
                        <div class="flex-grow-1">
                            <div class="mb-2">
                                <h5 class="mb-1">{{ $locataire->nom_complet }}
                                    @if ($locataire->statut === 'actif')
                                        <span class="badge bg-success-subtle text-success ms-1">Locataire actif</span>
                                    @else
                                        <span class="badge bg-light-subtle text-body ms-1">Inactif</span>
                                    @endif
                                    @if ($stats['profil'] === 'bon')
                                        <span class="badge bg-success-subtle text-success ms-1"><i class="bi bi-patch-check me-1"></i>Bon payeur</span>
                                    @elseif ($stats['profil'] === 'a_surveiller')
                                        <span class="badge bg-warning-subtle text-warning ms-1"><i class="bi bi-exclamation-triangle me-1"></i>À surveiller</span>
                                    @else
                                        <span class="badge bg-danger-subtle text-danger ms-1"><i class="bi bi-exclamation-octagon me-1"></i>Mauvais payeur</span>
                                    @endif
                                </h5>
                                <p class="text-muted fs-12 mb-0">{{ $locataire->numero }} — {{ ucfirst($locataire->type_locataire) }}</p>
                            </div>
                            <div class="d-flex flex-wrap gap-3">
                                <span class="badge bg-secondary-subtle text-secondary fs-13 px-3 py-2">{{ $contrats->count() }} Contrat(s)</span>
                                <span class="badge bg-secondary-subtle text-secondary fs-13 px-3 py-2">{{ $contratsActifs->count() }} Actif(s)</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        @php $cautionsDues = $cautions->where('statut', '!=', 'restituee')->values(); @endphp
        @if ($cautionsDues->count() > 0)
            <div id="carouselBullesLocataire" class="carousel slide mb-4" @if ($cautionsDues->count() > 1) data-bs-ride="carousel" data-bs-interval="4500" @endif>
                <div class="carousel-inner">
                    @foreach ($cautionsDues as $index => $caution)
                        <div class="carousel-item {{ $index === 0 ? 'active' : '' }}">
                            <div class="alert alert-info d-flex align-items-center gap-3 mb-0 {{ $cautionsDues->count() > 1 ? 'px-6' : '' }}">
                                <button type="button" class="btn btn-info text-white rounded-circle flex-shrink-0 p-0 d-flex align-items-center justify-content-center" style="width: 48px; height: 48px;" data-bs-toggle="modal" data-bs-target="#bulleCautionModal{{ $caution->id }}" title="Aperçu de la caution à restituer">
                                    <i class="bi bi-cash-stack fs-4"></i>
                                </button>
                                <div>
                                    <h6 class="mb-1">L'agence doit {{ number_format($caution->montantARestituer(), 0, ',', ' ') }} FCFA à ce locataire</h6>
                                    <p class="mb-0 fs-13">Caution/garantie — {{ $caution->contratLocation->bien->titre ?? '—' }}. Cliquez sur l'icône pour l'aperçu.</p>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
                @if ($cautionsDues->count() > 1)
                    <button class="carousel-control-prev" type="button" data-bs-target="#carouselBullesLocataire" data-bs-slide="prev">
                        <span class="carousel-control-prev-icon" aria-hidden="true" style="filter: invert(1) grayscale(1);"></span>
                    </button>
                    <button class="carousel-control-next" type="button" data-bs-target="#carouselBullesLocataire" data-bs-slide="next">
                        <span class="carousel-control-next-icon" aria-hidden="true" style="filter: invert(1) grayscale(1);"></span>
                    </button>
                @endif
            </div>
        @endif

        <div class="row g-3 mb-1">
            <div class="col-md-6 col-xl-3">
                <div class="card border">
                    <div class="card-body">
                        <div class="d-flex gap-4 border-bottom pb-5 mb-5">
                            <div class="h-50px w-50px bg-primary text-white d-flex align-items-center justify-content-center rounded fs-3">
                                <i class="bi bi-cash-coin"></i>
                            </div>
                            <div>
                                <h5 class="mb-2">{{ number_format($stats['total_du'], 0, ',', ' ') }}</h5>
                                <p class="text-muted mb-0 fs-12">Total loyers dus (FCFA)</p>
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
                            <div class="h-50px w-50px bg-danger text-white d-flex align-items-center justify-content-center rounded fs-3">
                                <i class="bi bi-exclamation-circle"></i>
                            </div>
                            <div>
                                <h5 class="mb-2">{{ number_format($stats['arrieres'], 0, ',', ' ') }}</h5>
                                <p class="text-muted mb-0 fs-12">Arriérés dus à l'agence (FCFA)</p>
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
                                <i class="bi bi-graph-up"></i>
                            </div>
                            <div>
                                <h5 class="mb-2">{{ $stats['taux_paiement'] }}%</h5>
                                <p class="text-muted mb-0 fs-12">Échéances payées à échéance</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-12">
                <div class="mb-6">
                    <ul class="nav nav-pills" id="locataireTab" role="tablist">
                        <li class="nav-item" role="presentation">
                            <button class="nav-link active" data-bs-toggle="tab" data-bs-target="#infos-tab-pane" type="button">Vue générale</button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" data-bs-toggle="tab" data-bs-target="#contrats-tab-pane" type="button">Locations &amp; contrats</button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" data-bs-toggle="tab" data-bs-target="#loyers-tab-pane" type="button">Loyers</button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" data-bs-toggle="tab" data-bs-target="#paiements-tab-pane" type="button">Paiements</button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" data-bs-toggle="tab" data-bs-target="#fiches-tab-pane" type="button">Fiches financières</button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" data-bs-toggle="tab" data-bs-target="#caution-tab-pane" type="button">
                                Caution &amp; dépenses
                                @if ($montantDuAuLocataire > 0)
                                    <span class="badge bg-info-subtle text-info ms-1">{{ number_format($montantDuAuLocataire, 0, ',', ' ') }} FCFA</span>
                                @endif
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" data-bs-toggle="tab" data-bs-target="#documents-tab-pane" type="button">Documents</button>
                        </li>
                    </ul>
                </div>

                <div class="tab-content">
                    {{-- Vue générale --}}
                    <div class="tab-pane fade show active" id="infos-tab-pane">
                        <div class="row g-4">
                            <div class="col-lg-6">
                                <div class="card">
                                    <div class="card-header"><h6 class="card-action-title mb-0">Coordonnées</h6></div>
                                    <div class="card-body">
                                        <div class="row mb-3"><div class="col-4 text-muted">Téléphone</div><div class="col-8 fw-medium">{{ $locataire->telephone ?: '—' }}</div></div>
                                        <div class="row mb-3"><div class="col-4 text-muted">WhatsApp</div><div class="col-8 fw-medium">{{ $locataire->whatsapp ?: '—' }}</div></div>
                                        <div class="row mb-3"><div class="col-4 text-muted">Email</div><div class="col-8 fw-medium">{{ $locataire->email ?: '—' }}</div></div>
                                        <div class="row"><div class="col-4 text-muted">Adresse</div><div class="col-8 fw-medium">{{ $locataire->adresse ?: '—' }}</div></div>
                                    </div>
                                </div>
                                <div class="card">
                                    <div class="card-header"><h6 class="card-action-title mb-0">Identité</h6></div>
                                    <div class="card-body">
                                        <div class="row"><div class="col-4 text-muted">Pièce d'identité</div><div class="col-8 fw-medium">{{ $locataire->piece_identite_type ?: '—' }} {{ $locataire->piece_identite_numero }}</div></div>
                                    </div>
                                </div>
                                @if ($locataire->notes)
                                    <div class="card">
                                        <div class="card-header"><h6 class="card-action-title mb-0">Notes</h6></div>
                                        <div class="card-body"><p class="mb-0">{{ $locataire->notes }}</p></div>
                                    </div>
                                @endif
                            </div>
                            <div class="col-lg-6">
                                <div class="card">
                                    <div class="card-header"><h6 class="card-action-title mb-0">Loyers attendus vs payés — 12 derniers mois</h6></div>
                                    <div class="card-body">
                                        <div id="chartTendanceLocataire"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Locations & contrats --}}
                    <div class="tab-pane fade" id="contrats-tab-pane">
                        <div class="card">
                            <div class="card-body p-0">
                                <div class="table-box table-responsive">
                                    <table class="table text-nowrap align-middle mb-0">
                                        <thead><tr><th>Numéro</th><th>Bien</th><th>Période</th><th>Loyer mensuel</th><th>Statut</th><th></th></tr></thead>
                                        <tbody>
                                            @forelse ($contrats as $contrat)
                                                <tr>
                                                    <td>{{ $contrat->numero }}</td>
                                                    <td>{{ $contrat->bien->titre ?? '—' }}</td>
                                                    <td>{{ $contrat->date_debut->format('d/m/Y') }} @if ($contrat->date_fin) → {{ $contrat->date_fin->format('d/m/Y') }} @endif</td>
                                                    <td>{{ number_format($contrat->loyer_mensuel, 0, ',', ' ') }} FCFA</td>
                                                    <td><span class="badge bg-secondary-subtle text-secondary text-capitalize">{{ str_replace('_', ' ', $contrat->statut) }}</span></td>
                                                    <td><a href="{{ route('locative.contrats.show', $contrat) }}" class="btn btn-light-success icon-btn-sm"><i class="bi bi-eye"></i></a></td>
                                                </tr>
                                            @empty
                                                <tr><td colspan="6" class="text-center text-muted py-5">Aucun contrat pour ce locataire.</td></tr>
                                            @endforelse
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Loyers --}}
                    <div class="tab-pane fade" id="loyers-tab-pane">
                        <div class="card">
                            <div class="card-body p-0">
                                <div class="table-box table-responsive">
                                    <table class="table text-nowrap align-middle mb-0">
                                        <thead><tr><th>Payé</th><th>Période</th><th>Bien</th><th>Attendu</th><th>Payé</th><th>Statut</th></tr></thead>
                                        <tbody>
                                            @php $classesE = ['paye' => 'success', 'partiellement_paye' => 'warning', 'en_retard' => 'danger', 'a_venir' => 'secondary', 'echu' => 'danger', 'annule' => 'dark']; @endphp
                                            @forelse ($echeances as $echeance)
                                                <tr>
                                                    <td>
                                                        @if ($echeance->statut === 'paye')
                                                            <span class="text-success"><i class="bi bi-check-circle-fill fs-5"></i></span>
                                                        @elseif ($echeance->statut === 'partiellement_paye')
                                                            <span class="text-warning"><i class="bi bi-dash-circle-fill fs-5"></i></span>
                                                        @else
                                                            <span class="text-danger"><i class="bi bi-x-circle-fill fs-5"></i></span>
                                                        @endif
                                                    </td>
                                                    <td>{{ \Carbon\Carbon::createFromDate($echeance->annee, $echeance->mois, 1)->translatedFormat('F Y') }}</td>
                                                    <td>{{ $echeance->contratLocation->bien->titre ?? '—' }}</td>
                                                    <td>{{ number_format($echeance->montant_attendu, 0, ',', ' ') }} FCFA</td>
                                                    <td>{{ number_format($echeance->montant_paye, 0, ',', ' ') }} FCFA</td>
                                                    <td><span class="badge bg-{{ $classesE[$echeance->statut] ?? 'secondary' }}-subtle text-{{ $classesE[$echeance->statut] ?? 'secondary' }} text-capitalize">{{ str_replace('_', ' ', $echeance->statut) }}</span></td>
                                                </tr>
                                            @empty
                                                <tr><td colspan="6" class="text-center text-muted py-5">Aucune échéance pour ce locataire.</td></tr>
                                            @endforelse
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Paiements --}}
                    <div class="tab-pane fade" id="paiements-tab-pane">
                        <div class="card">
                            <div class="card-body p-0">
                                <div class="table-box table-responsive">
                                    <table class="table text-nowrap align-middle mb-0">
                                        <thead><tr><th>Numéro</th><th>Date</th><th>Bien</th><th>Mode</th><th>Montant</th><th>Statut</th><th></th></tr></thead>
                                        <tbody>
                                            @forelse ($paiements as $paiement)
                                                <tr>
                                                    <td class="fw-medium">{{ $paiement->numero }}</td>
                                                    <td>{{ $paiement->date_paiement->format('d/m/Y') }}</td>
                                                    <td>{{ $paiement->echeance->contratLocation->bien->titre ?? '—' }}</td>
                                                    <td>{{ $paiement->modePaiement->nom ?? '—' }}</td>
                                                    <td>{{ number_format($paiement->montant, 0, ',', ' ') }} FCFA</td>
                                                    <td>
                                                        @if ($paiement->statut === 'annule')
                                                            <span class="badge bg-danger-subtle text-danger">Annulé</span>
                                                        @else
                                                            <span class="badge bg-success-subtle text-success">Valide</span>
                                                        @endif
                                                    </td>
                                                    <td><button type="button" class="btn btn-light-warning icon-btn-sm" data-bs-toggle="modal" data-bs-target="#recuLocModal{{ $paiement->id }}"><i class="bi bi-receipt"></i></button></td>
                                                </tr>
                                            @empty
                                                <tr><td colspan="7" class="text-center text-muted py-5">Aucun paiement pour ce locataire.</td></tr>
                                            @endforelse
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Fiches financières --}}
                    <div class="tab-pane fade" id="fiches-tab-pane">
                        <div class="card">
                            <div class="card-header d-flex justify-content-between align-items-center">
                                <h6 class="mb-0">Fiches proforma de loyer <span class="badge bg-secondary-subtle text-secondary ms-1">{{ $fiches->count() }}</span></h6>
                                @if ($contratsActifs->count() > 0)
                                    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#genererFicheModal">
                                        <i class="bi bi-file-earmark-plus me-1"></i>Générer la fiche du mois
                                    </button>
                                @endif
                            </div>
                            <div class="card-body p-0">
                                <div class="table-box table-responsive">
                                    <table class="table text-nowrap align-middle mb-0">
                                        <thead><tr><th>N° Référence</th><th>Période</th><th>Montant total</th><th>Générée le</th><th>Actions</th></tr></thead>
                                        <tbody>
                                            @forelse ($fiches as $fiche)
                                                <tr>
                                                    <td class="fw-medium">{{ $fiche->numero_reference }}</td>
                                                    <td>{{ ucfirst(\Carbon\Carbon::createFromDate($fiche->annee, $fiche->mois, 1)->translatedFormat('F Y')) }}</td>
                                                    <td>{{ number_format($fiche->montant_total, 0, ',', ' ') }} FCFA</td>
                                                    <td>{{ $fiche->created_at->format('d/m/Y') }}</td>
                                                    <td>
                                                        <button type="button" class="btn btn-light-warning icon-btn-sm" data-bs-toggle="modal" data-bs-target="#ficheModal{{ $fiche->id }}"><i class="bi bi-eye"></i></button>
                                                    </td>
                                                </tr>
                                            @empty
                                                <tr><td colspan="5" class="text-center text-muted py-5">Aucune fiche générée pour ce locataire.</td></tr>
                                            @endforelse
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Caution & dépenses --}}
                    <div class="tab-pane fade" id="caution-tab-pane">
                        <div class="card">
                            <div class="card-header"><h6 class="card-action-title mb-0">Garantie / caution</h6></div>
                            <div class="card-body p-0">
                                <div class="table-box table-responsive">
                                    <table class="table text-nowrap align-middle mb-0">
                                        <thead><tr><th>Bien / location</th><th>Montant reçu</th><th>Retenues</th><th>Motif</th><th>À restituer</th><th>Statut</th></tr></thead>
                                        <tbody>
                                            @forelse ($cautions as $caution)
                                                <tr>
                                                    <td>
                                                        <a href="{{ route('locative.locations.show', $caution->contratLocation->location_id) }}" class="text-reset fw-medium">
                                                            {{ $caution->contratLocation->bien->titre ?? '—' }}
                                                        </a>
                                                    </td>
                                                    <td>{{ number_format($caution->part_bailleur, 0, ',', ' ') }} FCFA</td>
                                                    <td class="{{ $caution->montant_retenu > 0 ? 'text-danger' : '' }}">{{ $caution->montant_retenu > 0 ? number_format($caution->montant_retenu, 0, ',', ' ').' FCFA' : '—' }}</td>
                                                    <td>{{ $caution->motif_retenue ?: '—' }}</td>
                                                    <td class="fw-medium">{{ number_format($caution->montantARestituer(), 0, ',', ' ') }} FCFA</td>
                                                    <td>
                                                        @if ($caution->statut === 'detenue')
                                                            <span class="badge bg-warning-subtle text-warning">Détenue</span>
                                                        @elseif ($caution->statut === 'partiellement_restituee')
                                                            <span class="badge bg-info-subtle text-info">Partiellement restituée</span>
                                                        @else
                                                            <span class="badge bg-success-subtle text-success">Restituée</span>
                                                        @endif
                                                    </td>
                                                </tr>
                                            @empty
                                                <tr><td colspan="6" class="text-center text-muted py-5">Aucune caution enregistrée pour ce locataire.</td></tr>
                                            @endforelse
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>

                        <div class="card">
                            <div class="card-header d-flex justify-content-between align-items-center">
                                <h6 class="card-action-title mb-0">Dépenses à sa charge</h6>
                                <span class="fw-medium text-danger">Total : {{ number_format($depensesLocataire->sum(fn ($d) => $d->montantImpute()), 0, ',', ' ') }} FCFA</span>
                            </div>
                            <div class="card-body p-0">
                                <div class="table-box table-responsive">
                                    <table class="table text-nowrap align-middle mb-0">
                                        <thead><tr><th>Numéro</th><th>Bien / location</th><th>Catégorie</th><th>Montant</th><th>Statut</th></tr></thead>
                                        <tbody>
                                            @forelse ($depensesLocataire as $depense)
                                                <tr>
                                                    <td class="fw-medium">
                                                        @can('finance.consulter')
                                                            <a href="{{ route('finance.depenses.show', $depense) }}" class="text-reset">{{ $depense->numero }}</a>
                                                        @else
                                                            {{ $depense->numero }}
                                                        @endcan
                                                    </td>
                                                    <td>
                                                        @if ($depense->contratLocation)
                                                            <a href="{{ route('locative.locations.show', $depense->contratLocation->location_id) }}" class="text-reset">{{ $depense->bien->titre ?? '—' }}</a>
                                                        @else
                                                            {{ $depense->bien->titre ?? '—' }}
                                                        @endif
                                                    </td>
                                                    <td>{{ $depense->categorie->nom ?? '—' }}</td>
                                                    <td>{{ number_format($depense->montantImpute(), 0, ',', ' ') }} FCFA</td>
                                                    <td>{{ $depense->libelleStatut() }}</td>
                                                </tr>
                                            @empty
                                                <tr><td colspan="5" class="text-center text-muted py-5">Aucune dépense à la charge de ce locataire.</td></tr>
                                            @endforelse
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Documents --}}
                    <div class="tab-pane fade" id="documents-tab-pane">
                        @include('locative.documents._liste', ['documentable' => $locataire, 'typeDocument' => 'locataire'])
                    </div>
                </div>
            </div>
        </div>

    </div>

    @foreach ($cautionsDues as $caution)
        <div class="modal fade" id="bulleCautionModal{{ $caution->id }}" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered" role="document">
                <div class="modal-content">
                    <div class="modal-header bg-info text-white">
                        <h5 class="modal-title">Caution à restituer</h5>
                        <button type="button" class="btn-close btn-close-white icon-btn-sm" data-bs-dismiss="modal" aria-label="Close"><i class="ri-close-large-line fw-semibold"></i></button>
                    </div>
                    <div class="modal-body">
                        <div class="row g-3">
                            <div class="col-6">
                                <p class="text-muted mb-1 fs-12">Bien / location</p>
                                <h6 class="mb-0">{{ $caution->contratLocation->bien->titre ?? '—' }}</h6>
                            </div>
                            <div class="col-6">
                                <p class="text-muted mb-1 fs-12">Montant reçu</p>
                                <h6 class="mb-0">{{ number_format($caution->part_bailleur, 0, ',', ' ') }} FCFA</h6>
                            </div>
                            <div class="col-6">
                                <p class="text-muted mb-1 fs-12">Retenues</p>
                                <h6 class="mb-0 {{ $caution->montant_retenu > 0 ? 'text-danger' : '' }}">{{ $caution->montant_retenu > 0 ? number_format($caution->montant_retenu, 0, ',', ' ').' FCFA' : '—' }}</h6>
                            </div>
                            <div class="col-6">
                                <p class="text-muted mb-1 fs-12">À restituer</p>
                                <h5 class="mb-0 text-success">{{ number_format($caution->montantARestituer(), 0, ',', ' ') }} FCFA</h5>
                            </div>
                            @if ($caution->motif_retenue)
                                <div class="col-12">
                                    <p class="text-muted mb-1 fs-12">Motif de la retenue</p>
                                    <p class="mb-0">{{ $caution->motif_retenue }}</p>
                                </div>
                            @endif
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Fermer</button>
                        <a href="{{ route('locative.locations.show', $caution->contratLocation->location_id) }}" class="btn btn-primary">Voir la location</a>
                    </div>
                </div>
            </div>
        </div>
    @endforeach

    @foreach ($paiements as $paiement)
        <div class="modal fade" id="recuLocModal{{ $paiement->id }}" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Reçu de paiement — {{ $paiement->numero }}</h5>
                        <button type="button" class="btn-close icon-btn-sm" data-bs-dismiss="modal" aria-label="Close"><i class="ri-close-large-line fw-semibold"></i></button>
                    </div>
                    <div class="modal-body p-0">
                        <iframe src="{{ route('locative.paiements.apercu', $paiement) }}" style="width: 100%; height: 70vh; border: 0;"></iframe>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Fermer</button>
                        <a href="{{ route('locative.paiements.recu', $paiement) }}" class="btn btn-primary"><i class="bi bi-download me-1"></i>Télécharger en PDF</a>
                    </div>
                </div>
            </div>
        </div>
    @endforeach

    @foreach ($fiches as $fiche)
        <div class="modal fade" id="ficheModal{{ $fiche->id }}" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Fiche proforma — {{ $fiche->numero_reference }}</h5>
                        <button type="button" class="btn-close icon-btn-sm" data-bs-dismiss="modal" aria-label="Close"><i class="ri-close-large-line fw-semibold"></i></button>
                    </div>
                    <div class="modal-body p-0">
                        <iframe src="{{ route('locative.fiches-locatives.apercu', $fiche) }}" style="width: 100%; height: 70vh; border: 0;"></iframe>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Fermer</button>
                        <a href="{{ route('locative.fiches-locatives.telecharger', $fiche) }}" class="btn btn-primary"><i class="bi bi-download me-1"></i>Télécharger en PDF</a>
                    </div>
                </div>
            </div>
        </div>
    @endforeach

    @if ($contratsActifs->count() > 0)
        <div class="modal fade" id="genererFicheModal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered" role="document">
                <div class="modal-content">
                    <form action="{{ route('locative.fiches-locatives.store') }}" method="POST">
                        @csrf
                        <div class="modal-header">
                            <h5 class="modal-title">Générer la fiche proforma de loyer</h5>
                            <button type="button" class="btn-close icon-btn-sm" data-bs-dismiss="modal" aria-label="Close"><i class="ri-close-large-line fw-semibold"></i></button>
                        </div>
                        <div class="modal-body">
                            <div class="row g-3">
                                @if ($contratsActifs->count() > 1)
                                    <div class="col-12">
                                        <label class="form-label">Contrat concerné<span class="text-danger ms-1">*</span></label>
                                        <select class="form-select" id="selectContratFiche" name="contrat_location_id" required>
                                            @foreach ($contratsActifs as $contrat)
                                                <option value="{{ $contrat->id }}"
                                                    data-tva="{{ $contrat->appliquer_tva ? 1 : 0 }}"
                                                    data-tom="{{ $contrat->appliquer_tom ? 1 : 0 }}">
                                                    {{ $contrat->bien->titre ?? $contrat->numero }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                @else
                                    <input type="hidden" name="contrat_location_id" value="{{ $contratsActifs->first()->id }}">
                                    <input type="hidden" id="contratFicheUniqueTva" value="{{ $contratsActifs->first()->appliquer_tva ? 1 : 0 }}">
                                    <input type="hidden" id="contratFicheUniqueTom" value="{{ $contratsActifs->first()->appliquer_tom ? 1 : 0 }}">
                                @endif
                                <div class="col-6">
                                    <label class="form-label">Mois<span class="text-danger ms-1">*</span></label>
                                    <select class="form-select" name="mois" required>
                                        @for ($mois = 1; $mois <= 12; $mois++)
                                            <option value="{{ $mois }}" @selected($mois === now()->month)>{{ ucfirst(\Carbon\Carbon::createFromDate(2026, $mois, 1)->translatedFormat('F')) }}</option>
                                        @endfor
                                    </select>
                                </div>
                                <div class="col-6">
                                    <label class="form-label">Année<span class="text-danger ms-1">*</span></label>
                                    <select class="form-select" name="annee" required>
                                        @for ($annee = now()->year - 1; $annee <= now()->year + 1; $annee++)
                                            <option value="{{ $annee }}" @selected($annee === now()->year)>{{ $annee }}</option>
                                        @endfor
                                    </select>
                                </div>
                                <div class="col-4">
                                    <label class="form-label">Frais d'agence</label>
                                    <input type="number" step="0.01" min="0" class="form-control" name="frais_agence" value="0">
                                </div>
                                <div class="col-4">
                                    <label class="form-label">TOM (%)</label>
                                    <input type="number" step="0.01" min="0" max="100" class="form-control" id="champTauxTom" name="taux_tom" value="{{ $reglage->taux_tom_defaut }}">
                                    <div class="fs-11 text-muted" id="noteTom"></div>
                                </div>
                                <div class="col-4">
                                    <label class="form-label">TVA (%)</label>
                                    <input type="number" step="0.01" min="0" max="100" class="form-control" id="champTauxTva" name="taux_tva" value="{{ $reglage->taux_tva_defaut }}">
                                    <div class="fs-11 text-muted" id="noteTva"></div>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-light" data-bs-dismiss="modal">Fermer</button>
                            <button type="submit" class="btn btn-primary">Générer</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endif

    </main>
@endsection

@section('js')
    <script src="{{ asset('assets/libs/apexcharts/apexcharts.min.js') }}"></script>
    <script type="module" src="{{ asset('assets/js/app.js') }}"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const defautTva = {{ (float) $reglage->taux_tva_defaut }};
            const defautTom = {{ (float) $reglage->taux_tom_defaut }};
            const champTva = document.getElementById('champTauxTva');
            const champTom = document.getElementById('champTauxTom');
            const noteTva = document.getElementById('noteTva');
            const noteTom = document.getElementById('noteTom');
            const selectContrat = document.getElementById('selectContratFiche');

            function appliquerFlags(actifTva, actifTom) {
                if (! champTva || ! champTom) return;
                champTva.value = actifTva ? defautTva : 0;
                champTva.readOnly = ! actifTva;
                noteTva.textContent = actifTva ? '' : "TVA non activée pour ce contrat.";
                champTom.value = actifTom ? defautTom : 0;
                champTom.readOnly = ! actifTom;
                noteTom.textContent = actifTom ? '' : "TOM non activée pour ce contrat.";
            }

            if (selectContrat) {
                selectContrat.addEventListener('change', function () {
                    const option = selectContrat.options[selectContrat.selectedIndex];
                    appliquerFlags(option.dataset.tva === '1', option.dataset.tom === '1');
                });
                selectContrat.dispatchEvent(new Event('change'));
            } else {
                const uniqueTva = document.getElementById('contratFicheUniqueTva');
                const uniqueTom = document.getElementById('contratFicheUniqueTom');
                if (uniqueTva && uniqueTom) {
                    appliquerFlags(uniqueTva.value === '1', uniqueTom.value === '1');
                }
            }

            const tendanceData = {!! json_encode([
                'categories' => $tendance->pluck('libelle'),
                'attendu' => $tendance->pluck('attendu'),
                'paye' => $tendance->pluck('paye'),
            ]) !!};

            new ApexCharts(document.querySelector('#chartTendanceLocataire'), {
                chart: { type: 'bar', height: 300, toolbar: { show: false } },
                series: [
                    { name: 'Attendu', data: tendanceData.attendu },
                    { name: 'Payé', data: tendanceData.paye },
                ],
                xaxis: { categories: tendanceData.categories },
                colors: ['#f7b84b', '#0ab39c'],
                plotOptions: { bar: { borderRadius: 4, columnWidth: '55%' } },
                dataLabels: { enabled: false },
                legend: { position: 'top' },
            }).render();
        });
    </script>
@endsection
