@extends('partials.layouts.master-commercial')

@section('title', 'Tableau de bord | Commercial')
@section('title-sub', 'Commercial')
@section('pagetitle', 'Tableau de bord')

@section('content')

    <div id="layout-wrapper">

        <div class="row">
            <div class="col-md-6 col-xxl-2">
                <div class="card">
                    <div class="card-body">
                        <h3>{{ $kpis['prospects'] }}</h3>
                        <p class="text-muted mb-0">Prospects</p>
                    </div>
                </div>
            </div>
            <div class="col-md-6 col-xxl-2">
                <div class="card">
                    <div class="card-body">
                        <h3 class="text-secondary">{{ $kpis['a_traiter'] }}</h3>
                        <p class="text-muted mb-0">À traiter</p>
                    </div>
                </div>
            </div>
            <div class="col-md-6 col-xxl-2">
                <div class="card">
                    <div class="card-body">
                        <h3 class="text-warning">{{ $kpis['en_cours'] }}</h3>
                        <p class="text-muted mb-0">En cours</p>
                    </div>
                </div>
            </div>
            <div class="col-md-6 col-xxl-2">
                <div class="card">
                    <div class="card-body">
                        <h3 class="text-success">{{ $kpis['gagnes'] }}</h3>
                        <p class="text-muted mb-0">Gagnés</p>
                    </div>
                </div>
            </div>
            <div class="col-md-6 col-xxl-2">
                <div class="card">
                    <div class="card-body">
                        <h3 class="text-danger">{{ $kpis['perdus'] }}</h3>
                        <p class="text-muted mb-0">Perdus</p>
                    </div>
                </div>
            </div>
            <div class="col-md-6 col-xxl-2">
                <div class="card">
                    <div class="card-body">
                        <h3 class="text-dark">{{ $kpis['annules'] }}</h3>
                        <p class="text-muted mb-0">Annulés</p>
                    </div>
                </div>
            </div>
        </div>

        @if ($sansActivite > 0)
            <div class="alert alert-warning">
                <i class="bi bi-exclamation-triangle me-1"></i>
                <strong>{{ $sansActivite }}</strong> prospect(s) sans activité depuis 7 jours.
                <a href="{{ route('commercial.prospects.index') }}" class="alert-link">Voir</a>
            </div>
        @endif

        <div class="row">
            <div class="col-xl-4">
                <div class="card">
                    <div class="card-header"><h6 class="mb-0">Répartition par statut</h6></div>
                    <div class="card-body">
                        <div id="chartStatuts"></div>
                        <div class="text-center mt-2">
                            <h3 class="mb-0">{{ $tauxConversion }} %</h3>
                            <p class="text-muted mb-0">taux de conversion ({{ $kpis['gagnes'] }} / {{ $kpis['prospects'] }})</p>
                        </div>
                    </div>
                </div>
                <div class="card">
                    <div class="card-header"><h6 class="mb-0">Prospects par source</h6></div>
                    <div class="card-body">
                        <div id="chartSources"></div>
                    </div>
                </div>
            </div>
            <div class="col-xl-8">
                <div class="card">
                    <div class="card-header"><h6 class="mb-0">Nouveaux prospects — 14 derniers jours</h6></div>
                    <div class="card-body">
                        <div id="chartTendance"></div>
                    </div>
                </div>
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h6 class="mb-0">Prospects récents</h6>
                        <a href="{{ route('commercial.prospects.index') }}" class="btn btn-outline-light text-muted btn-sm">Voir tout<i class="bi bi-arrow-right ms-1"></i></a>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-box table-responsive">
                            <table class="table text-nowrap align-middle mb-0">
                                <thead><tr><th>Prospect</th><th>Demande</th><th>Source</th><th>Statut</th><th></th></tr></thead>
                                <tbody>
                                    @forelse ($prospectsRecents as $prospect)
                                        @php
                                            $classesS = ['non_traite' => 'secondary', 'en_cours' => 'warning', 'gagne' => 'success', 'perdu' => 'danger', 'annule' => 'dark'];
                                        @endphp
                                        <tr>
                                            <td>{{ $prospect->nom_complet }}</td>
                                            <td>{{ $prospect->typeDemande->nom ?? '—' }}</td>
                                            <td>{{ $prospect->source->nom ?? '—' }}</td>
                                            <td><span class="badge bg-{{ $classesS[$prospect->statut] }}-subtle text-{{ $classesS[$prospect->statut] }} text-capitalize">{{ str_replace('_', ' ', $prospect->statut) }}</span></td>
                                            <td><a href="{{ route('commercial.prospects.show', $prospect) }}" class="btn btn-light-success icon-btn-sm"><i class="bi bi-eye"></i></a></td>
                                        </tr>
                                    @empty
                                        <tr><td colspan="5" class="text-center text-muted py-5">Aucun prospect pour le moment.</td></tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <div class="card">
                    <div class="card-header"><h6 class="mb-0">Activités récentes</h6></div>
                    <div class="card-body p-0">
                        <div class="table-box table-responsive">
                            <table class="table text-nowrap align-middle mb-0">
                                <thead><tr><th>Date</th><th>Type</th><th>Prospect</th><th>Objet</th><th>Par</th></tr></thead>
                                <tbody>
                                    @forelse ($activitesRecentes as $activite)
                                        <tr>
                                            <td>{{ $activite->date_activite->format('d/m/Y H:i') }}</td>
                                            <td class="text-capitalize">{{ str_replace('_', ' ', $activite->type) }}</td>
                                            <td>{{ $activite->prospect->nom_complet }}</td>
                                            <td>{{ $activite->objet }}</td>
                                            <td>{{ $activite->utilisateur->name ?? '—' }}</td>
                                        </tr>
                                    @empty
                                        <tr><td colspan="5" class="text-center text-muted py-5">Aucune activité enregistrée.</td></tr>
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
            const statutsData = {!! json_encode([
                'labels' => ['À traiter', 'En cours', 'Gagnés', 'Perdus', 'Annulés'],
                'series' => [$kpis['a_traiter'], $kpis['en_cours'], $kpis['gagnes'], $kpis['perdus'], $kpis['annules']],
            ]) !!};

            new ApexCharts(document.querySelector('#chartStatuts'), {
                chart: { type: 'donut', height: 240 },
                labels: statutsData.labels,
                series: statutsData.series,
                colors: ['#6c757d', '#f7b84b', '#28a745', '#f06548', '#343a40'],
                legend: { position: 'bottom' },
                dataLabels: { enabled: false },
            }).render();

            const sourcesData = {!! json_encode([
                'categories' => $parSource->pluck('nom'),
                'series' => $parSource->pluck('prospects_count'),
            ]) !!};

            new ApexCharts(document.querySelector('#chartSources'), {
                chart: { type: 'bar', height: 240, toolbar: { show: false } },
                series: [{ name: 'Prospects', data: sourcesData.series }],
                xaxis: { categories: sourcesData.categories },
                colors: ['#0d6efd'],
                plotOptions: { bar: { borderRadius: 4, horizontal: true } },
                dataLabels: { enabled: false },
            }).render();

            const tendanceData = {!! json_encode([
                'categories' => $tendance->pluck('date'),
                'series' => $tendance->pluck('total'),
            ]) !!};

            new ApexCharts(document.querySelector('#chartTendance'), {
                chart: { type: 'area', height: 260, toolbar: { show: false } },
                series: [{ name: 'Nouveaux prospects', data: tendanceData.series }],
                xaxis: { categories: tendanceData.categories },
                colors: ['#0d6efd'],
                stroke: { curve: 'smooth', width: 2 },
                dataLabels: { enabled: false },
                fill: { type: 'gradient', gradient: { opacityFrom: 0.4, opacityTo: 0.05 } },
            }).render();
        });
    </script>
@endsection
