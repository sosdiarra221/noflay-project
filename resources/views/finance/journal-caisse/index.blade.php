@extends('partials.layouts.master-finance')

@section('title', 'Journal de caisse | Finance')
@section('title-sub', 'Finance')
@section('pagetitle', 'Journal de caisse')

@section('content')
    <div id="layout-wrapper">

        <div class="row">
            <div class="col-12 mb-4">
                <div class="card">
                    <div class="card-body">
                        <form method="GET" class="row g-3 align-items-end">
                            <div class="col-md-4">
                                <label class="form-label">Date</label>
                                <input type="date" class="form-control" name="date" value="{{ $date->format('Y-m-d') }}" onchange="this.form.submit()">
                            </div>
                            <div class="col-md-8 d-flex gap-2">
                                <a href="{{ route('finance.journal-caisse.index', ['date' => now()->format('Y-m-d')]) }}" class="btn btn-light-primary">Aujourd'hui</a>
                                <a href="{{ route('finance.journal-caisse.index', ['date' => $date->copy()->subDay()->format('Y-m-d')]) }}" class="btn btn-light"><i class="bi bi-chevron-left"></i> Veille</a>
                                <a href="{{ route('finance.journal-caisse.index', ['date' => $date->copy()->addDay()->format('Y-m-d')]) }}" class="btn btn-light">Lendemain <i class="bi bi-chevron-right"></i></a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <p class="text-muted mb-3">Flux financier du <strong>{{ $date->versionLongue() }}</strong></p>

        <div class="row">
            <div class="col-md-2">
                <div class="card">
                    <div class="card-body">
                        <h4 class="text-success">{{ number_format($totaux['total_encaisse'], 0, ',', ' ') }}</h4>
                        <p class="text-muted mb-0 fs-12">Total encaissé (FCFA)</p>
                    </div>
                </div>
            </div>
            <div class="col-md-2">
                <div class="card">
                    <div class="card-body">
                        <h4 class="text-warning">{{ number_format($totaux['vers_bailleurs'], 0, ',', ' ') }}</h4>
                        <p class="text-muted mb-0 fs-12">Part bailleurs (FCFA)</p>
                    </div>
                </div>
            </div>
            <div class="col-md-2">
                <div class="card">
                    <div class="card-body">
                        <h4 class="text-primary">{{ number_format($totaux['vers_agence'], 0, ',', ' ') }}</h4>
                        <p class="text-muted mb-0 fs-12">Part agence (FCFA)</p>
                    </div>
                </div>
            </div>
            <div class="col-md-2">
                <div class="card">
                    <div class="card-body">
                        <h4 class="text-info">{{ number_format($totaux['vers_caution'], 0, ',', ' ') }}</h4>
                        <p class="text-muted mb-0 fs-12">Vers caution (FCFA)</p>
                    </div>
                </div>
            </div>
            <div class="col-md-2">
                <div class="card">
                    <div class="card-body">
                        <h4 class="text-danger">{{ number_format($totaux['total_reverse'] + $totaux['total_restitue'], 0, ',', ' ') }}</h4>
                        <p class="text-muted mb-0 fs-12">Décaissé (FCFA)</p>
                    </div>
                </div>
            </div>
            <div class="col-md-2">
                <div class="card border {{ $totaux['solde_net'] >= 0 ? 'border-success' : 'border-danger' }}">
                    <div class="card-body">
                        <h4 class="{{ $totaux['solde_net'] >= 0 ? 'text-success' : 'text-danger' }}">{{ number_format($totaux['solde_net'], 0, ',', ' ') }}</h4>
                        <p class="text-muted mb-0 fs-12">Solde net du jour (FCFA)</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h6 class="mb-0"><i class="bi bi-arrow-down-circle text-success me-1"></i>Encaissements <span class="badge bg-secondary-subtle text-secondary ms-1">{{ $encaissements->count() }}</span></h6>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-box table-responsive">
                            <table class="table text-nowrap align-middle mb-0">
                                <thead>
                                    <tr>
                                        <th>Heure</th>
                                        <th>Numéro</th>
                                        <th>Type</th>
                                        <th>Bien</th>
                                        <th>Locataire</th>
                                        <th>Mode</th>
                                        <th>Montant</th>
                                        <th>Ventilation</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($encaissements as $ligne)
                                        <tr>
                                            <td>{{ $ligne['heure'] }}</td>
                                            <td class="fw-medium">{{ $ligne['numero'] }}</td>
                                            <td>
                                                @if ($ligne['type'] === 'entree')
                                                    <span class="badge bg-info-subtle text-info">Entrée / signature</span>
                                                @else
                                                    <span class="badge bg-primary-subtle text-primary">Loyer</span>
                                                @endif
                                            </td>
                                            <td>{{ $ligne['bien'] }}</td>
                                            <td>{{ $ligne['locataire'] }}</td>
                                            <td>{{ $ligne['mode_paiement'] }}</td>
                                            <td class="fw-semibold">{{ number_format($ligne['montant'], 0, ',', ' ') }} FCFA</td>
                                            <td class="fs-12">
                                                @if ($ligne['type'] === 'entree')
                                                    <span class="text-warning">{{ number_format($ligne['part_caution'], 0, ',', ' ') }} → Caution</span>
                                                    <br>
                                                    <span class="text-primary">{{ number_format($ligne['part_frais_agence'], 0, ',', ' ') }} → Frais agence</span>
                                                @else
                                                    <span class="text-success">{{ number_format($ligne['part_bailleur'], 0, ',', ' ') }} → Bailleur</span>
                                                    <br>
                                                    <span class="text-primary">{{ number_format($ligne['part_commission_agence'], 0, ',', ' ') }} → Commission agence</span>
                                                @endif
                                            </td>
                                        </tr>
                                    @empty
                                        <tr><td colspan="8" class="text-center text-muted py-4">Aucun encaissement ce jour-là.</td></tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h6 class="mb-0"><i class="bi bi-arrow-up-circle text-danger me-1"></i>Reversements aux bailleurs <span class="badge bg-secondary-subtle text-secondary ms-1">{{ $reversements->count() }}</span></h6>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-box table-responsive">
                            <table class="table text-nowrap align-middle mb-0">
                                <thead><tr><th>Numéro</th><th>Bailleur</th><th>Mode</th><th>Montant</th></tr></thead>
                                <tbody>
                                    @forelse ($reversements as $reversement)
                                        <tr>
                                            <td class="fw-medium">{{ $reversement->numero }}</td>
                                            <td>{{ $reversement->bailleur->nom_complet ?? '—' }}</td>
                                            <td>{{ $reversement->modePaiement->nom ?? '—' }}</td>
                                            <td class="text-danger">{{ number_format($reversement->montant_net, 0, ',', ' ') }} FCFA</td>
                                        </tr>
                                    @empty
                                        <tr><td colspan="4" class="text-center text-muted py-4">Aucun reversement ce jour-là.</td></tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h6 class="mb-0"><i class="bi bi-arrow-up-circle text-danger me-1"></i>Restitutions de caution <span class="badge bg-secondary-subtle text-secondary ms-1">{{ $restitutionsCaution->count() }}</span></h6>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-box table-responsive">
                            <table class="table text-nowrap align-middle mb-0">
                                <thead><tr><th>Bien</th><th>Locataire</th><th>Retenu</th><th>Restitué</th></tr></thead>
                                <tbody>
                                    @forelse ($restitutionsCaution as $restitution)
                                        <tr>
                                            <td>{{ $restitution->contratLocation->bien->titre ?? '—' }}</td>
                                            <td>{{ $restitution->contratLocation->location->locataire->nom_complet ?? '—' }}</td>
                                            <td class="text-muted">{{ number_format($restitution->montant_retenu, 0, ',', ' ') }} FCFA</td>
                                            <td class="text-danger">{{ number_format($restitution->montant_restitue, 0, ',', ' ') }} FCFA</td>
                                        </tr>
                                    @empty
                                        <tr><td colspan="4" class="text-center text-muted py-4">Aucune restitution ce jour-là.</td></tr>
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
