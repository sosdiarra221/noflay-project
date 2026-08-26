@extends('partials.layouts.master-administration')

@section('title', "Types d'absence | Administration")
@section('title-sub', 'Administration')
@section('pagetitle', "Types d'absence")

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
                <div>
                    <h6 class="mb-0">Types d'absence <span class="badge bg-secondary-subtle text-secondary ms-1">{{ $typesAbsence->count() }}</span></h6>
                    <p class="text-muted fs-12 mb-0">Un type marqué « Urgence » n'est pas soumis au délai de préavis de 3 jours sur une demande d'absence.</p>
                </div>
                <button type="button" class="btn btn-primary text-nowrap" data-bs-toggle="modal" data-bs-target="#creerTypeAbsenceModal">
                    <i class="bi bi-plus-lg me-1"></i>Nouveau type
                </button>
            </div>
            <div class="card-body p-0">
                <div class="table-box table-responsive">
                    <table class="table text-nowrap align-middle mb-0">
                        <thead><tr><th>Nom</th><th>Urgence (pas de préavis)</th><th>Demandes liées</th><th>Statut</th><th></th></tr></thead>
                        <tbody>
                            @forelse ($typesAbsence as $type)
                                <tr>
                                    <td class="fw-medium">{{ $type->nom }}</td>
                                    <td>
                                        @if ($type->est_urgence)
                                            <span class="badge bg-danger-subtle text-danger">Oui</span>
                                        @else
                                            <span class="badge bg-secondary-subtle text-secondary">Non</span>
                                        @endif
                                    </td>
                                    <td>{{ $type->absences_count }}</td>
                                    <td>
                                        @if ($type->actif)
                                            <span class="badge bg-success-subtle text-success">Actif</span>
                                        @else
                                            <span class="badge bg-secondary-subtle text-secondary">Inactif</span>
                                        @endif
                                    </td>
                                    <td>
                                        <button type="button" class="btn btn-light-primary icon-btn-sm" data-bs-toggle="modal" data-bs-target="#modifierTypeAbsenceModal{{ $type->id }}"><i class="bi bi-pencil-square"></i></button>
                                    </td>
                                </tr>
                            @empty
                                <tr><td colspan="5" class="text-center text-muted py-5">Aucun type d'absence enregistré.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

    </div>

    <div class="modal fade" id="creerTypeAbsenceModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <form action="{{ route('administration.types-absence.store') }}" method="POST">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title">Nouveau type d'absence</h5>
                        <button type="button" class="btn-close icon-btn-sm" data-bs-dismiss="modal" aria-label="Close"><i class="ri-close-large-line fw-semibold"></i></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">Nom<span class="text-danger ms-1">*</span></label>
                            <input type="text" class="form-control" name="nom" placeholder="Ex : Congé sans solde" required>
                        </div>
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" name="est_urgence" value="1" id="urgenceCreer">
                            <label class="form-check-label" for="urgenceCreer">Marquer comme urgence (dispense du préavis de 3 jours)</label>
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

    @foreach ($typesAbsence as $type)
        <div class="modal fade" id="modifierTypeAbsenceModal{{ $type->id }}" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered" role="document">
                <div class="modal-content">
                    <form action="{{ route('administration.types-absence.update', $type) }}" method="POST">
                        @csrf
                        @method('PUT')
                        <div class="modal-header">
                            <h5 class="modal-title">Modifier {{ $type->nom }}</h5>
                            <button type="button" class="btn-close icon-btn-sm" data-bs-dismiss="modal" aria-label="Close"><i class="ri-close-large-line fw-semibold"></i></button>
                        </div>
                        <div class="modal-body">
                            <div class="mb-3">
                                <label class="form-label">Nom<span class="text-danger ms-1">*</span></label>
                                <input type="text" class="form-control" name="nom" value="{{ $type->nom }}" required>
                            </div>
                            <div class="form-check form-switch mb-2">
                                <input class="form-check-input" type="checkbox" name="est_urgence" value="1" id="urgenceModifier{{ $type->id }}" @checked($type->est_urgence)>
                                <label class="form-check-label" for="urgenceModifier{{ $type->id }}">Marquer comme urgence (dispense du préavis de 3 jours)</label>
                            </div>
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" name="actif" value="1" id="actifModifier{{ $type->id }}" @checked($type->actif)>
                                <label class="form-check-label" for="actifModifier{{ $type->id }}">Actif (proposé lors d'une nouvelle demande)</label>
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
    @endforeach

    </main>
@endsection

@section('js')
    <script type="module" src="{{ asset('assets/js/app.js') }}"></script>
@endsection
