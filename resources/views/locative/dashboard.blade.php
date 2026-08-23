@extends('partials.layouts.master-locative')

@section('title', 'Tableau de bord | Locative')
@section('title-sub', 'Locative')
@section('pagetitle', 'Tableau de bord')

@section('content')
    <div id="layout-wrapper">

        <div class="row">
            <div class="col-md-6 col-xxl-3">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex align-items-start gap-3">
                            <div class="flex-shrink-0 h-40px w-40px bg-primary-subtle d-flex justify-content-center align-items-center rounded-pill text-primary fs-4">
                                <i class="bi bi-person-badge"></i>
                            </div>
                            <div class="flex-grow-1">
                                <h2>{{ $kpis['bailleurs_actifs'] }}</h2>
                                <h6 class="fw-medium mb-0">Bailleurs actifs</h6>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-6 col-xxl-3">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex align-items-start gap-3">
                            <div class="flex-shrink-0 h-40px w-40px bg-secondary-subtle d-flex justify-content-center align-items-center rounded-pill text-secondary fs-4">
                                <i class="bi bi-building"></i>
                            </div>
                            <div class="flex-grow-1">
                                <h2>{{ $kpis['biens_geres'] }}</h2>
                                <h6 class="fw-medium mb-0">Biens gérés</h6>
                                <p class="pt-2 mb-0 text-muted fs-12">{{ $kpis['biens_disponibles'] }} disponibles — {{ $kpis['biens_occupes'] }} occupés</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-6 col-xxl-3">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex align-items-start gap-3">
                            <div class="flex-shrink-0 h-40px w-40px bg-success-subtle d-flex justify-content-center align-items-center rounded-pill text-success fs-4">
                                <i class="bi bi-cash-coin"></i>
                            </div>
                            <div class="flex-grow-1">
                                <h2>{{ number_format($kpis['loyers_encaisses'], 0, ',', ' ') }}</h2>
                                <h6 class="fw-medium mb-0">Loyers encaissés ce mois</h6>
                                <p class="pt-2 mb-0 text-muted fs-12">sur {{ number_format($kpis['loyers_attendus'], 0, ',', ' ') }} FCFA attendus</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-6 col-xxl-3">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex align-items-start gap-3">
                            <div class="flex-shrink-0 h-40px w-40px bg-danger-subtle d-flex justify-content-center align-items-center rounded-pill text-danger fs-4">
                                <i class="bi bi-exclamation-octagon"></i>
                            </div>
                            <div class="flex-grow-1">
                                <h2>{{ number_format($kpis['impayes'], 0, ',', ' ') }}</h2>
                                <h6 class="fw-medium mb-0">Impayés (FCFA)</h6>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-lg-6">
                <div class="card">
                    <div class="card-header">
                        <h6 class="card-title mb-0 fw-semibold text-danger"><i class="bi bi-exclamation-triangle me-1"></i>Loyers en retard</h6>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-box table-responsive">
                            <table class="table text-nowrap align-middle mb-0">
                                <tbody>
                                    @forelse ($alertes['loyers_en_retard'] as $echeance)
                                        <tr>
                                            <td>
                                                <h6 class="mb-0 fw-medium fs-13">{{ $echeance->contratLocation->location->locataire->nom_complet }}</h6>
                                                <span class="text-muted fs-12">{{ $echeance->contratLocation->bien->titre }}</span>
                                            </td>
                                            <td>{{ number_format($echeance->montant_attendu - $echeance->montant_paye, 0, ',', ' ') }} FCFA</td>
                                            <td><a href="{{ route('locative.contrats.show', $echeance->contratLocation) }}" class="btn btn-light-danger icon-btn-sm"><i class="bi bi-eye"></i></a></td>
                                        </tr>
                                    @empty
                                        <tr><td class="text-center text-muted py-4">Aucun loyer en retard.</td></tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-6">
                <div class="card">
                    <div class="card-header">
                        <h6 class="card-title mb-0 fw-semibold text-warning"><i class="bi bi-hourglass-split me-1"></i>Contrats arrivant à expiration (30 jours)</h6>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-box table-responsive">
                            <table class="table text-nowrap align-middle mb-0">
                                <tbody>
                                    @foreach ($alertes['gerances_expirant'] as $gerance)
                                        <tr>
                                            <td>
                                                <h6 class="mb-0 fw-medium fs-13">Gérance {{ $gerance->numero }}</h6>
                                                <span class="text-muted fs-12">{{ $gerance->bailleur->nom_complet }} — expire le {{ $gerance->date_fin->format('d/m/Y') }}</span>
                                            </td>
                                            <td><a href="{{ route('locative.gerances.show', $gerance) }}" class="btn btn-light-warning icon-btn-sm"><i class="bi bi-eye"></i></a></td>
                                        </tr>
                                    @endforeach
                                    @foreach ($alertes['locations_expirant'] as $contrat)
                                        <tr>
                                            <td>
                                                <h6 class="mb-0 fw-medium fs-13">Location {{ $contrat->numero }}</h6>
                                                <span class="text-muted fs-12">{{ $contrat->bien->titre }} — expire le {{ $contrat->date_fin->format('d/m/Y') }}</span>
                                            </td>
                                            <td><a href="{{ route('locative.contrats.show', $contrat) }}" class="btn btn-light-warning icon-btn-sm"><i class="bi bi-eye"></i></a></td>
                                        </tr>
                                    @endforeach
                                    @if ($alertes['gerances_expirant']->isEmpty() && $alertes['locations_expirant']->isEmpty())
                                        <tr><td class="text-center text-muted py-4">Aucun contrat n'expire dans les 30 prochains jours.</td></tr>
                                    @endif
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h6 class="card-title mb-0 fw-semibold text-secondary"><i class="bi bi-clock-history me-1"></i>Biens disponibles depuis plus de 60 jours</h6>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-box table-responsive">
                            <table class="table text-nowrap align-middle mb-0">
                                <tbody>
                                    @forelse ($alertes['biens_vacants'] as $bien)
                                        <tr>
                                            <td>
                                                <h6 class="mb-0 fw-medium fs-13">{{ $bien->titre }}</h6>
                                                <span class="text-muted fs-12">{{ $bien->zone }} — {{ $bien->bailleur->nom_complet }}</span>
                                            </td>
                                            <td>{{ number_format($bien->montantExploitation() ?? 0, 0, ',', ' ') }} FCFA</td>
                                        </tr>
                                    @empty
                                        <tr><td class="text-center text-muted py-4">Aucun bien vacant depuis plus de 60 jours.</td></tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>
    </main>
@endsection

@section('js')
    <script type="module" src="{{ asset('assets/js/app.js') }}"></script>
@endsection
