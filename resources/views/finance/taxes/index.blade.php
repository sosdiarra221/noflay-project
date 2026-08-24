@extends('partials.layouts.master-finance')

@section('title', 'Taxes collectées | Finance')
@section('title-sub', 'Finance')
@section('pagetitle', 'Taxes collectées')

@section('content')
    <div id="layout-wrapper">

        <div class="row">
            <div class="col-12 mb-4">
                <div class="card">
                    <div class="card-body">
                        <form method="GET" class="row g-3 align-items-end">
                            <div class="col-md-4">
                                <label class="form-label">Année</label>
                                <select class="form-select" name="annee" onchange="this.form.submit()">
                                    @foreach ($anneesDisponibles as $anneeOption)
                                        <option value="{{ $anneeOption }}" @selected($anneeOption == $annee)>{{ $anneeOption }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-4">
                <div class="card">
                    <div class="card-body">
                        <h3 class="text-primary">{{ number_format($stats['total_tva'], 0, ',', ' ') }}</h3>
                        <p class="text-muted mb-0">Total TVA collectée (FCFA)</p>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card">
                    <div class="card-body">
                        <h3 class="text-secondary">{{ number_format($stats['total_tom'], 0, ',', ' ') }}</h3>
                        <p class="text-muted mb-0">Total TOM collectée (FCFA)</p>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card">
                    <div class="card-body">
                        <h3>{{ $stats['total_fiches'] }}</h3>
                        <p class="text-muted mb-0">Fiches proforma émises</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-xl-8">
                <div class="card">
                    <div class="card-header"><h6 class="mb-0">TVA &amp; TOM par mois — {{ $annee }}</h6></div>
                    <div class="card-body">
                        <div id="chartTaxes"></div>
                    </div>
                </div>
            </div>
            <div class="col-xl-4">
                <div class="card">
                    <div class="card-body p-0">
                        <div class="table-box table-responsive">
                            <table class="table text-nowrap align-middle mb-0">
                                <thead><tr><th>Mois</th><th>TVA</th><th>TOM</th></tr></thead>
                                <tbody>
                                    @foreach ($parMois as $ligne)
                                        <tr>
                                            <td>{{ $ligne['libelle'] }}</td>
                                            <td>{{ number_format($ligne['tva'], 0, ',', ' ') }}</td>
                                            <td>{{ number_format($ligne['tom'], 0, ',', ' ') }}</td>
                                        </tr>
                                    @endforeach
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
            const data = {!! json_encode([
                'categories' => $parMois->pluck('libelle'),
                'tva' => $parMois->pluck('tva'),
                'tom' => $parMois->pluck('tom'),
            ]) !!};

            new ApexCharts(document.querySelector('#chartTaxes'), {
                chart: { type: 'bar', height: 320, toolbar: { show: false } },
                series: [
                    { name: 'TVA', data: data.tva },
                    { name: 'TOM', data: data.tom },
                ],
                xaxis: { categories: data.categories },
                colors: ['#405189', '#f7b84b'],
                plotOptions: { bar: { borderRadius: 4, columnWidth: '55%' } },
                dataLabels: { enabled: false },
                legend: { position: 'top' },
            }).render();
        });
    </script>
@endsection
