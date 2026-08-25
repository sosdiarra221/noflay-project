@extends('partials.layouts.master-rh')

@section('title', $site->nom.' | RH')
@section('title-sub', 'Sites')
@section('pagetitle', $site->nom)

@section('content')
    <div id="layout-wrapper">

        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <div class="card">
            <div class="card-body">
                <div class="d-flex flex-wrap justify-content-between align-items-start gap-3">
                    <div>
                        <h5 class="mb-1"><i class="bi bi-geo-alt me-1"></i>{{ $site->nom }}
                            @if ($site->actif)
                                <span class="badge bg-success-subtle text-success ms-1">Actif</span>
                            @else
                                <span class="badge bg-secondary-subtle text-secondary ms-1">Inactif</span>
                            @endif
                        </h5>
                        <p class="text-muted mb-0">
                            @if ($site->client)
                                Client : <strong>{{ $site->client->nom_complet }}</strong>
                            @else
                                Site interne à l'agence
                            @endif
                            @if ($site->adresse) — {{ $site->adresse }} @endif
                        </p>
                    </div>
                    <a href="{{ route('rh.affectations.index') }}" class="btn btn-light-primary"><i class="bi bi-arrow-left me-1"></i>Retour aux affectations</a>
                </div>
            </div>
        </div>

        <div class="row g-3 mb-1">
            <div class="col-md-6 col-xl-3">
                <div class="card border">
                    <div class="card-body">
                        <div class="d-flex gap-4 border-bottom pb-5 mb-5">
                            <div class="h-50px w-50px bg-primary text-white d-flex align-items-center justify-content-center rounded fs-3"><i class="bi bi-people"></i></div>
                            <div><h5 class="mb-2">{{ $employesAffectes->count() }}</h5><p class="text-muted mb-0 fs-12">Agents affectés</p></div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-6 col-xl-3">
                <div class="card border">
                    <div class="card-body">
                        <div class="d-flex gap-4 border-bottom pb-5 mb-5">
                            <div class="h-50px w-50px bg-info text-white d-flex align-items-center justify-content-center rounded fs-3"><i class="bi bi-person"></i></div>
                            <div><h5 class="mb-2">{{ $hommes }}</h5><p class="text-muted mb-0 fs-12">Hommes</p></div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-6 col-xl-3">
                <div class="card border">
                    <div class="card-body">
                        <div class="d-flex gap-4 border-bottom pb-5 mb-5">
                            <div class="h-50px w-50px bg-danger text-white d-flex align-items-center justify-content-center rounded fs-3"><i class="bi bi-person-dress"></i></div>
                            <div><h5 class="mb-2">{{ $femmes }}</h5><p class="text-muted mb-0 fs-12">Femmes</p></div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-6 col-xl-3">
                <div class="card border">
                    <div class="card-body">
                        <div class="d-flex gap-4 border-bottom pb-5 mb-5">
                            <div class="h-50px w-50px bg-secondary text-white d-flex align-items-center justify-content-center rounded fs-3"><i class="bi bi-calendar-check"></i></div>
                            <div><h5 class="mb-2">{{ $derniereLigne ? \Illuminate\Support\Carbon::parse($derniereLigne->created_at)->format('d/m/Y') : '—' }}</h5><p class="text-muted mb-0 fs-12">Dernière affectation</p></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="card">
            <div class="card-header"><h6 class="card-action-title mb-0">Rapport de situation</h6></div>
            <div class="card-body">
                <p class="mb-0">{{ $rapport }}</p>
            </div>
        </div>

        <div class="row g-4">
            <div class="col-lg-4">
                <div class="card">
                    <div class="card-header"><h6 class="mb-0">Répartition Hommes / Femmes</h6></div>
                    <div class="card-body">
                        @if ($employesAffectes->isNotEmpty())
                            <div id="chartSiteSexe"></div>
                        @else
                            <p class="text-center text-muted py-5 mb-0">Aucun agent affecté.</p>
                        @endif
                    </div>
                </div>
            </div>
            <div class="col-lg-8">
                <div class="card">
                    <div class="card-header"><h6 class="mb-0">Employés affectés <span class="badge bg-secondary-subtle text-secondary ms-1">{{ $employesAffectes->count() }}</span></h6></div>
                    <div class="card-body p-0">
                        <div class="table-box table-responsive">
                            <table class="table text-nowrap align-middle mb-0">
                                <thead><tr><th>Matricule</th><th>Nom</th><th>Sexe</th><th>Poste</th><th>Département</th><th>Contrat</th><th></th></tr></thead>
                                <tbody>
                                    @forelse ($employesAffectes as $employe)
                                        <tr>
                                            <td class="fw-medium">{{ $employe->matricule }}</td>
                                            <td>{{ $employe->nom_complet }}</td>
                                            <td class="text-capitalize">{{ $employe->sexe ?: '—' }}</td>
                                            <td>{{ $employe->poste->nom ?? '—' }}</td>
                                            <td>{{ $employe->departement->nom ?? '—' }}</td>
                                            <td>
                                                @if ($employe->contratActif)
                                                    <span class="badge bg-success-subtle text-success">Actif</span>
                                                @else
                                                    <span class="badge bg-danger-subtle text-danger">Sans contrat</span>
                                                @endif
                                            </td>
                                            <td><a href="{{ route('rh.employes.show', $employe) }}" class="btn btn-light-success btn-sm">Voir</a></td>
                                        </tr>
                                    @empty
                                        <tr><td colspan="7" class="text-center text-muted py-5">Aucun agent affecté à ce site.</td></tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="card">
            <div class="card-header"><h6 class="mb-0">Historique des transferts sur ce site</h6></div>
            <div class="card-body p-0">
                <div class="table-box table-responsive">
                    <table class="table text-nowrap align-middle mb-0">
                        <thead><tr><th>Date</th><th>Employé</th><th>Anciens sites</th><th>Nouveaux sites</th><th>Motif</th></tr></thead>
                        <tbody>
                            @forelse ($historique as $affectation)
                                <tr>
                                    <td>{{ $affectation->date_affectation->format('d/m/Y') }}</td>
                                    <td class="fw-medium">{{ $affectation->employe->nom_complet ?? '—' }}</td>
                                    <td class="text-muted fs-12">{{ $affectation->anciens_sites ?: '—' }}</td>
                                    <td class="fw-medium">{{ $affectation->nouveaux_sites ?: '—' }}</td>
                                    <td class="text-muted fs-12">{{ $affectation->motif ?: '—' }}</td>
                                </tr>
                            @empty
                                <tr><td colspan="5" class="text-center text-muted py-5">Aucun transfert enregistré pour ce site.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
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
            const sexeData = { labels: ['Hommes', 'Femmes', 'Non renseigné'], series: [{{ $hommes }}, {{ $femmes }}, {{ $nonRenseigne }}] };
            const total = sexeData.series.reduce((a, b) => a + b, 0);
            if (total > 0) {
                new ApexCharts(document.querySelector('#chartSiteSexe'), {
                    chart: { type: 'donut', height: 280 },
                    labels: sexeData.labels,
                    series: sexeData.series,
                    colors: ['#0d6efd', '#e83e8c', '#adb5bd'],
                    legend: { position: 'bottom', fontSize: '11px' },
                    dataLabels: { enabled: true },
                }).render();
            }
        });
    </script>
@endsection
