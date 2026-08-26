@extends('partials.layouts.master-administration')

@section('title', 'Jours fériés | Administration')
@section('title-sub', 'Administration')
@section('pagetitle', 'Jours fériés')

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
                    <h6 class="mb-0">Jours fériés <span class="badge bg-secondary-subtle text-secondary ms-1">{{ $joursFeries->count() }}</span></h6>
                    <p class="text-muted fs-12 mb-0">Utilisés par le module RH pour calculer le nombre de jours des demandes de congé/absence.</p>
                </div>
                <button type="button" class="btn btn-primary text-nowrap" data-bs-toggle="modal" data-bs-target="#creerJourFerieModal">
                    <i class="bi bi-plus-lg me-1"></i>Nouveau jour férié
                </button>
            </div>
            <div class="card-body p-0">
                <div class="table-box table-responsive">
                    <table class="table text-nowrap align-middle mb-0">
                        <thead><tr><th>Nom</th><th>Date</th><th>Récurrent chaque année</th><th></th></tr></thead>
                        <tbody>
                            @forelse ($joursFeries as $jourFerie)
                                <tr>
                                    <td class="fw-medium">{{ $jourFerie->nom }}</td>
                                    <td>{{ $jourFerie->date->format('d/m/Y') }}</td>
                                    <td>
                                        @if ($jourFerie->recurrent_annuel)
                                            <span class="badge bg-success-subtle text-success">Oui</span>
                                        @else
                                            <span class="badge bg-secondary-subtle text-secondary">Non</span>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="hstack gap-2">
                                            <button type="button" class="btn btn-light-primary icon-btn-sm" data-bs-toggle="modal" data-bs-target="#modifierJourFerieModal{{ $jourFerie->id }}"><i class="bi bi-pencil-square"></i></button>
                                            <form action="{{ route('administration.jours-feries.destroy', $jourFerie) }}" method="POST" onsubmit="return confirm('Supprimer ce jour férié ?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-light-danger icon-btn-sm"><i class="ri-delete-bin-line"></i></button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr><td colspan="4" class="text-center text-muted py-5">Aucun jour férié enregistré.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

    </div>

    <div class="modal fade" id="creerJourFerieModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <form action="{{ route('administration.jours-feries.store') }}" method="POST">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title">Nouveau jour férié</h5>
                        <button type="button" class="btn-close icon-btn-sm" data-bs-dismiss="modal" aria-label="Close"><i class="ri-close-large-line fw-semibold"></i></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">Nom<span class="text-danger ms-1">*</span></label>
                            <input type="text" class="form-control" name="nom" placeholder="Ex : Fête de l'Indépendance" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Date<span class="text-danger ms-1">*</span></label>
                            <input type="date" class="form-control" name="date" required>
                        </div>
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" name="recurrent_annuel" value="1" id="recurrentCreer" checked>
                            <label class="form-check-label" for="recurrentCreer">Récurrent chaque année (même jour/mois)</label>
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

    @foreach ($joursFeries as $jourFerie)
        <div class="modal fade" id="modifierJourFerieModal{{ $jourFerie->id }}" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered" role="document">
                <div class="modal-content">
                    <form action="{{ route('administration.jours-feries.update', $jourFerie) }}" method="POST">
                        @csrf
                        @method('PUT')
                        <div class="modal-header">
                            <h5 class="modal-title">Modifier {{ $jourFerie->nom }}</h5>
                            <button type="button" class="btn-close icon-btn-sm" data-bs-dismiss="modal" aria-label="Close"><i class="ri-close-large-line fw-semibold"></i></button>
                        </div>
                        <div class="modal-body">
                            <div class="mb-3">
                                <label class="form-label">Nom<span class="text-danger ms-1">*</span></label>
                                <input type="text" class="form-control" name="nom" value="{{ $jourFerie->nom }}" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Date<span class="text-danger ms-1">*</span></label>
                                <input type="date" class="form-control" name="date" value="{{ $jourFerie->date->format('Y-m-d') }}" required>
                            </div>
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" name="recurrent_annuel" value="1" id="recurrentModifier{{ $jourFerie->id }}" @checked($jourFerie->recurrent_annuel)>
                                <label class="form-check-label" for="recurrentModifier{{ $jourFerie->id }}">Récurrent chaque année (même jour/mois)</label>
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
