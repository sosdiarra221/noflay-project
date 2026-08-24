@extends('partials.layouts.master-finance')

@section('title', 'Tableau de bord | Finance')
@section('title-sub', 'Finance')
@section('pagetitle', 'Tableau de bord')

@section('content')
    <div id="layout-wrapper">

        <div class="row">
            <div class="col-md-6 col-xxl-3">
                <div class="card">
                    <div class="card-body">
                        <h3 class="text-success">{{ number_format($kpis['encaisse'], 0, ',', ' ') }}</h3>
                        <p class="text-muted mb-0">Encaissé ce mois (FCFA)</p>
                    </div>
                </div>
            </div>
            <div class="col-md-6 col-xxl-3">
                <div class="card">
                    <div class="card-body">
                        <h3 class="text-primary">{{ number_format($kpis['commissions'], 0, ',', ' ') }}</h3>
                        <p class="text-muted mb-0">Commissions agence (FCFA)</p>
                    </div>
                </div>
            </div>
            <div class="col-md-6 col-xxl-3">
                <div class="card">
                    <div class="card-body">
                        <h3 class="text-warning">{{ number_format($kpis['net_a_reverser'], 0, ',', ' ') }}</h3>
                        <p class="text-muted mb-0">Net à reverser (FCFA)</p>
                    </div>
                </div>
            </div>
            <div class="col-md-6 col-xxl-3">
                <div class="card">
                    <div class="card-body">
                        <h3 class="text-info">{{ number_format($kpis['deja_verse'], 0, ',', ' ') }}</h3>
                        <p class="text-muted mb-0">Déjà versé aux bailleurs (FCFA)</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-body">
                        <h3 class="text-secondary">{{ number_format($kpis['tva_collectee'], 0, ',', ' ') }}</h3>
                        <p class="text-muted mb-0">TVA collectée ce mois (FCFA)</p>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card">
                    <div class="card-body">
                        <h3 class="text-secondary">{{ number_format($kpis['tom_collectee'], 0, ',', ' ') }}</h3>
                        <p class="text-muted mb-0">TOM collectée ce mois (FCFA)</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-xl-8">
                <div class="card">
                    <div class="card-header"><h6 class="mb-0">Encaissements — 12 derniers mois</h6></div>
                    <div class="card-body">
                        <div id="chartTendanceFinance"></div>
                    </div>
                </div>
            </div>
            <div class="col-xl-4">
                <div class="card">
                    <div class="card-header"><h6 class="mb-0">Top bailleurs — ce mois</h6></div>
                    <div class="card-body">
                        <div id="chartBailleursFinance"></div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body d-flex flex-wrap gap-2">
                        <a href="{{ route('finance.revenus.index') }}" class="btn btn-light-primary"><i class="bi bi-graph-up-arrow me-1"></i>Voir les revenus</a>
                        <a href="{{ route('finance.reversements.index') }}" class="btn btn-light-warning"><i class="bi bi-arrow-left-right me-1"></i>Gérer les reversements</a>
                        <a href="{{ route('finance.taxes.index') }}" class="btn btn-light-secondary"><i class="bi bi-receipt me-1"></i>Rapport des taxes</a>
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
            const tendanceData = {!! json_encode([
                'categories' => $tendance->pluck('libelle'),
                'series' => $tendance->pluck('total'),
            ]) !!};

            new ApexCharts(document.querySelector('#chartTendanceFinance'), {
                chart: { type: 'area', height: 280, toolbar: { show: false } },
                series: [{ name: 'Encaissé', data: tendanceData.series }],
                xaxis: { categories: tendanceData.categories },
                colors: ['#0d6efd'],
                stroke: { curve: 'smooth', width: 2 },
                dataLabels: { enabled: false },
                fill: { type: 'gradient', gradient: { opacityFrom: 0.4, opacityTo: 0.05 } },
            }).render();

            const bailleursData = {!! json_encode([
                'labels' => $topBailleurs->pluck('nom'),
                'series' => $topBailleurs->pluck('montant'),
            ]) !!};

            if (bailleursData.labels.length > 0) {
                new ApexCharts(document.querySelector('#chartBailleursFinance'), {
                    chart: { type: 'donut', height: 280 },
                    labels: bailleursData.labels,
                    series: bailleursData.series,
                    legend: { position: 'bottom', fontSize: '11px' },
                    dataLabels: { enabled: false },
                }).render();
            } else {
                document.querySelector('#chartBailleursFinance').innerHTML = '<p class="text-muted text-center py-5 mb-0">Aucun encaissement ce mois-ci.</p>';
            }
        });
    </script>
@endsection
