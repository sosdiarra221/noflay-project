@extends('partials.layouts.master-locative')

@section('title', 'Paramètres | Locative')
@section('title-sub', 'Locative')
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
                    <ul class="nav nav-pills" id="parametresTab" role="tablist">
                        <li class="nav-item" role="presentation">
                            <button class="nav-link active" id="categories-tab" data-bs-toggle="tab"
                                data-bs-target="#categories-tab-pane" type="button" role="tab"
                                aria-controls="categories-tab-pane" aria-selected="true">Catégories de biens</button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="modes-tab" data-bs-toggle="tab" data-bs-target="#modes-tab-pane"
                                type="button" role="tab" aria-controls="modes-tab-pane"
                                aria-selected="false">Modes de paiement</button>
                        </li>
                    </ul>
                </div>

                <div class="tab-content" id="parametresTabContent">

                    {{-- Onglet Catégories de biens --}}
                    <div class="tab-pane fade show active" id="categories-tab-pane" role="tabpanel"
                        aria-labelledby="categories-tab" tabindex="0">
                        <div class="card">
                            <div class="card-header">
                                <div class="d-flex flex-wrap gap-3 justify-content-between align-items-center">
                                    <h6 class="card-title mb-0 fw-semibold">
                                        Catégories de biens <span class="badge bg-secondary-subtle text-secondary ms-1">{{ $categories->count() }}</span>
                                    </h6>
                                    <button type="button" class="btn btn-light-primary" data-bs-toggle="modal"
                                        data-bs-target="#createCategorieModal">
                                        <i class="bi bi-plus-lg me-1"></i>Ajouter une catégorie
                                    </button>
                                </div>
                            </div>
                            <div class="card-body p-0">
                                <div class="table-box table-responsive">
                                    <table class="table text-nowrap align-middle mb-0">
                                        <thead>
                                            <tr>
                                                <th scope="col">Nom</th>
                                                <th scope="col">Statut</th>
                                                <th scope="col">Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @forelse ($categories as $categorie)
                                                <tr>
                                                    <td>{{ $categorie->nom }}</td>
                                                    <td>
                                                        @if ($categorie->actif)
                                                            <span class="badge bg-success-subtle text-success">Active</span>
                                                        @else
                                                            <span class="badge bg-light-subtle text-body">Inactive</span>
                                                        @endif
                                                    </td>
                                                    <td>
                                                        <div class="hstack gap-2">
                                                            <button type="button" class="btn btn-light-primary icon-btn-sm"
                                                                data-bs-toggle="modal"
                                                                data-bs-target="#editCategorieModal{{ $categorie->id }}">
                                                                <i class="bi bi-pencil-square"></i>
                                                            </button>
                                                            <form action="{{ route('locative.categories-biens.destroy', $categorie) }}"
                                                                method="POST"
                                                                onsubmit="return confirm('Supprimer cette catégorie ?');">
                                                                @csrf
                                                                @method('DELETE')
                                                                <button type="submit" class="btn btn-light-danger icon-btn-sm">
                                                                    <i class="ri-delete-bin-line"></i>
                                                                </button>
                                                            </form>
                                                        </div>
                                                    </td>
                                                </tr>
                                            @empty
                                                <tr>
                                                    <td colspan="3" class="text-center text-muted py-5">Aucune catégorie enregistrée.</td>
                                                </tr>
                                            @endforelse
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                        @foreach ($categories as $categorie)
                            <div class="modal fade" id="editCategorieModal{{ $categorie->id }}" tabindex="-1" aria-hidden="true">
                                <div class="modal-dialog modal-dialog-centered" role="document">
                                    <div class="modal-content">
                                        <form action="{{ route('locative.categories-biens.update', $categorie) }}" method="POST">
                                            @csrf
                                            @method('PUT')
                                            <div class="modal-header">
                                                <h5 class="modal-title">Modifier la catégorie</h5>
                                                <button type="button" class="btn-close icon-btn-sm" data-bs-dismiss="modal" aria-label="Close">
                                                    <i class="ri-close-large-line fw-semibold"></i>
                                                </button>
                                            </div>
                                            <div class="modal-body">
                                                <div class="row g-3">
                                                    <div class="col-12">
                                                        <label class="form-label">Nom</label>
                                                        <input type="text" class="form-control" name="nom" value="{{ $categorie->nom }}" required>
                                                    </div>
                                                    <div class="col-12">
                                                        <div class="form-check">
                                                            <input class="form-check-input" type="checkbox" value="1" name="actif" id="categorieActif{{ $categorie->id }}" @checked($categorie->actif)>
                                                            <label class="form-check-label" for="categorieActif{{ $categorie->id }}">Catégorie active</label>
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

                    {{-- Onglet Modes de paiement --}}
                    <div class="tab-pane fade" id="modes-tab-pane" role="tabpanel" aria-labelledby="modes-tab" tabindex="0">
                        <div class="card">
                            <div class="card-header">
                                <div class="d-flex flex-wrap gap-3 justify-content-between align-items-center">
                                    <h6 class="card-title mb-0 fw-semibold">
                                        Modes de paiement <span class="badge bg-secondary-subtle text-secondary ms-1">{{ $modesPaiement->count() }}</span>
                                    </h6>
                                    <button type="button" class="btn btn-light-primary" data-bs-toggle="modal"
                                        data-bs-target="#createModeModal">
                                        <i class="bi bi-plus-lg me-1"></i>Ajouter un mode
                                    </button>
                                </div>
                            </div>
                            <div class="card-body p-0">
                                <div class="table-box table-responsive">
                                    <table class="table text-nowrap align-middle mb-0">
                                        <thead>
                                            <tr>
                                                <th scope="col">Nom</th>
                                                <th scope="col">Statut</th>
                                                <th scope="col">Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @forelse ($modesPaiement as $mode)
                                                <tr>
                                                    <td>{{ $mode->nom }}</td>
                                                    <td>
                                                        @if ($mode->actif)
                                                            <span class="badge bg-success-subtle text-success">Actif</span>
                                                        @else
                                                            <span class="badge bg-light-subtle text-body">Inactif</span>
                                                        @endif
                                                    </td>
                                                    <td>
                                                        <div class="hstack gap-2">
                                                            <button type="button" class="btn btn-light-primary icon-btn-sm"
                                                                data-bs-toggle="modal"
                                                                data-bs-target="#editModeModal{{ $mode->id }}">
                                                                <i class="bi bi-pencil-square"></i>
                                                            </button>
                                                            <form action="{{ route('locative.modes-paiement.destroy', $mode) }}"
                                                                method="POST"
                                                                onsubmit="return confirm('Supprimer ce mode de paiement ?');">
                                                                @csrf
                                                                @method('DELETE')
                                                                <button type="submit" class="btn btn-light-danger icon-btn-sm">
                                                                    <i class="ri-delete-bin-line"></i>
                                                                </button>
                                                            </form>
                                                        </div>
                                                    </td>
                                                </tr>
                                            @empty
                                                <tr>
                                                    <td colspan="3" class="text-center text-muted py-5">Aucun mode de paiement enregistré.</td>
                                                </tr>
                                            @endforelse
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                        @foreach ($modesPaiement as $mode)
                            <div class="modal fade" id="editModeModal{{ $mode->id }}" tabindex="-1" aria-hidden="true">
                                <div class="modal-dialog modal-dialog-centered" role="document">
                                    <div class="modal-content">
                                        <form action="{{ route('locative.modes-paiement.update', $mode) }}" method="POST">
                                            @csrf
                                            @method('PUT')
                                            <div class="modal-header">
                                                <h5 class="modal-title">Modifier le mode de paiement</h5>
                                                <button type="button" class="btn-close icon-btn-sm" data-bs-dismiss="modal" aria-label="Close">
                                                    <i class="ri-close-large-line fw-semibold"></i>
                                                </button>
                                            </div>
                                            <div class="modal-body">
                                                <div class="row g-3">
                                                    <div class="col-12">
                                                        <label class="form-label">Nom</label>
                                                        <input type="text" class="form-control" name="nom" value="{{ $mode->nom }}" required>
                                                    </div>
                                                    <div class="col-12">
                                                        <div class="form-check">
                                                            <input class="form-check-input" type="checkbox" value="1" name="actif" id="modeActif{{ $mode->id }}" @checked($mode->actif)>
                                                            <label class="form-check-label" for="modeActif{{ $mode->id }}">Mode actif</label>
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

        <!-- Create Categorie Modal -->
        <div class="modal fade" id="createCategorieModal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered" role="document">
                <div class="modal-content">
                    <form action="{{ route('locative.categories-biens.store') }}" method="POST">
                        @csrf
                        <div class="modal-header">
                            <h5 class="modal-title">Ajouter une catégorie</h5>
                            <button type="button" class="btn-close icon-btn-sm" data-bs-dismiss="modal" aria-label="Close">
                                <i class="ri-close-large-line fw-semibold"></i>
                            </button>
                        </div>
                        <div class="modal-body">
                            <label class="form-label">Nom</label>
                            <input type="text" class="form-control" name="nom" placeholder="Ex: Appartement" required>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-light" data-bs-dismiss="modal">Fermer</button>
                            <button type="submit" class="btn btn-primary">Ajouter</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Create Mode Modal -->
        <div class="modal fade" id="createModeModal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered" role="document">
                <div class="modal-content">
                    <form action="{{ route('locative.modes-paiement.store') }}" method="POST">
                        @csrf
                        <div class="modal-header">
                            <h5 class="modal-title">Ajouter un mode de paiement</h5>
                            <button type="button" class="btn-close icon-btn-sm" data-bs-dismiss="modal" aria-label="Close">
                                <i class="ri-close-large-line fw-semibold"></i>
                            </button>
                        </div>
                        <div class="modal-body">
                            <label class="form-label">Nom</label>
                            <input type="text" class="form-control" name="nom" placeholder="Ex: Wave" required>
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
