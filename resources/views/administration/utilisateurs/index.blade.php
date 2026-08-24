@extends('partials.layouts.master-administration')

@section('title', 'Utilisateurs | Direction & Administration')
@section('title-sub', 'Direction & Administration')
@section('pagetitle', 'Utilisateurs')

@section('content')
    <div id="layout-wrapper">

        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <div class="row">
            <div class="col-12 mb-4">
                <div class="card">
                    <div class="card-body">
                        <form method="GET" class="row g-3">
                            <div class="col-md-4">
                                <select class="form-select" name="role_id" onchange="this.form.submit()">
                                    <option value="">Tous les rôles</option>
                                    @foreach ($roles as $role)
                                        <option value="{{ $role->id }}" @selected(request('role_id') == $role->id)>{{ $role->libelle }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-4">
                                <select class="form-select" name="statut" onchange="this.form.submit()">
                                    <option value="">Tous les statuts</option>
                                    <option value="actif" @selected(request('statut') === 'actif')>Actif</option>
                                    <option value="inactif" @selected(request('statut') === 'inactif')>Inactif</option>
                                </select>
                            </div>
                            <div class="col-md-4 text-end">
                                <a href="{{ route('administration.utilisateurs.index') }}" class="btn btn-light-danger">Réinitialiser</a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            <div class="col-12">
                <div class="card">
                    <div class="card-header d-flex flex-wrap gap-3 justify-content-between align-items-center">
                        <h6 class="mb-0">Utilisateurs <span class="badge bg-secondary-subtle text-secondary ms-1">{{ $utilisateurs->count() }}</span></h6>
                        <a href="{{ route('administration.utilisateurs.create') }}" class="btn btn-primary"><i class="bi bi-person-plus me-1"></i>Nouvel utilisateur</a>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-box table-responsive">
                            <table class="table text-nowrap align-middle mb-0">
                                <thead>
                                    <tr>
                                        <th></th>
                                        <th>Nom</th>
                                        <th>Email / identifiant</th>
                                        <th>Rôle</th>
                                        <th>Département</th>
                                        <th>Statut</th>
                                        <th>Dernière activité</th>
                                        <th></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($utilisateurs as $utilisateur)
                                        <tr>
                                            <td><img src="{{ $utilisateur->avatarUrl() }}" class="avatar-sm rounded-circle" alt=""></td>
                                            <td class="fw-medium">{{ $utilisateur->name }}</td>
                                            <td>{{ $utilisateur->email }}@if ($utilisateur->identifiant)<br><span class="text-muted fs-12">{{ $utilisateur->identifiant }}</span>@endif</td>
                                            <td>{{ $utilisateur->role->libelle ?? '—' }}</td>
                                            <td>{{ $utilisateur->departement->nom ?? '—' }}</td>
                                            <td>
                                                @if ($utilisateur->statut === 'actif')
                                                    <span class="badge bg-success-subtle text-success">Actif</span>
                                                @else
                                                    <span class="badge bg-danger-subtle text-danger">Inactif</span>
                                                @endif
                                            </td>
                                            <td>{{ $utilisateur->derniere_activite_at?->diffForHumans() ?? '—' }}</td>
                                            <td>
                                                <div class="hstack gap-2">
                                                    <a href="{{ route('administration.utilisateurs.edit', $utilisateur) }}" class="btn btn-light-primary icon-btn-sm"><i class="bi bi-pencil-square"></i></a>
                                                    @if ($utilisateur->id !== auth()->id())
                                                        <form action="{{ route('administration.utilisateurs.toggle-statut', $utilisateur) }}" method="POST">
                                                            @csrf
                                                            <button type="submit" class="btn btn-light-{{ $utilisateur->statut === 'actif' ? 'warning' : 'success' }} icon-btn-sm" title="{{ $utilisateur->statut === 'actif' ? 'Désactiver' : 'Activer' }}">
                                                                <i class="bi bi-{{ $utilisateur->statut === 'actif' ? 'slash-circle' : 'check-circle' }}"></i>
                                                            </button>
                                                        </form>
                                                        <form action="{{ route('administration.utilisateurs.destroy', $utilisateur) }}" method="POST" onsubmit="return confirm('Supprimer cet utilisateur ?');">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="submit" class="btn btn-light-danger icon-btn-sm"><i class="ri-delete-bin-line"></i></button>
                                                        </form>
                                                    @endif
                                                </div>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr><td colspan="8" class="text-center text-muted py-5">Aucun utilisateur ne correspond à ces filtres.</td></tr>
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
