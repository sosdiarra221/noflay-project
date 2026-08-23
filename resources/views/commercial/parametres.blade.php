@extends('partials.layouts.master-commercial')

@section('title', 'Paramètres | Commercial')
@section('title-sub', 'Commercial')
@section('pagetitle', 'Paramètres')

@section('content')
    <div id="layout-wrapper">

        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <div class="row">
            <div class="col-12">
                <div class="mb-6">
                    <ul class="nav nav-pills" role="tablist">
                        <li class="nav-item"><button class="nav-link active" data-bs-toggle="tab" data-bs-target="#sources-pane" type="button">Sources</button></li>
                        <li class="nav-item"><button class="nav-link" data-bs-toggle="tab" data-bs-target="#types-pane" type="button">Types de demande</button></li>
                    </ul>
                </div>

                <div class="tab-content">
                    <div class="tab-pane fade show active" id="sources-pane">
                        <div class="card">
                            <div class="card-header d-flex justify-content-between align-items-center">
                                <h6 class="card-title mb-0 fw-semibold">Sources <span class="badge bg-secondary-subtle text-secondary ms-1">{{ $sources->count() }}</span></h6>
                                <button type="button" class="btn btn-light-primary" data-bs-toggle="modal" data-bs-target="#createSourceModal"><i class="bi bi-plus-lg me-1"></i>Ajouter</button>
                            </div>
                            <div class="card-body p-0">
                                <div class="table-box table-responsive">
                                    <table class="table text-nowrap align-middle mb-0">
                                        <thead><tr><th>Nom</th><th>Statut</th><th>Actions</th></tr></thead>
                                        <tbody>
                                            @forelse ($sources as $source)
                                                <tr>
                                                    <td>{{ $source->nom }}</td>
                                                    <td>@if ($source->actif)<span class="badge bg-success-subtle text-success">Active</span>@else<span class="badge bg-light-subtle text-body">Inactive</span>@endif</td>
                                                    <td>
                                                        <div class="hstack gap-2">
                                                            <button type="button" class="btn btn-light-primary icon-btn-sm" data-bs-toggle="modal" data-bs-target="#editSourceModal{{ $source->id }}"><i class="bi bi-pencil-square"></i></button>
                                                            <form action="{{ route('commercial.sources.destroy', $source) }}" method="POST" onsubmit="return confirm('Supprimer cette source ?');">
                                                                @csrf
                                                                @method('DELETE')
                                                                <button type="submit" class="btn btn-light-danger icon-btn-sm"><i class="ri-delete-bin-line"></i></button>
                                                            </form>
                                                        </div>
                                                    </td>
                                                </tr>
                                            @empty
                                                <tr><td colspan="3" class="text-center text-muted py-5">Aucune source.</td></tr>
                                            @endforelse
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                        @foreach ($sources as $source)
                            <div class="modal fade" id="editSourceModal{{ $source->id }}" tabindex="-1" aria-hidden="true">
                                <div class="modal-dialog modal-dialog-centered" role="document">
                                    <div class="modal-content">
                                        <form action="{{ route('commercial.sources.update', $source) }}" method="POST">
                                            @csrf
                                            @method('PUT')
                                            <div class="modal-header">
                                                <h5 class="modal-title">Modifier la source</h5>
                                                <button type="button" class="btn-close icon-btn-sm" data-bs-dismiss="modal" aria-label="Close"><i class="ri-close-large-line fw-semibold"></i></button>
                                            </div>
                                            <div class="modal-body">
                                                <div class="row g-3">
                                                    <div class="col-12">
                                                        <label class="form-label">Nom</label>
                                                        <input type="text" class="form-control" name="nom" value="{{ $source->nom }}" required>
                                                    </div>
                                                    <div class="col-12">
                                                        <div class="form-check">
                                                            <input class="form-check-input" type="checkbox" value="1" name="actif" id="sourceActif{{ $source->id }}" @checked($source->actif)>
                                                            <label class="form-check-label" for="sourceActif{{ $source->id }}">Source active</label>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-light" data-bs-dismiss="modal">Fermer</button>
                                                <button type="submit" class="btn btn-primary">Enregistrer</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <div class="tab-pane fade" id="types-pane">
                        <div class="card">
                            <div class="card-header d-flex justify-content-between align-items-center">
                                <h6 class="card-title mb-0 fw-semibold">Types de demande <span class="badge bg-secondary-subtle text-secondary ms-1">{{ $typesDemande->count() }}</span></h6>
                                <button type="button" class="btn btn-light-primary" data-bs-toggle="modal" data-bs-target="#createTypeModal"><i class="bi bi-plus-lg me-1"></i>Ajouter</button>
                            </div>
                            <div class="card-body p-0">
                                <div class="table-box table-responsive">
                                    <table class="table text-nowrap align-middle mb-0">
                                        <thead><tr><th>Nom</th><th>Statut</th><th>Actions</th></tr></thead>
                                        <tbody>
                                            @forelse ($typesDemande as $type)
                                                <tr>
                                                    <td>{{ $type->nom }}</td>
                                                    <td>@if ($type->actif)<span class="badge bg-success-subtle text-success">Actif</span>@else<span class="badge bg-light-subtle text-body">Inactif</span>@endif</td>
                                                    <td>
                                                        <div class="hstack gap-2">
                                                            <button type="button" class="btn btn-light-primary icon-btn-sm" data-bs-toggle="modal" data-bs-target="#editTypeModal{{ $type->id }}"><i class="bi bi-pencil-square"></i></button>
                                                            <form action="{{ route('commercial.types-demande.destroy', $type) }}" method="POST" onsubmit="return confirm('Supprimer ce type ?');">
                                                                @csrf
                                                                @method('DELETE')
                                                                <button type="submit" class="btn btn-light-danger icon-btn-sm"><i class="ri-delete-bin-line"></i></button>
                                                            </form>
                                                        </div>
                                                    </td>
                                                </tr>
                                            @empty
                                                <tr><td colspan="3" class="text-center text-muted py-5">Aucun type de demande.</td></tr>
                                            @endforelse
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                        @foreach ($typesDemande as $type)
                            <div class="modal fade" id="editTypeModal{{ $type->id }}" tabindex="-1" aria-hidden="true">
                                <div class="modal-dialog modal-dialog-centered" role="document">
                                    <div class="modal-content">
                                        <form action="{{ route('commercial.types-demande.update', $type) }}" method="POST">
                                            @csrf
                                            @method('PUT')
                                            <div class="modal-header">
                                                <h5 class="modal-title">Modifier le type de demande</h5>
                                                <button type="button" class="btn-close icon-btn-sm" data-bs-dismiss="modal" aria-label="Close"><i class="ri-close-large-line fw-semibold"></i></button>
                                            </div>
                                            <div class="modal-body">
                                                <div class="row g-3">
                                                    <div class="col-12">
                                                        <label class="form-label">Nom</label>
                                                        <input type="text" class="form-control" name="nom" value="{{ $type->nom }}" required>
                                                    </div>
                                                    <div class="col-12">
                                                        <div class="form-check">
                                                            <input class="form-check-input" type="checkbox" value="1" name="actif" id="typeActif{{ $type->id }}" @checked($type->actif)>
                                                            <label class="form-check-label" for="typeActif{{ $type->id }}">Type actif</label>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-light" data-bs-dismiss="modal">Fermer</button>
                                                <button type="submit" class="btn btn-primary">Enregistrer</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>

        <div class="modal fade" id="createSourceModal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered" role="document">
                <div class="modal-content">
                    <form action="{{ route('commercial.sources.store') }}" method="POST">
                        @csrf
                        <div class="modal-header">
                            <h5 class="modal-title">Ajouter une source</h5>
                            <button type="button" class="btn-close icon-btn-sm" data-bs-dismiss="modal" aria-label="Close"><i class="ri-close-large-line fw-semibold"></i></button>
                        </div>
                        <div class="modal-body">
                            <label class="form-label">Nom</label>
                            <input type="text" class="form-control" name="nom" placeholder="Ex: LinkedIn" required>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-light" data-bs-dismiss="modal">Fermer</button>
                            <button type="submit" class="btn btn-primary">Ajouter</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="modal fade" id="createTypeModal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered" role="document">
                <div class="modal-content">
                    <form action="{{ route('commercial.types-demande.store') }}" method="POST">
                        @csrf
                        <div class="modal-header">
                            <h5 class="modal-title">Ajouter un type de demande</h5>
                            <button type="button" class="btn-close icon-btn-sm" data-bs-dismiss="modal" aria-label="Close"><i class="ri-close-large-line fw-semibold"></i></button>
                        </div>
                        <div class="modal-body">
                            <label class="form-label">Nom</label>
                            <input type="text" class="form-control" name="nom" placeholder="Ex: Colocation" required>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-light" data-bs-dismiss="modal">Fermer</button>
                            <button type="submit" class="btn btn-primary">Ajouter</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

    </div>
    </main>
@endsection

@section('js')
    <script type="module" src="{{ asset('assets/js/app.js') }}"></script>
@endsection
