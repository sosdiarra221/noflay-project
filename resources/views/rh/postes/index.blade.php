@extends('partials.layouts.master-rh')

@section('title', 'Postes | RH')
@section('title-sub', 'RH')
@section('pagetitle', 'Postes')

@section('content')
    <div id="layout-wrapper">

        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif
        @if ($errors->any())
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <ul class="mb-0 ps-3">
                    @foreach ($errors->all() as $erreur)
                        <li>{{ $erreur }}</li>
                    @endforeach
                </ul>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h6 class="mb-0">Postes <span class="badge bg-secondary-subtle text-secondary ms-1">{{ $postes->count() }}</span></h6>
                <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#creerPosteModal">
                    <i class="bi bi-plus-lg me-1"></i>Nouveau poste
                </button>
            </div>
            <div class="card-body p-0">
                <div class="table-box table-responsive">
                    <table class="table text-nowrap align-middle mb-0">
                        <thead><tr><th>Poste</th><th>Fiche de poste</th><th>Employés</th><th>Statut</th><th></th></tr></thead>
                        <tbody>
                            @forelse ($postes as $poste)
                                <tr>
                                    <td class="fw-medium">{{ $poste->nom }}</td>
                                    <td class="text-muted fs-12">
                                        @if ($poste->fiche_poste)
                                            {{ \Illuminate\Support\Str::limit($poste->fiche_poste, 80) }}
                                        @else
                                            —
                                        @endif
                                    </td>
                                    <td>{{ $poste->employes_count }}</td>
                                    <td>
                                        @if ($poste->actif)
                                            <span class="badge bg-success-subtle text-success">Actif</span>
                                        @else
                                            <span class="badge bg-secondary-subtle text-secondary">Inactif</span>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="hstack gap-2">
                                            @if ($poste->fiche_poste)
                                                <button type="button" class="btn btn-light-info icon-btn-sm" data-bs-toggle="modal" data-bs-target="#ficheeposteModal{{ $poste->id }}" title="Voir la fiche de poste"><i class="bi bi-file-text"></i></button>
                                            @endif
                                            <button type="button" class="btn btn-light-primary icon-btn-sm" data-bs-toggle="modal" data-bs-target="#modifierPosteModal{{ $poste->id }}" title="Modifier"><i class="bi bi-pencil-square"></i></button>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr><td colspan="5" class="text-center text-muted py-5">Aucun poste enregistré.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

    </div>

    <div class="modal fade" id="creerPosteModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
            <div class="modal-content">
                <form action="{{ route('rh.postes.store') }}" method="POST">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title">Nouveau poste</h5>
                        <button type="button" class="btn-close icon-btn-sm" data-bs-dismiss="modal" aria-label="Close"><i class="ri-close-large-line fw-semibold"></i></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">Nom du poste<span class="text-danger ms-1">*</span></label>
                            <input type="text" class="form-control" name="nom" required>
                        </div>
                        <div class="mb-0">
                            <label class="form-label">Fiche de poste</label>
                            <textarea class="form-control" name="fiche_poste" rows="8" placeholder="Missions, responsabilités, compétences attendues..."></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Annuler</button>
                        <button type="submit" class="btn btn-primary">Créer</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    @foreach ($postes as $poste)
        <div class="modal fade" id="modifierPosteModal{{ $poste->id }}" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
                <div class="modal-content">
                    <form action="{{ route('rh.postes.update', $poste) }}" method="POST">
                        @csrf
                        @method('PUT')
                        <div class="modal-header">
                            <h5 class="modal-title">Modifier {{ $poste->nom }}</h5>
                            <button type="button" class="btn-close icon-btn-sm" data-bs-dismiss="modal" aria-label="Close"><i class="ri-close-large-line fw-semibold"></i></button>
                        </div>
                        <div class="modal-body">
                            <div class="mb-3">
                                <label class="form-label">Nom du poste<span class="text-danger ms-1">*</span></label>
                                <input type="text" class="form-control" name="nom" value="{{ $poste->nom }}" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Fiche de poste</label>
                                <textarea class="form-control" name="fiche_poste" rows="8">{{ $poste->fiche_poste }}</textarea>
                            </div>
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" name="actif" value="1" id="actifPoste{{ $poste->id }}" @checked($poste->actif)>
                                <label class="form-check-label" for="actifPoste{{ $poste->id }}">Poste actif (proposé à la création d'un employé)</label>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-light" data-bs-dismiss="modal">Annuler</button>
                            <button type="submit" class="btn btn-primary">Enregistrer</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        @if ($poste->fiche_poste)
            <div class="modal fade" id="ficheeposteModal{{ $poste->id }}" tabindex="-1" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title">Fiche de poste — {{ $poste->nom }}</h5>
                            <button type="button" class="btn-close icon-btn-sm" data-bs-dismiss="modal" aria-label="Close"><i class="ri-close-large-line fw-semibold"></i></button>
                        </div>
                        <div class="modal-body">
                            <p class="mb-0" style="white-space: pre-line;">{{ $poste->fiche_poste }}</p>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-light" data-bs-dismiss="modal">Fermer</button>
                        </div>
                    </div>
                </div>
            </div>
        @endif
    @endforeach

    </main>
@endsection

@section('js')
    <script type="module" src="{{ asset('assets/js/app.js') }}"></script>
@endsection
