@extends('partials.layouts.master-finance')

@section('title', 'Dépenses & travaux | Finance')
@section('title-sub', 'Finance')
@section('pagetitle', 'Dépenses & travaux')

@section('css')
    <link rel="stylesheet" href="{{ asset('assets/libs/choices.js/public/assets/styles/choices.min.css') }}">
@endsection

@section('content')
    <div id="layout-wrapper">

        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <div class="row">
            <div class="col-md-3">
                <div class="card">
                    <div class="card-body">
                        <h3>{{ $stats['en_cours'] }}</h3>
                        <p class="text-muted mb-0">Dossiers en cours</p>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card">
                    <div class="card-body">
                        <h3 class="text-warning">{{ number_format($stats['a_payer'], 0, ',', ' ') }}</h3>
                        <p class="text-muted mb-0">À payer (FCFA)</p>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card">
                    <div class="card-body">
                        <h3 class="text-success">{{ number_format($stats['payees_mois'], 0, ',', ' ') }}</h3>
                        <p class="text-muted mb-0">Payées ce mois (FCFA)</p>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card">
                    <div class="card-body">
                        <h3 class="text-danger">{{ number_format($stats['a_charge_bailleurs_mois'], 0, ',', ' ') }}</h3>
                        <p class="text-muted mb-0">À la charge des bailleurs ce mois (FCFA)</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-12 mb-4">
                <div class="accordion accordion-icon accordion-primary accordion-border-box" id="filtres_depenses">
                    <div class="accordion-item">
                        <h2 class="accordion-header">
                            <button class="accordion-button" type="button" data-bs-toggle="collapse"
                                data-bs-target="#filtres_depenses_body" aria-expanded="true">
                                <i class="bi bi-funnel-fill me-2"></i> Filtres
                            </button>
                        </h2>
                        <div id="filtres_depenses_body" class="accordion-collapse collapse show" data-bs-parent="#filtres_depenses">
                            <div class="accordion-body py-5">
                                <form method="GET" class="row g-4" id="formFiltresDepenses">
                                    <div class="col-md-3">
                                        <select class="form-select" name="bien_id" id="filtreBienDepenseSelect">
                                            <option value="">Tous les biens</option>
                                            @foreach ($biens as $bien)
                                                <option value="{{ $bien->id }}" @selected(request('bien_id') == $bien->id)>{{ $bien->titre }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-md-3">
                                        <select class="form-select" name="bailleur_id" id="filtreBailleurDepenseSelect">
                                            <option value="">Tous les bailleurs</option>
                                            @foreach ($bailleurs as $bailleur)
                                                <option value="{{ $bailleur->id }}" @selected(request('bailleur_id') == $bailleur->id)>{{ $bailleur->nom_complet }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-md-3">
                                        <select class="form-select" name="statut" onchange="this.form.submit()">
                                            <option value="">Tous les statuts</option>
                                            @foreach (['brouillon' => 'Brouillon', 'en_attente_validation' => 'En attente de validation', 'approuvee' => 'Approuvée', 'refusee' => 'Refusée', 'intervention_en_cours' => 'Intervention en cours', 'facture_recue' => 'Facture reçue', 'a_payer' => 'À payer', 'payee' => 'Payée', 'cloturee' => 'Clôturée'] as $valeur => $libelle)
                                                <option value="{{ $valeur }}" @selected(request('statut') === $valeur)>{{ $libelle }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-md-3">
                                        <select class="form-select" name="qui_supporte" onchange="this.form.submit()">
                                            <option value="">Qui supporte : tous</option>
                                            <option value="bailleur" @selected(request('qui_supporte') === 'bailleur')>Bailleur</option>
                                            <option value="locataire" @selected(request('qui_supporte') === 'locataire')>Locataire</option>
                                            <option value="agence" @selected(request('qui_supporte') === 'agence')>Agence</option>
                                        </select>
                                    </div>
                                    <div class="col-12 d-flex justify-content-end gap-2">
                                        <a href="{{ route('finance.depenses.index') }}" class="btn btn-light-danger">Réinitialiser</a>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <div class="d-flex flex-wrap gap-3 justify-content-between align-items-center">
                            <h6 class="mb-0">Dépenses <span class="badge bg-secondary-subtle text-secondary ms-1">{{ $depenses->count() }}</span></h6>
                            @can('finance.gerer')
                                <a href="{{ route('finance.depenses.create') }}" class="btn btn-primary"><i class="bi bi-plus-lg me-1"></i>Nouvelle dépense</a>
                            @endcan
                        </div>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-box table-responsive">
                            <table class="table text-nowrap align-middle mb-0">
                                <thead>
                                    <tr>
                                        <th>Numéro</th>
                                        <th>Bien</th>
                                        <th>Catégorie</th>
                                        <th>Description</th>
                                        <th>Montant</th>
                                        <th>Qui supporte</th>
                                        <th>Statut</th>
                                        <th></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($depenses as $depense)
                                        <tr>
                                            <td class="fw-medium">{{ $depense->numero }}</td>
                                            <td>{{ $depense->bien->titre ?? '—' }}</td>
                                            <td>{{ $depense->categorie->nom ?? '—' }}</td>
                                            <td class="text-truncate" style="max-width: 220px;">{{ $depense->description }}</td>
                                            <td>{{ number_format($depense->montantImpute(), 0, ',', ' ') }} FCFA</td>
                                            <td><span class="badge bg-secondary-subtle text-secondary text-capitalize">{{ $depense->qui_supporte }}</span></td>
                                            <td>
                                                @php
                                                    $couleurs = [
                                                        'brouillon' => 'light-subtle text-body',
                                                        'en_attente_validation' => 'warning-subtle text-warning',
                                                        'approuvee' => 'info-subtle text-info',
                                                        'refusee' => 'danger-subtle text-danger',
                                                        'intervention_en_cours' => 'info-subtle text-info',
                                                        'facture_recue' => 'primary-subtle text-primary',
                                                        'a_payer' => 'warning-subtle text-warning',
                                                        'payee' => 'success-subtle text-success',
                                                        'cloturee' => 'success-subtle text-success',
                                                    ];
                                                @endphp
                                                <span class="badge bg-{{ $couleurs[$depense->statut] ?? 'light-subtle text-body' }}">{{ $depense->libelleStatut() }}</span>
                                                @if ($depense->urgence)
                                                    <span class="badge bg-danger-subtle text-danger ms-1"><i class="bi bi-lightning-fill"></i> Urgent</span>
                                                @endif
                                            </td>
                                            <td>
                                                <a href="{{ route('finance.depenses.show', $depense) }}" class="btn btn-light-primary icon-btn-sm"><i class="bi bi-eye"></i></a>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr><td colspan="8" class="text-center text-muted py-5">Aucune dépense ne correspond à ces filtres.</td></tr>
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
            const form = document.getElementById('formFiltresDepenses');
            ['filtreBienDepenseSelect', 'filtreBailleurDepenseSelect'].forEach(function (id) {
                new Choices(document.getElementById(id), {
                    searchEnabled: true,
                    itemSelectText: '',
                    searchPlaceholderValue: 'Rechercher...',
                });
                document.getElementById(id).addEventListener('change', () => form.submit());
            });
        });
    </script>
@endsection
