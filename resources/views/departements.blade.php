@extends('partials.layouts.master')

@section('title', 'Départements | FabKin Admin & Dashboards Template')
@section('title-sub', 'Réglages')
@section('pagetitle', 'Départements')

@section('content')
    <!-- Begin page -->
    <div id="layout-wrapper">

        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <div class="d-flex flex-wrap gap-3 justify-content-between align-items-center">
                            <h6 class="card-title mb-0 fw-semibold">
                                Départements <span class="badge bg-secondary-subtle text-secondary ms-1">{{ $departements->count() }}</span>
                            </h6>
                            <div class="d-flex flex-wrap gap-3 align-items-center">
                                <button type="button" class="btn btn-light-primary" data-bs-toggle="modal"
                                    data-bs-target="#createDepartementModal">
                                    <i class="bi bi-plus-lg me-1"></i>Ajouter un Département
                                </button>
                            </div>
                        </div>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-box table-responsive">
                            <table class="table text-nowrap align-middle mb-0">
                                <thead>
                                    <tr>
                                        <th scope="col">Nom</th>
                                        <th scope="col">Description</th>
                                        <th scope="col">Créé le</th>
                                        <th scope="col">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($departements as $departement)
                                        <tr>
                                            <td>
                                                <div class="d-flex align-items-center gap-3">
                                                    <div
                                                        class="flex-shrink-0 h-32px w-32px bg-primary-subtle d-flex justify-content-center align-items-center rounded-pill text-primary fs-6">
                                                        <i class="bi bi-diagram-3"></i>
                                                    </div>
                                                    <h6 class="mb-0 fw-medium fs-13">{{ $departement->nom }}</h6>
                                                </div>
                                            </td>
                                            <td>{{ $departement->description ?: '—' }}</td>
                                            <td>{{ $departement->created_at->format('d M Y') }}</td>
                                            <td>
                                                <div class="hstack gap-2">
                                                    <button type="button" class="btn btn-light-primary icon-btn-sm"
                                                        data-bs-toggle="modal"
                                                        data-bs-target="#editDepartementModal{{ $departement->id }}"
                                                        data-bs-custom-class="tooltip-white" data-bs-title="Modifier">
                                                        <i class="bi bi-pencil-square"></i>
                                                    </button>
                                                    <form action="{{ route('departements.destroy', $departement) }}"
                                                        method="POST"
                                                        onsubmit="return confirm('Supprimer ce département ?');">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="btn btn-light-danger icon-btn-sm"
                                                            data-bs-toggle="tooltip" data-bs-custom-class="tooltip-white"
                                                            data-bs-placement="top" data-bs-title="Supprimer">
                                                            <i class="ri-delete-bin-line"></i>
                                                        </button>
                                                    </form>
                                                </div>
                                            </td>
                                        </tr>

                                        <!-- Edit Departement Modal -->
                                        <div class="modal fade" id="editDepartementModal{{ $departement->id }}"
                                            tabindex="-1" aria-labelledby="editDepartementModalLabel{{ $departement->id }}"
                                            aria-hidden="true">
                                            <div class="modal-dialog modal-dialog-centered" role="document">
                                                <div class="modal-content">
                                                    <form action="{{ route('departements.update', $departement) }}"
                                                        method="POST">
                                                        @csrf
                                                        @method('PUT')
                                                        <div class="modal-header">
                                                            <h5 class="modal-title"
                                                                id="editDepartementModalLabel{{ $departement->id }}">
                                                                Modifier le Département</h5>
                                                            <button type="button" class="btn-close icon-btn-sm"
                                                                data-bs-dismiss="modal" aria-label="Close">
                                                                <i class="ri-close-large-line fw-semibold"></i>
                                                            </button>
                                                        </div>
                                                        <div class="modal-body">
                                                            <div class="row g-3">
                                                                <div class="col-12">
                                                                    <label class="form-label">Nom</label>
                                                                    <input type="text" class="form-control" name="nom"
                                                                        value="{{ $departement->nom }}" required>
                                                                </div>
                                                                <div class="col-12">
                                                                    <label class="form-label">Description</label>
                                                                    <input type="text" class="form-control"
                                                                        name="description"
                                                                        value="{{ $departement->description }}">
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="modal-footer">
                                                            <button type="button" class="btn btn-light"
                                                                data-bs-dismiss="modal">Fermer</button>
                                                            <button type="submit" class="btn btn-primary">Enregistrer</button>
                                                        </div>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                    @empty
                                        <tr>
                                            <td colspan="4" class="text-center text-muted py-5">Aucun département pour le
                                                moment.</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Create Departement Modal -->
        <div class="modal fade" id="createDepartementModal" data-bs-keyboard="false" tabindex="-1"
            aria-labelledby="createDepartementModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered" role="document">
                <div class="modal-content">
                    <form action="{{ route('departements.store') }}" method="POST">
                        @csrf
                        <div class="modal-header">
                            <h5 class="modal-title" id="createDepartementModalLabel">Ajouter un Département</h5>
                            <button type="button" class="btn-close icon-btn-sm" data-bs-dismiss="modal"
                                aria-label="Close">
                                <i class="ri-close-large-line fw-semibold"></i>
                            </button>
                        </div>
                        <div class="modal-body">
                            <div class="row g-3">
                                <div class="col-12">
                                    <label for="departementNom" class="form-label">Nom</label>
                                    <input type="text" class="form-control" id="departementNom" name="nom"
                                        placeholder="Ex: Direction" required>
                                </div>
                                <div class="col-12">
                                    <label for="departementDescription" class="form-label">Description</label>
                                    <input type="text" class="form-control" id="departementDescription"
                                        name="description" placeholder="Description (optionnel)">
                                </div>
                            </div>
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
    <!-- App js -->
    <script type="module" src="assets/js/app.js"></script>
@endsection
