@extends('partials.layouts.master-locative')

@section('title', $bailleur->nom_complet.' | Locative')
@section('title-sub', 'Bailleurs')
@section('pagetitle', $bailleur->nom_complet)

@section('content')
    <div id="layout-wrapper">

        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <div class="card overflow-hidden">
            <div class="card-body h-176px"
                style="background-image: url('assets/images/background.png');background-repeat: no-repeat;background-position: right;">
            </div>
            <div class="mt-2">
                <div class="card-body p-5">
                    <div class="d-flex flex-wrap align-items-start gap-5">
                        <div class="mt-n12 flex-shrink-0">
                            <div class="h-128px w-128px border border-4 border-white shadow-lg bg-primary-subtle text-primary d-flex align-items-center justify-content-center fs-1">
                                <i class="bi bi-person-badge"></i>
                            </div>
                        </div>
                        <div class="flex-grow-1">
                            <div class="mb-2">
                                <h5 class="mb-1">{{ $bailleur->nom_complet }}
                                    @if ($bailleur->statut === 'actif')
                                        <span class="badge bg-success-subtle text-success ms-1">Bailleur actif</span>
                                    @else
                                        <span class="badge bg-light-subtle text-body ms-1">Inactif</span>
                                    @endif
                                </h5>
                                <p class="text-muted fs-12 mb-0">{{ $bailleur->numero }} — {{ ucfirst($bailleur->type) }}</p>
                            </div>
                            <div class="d-flex flex-wrap gap-3">
                                <span class="badge bg-secondary-subtle text-secondary fs-13 px-3 py-2">{{ $bailleur->biens->count() }} Biens</span>
                                <span class="badge bg-secondary-subtle text-secondary fs-13 px-3 py-2">{{ $bailleur->gerances->count() }} Gérances</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-12">
                <div class="mb-6">
                    <ul class="nav nav-pills" id="bailleurTab" role="tablist">
                        <li class="nav-item" role="presentation">
                            <button class="nav-link active" data-bs-toggle="tab" data-bs-target="#infos-tab-pane" type="button">Vue générale</button>
                        </li>
                        @if ($compte)
                            <li class="nav-item" role="presentation">
                                <button class="nav-link" data-bs-toggle="tab" data-bs-target="#compte-tab-pane" type="button">Compte &amp; reversements</button>
                            </li>
                        @endif
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" data-bs-toggle="tab" data-bs-target="#gerances-tab-pane" type="button">Gérances</button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" data-bs-toggle="tab" data-bs-target="#biens-tab-pane" type="button">Biens</button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" data-bs-toggle="tab" data-bs-target="#documents-tab-pane" type="button">Documents</button>
                        </li>
                    </ul>
                </div>

                <div class="tab-content">
                    <div class="tab-pane fade show active" id="infos-tab-pane">
                        <div class="row g-4">
                            <div class="col-lg-6">
                                <div class="card">
                                    <div class="card-header"><h6 class="card-action-title mb-0">Coordonnées</h6></div>
                                    <div class="card-body">
                                        <div class="row mb-3"><div class="col-4 text-muted">Téléphone</div><div class="col-8 fw-medium">{{ $bailleur->telephone ?: '—' }}</div></div>
                                        <div class="row mb-3"><div class="col-4 text-muted">WhatsApp</div><div class="col-8 fw-medium">{{ $bailleur->whatsapp ?: '—' }}</div></div>
                                        <div class="row mb-3"><div class="col-4 text-muted">Email</div><div class="col-8 fw-medium">{{ $bailleur->email ?: '—' }}</div></div>
                                        <div class="row"><div class="col-4 text-muted">Adresse</div><div class="col-8 fw-medium">{{ $bailleur->adresse ?: '—' }}</div></div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-6">
                                <div class="card">
                                    <div class="card-header"><h6 class="card-action-title mb-0">Identité</h6></div>
                                    <div class="card-body">
                                        <div class="row mb-3"><div class="col-4 text-muted">Pièce d'identité</div><div class="col-8 fw-medium">{{ $bailleur->piece_identite_type ?: '—' }} {{ $bailleur->piece_identite_numero }}</div></div>
                                        <div class="row mb-3"><div class="col-4 text-muted">NINEA</div><div class="col-8 fw-medium">{{ $bailleur->ninea ?: '—' }}</div></div>
                                        <div class="row"><div class="col-4 text-muted">Coordonnées de paiement</div><div class="col-8 fw-medium">{{ $bailleur->coordonnees_paiement ?: '—' }}</div></div>
                                    </div>
                                </div>
                                @if ($bailleur->notes)
                                    <div class="card">
                                        <div class="card-header"><h6 class="card-action-title mb-0">Notes</h6></div>
                                        <div class="card-body"><p class="mb-0">{{ $bailleur->notes }}</p></div>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>

                    @if ($compte)
                        <div class="tab-pane fade" id="compte-tab-pane">
                            <div class="row row-cols-1 row-cols-md-3 row-cols-xl-5 g-3 mb-1">
                                <div class="col">
                                    <div class="card border h-100">
                                        <div class="card-body">
                                            <h5 class="mb-1">{{ number_format($compte['loyers_encaisses'], 0, ',', ' ') }}</h5>
                                            <p class="text-muted mb-0 fs-12">Loyers encaissés (FCFA)</p>
                                        </div>
                                    </div>
                                </div>
                                <div class="col">
                                    <div class="card border h-100">
                                        <div class="card-body">
                                            <h5 class="mb-1 text-primary">- {{ number_format($compte['commission_agence'], 0, ',', ' ') }}</h5>
                                            <p class="text-muted mb-0 fs-12">Commission agence (FCFA)</p>
                                        </div>
                                    </div>
                                </div>
                                <div class="col">
                                    <div class="card border h-100">
                                        <div class="card-body">
                                            <h5 class="mb-1 text-danger">- {{ number_format($compte['travaux_depenses'], 0, ',', ' ') }}</h5>
                                            <p class="text-muted mb-0 fs-12">Travaux / dépenses (FCFA)</p>
                                        </div>
                                    </div>
                                </div>
                                <div class="col">
                                    <div class="card border h-100">
                                        <div class="card-body">
                                            <h5 class="mb-1 text-info">{{ number_format($compte['deja_reverse'], 0, ',', ' ') }}</h5>
                                            <p class="text-muted mb-0 fs-12">Déjà reçu par le bailleur (FCFA)</p>
                                        </div>
                                    </div>
                                </div>
                                <div class="col">
                                    <div class="card border h-100 {{ $compte['a_reverser'] > 0 ? 'border-success' : '' }}">
                                        <div class="card-body">
                                            <h5 class="mb-1 {{ $compte['a_reverser'] > 0 ? 'text-success' : '' }}">{{ number_format($compte['a_reverser'], 0, ',', ' ') }}</h5>
                                            <p class="text-muted mb-0 fs-12">Reste à recevoir (FCFA)</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
                                <p class="text-muted fs-12 mb-0">
                                    Solde du compte bailleur : loyers encaissés pour son compte, moins la commission de gestion de l'agence, moins les travaux/dépenses mis à sa charge, moins ce qui lui a déjà été versé — le résultat est le montant qu'il lui reste à recevoir
                                    (dont <strong>{{ number_format($compte['du_mois_courant'], 0, ',', ' ') }} FCFA</strong> pour le mois en cours et <strong class="{{ $compte['arriere_anterieur'] > 0 ? 'text-danger' : '' }}">{{ number_format($compte['arriere_anterieur'], 0, ',', ' ') }} FCFA</strong> d'arriéré des mois précédents).
                                    @can('finance.consulter')
                                        <a href="{{ route('finance.bailleurs.show', $bailleur) }}">Voir le détail complet dans le module Finance</a>
                                    @endcan
                                </p>
                                @can('locative.finances')
                                    <a href="{{ route('locative.versements.index', ['bailleur_id' => $bailleur->id]) }}" class="btn btn-primary btn-sm flex-shrink-0">
                                        <i class="bi bi-cash-coin me-1"></i>Enregistrer un versement
                                    </a>
                                @endcan
                            </div>

                            <div class="card">
                                <div class="card-header"><h6 class="mb-0">Versements &amp; avances <span class="badge bg-secondary-subtle text-secondary ms-1">{{ $versements->count() }}</span></h6></div>
                                <div class="card-body p-0">
                                    <div class="table-box table-responsive">
                                        <table class="table text-nowrap align-middle mb-0">
                                            <thead><tr><th>Numéro</th><th>Date</th><th>Type</th><th>Montant</th><th>Mode</th><th>Référence</th></tr></thead>
                                            <tbody>
                                                @forelse ($versements as $versement)
                                                    <tr>
                                                        <td class="fw-medium">{{ $versement->numero }}</td>
                                                        <td>{{ $versement->date_versement->format('d/m/Y') }}</td>
                                                        <td>
                                                            @if ($versement->type === 'avance')
                                                                <span class="badge bg-info-subtle text-info">Avance</span>
                                                            @else
                                                                <span class="badge bg-success-subtle text-success">Normal</span>
                                                            @endif
                                                        </td>
                                                        <td class="fw-medium">{{ number_format($versement->montant, 0, ',', ' ') }} FCFA</td>
                                                        <td>{{ $versement->modePaiement->nom ?? '—' }}</td>
                                                        <td>{{ $versement->reference ?: '—' }}</td>
                                                    </tr>
                                                @empty
                                                    <tr><td colspan="6" class="text-center text-muted py-5">Aucun versement enregistré pour ce bailleur.</td></tr>
                                                @endforelse
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>

                            <div class="card">
                                <div class="card-header"><h6 class="mb-0">Historique des reversements (module Finance) <span class="badge bg-secondary-subtle text-secondary ms-1">{{ $reversements->count() }}</span></h6></div>
                                <div class="card-body p-0">
                                    <div class="table-box table-responsive">
                                        <table class="table text-nowrap align-middle mb-0">
                                            <thead><tr><th>Numéro</th><th>Période</th><th>Encaissé</th><th>Frais de gestion</th><th>Net reversé</th><th>Statut</th><th>Date de versement</th></tr></thead>
                                            <tbody>
                                                @forelse ($reversements as $reversement)
                                                    <tr>
                                                        <td class="fw-medium">{{ $reversement->numero }}</td>
                                                        <td>{{ ucfirst(\Carbon\Carbon::createFromDate($reversement->periode_annee, $reversement->periode_mois, 1)->translatedFormat('F Y')) }}</td>
                                                        <td>{{ number_format($reversement->montant_encaisse, 0, ',', ' ') }} FCFA</td>
                                                        <td>{{ number_format($reversement->montant_frais_gestion, 0, ',', ' ') }} FCFA</td>
                                                        <td class="fw-medium">{{ number_format($reversement->montant_net, 0, ',', ' ') }} FCFA</td>
                                                        <td>
                                                            @if ($reversement->statut === 'verse')
                                                                <span class="badge bg-success-subtle text-success">Versé</span>
                                                            @else
                                                                <span class="badge bg-warning-subtle text-warning">À verser</span>
                                                            @endif
                                                        </td>
                                                        <td>{{ optional($reversement->date_versement)->format('d/m/Y') ?: '—' }}</td>
                                                    </tr>
                                                @empty
                                                    <tr><td colspan="7" class="text-center text-muted py-5">Aucun reversement enregistré pour ce bailleur.</td></tr>
                                                @endforelse
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif

                    <div class="tab-pane fade" id="gerances-tab-pane">
                        <div class="card">
                            <div class="card-body p-0">
                                <div class="table-box table-responsive">
                                    <table class="table text-nowrap align-middle mb-0">
                                        <thead><tr><th>Numéro</th><th>Type</th><th>Période</th><th>Statut</th><th></th></tr></thead>
                                        <tbody>
                                            @forelse ($bailleur->gerances as $gerance)
                                                <tr>
                                                    <td>{{ $gerance->numero }}</td>
                                                    <td class="text-capitalize">{{ str_replace('_', ' ', $gerance->type_gerance) }}</td>
                                                    <td>{{ optional($gerance->date_debut)->format('d/m/Y') }} @if($gerance->date_fin) → {{ $gerance->date_fin->format('d/m/Y') }} @endif</td>
                                                    <td><span class="badge bg-secondary-subtle text-secondary text-capitalize">{{ str_replace('_', ' ', $gerance->statut) }}</span></td>
                                                    <td><a href="{{ route('locative.gerances.show', $gerance) }}" class="btn btn-light-success icon-btn-sm"><i class="bi bi-eye"></i></a></td>
                                                </tr>
                                            @empty
                                                <tr><td colspan="5" class="text-center text-muted py-5">Aucune gérance pour ce bailleur.</td></tr>
                                            @endforelse
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="tab-pane fade" id="biens-tab-pane">
                        <div class="card">
                            <div class="card-body p-0">
                                <div class="table-box table-responsive">
                                    <table class="table text-nowrap align-middle mb-0">
                                        <thead><tr><th>Bien</th><th>Catégorie</th><th>Exploitation</th><th>Statut</th><th></th></tr></thead>
                                        <tbody>
                                            @forelse ($bailleur->biens as $bien)
                                                <tr>
                                                    <td>{{ $bien->titre }}</td>
                                                    <td>{{ $bien->categorie->nom ?? '—' }}</td>
                                                    <td class="text-capitalize">{{ $bien->type_exploitation }}</td>
                                                    <td><span class="badge bg-secondary-subtle text-secondary text-capitalize">{{ str_replace('_', ' ', $bien->statut) }}</span></td>
                                                    <td></td>
                                                </tr>
                                            @empty
                                                <tr><td colspan="5" class="text-center text-muted py-5">Aucun bien pour ce bailleur.</td></tr>
                                            @endforelse
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="tab-pane fade" id="documents-tab-pane">
                        @include('locative.documents._liste', ['documentable' => $bailleur, 'typeDocument' => 'bailleur'])
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
