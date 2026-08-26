@extends('partials.layouts.master-rh')

@section('title', 'Tableau de bord | RH')
@section('title-sub', 'RH')
@section('pagetitle', 'Tableau de bord')

@section('content')
    <div id="layout-wrapper">

        <div class="row g-3 mb-1">
            <div class="col-md-6 col-xl-3">
                <div class="card border">
                    <div class="card-body">
                        <div class="d-flex gap-4 border-bottom pb-5 mb-5">
                            <div class="h-50px w-50px bg-primary text-white d-flex align-items-center justify-content-center rounded fs-3">
                                <i class="bi bi-people"></i>
                            </div>
                            <div>
                                <h5 class="mb-2">{{ $stats['total_actifs'] }}</h5>
                                <p class="text-muted mb-0 fs-12">Employés actifs</p>
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
                                <i class="bi bi-file-earmark-check"></i>
                            </div>
                            <div>
                                <h5 class="mb-2">{{ $stats['contrats_actifs'] }}</h5>
                                <p class="text-muted mb-0 fs-12">Contrats actifs</p>
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
                                <i class="bi bi-exclamation-triangle"></i>
                            </div>
                            <div>
                                <h5 class="mb-2">{{ $stats['sans_contrat_actif'] }}</h5>
                                <p class="text-muted mb-0 fs-12">Actifs sans contrat en cours</p>
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
                                <i class="bi bi-box-arrow-right"></i>
                            </div>
                            <div>
                                <h5 class="mb-2">{{ $stats['total_sortis'] }}</h5>
                                <p class="text-muted mb-0 fs-12">Sorties (historique)</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row g-4">
            <div class="col-lg-6">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h6 class="mb-0">Contrats arrivant à échéance <span class="badge bg-warning-subtle text-warning ms-1">{{ $contratsEcheanceProche->count() }}</span></h6>
                        <p class="text-muted fs-11 mb-0">Sous 30 jours</p>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-box table-responsive">
                            <table class="table text-nowrap align-middle mb-0">
                                <thead><tr><th>Employé</th><th>Type</th><th>Échéance</th><th></th></tr></thead>
                                <tbody>
                                    @forelse ($contratsEcheanceProche as $contrat)
                                        <tr>
                                            <td>{{ $contrat->employe->nom_complet }}</td>
                                            <td>{{ $contrat->libelleType() }}</td>
                                            <td>
                                                <span class="badge bg-{{ $contrat->joursAvantEcheance() <= 7 ? 'danger' : 'warning' }}-subtle text-{{ $contrat->joursAvantEcheance() <= 7 ? 'danger' : 'warning' }}">
                                                    {{ $contrat->date_prevu_fin->format('d/m/Y') }} ({{ $contrat->joursAvantEcheance() }} j)
                                                </span>
                                            </td>
                                            <td><a href="{{ route('rh.employes.show', $contrat->employe) }}" class="btn btn-light-success btn-sm">Voir</a></td>
                                        </tr>
                                    @empty
                                        <tr><td colspan="4" class="text-center text-muted py-5">Aucun contrat proche de l'échéance.</td></tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-6">
                <div class="card">
                    <div class="card-header"><h6 class="mb-0">Répartition par département</h6></div>
                    <div class="card-body">
                        @if ($repartitionDepartements->isNotEmpty())
                            <div id="chartRepartitionDepartements"></div>
                        @else
                            <p class="text-center text-muted py-5 mb-0">Aucun employé enregistré.</p>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <div class="row g-4">
            <div class="col-lg-6">
                <div class="card">
                    <div class="card-header"><h6 class="mb-0">Répartition par sexe</h6></div>
                    <div class="card-body">
                        @if ($repartitionSexe->isNotEmpty())
                            <div id="chartRepartitionSexe"></div>
                        @else
                            <p class="text-center text-muted py-5 mb-0">Aucun employé enregistré.</p>
                        @endif
                    </div>
                </div>
            </div>
            <div class="col-lg-6">
                <div class="card">
                    <div class="card-header"><h6 class="mb-0">Répartition par poste</h6></div>
                    <div class="card-body">
                        @if ($repartitionPoste->isNotEmpty())
                            <div id="chartRepartitionPoste"></div>
                        @else
                            <p class="text-center text-muted py-5 mb-0">Aucun employé enregistré.</p>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <div class="row g-3 mb-1">
            <div class="col-12"><h6 class="text-muted fs-13 text-uppercase mb-0">Congés &amp; absences</h6></div>
            <div class="col-md-6 col-xl-3">
                <div class="card border">
                    <div class="card-body">
                        <div class="d-flex gap-4 border-bottom pb-5 mb-5">
                            <div class="h-50px w-50px bg-warning text-white d-flex align-items-center justify-content-center rounded fs-3"><i class="bi bi-hourglass-split"></i></div>
                            <div><h5 class="mb-2">{{ $statsConges['en_attente'] }}</h5><p class="text-muted mb-0 fs-12">Demandes en attente</p></div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-6 col-xl-3">
                <div class="card border">
                    <div class="card-body">
                        <div class="d-flex gap-4 border-bottom pb-5 mb-5">
                            <div class="h-50px w-50px bg-success text-white d-flex align-items-center justify-content-center rounded fs-3"><i class="bi bi-check-circle"></i></div>
                            <div><h5 class="mb-2">{{ $statsConges['validees_mois'] }}</h5><p class="text-muted mb-0 fs-12">Validées ce mois-ci</p></div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-6 col-xl-3">
                <div class="card border">
                    <div class="card-body">
                        <div class="d-flex gap-4 border-bottom pb-5 mb-5">
                            <div class="h-50px w-50px bg-info text-white d-flex align-items-center justify-content-center rounded fs-3"><i class="bi bi-person-walking"></i></div>
                            <div><h5 class="mb-2">{{ $statsConges['en_cours'] }}</h5><p class="text-muted mb-0 fs-12">Actuellement absents</p></div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-6 col-xl-3">
                <div class="card border">
                    <div class="card-body">
                        <div class="d-flex gap-4 border-bottom pb-5 mb-5">
                            <div class="h-50px w-50px bg-secondary text-white d-flex align-items-center justify-content-center rounded fs-3"><i class="bi bi-calendar2-check"></i></div>
                            <div><h5 class="mb-2">{{ $statsConges['solde_moyen'] }} j</h5><p class="text-muted mb-0 fs-12">Solde moyen de congé</p></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row g-4">
            <div class="col-12">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h6 class="mb-0">Employés actuellement absents <span class="badge bg-secondary-subtle text-secondary ms-1">{{ $absencesEnCours->count() }}</span></h6>
                        <a href="{{ route('rh.absences.index') }}" class="btn btn-primary btn-sm">
                            <i class="bi bi-calendar2-week me-1"></i>Congés &amp; Absences
                        </a>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-box table-responsive">
                            <table class="table text-nowrap align-middle mb-0">
                                <thead><tr><th>Employé</th><th>Type</th><th>Retour prévu</th></tr></thead>
                                <tbody>
                                    @forelse ($absencesEnCours as $absence)
                                        <tr>
                                            <td class="fw-medium">{{ $absence->employe->nom_complet ?? '—' }}</td>
                                            <td>{{ $absence->typeAbsence->nom ?? '—' }}</td>
                                            <td>{{ $absence->date_retour->format('d/m/Y') }}</td>
                                        </tr>
                                    @empty
                                        <tr><td colspan="3" class="text-center text-muted py-5">Aucun employé absent actuellement.</td></tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row g-4">
            <div class="col-12">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h6 class="mb-0">Derniers employés ajoutés</h6>
                        <a href="{{ route('rh.employes.create') }}" class="btn btn-primary btn-sm">
                            <i class="bi bi-plus-lg me-1"></i>Nouvel employé
                        </a>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-box table-responsive">
                            <table class="table text-nowrap align-middle mb-0">
                                <thead><tr><th>Matricule</th><th>Nom</th><th>Poste</th><th>Département</th><th></th></tr></thead>
                                <tbody>
                                    @forelse ($derniersEmployes as $employe)
                                        <tr>
                                            <td class="fw-medium">{{ $employe->matricule }}</td>
                                            <td>{{ $employe->nom_complet }}</td>
                                            <td>{{ $employe->poste->nom ?? '—' }}</td>
                                            <td>{{ $employe->departement->nom ?? '—' }}</td>
                                            <td><a href="{{ route('rh.employes.show', $employe) }}" class="btn btn-light-success btn-sm">Voir</a></td>
                                        </tr>
                                    @empty
                                        <tr><td colspan="5" class="text-center text-muted py-5">Aucun employé enregistré.</td></tr>
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
    <script src="{{ asset('assets/libs/apexcharts/apexcharts.min.js') }}"></script>
    <script type="module" src="{{ asset('assets/js/app.js') }}"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const departementsData = {!! json_encode(['categories' => $repartitionDepartements->keys(), 'series' => $repartitionDepartements->values()]) !!};
            if (departementsData.categories.length > 0) {
                new ApexCharts(document.querySelector('#chartRepartitionDepartements'), {
                    chart: { type: 'bar', height: 280, toolbar: { show: false } },
                    series: [{ name: 'Employés', data: departementsData.series }],
                    xaxis: { categories: departementsData.categories },
                    colors: ['#ff7a1a'],
                    plotOptions: { bar: { borderRadius: 4, columnWidth: '45%' } },
                    dataLabels: { enabled: true },
                }).render();
            }

            const sexeData = {!! json_encode(['labels' => $repartitionSexe->keys(), 'series' => $repartitionSexe->values()]) !!};
            if (sexeData.labels.length > 0) {
                new ApexCharts(document.querySelector('#chartRepartitionSexe'), {
                    chart: { type: 'donut', height: 280 },
                    labels: sexeData.labels,
                    series: sexeData.series,
                    colors: ['#0d6efd', '#e83e8c', '#adb5bd'],
                    legend: { position: 'bottom', fontSize: '11px' },
                    dataLabels: { enabled: true },
                }).render();
            }

            const posteData = {!! json_encode(['labels' => $repartitionPoste->keys(), 'series' => $repartitionPoste->values()]) !!};
            if (posteData.labels.length > 0) {
                new ApexCharts(document.querySelector('#chartRepartitionPoste'), {
                    chart: { type: 'donut', height: 280 },
                    labels: posteData.labels,
                    series: posteData.series,
                    colors: ['#0ab39c', '#f7b84b', '#ff7a1a', '#0d6efd', '#e83e8c', '#6f42c1'],
                    legend: { position: 'bottom', fontSize: '11px' },
                    dataLabels: { enabled: true },
                }).render();
            }
        });
    </script>
@endsection
