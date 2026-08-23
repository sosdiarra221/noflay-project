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
                            <p class="text-muted fs-12 mb-3">{{ $contrat->bien->categorie->nom ?? '—' }} — {{ $contrat->numero }}</p>
                            <ul class="list-unstyled mb-3 fs-13">
                                <li class="d-flex justify-content-between border-bottom py-2">
                                    <span class="text-muted">Loyer mensuel</span>
                                    <span class="fw-medium">{{ number_format($contrat->loyer_mensuel, 0, ',', ' ') }} FCFA</span>
                                </li>
                                <li class="d-flex justify-content-between border-bottom py-2">
                                    <span class="text-muted">Période</span>
                                    <span class="fw-medium">{{ $contrat->date_debut->format('d/m/Y') }}@if ($contrat->date_fin) → {{ $contrat->date_fin->format('d/m/Y') }} @endif</span>
                                </li>
                                <li class="d-flex justify-content-between py-2">
                                    <span class="text-muted">Échéances</span>
                                    <span class="fw-medium">{{ $contrat->echeances->count() }}</span>
                                </li>
                            </ul>
                            <div class="d-flex gap-2">
                                <a href="{{ route('locative.contrats.show', $contrat) }}" class="btn btn-light-success btn-sm flex-fill"><i class="bi bi-eye me-1"></i>Ouvrir</a>
                                <a href="{{ route('locative.contrats.pdf', $contrat) }}" class="btn btn-light-info btn-sm"><i class="bi bi-file-earmark-pdf"></i></a>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

    </div>
    </main>
@endsection

@section('js')
    <script type="module" src="{{ asset('assets/js/app.js') }}"></script>
@endsection
