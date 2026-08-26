@extends('partials.layouts.master-central')

@section('title', 'Packages | Espace éditeur')
@section('title-sub', 'Espace éditeur')
@section('pagetitle', 'Packages')

@section('content')
    <div id="layout-wrapper">

        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif
        @if ($errors->any())
            <div class="alert alert-danger">
                <ul class="mb-0">
                    @foreach ($errors->all() as $erreur)
                        <li>{{ $erreur }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="row g-4">
            <div class="col-lg-8">
                <div class="card">
                    <div class="card-header"><h6 class="mb-0">Packages existants</h6></div>
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>Nom</th>
                                    <th>Modules inclus</th>
                                    <th>Licences émises</th>
                                    <th>Statut</th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($packages as $package)
                                    <tr>
                                        <td class="fw-medium">{{ $package->nom }}</td>
                                        <td class="fs-12">
                                            @foreach ($package->modules as $cle)
                                                <span class="badge bg-light text-dark border me-1">{{ $catalogue[$cle]['nom'] ?? $cle }}</span>
                                            @endforeach
                                            <span class="badge bg-light text-dark border">Administration</span>
                                        </td>
                                        <td>{{ $package->licences_count }}</td>
                                        <td>
                                            @if ($package->actif)
                                                <span class="badge bg-success-subtle text-success">Actif</span>
                                            @else
                                                <span class="badge bg-secondary-subtle text-secondary">Désactivé</span>
                                            @endif
                                        </td>
                                        <td class="text-end">
                                            <button type="button" class="btn btn-sm btn-outline-secondary" data-bs-toggle="modal" data-bs-target="#editModal{{ $package->id }}">Modifier</button>
                                            <form action="{{ route('central.packages.toggle', $package) }}" method="POST" class="d-inline">
                                                @csrf
                                                <button type="submit" class="btn btn-sm btn-outline-{{ $package->actif ? 'danger' : 'success' }}">
                                                    {{ $package->actif ? 'Désactiver' : 'Activer' }}
                                                </button>
                                            </form>
                                        </td>
                                    </tr>

                                    <div class="modal fade" id="editModal{{ $package->id }}" tabindex="-1" aria-hidden="true">
                                        <div class="modal-dialog modal-dialog-centered">
                                            <div class="modal-content">
                                                <form action="{{ route('central.packages.update', $package) }}" method="POST">
                                                    @csrf
                                                    @method('PUT')
                                                    <div class="modal-header">
                                                        <h6 class="modal-title">Modifier « {{ $package->nom }} »</h6>
                                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                    </div>
                                                    <div class="modal-body">
                                                        <div class="mb-3">
                                                            <label class="form-label">Nom <span class="text-danger">*</span></label>
                                                            <input type="text" class="form-control" name="nom" value="{{ $package->nom }}" required>
                                                        </div>
                                                        <div class="mb-3">
                                                            <label class="form-label">Description</label>
                                                            <textarea class="form-control" name="description" rows="2">{{ $package->description }}</textarea>
                                                        </div>
                                                        <div class="mb-0">
                                                            <label class="form-label">Modules inclus</label>
                                                            @foreach ($catalogue as $cle => $meta)
                                                                @continue($cle === 'administration')
                                                                <div class="form-check">
                                                                    <input class="form-check-input" type="checkbox" id="edit-{{ $package->id }}-{{ $cle }}" name="modules[]" value="{{ $cle }}" {{ in_array($cle, $package->modules) ? 'checked' : '' }}>
                                                                    <label class="form-check-label" for="edit-{{ $package->id }}-{{ $cle }}">{{ $meta['nom'] }}</label>
                                                                </div>
                                                            @endforeach
                                                        </div>
                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Annuler</button>
                                                        <button type="submit" class="btn btn-primary">Enregistrer</button>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                @empty
                                    <tr>
                                        <td colspan="5" class="text-center text-muted py-4">Aucun package pour le moment — créez-en un pour pouvoir créer des sociétés.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <div class="col-lg-4">
                <div class="card">
                    <div class="card-header"><h6 class="mb-0">Nouveau package</h6></div>
                    <div class="card-body">
                        <form action="{{ route('central.packages.store') }}" method="POST">
                            @csrf
                            <div class="mb-3">
                                <label for="nom" class="form-label">Nom <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="nom" name="nom" value="{{ old('nom') }}" placeholder="ex: Standard" required>
                            </div>
                            <div class="mb-3">
                                <label for="description" class="form-label">Description</label>
                                <textarea class="form-control" id="description" name="description" rows="2">{{ old('description') }}</textarea>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Modules inclus</label>
                                @foreach ($catalogue as $cle => $meta)
                                    @continue($cle === 'administration')
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" id="new-{{ $cle }}" name="modules[]" value="{{ $cle }}" {{ in_array($cle, old('modules', [])) ? 'checked' : '' }}>
                                        <label class="form-check-label" for="new-{{ $cle }}">{{ $meta['nom'] }}</label>
                                    </div>
                                @endforeach
                            </div>
                            <button type="submit" class="btn btn-primary w-100">
                                <i class="bi bi-plus-lg me-1"></i>Créer le package
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>

    </div>
@endsection

@section('js')
    <script type="module" src="{{ asset('assets/js/app.js') }}"></script>
@endsection
