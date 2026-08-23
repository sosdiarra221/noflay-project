@extends('partials.layouts.master-commercial')

@section('title', 'Rapports | Commercial')
@section('title-sub', 'Commercial')
@section('pagetitle', 'Rapports')

@section('content')

    <div id="layout-wrapper">

        <div class="row">
            <div class="col-12 mb-4">
                <div class="card">
                    <div class="card-body">
                        <form method="GET" class="row g-3 align-items-end">
                            <div class="col-md-3">
                                <label class="form-label">Du</label>
                                <input type="date" class="form-control" name="debut" value="{{ $debut->format('Y-m-d') }}">
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Au</label>
                                <input type="date" class="form-control" name="fin" value="{{ $fin->format('Y-m-d') }}">
                            </div>
                            <div class="col-md-6 d-flex justify-content-end gap-2">
                                <button type="submit" class="btn btn-primary"><i class="ri-equalizer-line me-1"></i>Filtrer</button>
                                <a href="{{ route('commercial.rapports.export', request()->query()) }}" class="btn btn-light-success"><i class="bi bi-download me-1"></i>Exporter CSV</a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-6 col-xxl-3">
                <div class="card">
                    <div class="card-body">
                        <h3>{{ $kpis['total'] }}</h3>
                        <p class="text-muted mb-0">Prospects sur la période</p>
                    </div>
                </div>
            </div>
            <div class="col-md-6 col-xxl-3">
                <div class="card">
                    <div class="card-body">
                        <h3 class="text-success">{{ $tauxConversion }} %</h3>
                        <p class="text-muted mb-0">Taux de conversion</p>
                    </div>
                </div>
            </div>
            <div class="col-md-6 col-xxl-3">
                <div class="card">
                    <div class="card-body">
                        <h3 class="text-warning">{{ $kpis['en_cours'] }}</h3>
                        <p class="text-muted mb-0">En cours</p>
                    </div>
                </div>
            </div>
            <div class="col-md-6 col-xxl-3">
                <div class="card">
                    <div class="card-body">
                        <h3 class="text-danger">{{ $kpis['perdus'] }}</h3>
                        <p class="text-muted mb-0">Perdus</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-xl-4">
                <div class="card">
                    <div class="card-header"><h6 class="mb-0">Répartition par statut</h6></div>
                    <div class="card-body">
                        <div id="chartRapportStatut"></div>
                    </div>
                </div>
            </div>
            <div class="col-xl-4">
                <div class="card">
                    <div class="card-header"><h6 class="mb-0">Par source</h6></div>
                    <div class="card-body">
                        <div id="chartRapportSource"></div>
                    </div>
                </div>
            </div>
            <div class="col-xl-4">
                <div class="card">
                    <div class="card-header"><h6 class="mb-0">Par type de demande</h6></div>
                    <div class="card-body">
                        <div id="chartRapportType"></div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header"><h6 class="mb-0">Performance par commercial</h6></div>
                    <div class="card-body p-0">
                        <div class="table-box table-responsive">
                            <table class="table text-nowrap align-middle mb-0">
                                <thead>
                                    <tr>
                                        <th>Commercial</th>
                                        <th>Prospects gérés</th>
                                        <th>Gagnés</th>
                                        <th>Taux de conversion</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($performanceCommerciaux as $ligne)
                                        <tr>
                                            <td>{{ $ligne['nom'] }}</td>
                                            <td>{{ $ligne['total'] }}</td>
                                            <td>{{ $ligne['gagnes'] }}</td>
                                            <td>{{ $ligne['taux'] }} %</td>
                                        </tr>
                                    @empty
                                        <tr><td colspan="4" class="text-center text-muted py-5">Aucune donnée sur cette période.</td></tr>
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
            const statutData = {!! json_encode([
                'labels' => $parStatut->pluck('statut')->map(fn ($s) => str_replace('_', ' ', ucfirst($s))),
                'series' => $parStatut->pluck('total'),
            ]) !!};

            new ApexCharts(document.querySelector('#chartRapportStatut'), {
                chart: { type: 'donut', height: 260 },
                labels: statutData.labels,
                series: statutData.series,
                colors: ['#6c757d', '#f7b84b', '#28a745', '#f06548', '#343a40'],
                legend: { position: 'bottom' },
                dataLabels: { enabled: false },
            }).render();

            const sourceData = {!! json_encode([
                'categories' => $parSource->pluck('nom'),
                'series' => $parSource->pluck('prospects_count'),
            ]) !!};

            new ApexCharts(document.querySelector('#chartRapportSource'), {
                chart: { type: 'bar', height: 260, toolbar: { show: false } },
                series: [{ name: 'Prospects', data: sourceData.series }],
                xaxis: { categories: sourceData.categories },
                colors: ['#0d6efd'],
                plotOptions: { bar: { borderRadius: 4, horizontal: true } },
                dataLabels: { enabled: false },
            }).render();

            const typeData = {!! json_encode([
                'categories' => $parTypeDemande->pluck('nom'),
                'series' => $parTypeDemande->pluck('prospects_count'),
            ]) !!};

            new ApexCharts(document.querySelector('#chartRapportType'), {
                chart: { type: 'bar', height: 260, toolbar: { show: false } },
                series: [{ name: 'Prospects', data: typeData.series }],
                xaxis: { categories: typeData.categories },
                colors: ['#0ab39c'],
                plotOptions: { bar: { borderRadius: 4, horizontal: true } },
                dataLabels: { enabled: false },
            }).render();
        });
    </script>
@endsection
