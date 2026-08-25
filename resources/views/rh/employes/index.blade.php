@extends('partials.layouts.master-rh')

@section('title', 'Employés | RH')
@section('title-sub', 'RH')
@section('pagetitle', 'Employés')

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

        <div class="card">
            <div class="card-body">
                <form method="GET" class="row g-3 align-items-end">
                    <div class="col-md-3">
                        <label class="form-label">Recherche</label>
                        <input type="text" class="form-control" name="recherche" value="{{ request('recherche') }}" placeholder="Nom, matricule, téléphone...">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Département</label>
                        <select class="form-select" name="departement_id">
                            <option value="">Tous</option>
                            @foreach ($departements as $departement)
                                <option value="{{ $departement->id }}" @selected(request('departement_id') == $departement->id)>{{ $departement->nom }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Poste</label>
                        <select class="form-select" name="poste_id">
                            <option value="">Tous</option>
                            @foreach ($postes as $poste)
                                <option value="{{ $poste->id }}" @selected(request('poste_id') == $poste->id)>{{ $poste->nom }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">Statut</label>
                        <select class="form-select" name="statut">
                            <option value="actif" @selected(request('statut', 'actif') === 'actif')>Actif</option>
                            <option value="sortie" @selected(request('statut') === 'sortie')>Sortie</option>
                        </select>
                    </div>
                    <div class="col-md-1 d-flex gap-2">
                        <button type="submit" class="btn btn-primary w-100">Filtrer</button>
                    </div>
                </form>
            </div>
        </div>

        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h6 class="mb-0">Employés <span class="badge bg-secondary-subtle text-secondary ms-1">{{ $employes->count() }}</span></h6>
                @can('rh.gerer')
                    <a href="{{ route('rh.employes.create') }}" class="btn btn-primary">
                        <i class="bi bi-plus-lg me-1"></i>Nouvel employé
                    </a>
                @endcan
            </div>
            <div class="card-body p-0">
                <div class="table-box table-responsive">
                    <table class="table text-nowrap align-middle mb-0">
                        <thead><tr><th>Matricule</th><th>Nom</th><th>Poste</th><th>Département</th><th>Sites</th><th>Contrat</th><th>Statut</th><th>Actions</th></tr></thead>
                        <tbody>
                            @forelse ($employes as $employe)
                                <tr>
                                    <td class="fw-medium">{{ $employe->matricule }}</td>
                                    <td>
                                        <div class="d-flex align-items-center gap-2">
                                            <img src="{{ $employe->photo ? asset('storage/'.$employe->photo) : asset('assets/images/avatar/avatar-10.jpg') }}" class="rounded-circle" width="32" height="32" style="object-fit: cover;">
                                            {{ $employe->nom_complet }}
                                        </div>
                                    </td>
                                    <td>{{ $employe->poste->nom ?? '—' }}</td>
                                    <td>{{ $employe->departement->nom ?? '—' }}</td>
                                    <td class="text-muted fs-12">{{ $employe->sites->pluck('nom')->implode(', ') ?: '—' }}</td>
                                    <td>
                                        @if ($employe->contratActif)
                                            <span class="badge bg-success-subtle text-success">{{ $employe->contratActif->libelleType() }}</span>
                                        @else
                                            <span class="badge bg-danger-subtle text-danger">Aucun</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if ($employe->statut === 'actif')
                                            <span class="badge bg-success-subtle text-success">Actif</span>
                                        @else
                                            <span class="badge bg-secondary-subtle text-secondary">Sortie</span>
                                        @endif
                                    </td>
                                    <td>
                                        <a href="{{ route('rh.employes.show', $employe) }}" class="btn btn-light-success btn-sm" title="Voir la fiche"><i class="bi bi-eye me-1"></i>Détail</a>
                                    </td>
                                </tr>
                            @empty
                                <tr><td colspan="8" class="text-center text-muted py-5">Aucun employé ne correspond à ces filtres.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
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
            new Choices(document.querySelector('select[name="departement_id"]'), { searchEnabled: true, itemSelectText: '' });
            new Choices(document.querySelector('select[name="poste_id"]'), { searchEnabled: true, itemSelectText: '' });
            new Choices(document.querySelector('select[name="statut"]'), { searchEnabled: false, itemSelectText: '' });
        });
    </script>
@endsection
