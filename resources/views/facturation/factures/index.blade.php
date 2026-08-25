@extends('partials.layouts.master-facturation')

@section('title', 'Factures | Facturation')
@section('title-sub', 'Facturation')
@section('pagetitle', 'Factures')

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
            <div class="col-md-6 col-xl-3">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex align-items-start pb-4 gap-4 border-bottom border-dashed">
                            <div class="flex-grow-1">
                                <h2>{{ $stats['total'] }}</h2>
                                <h6 class="fw-medium mb-0">Total factures</h6>
                            </div>
                            <div class="flex-shrink-0 h-56px w-56px bg-light-subtle d-flex justify-content-center align-items-center rounded text-muted fs-3">
                                <i class="bi bi-receipt"></i>
                            </div>
                        </div>
                        <p class="fs-12 pt-4 mb-0 text-muted text-center">Toutes sources confondues</p>
                    </div>
                </div>
            </div>
            <div class="col-md-6 col-xl-3">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex align-items-start pb-4 gap-4 border-bottom border-dashed">
                            <div class="flex-grow-1">
                                <h2>{{ $stats['emise'] }}</h2>
                                <h6 class="fw-medium mb-0">Émises</h6>
                            </div>
                            <div class="flex-shrink-0 h-56px w-56px bg-info-subtle d-flex justify-content-center align-items-center rounded text-info fs-3">
                                <i class="bi bi-send"></i>
                            </div>
                        </div>
                        <p class="fs-12 pt-4 mb-0 text-muted text-center">En attente de paiement</p>
                    </div>
                </div>
            </div>
            <div class="col-md-6 col-xl-3">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex align-items-start pb-4 gap-4 border-bottom border-dashed">
                            <div class="flex-grow-1">
                                <h2>{{ $stats['payee'] }}</h2>
                                <h6 class="fw-medium mb-0">Payées</h6>
                            </div>
                            <div class="flex-shrink-0 h-56px w-56px bg-success-subtle d-flex justify-content-center align-items-center rounded text-success fs-3">
                                <i class="bi bi-patch-check"></i>
                            </div>
                        </div>
                        <p class="fs-12 pt-4 mb-0 text-muted text-center">Encaissées</p>
                    </div>
                </div>
            </div>
            <div class="col-md-6 col-xl-3">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex align-items-start pb-4 gap-4 border-bottom border-dashed">
                            <div class="flex-grow-1">
                                <h2>{{ $stats['annulee'] }}</h2>
                                <h6 class="fw-medium mb-0">Annulées</h6>
                            </div>
                            <div class="flex-shrink-0 h-56px w-56px bg-danger-subtle d-flex justify-content-center align-items-center rounded text-danger fs-3">
                                <i class="bi bi-x-circle"></i>
                            </div>
                        </div>
                        <p class="fs-12 pt-4 mb-0 text-muted text-center">Sans effet comptable</p>
                    </div>
                </div>
            </div>
        </div>

        <ul class="nav nav-tabs mb-3" id="ongletsFacturation" role="tablist">
            <li class="nav-item" role="presentation">
                <button class="nav-link active" data-bs-toggle="tab" data-bs-target="#ongletFactures" type="button">
                    <i class="bi bi-receipt me-1"></i>Factures
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" data-bs-toggle="tab" data-bs-target="#ongletClients" type="button">
                    <i class="bi bi-people me-1"></i>Clients <span class="badge bg-secondary-subtle text-secondary ms-1">{{ $clients->count() }}</span>
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" data-bs-toggle="tab" data-bs-target="#ongletProspects" type="button">
                    <i class="bi bi-person-lines-fill me-1"></i>Prospects <span class="badge bg-secondary-subtle text-secondary ms-1">{{ $prospects->count() }}</span>
                </button>
            </li>
        </ul>

        <div class="tab-content">
            <div class="tab-pane fade show active" id="ongletFactures" role="tabpanel">
                <div class="card">
                    <div class="card-header d-flex flex-wrap gap-3 justify-content-between align-items-center">
                        <h6 class="mb-0">Factures <span class="badge bg-secondary-subtle text-secondary ms-1">{{ $factures->count() }}</span></h6>
                        <form method="GET" class="d-flex flex-wrap gap-2 align-items-center" id="formFiltresFactures">
                            <div class="form-icon">
                                <input type="text" class="form-control form-control-icon" name="recherche" value="{{ request('recherche') }}" placeholder="Numéro ou client...">
                                <i class="ri-search-2-line text-muted"></i>
                            </div>
                            <select class="form-select" name="statut" id="selectFiltreStatutFacture" onchange="document.getElementById('formFiltresFactures').submit()">
                                <option value="">Tous statuts</option>
                                @foreach (\App\Models\Facturation\Facture::STATUTS as $valeur => $libelle)
                                    <option value="{{ $valeur }}" @selected(request('statut') === $valeur)>{{ $libelle }}</option>
                                @endforeach
                            </select>
                            @if (request()->anyFilled(['statut', 'recherche']))
                                <a href="{{ route('facturation.factures.index') }}" class="btn btn-light-danger icon-btn" title="Réinitialiser"><i class="bi bi-x-lg"></i></a>
                            @endif
                        </form>
                    </div>
                    <p class="text-muted fs-12 px-4 pb-2 mb-0"><i class="bi bi-info-circle me-1"></i>Les factures sont générées automatiquement lorsqu'un devis passe au statut « Gagné ».</p>
                    <div class="card-body p-0">
                        <div class="table-box table-responsive">
                            <table class="table text-nowrap align-middle mb-0">
                                <thead><tr><th>Numéro</th><th>Client</th><th>Source</th><th>Date</th><th>Total TTC</th><th>Statut</th><th>Actions</th></tr></thead>
                                <tbody>
                                    @php $classesStatut = ['emise' => 'info', 'payee' => 'success', 'annulee' => 'danger']; @endphp
                                    @forelse ($factures as $facture)
                                        <tr>
                                            <td class="fw-medium"><a href="{{ route('facturation.factures.show', $facture) }}" class="text-reset">{{ $facture->numero }}</a></td>
                                            <td>{{ $facture->client->nom_complet ?? '—' }}</td>
                                            <td class="text-muted fs-12">{{ $facture->source ?: '—' }}</td>
                                            <td>{{ $facture->date_facture->format('d/m/Y') }}</td>
                                            <td class="fw-medium">{{ number_format($facture->total_ttc, 0, ',', ' ') }} FCFA</td>
                                            <td><span class="badge bg-{{ $classesStatut[$facture->statut] ?? 'secondary' }}-subtle text-{{ $classesStatut[$facture->statut] ?? 'secondary' }}">{{ $facture->libelleStatut() }}</span></td>
                                            <td>
                                                <a href="{{ route('facturation.factures.show', $facture) }}" class="btn btn-light-success btn-sm" title="Voir la facture"><i class="bi bi-eye me-1"></i>Détail</a>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr><td colspan="7" class="text-center text-muted py-5">Aucune facture ne correspond à ces filtres.</td></tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <div class="tab-pane fade" id="ongletClients" role="tabpanel">
                <div class="card">
                    <div class="card-header"><h6 class="mb-0">Clients de facturation</h6></div>
                    <div class="card-body p-0">
                        <div class="table-box table-responsive">
                            <table class="table text-nowrap align-middle mb-0">
                                <thead><tr><th>Nom</th><th>Téléphone</th><th>Email</th><th>Origine</th><th>Devis</th><th>Factures</th></tr></thead>
                                <tbody>
                                    @forelse ($clients as $client)
                                        <tr>
                                            <td class="fw-medium">{{ $client->nom_complet }}</td>
                                            <td>{{ $client->telephone ?: '—' }}</td>
                                            <td>{{ $client->email ?: '—' }}</td>
                                            <td>
                                                @if ($client->prospect)
                                                    <span class="badge bg-info-subtle text-info">Prospect {{ $client->prospect->numero }}</span>
                                                @else
                                                    <span class="badge bg-secondary-subtle text-secondary">Client direct</span>
                                                @endif
                                            </td>
                                            <td>{{ $client->devis_count }}</td>
                                            <td>{{ $client->factures_count }}</td>
                                        </tr>
                                    @empty
                                        <tr><td colspan="6" class="text-center text-muted py-5">Aucun client enregistré.</td></tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <div class="tab-pane fade" id="ongletProspects" role="tabpanel">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h6 class="mb-0">Prospects commerciaux</h6>
                        <a href="{{ route('commercial.prospects.index') }}" class="btn btn-light-primary btn-sm"><i class="bi bi-box-arrow-up-right me-1"></i>Module Commercial</a>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-box table-responsive">
                            <table class="table text-nowrap align-middle mb-0">
                                <thead><tr><th>Numéro</th><th>Nom</th><th>Téléphone</th><th>Email</th><th>Statut</th><th>Activités</th></tr></thead>
                                <tbody>
                                    @php $classesStatutProspect = ['non_traite' => 'secondary', 'en_cours' => 'warning', 'gagne' => 'success', 'perdu' => 'danger', 'annule' => 'dark']; @endphp
                                    @forelse ($prospects as $prospect)
                                        <tr>
                                            <td class="fw-medium">{{ $prospect->numero }}</td>
                                            <td>{{ $prospect->nom_complet }}</td>
                                            <td>{{ $prospect->telephone ?: '—' }}</td>
                                            <td>{{ $prospect->email ?: '—' }}</td>
                                            <td><span class="badge bg-{{ $classesStatutProspect[$prospect->statut] ?? 'secondary' }}-subtle text-{{ $classesStatutProspect[$prospect->statut] ?? 'secondary' }}">{{ $prospect->statut }}</span></td>
                                            <td>{{ $prospect->activites_count }}</td>
                                        </tr>
                                    @empty
                                        <tr><td colspan="6" class="text-center text-muted py-5">Aucun prospect enregistré.</td></tr>
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
            new Choices(document.getElementById('selectFiltreStatutFacture'), { searchEnabled: false, itemSelectText: '' });

            let minuteur;
            document.querySelector('input[name="recherche"]').addEventListener('input', function () {
                clearTimeout(minuteur);
                minuteur = setTimeout(() => document.getElementById('formFiltresFactures').submit(), 500);
            });
        });
    </script>
@endsection
