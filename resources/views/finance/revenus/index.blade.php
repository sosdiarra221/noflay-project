@extends('partials.layouts.master-finance')

@section('title', 'Revenus | Finance')
@section('title-sub', 'Finance')
@section('pagetitle', 'Revenus')

@section('css')
    <link rel="stylesheet" href="{{ asset('assets/libs/choices.js/public/assets/styles/choices.min.css') }}">
@endsection

@section('content')
    <div id="layout-wrapper">

        <div class="row">
            <div class="col-md-4">
                <div class="card">
                    <div class="card-body">
                        <h3 class="text-success">{{ number_format($stats['total_encaisse'], 0, ',', ' ') }}</h3>
                        <p class="text-muted mb-0">Total encaissé (FCFA)</p>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card">
                    <div class="card-body">
                        <h3 class="text-primary">{{ number_format($stats['total_commission'], 0, ',', ' ') }}</h3>
                        <p class="text-muted mb-0">Commission agence (FCFA)</p>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card">
                    <div class="card-body">
                        <h3 class="text-warning">{{ number_format($stats['total_net'], 0, ',', ' ') }}</h3>
                        <p class="text-muted mb-0">Net revenant aux bailleurs (FCFA)</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-12 mb-4">
                <div class="accordion accordion-icon accordion-primary accordion-border-box" id="filtres_revenus">
                    <div class="accordion-item">
                        <h2 class="accordion-header">
                            <button class="accordion-button" type="button" data-bs-toggle="collapse"
                                data-bs-target="#filtres_revenus_body" aria-expanded="true">
                                <i class="bi bi-funnel-fill me-2"></i> Filtres
                            </button>
                        </h2>
                        <div id="filtres_revenus_body" class="accordion-collapse collapse show" data-bs-parent="#filtres_revenus">
                            <div class="accordion-body py-5">
                                <form method="GET" class="row g-4" id="formFiltresRevenus">
                                    <div class="col-md-4">
                                        <select class="form-select" name="bailleur_id" id="filtreBailleurRevenuSelect">
                                            <option value="">Tous les bailleurs</option>
                                            @foreach ($bailleurs as $bailleur)
                                                <option value="{{ $bailleur->id }}" @selected(request('bailleur_id') == $bailleur->id)>{{ $bailleur->nom_complet }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-md-4">
                                        <select class="form-select" name="mode_paiement_id" onchange="this.form.submit()">
                                            <option value="">Tous les modes</option>
                                            @foreach ($modesPaiement as $mode)
                                                <option value="{{ $mode->id }}" @selected(request('mode_paiement_id') == $mode->id)>{{ $mode->nom }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-md-4">
                                        <select class="form-select" name="mois_annee" id="filtreMoisAnneeRevenuSelect">
                                            <option value="">Tous les mois</option>
                                            @foreach ($periodesDisponibles as $periode)
                                                <option value="{{ $periode['valeur'] }}" @selected(request('mois_annee') === $periode['valeur'])>{{ $periode['libelle'] }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-12 d-flex justify-content-end gap-2">
                                        <a href="{{ route('finance.revenus.index') }}" class="btn btn-light-danger">Réinitialiser</a>
                                    </div>
                                </form>
                                <p class="text-muted fs-12 mt-3 mb-0"><i class="bi bi-info-circle me-1"></i>Les filtres s'appliquent automatiquement.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h6 class="mb-0">Encaissements <span class="badge bg-secondary-subtle text-secondary ms-1">{{ $lignes->count() }}</span></h6>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-box table-responsive">
                            <table class="table text-nowrap align-middle mb-0">
                                <thead>
                                    <tr>
                                        <th>Date</th>
                                        <th>Numéro</th>
                                        <th>Bailleur</th>
                                        <th>Locataire</th>
                                        <th>Bien</th>
                                        <th>Mode</th>
                                        <th>Montant encaissé</th>
                                        <th>Commission agence</th>
                                        <th>Net bailleur</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($lignes as $ligne)
                                        <tr>
                                            <td>{{ $ligne['paiement']->date_paiement->format('d/m/Y') }}</td>
                                            <td class="fw-medium">{{ $ligne['paiement']->numero }}</td>
                                            <td>{{ $ligne['bailleur']->nom_complet ?? '—' }}</td>
                                            <td>{{ $ligne['locataire']->nom_complet ?? '—' }}</td>
                                            <td>{{ $ligne['bien']->titre ?? '—' }}</td>
                                            <td>{{ $ligne['paiement']->modePaiement->nom ?? '—' }}</td>
                                            <td>{{ number_format($ligne['paiement']->montant, 0, ',', ' ') }} FCFA</td>
                                            <td class="text-primary">{{ number_format($ligne['commission'], 0, ',', ' ') }} FCFA</td>
                                            <td class="text-success">{{ number_format($ligne['net'], 0, ',', ' ') }} FCFA</td>
                                        </tr>
                                    @empty
                                        <tr><td colspan="9" class="text-center text-muted py-5">Aucun encaissement ne correspond à ces filtres.</td></tr>
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
    <script src="{{ asset('assets/libs/choices.js/public/assets/scripts/choices.min.js') }}"></script>
    <script type="module" src="{{ asset('assets/js/app.js') }}"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const form = document.getElementById('formFiltresRevenus');

            const bailleurSelect = new Choices(document.getElementById('filtreBailleurRevenuSelect'), {
                searchEnabled: true,
                itemSelectText: '',
                placeholderValue: 'Rechercher un bailleur...',
                searchPlaceholderValue: 'Rechercher...',
            });
            document.getElementById('filtreBailleurRevenuSelect').addEventListener('change', () => form.submit());

            const moisSelect = new Choices(document.getElementById('filtreMoisAnneeRevenuSelect'), {
                searchEnabled: true,
                itemSelectText: '',
                placeholderValue: 'Rechercher un mois...',
                searchPlaceholderValue: 'Rechercher...',
            });
            document.getElementById('filtreMoisAnneeRevenuSelect').addEventListener('change', () => form.submit());
        });
    </script>
@endsection
