@extends('partials.layouts.master-central')

@section('title', 'Tableau de bord | Espace éditeur')
@section('title-sub', 'Espace éditeur')
@section('pagetitle', 'Tableau de bord')

@section('content')
    <div id="layout-wrapper">

        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <div class="row g-3 mb-1">
            <div class="col-md-6 col-xl-3">
                <div class="card border">
                    <div class="card-body">
                        <div class="d-flex gap-4 border-bottom pb-5 mb-5">
                            <div class="h-50px w-50px bg-primary text-white d-flex align-items-center justify-content-center rounded fs-3">
                                <i class="bi bi-building"></i>
                            </div>
                            <div>
                                <h5 class="mb-2">{{ $stats['total_societes'] }}</h5>
                                <p class="text-muted mb-0 fs-12">Sociétés au total</p>
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
                                <h5 class="mb-2">{{ $stats['societes_actives'] }}</h5>
                                <p class="text-muted mb-0 fs-12">Comptes actifs</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-6 col-xl-3">
                <div class="card border">
                    <div class="card-body">
                        <div class="d-flex gap-4 border-bottom pb-5 mb-5">
                            <div class="h-50px w-50px bg-warning text-white d-flex align-items-center justify-content-center rounded fs-3">
                                <i class="bi bi-hourglass-split"></i>
                            </div>
                            <div>
                                <h5 class="mb-2">{{ $stats['licences_expirant_bientot'] }}</h5>
                                <p class="text-muted mb-0 fs-12">Licences expirant sous 5 jours</p>
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
                                <i class="bi bi-x-circle"></i>
                            </div>
                            <div>
                                <h5 class="mb-2">{{ $stats['licences_expirees'] }}</h5>
                                <p class="text-muted mb-0 fs-12">Licences expirées</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row g-3">
            <div class="col-lg-8">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h6 class="mb-0">Licences à examiner</h6>
                        <a href="{{ route('central.societes.index') }}" class="fs-12">Voir toutes les sociétés</a>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>Société</th>
                                    <th>Package</th>
                                    <th>Expire le</th>
                                    <th>Statut</th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($licencesAExaminer as $licence)
                                    <tr>
                                        <td class="fw-medium">{{ $societes[$licence->tenant_id]->nom_pme ?? $licence->tenant_id }}</td>
                                        <td>{{ $licence->package->nom ?? '—' }}</td>
                                        <td>{{ $licence->date_fin->format('d/m/Y') }}</td>
                                        <td>
                                            @if ($licence->estExpiree())
                                                <span class="badge bg-danger-subtle text-danger">Expirée</span>
                                            @else
                                                <span class="badge bg-warning-subtle text-warning">{{ $licence->joursRestants() }} j. restants</span>
                                            @endif
                                        </td>
                                        <td class="text-end">
                                            <a href="{{ route('central.societes.show', $licence->tenant_id) }}" class="btn btn-sm btn-outline-secondary">Gérer</a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="text-center text-muted py-4">Aucune licence à examiner pour le moment.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <div class="col-lg-4">
                <div class="card">
                    <div class="card-header"><h6 class="mb-0">Résumé</h6></div>
                    <div class="card-body">
                        <dl class="row mb-0 fs-14">
                            <dt class="col-8 text-muted">Comptes suspendus</dt>
                            <dd class="col-4 text-end">{{ $stats['societes_suspendues'] }}</dd>
                            <dt class="col-8 text-muted">Packages actifs</dt>
                            <dd class="col-4 text-end">{{ $stats['packages_actifs'] }}</dd>
                        </dl>
                    </div>
                    <div class="card-footer d-flex gap-2">
                        <a href="{{ route('central.societes.create') }}" class="btn btn-primary btn-sm flex-grow-1">
                            <i class="bi bi-plus-lg me-1"></i>Nouvelle société
                        </a>
                        <a href="{{ route('central.packages.index') }}" class="btn btn-outline-secondary btn-sm flex-grow-1">
                            <i class="bi bi-box-seam me-1"></i>Packages
                        </a>
                    </div>
                </div>
            </div>
        </div>

    </div>
@endsection

@section('js')
    <script type="module" src="{{ asset('assets/js/app.js') }}"></script>
@endsection
