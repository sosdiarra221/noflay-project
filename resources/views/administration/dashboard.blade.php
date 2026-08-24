@extends('partials.layouts.master-administration')

@section('title', 'Tableau de bord | Direction & Administration')
@section('title-sub', 'Direction & Administration')
@section('pagetitle', 'Tableau de bord')

@section('content')
    <div id="layout-wrapper">

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
