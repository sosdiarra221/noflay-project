@extends('partials.layouts.master-administration')

@section('title', 'Tableau de bord | Direction & Administration')
@section('title-sub', 'Direction & Administration')
@section('pagetitle', 'Tableau de bord')

@section('content')
    <div id="layout-wrapper">

        <div class="row">
            <div class="col-lg-7">
                <div class="card">
                    <div class="card-header"><h6 class="mb-0">Licence en cours</h6></div>
                    <div class="card-body">
                        @if ($licence)
                            <div class="d-flex flex-wrap align-items-center gap-4">
                                <div>
                                    <p class="text-muted mb-1 fs-12">Package</p>
                                    <h6 class="mb-0">{{ $licence->package->nom ?? '—' }}</h6>
                                </div>
                                <div>
                                    <p class="text-muted mb-1 fs-12">Valide jusqu'au</p>
                                    <h6 class="mb-0">{{ $licence->date_fin->format('d/m/Y') }}</h6>
                                </div>
                                <div>
                                    <p class="text-muted mb-1 fs-12">Statut</p>
                                    @if ($licence->estExpiree())
                                        <span class="badge bg-danger-subtle text-danger">Expirée</span>
                                    @elseif ($licence->joursRestants() <= 5)
                                        <span class="badge bg-warning-subtle text-warning">{{ $licence->joursRestants() }} j. restants</span>
                                    @else
                                        <span class="badge bg-success-subtle text-success">Valide</span>
                                    @endif
                                </div>
                            </div>
                            <p class="text-muted fs-12 mb-0 mt-3">Pour renouveler ou changer de package, contactez l'éditeur du logiciel.</p>
                        @else
                            <p class="text-muted mb-0">Aucune information de licence disponible.</p>
                        @endif
                    </div>
                </div>
            </div>
            <div class="col-lg-5">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h6 class="mb-0">Modules souscrits</h6>
                        <a href="{{ route('administration.modules.index') }}" class="fs-12">Voir le détail</a>
                    </div>
                    <div class="card-body">
                        <h3 class="mb-0">{{ $modulesActifsCount }} <span class="fs-14 text-muted fw-normal">/ {{ $modulesCatalogue->count() }} + Administration</span></h3>
                        <p class="text-muted mb-0 fs-12">modules actifs pour votre société</p>
                    </div>
                </div>
            </div>
            @if (! is_null($soldeTresorerie))
                <div class="col-lg-12">
                    <div class="card bg-dark-subtle">
                        <div class="card-body d-flex justify-content-between align-items-center flex-wrap gap-3">
                            <div>
                                <p class="text-muted mb-1 fs-12">Solde de trésorerie global (temps réel)</p>
                                <h3 class="mb-0">{{ number_format($soldeTresorerie, 0, ',', ' ') }} FCFA</h3>
                            </div>
                            <a href="{{ route('finance.comptabilite.index') }}" class="btn btn-dark">
                                <i class="bi bi-calculator me-1"></i>Comptabilité générale
                            </a>
                        </div>
                    </div>
                </div>
            @endif
        </div>

        <div class="row">
            <div class="col-md-4">
                <div class="card">
                    <div class="card-body">
                        <h3 class="text-success">{{ $kpis['utilisateurs_actifs'] }}</h3>
                        <p class="text-muted mb-0">Utilisateurs actifs</p>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card">
                    <div class="card-body">
                        <h3 class="text-danger">{{ $kpis['utilisateurs_inactifs'] }}</h3>
                        <p class="text-muted mb-0">Utilisateurs désactivés</p>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card">
                    <div class="card-body">
                        <h3>{{ $kpis['roles'] }}</h3>
                        <p class="text-muted mb-0">Rôles définis</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-xl-7">
                <div class="card">
                    <div class="card-header"><h6 class="mb-0">Dernières connexions</h6></div>
                    <div class="card-body p-0">
                        <div class="table-box table-responsive">
                            <table class="table text-nowrap align-middle mb-0">
                                <thead><tr><th>Utilisateur</th><th>Rôle</th><th>Dernière activité</th></tr></thead>
                                <tbody>
                                    @forelse ($derniersConnectes as $utilisateur)
                                        <tr>
                                            <td class="fw-medium">{{ $utilisateur->name }}</td>
                                            <td>{{ $utilisateur->role->libelle ?? '—' }}</td>
                                            <td>{{ $utilisateur->derniere_activite_at->diffForHumans() }}</td>
                                        </tr>
                                    @empty
                                        <tr><td colspan="3" class="text-center text-muted py-4">Aucune activité enregistrée.</td></tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xl-5">
                <div class="card">
                    <div class="card-header"><h6 class="mb-0">Répartition par rôle</h6></div>
                    <div class="card-body p-0">
                        <div class="table-box table-responsive">
                            <table class="table text-nowrap align-middle mb-0">
                                <thead><tr><th>Rôle</th><th>Utilisateurs</th></tr></thead>
                                <tbody>
                                    @foreach ($parRole as $libelle => $nombre)
                                        <tr><td>{{ $libelle }}</td><td>{{ $nombre }}</td></tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body d-flex flex-wrap gap-2">
                        <a href="{{ route('administration.utilisateurs.create') }}" class="btn btn-primary"><i class="bi bi-person-plus me-1"></i>Nouvel utilisateur</a>
                        <a href="{{ route('administration.roles.index') }}" class="btn btn-light-primary"><i class="bi bi-shield-check me-1"></i>Rôles &amp; permissions</a>
                        <a href="{{ route('administration.securite.index') }}" class="btn btn-light-secondary"><i class="bi bi-lock me-1"></i>Sécurité &amp; session</a>
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
