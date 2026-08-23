@extends('partials.layouts.master-locative')

@section('title', 'Nouvelle location | Locative')
@section('title-sub', 'Locations')
@section('pagetitle', 'Nouvelle location')

@section('content')
    <div id="layout-wrapper">

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

        <form action="{{ route('locative.locations.store') }}" method="POST">
            @csrf

            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header"><h6 class="card-title mb-0 fw-semibold">1. Locataire</h6></div>
                        <div class="card-body">
                            <div class="row g-3 align-items-end">
                                <div class="col-md-8">
                                    <label class="form-label">Locataire<span class="text-danger ms-1">*</span></label>
                                    <select class="form-select" name="locataire_id" required>
                                        <option value="">Sélectionner un locataire...</option>
                                        @foreach ($locataires as $locataire)
                                            <option value="{{ $locataire->id }}" @selected(old('locataire_id', session('locataire_cree_id')) == $locataire->id)>
                                                {{ $locataire->nom_complet }} — {{ $locataire->numero }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-4">
                                    <button type="button" class="btn btn-light-primary w-100" data-bs-toggle="modal" data-bs-target="#createLocataireModal">
                                        <i class="bi bi-plus-lg me-1"></i>Nouveau locataire
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="card">
                        <div class="card-header"><h6 class="card-title mb-0 fw-semibold">2. Conditions de location</h6></div>
                        <div class="card-body">
                            <div class="row g-3">
                                <div class="col-md-3">
                                    <label class="form-label">Date de début<span class="text-danger ms-1">*</span></label>
                                    <input type="date" class="form-control" name="date_debut" value="{{ old('date_debut', now()->format('Y-m-d')) }}" required>
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label">Date de fin</label>
                                    <input type="date" class="form-control" name="date_fin" value="{{ old('date_fin') }}">
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label">Jour d'échéance<span class="text-danger ms-1">*</span></label>
                                    <input type="number" min="1" max="28" class="form-control" name="jour_echeance" value="{{ old('jour_echeance', 1) }}" required>
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label">Mode de paiement préféré</label>
                                    <select class="form-select" name="mode_paiement_prefere_id">
                                        <option value="">—</option>
                                        @foreach ($modesPaiement as $mode)
                                            <option value="{{ $mode->id }}" @selected(old('mode_paiement_prefere_id') == $mode->id)>{{ $mode->nom }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="card">
                        <div class="card-header">
                            <h6 class="card-title mb-0 fw-semibold">3. Biens à louer</h6>
                            <p class="text-muted mb-0 fs-12">Cochez un ou plusieurs biens disponibles. Le loyer proposé est pré-rempli depuis la fiche du bien, modifiable si besoin.</p>
                        </div>
                        <div class="card-body p-0">
                            <div class="table-box table-responsive">
                                <table class="table text-nowrap align-middle mb-0">
                                    <thead>
                                        <tr>
                                            <th></th>
                                            <th>Bien</th>
                                            <th>Bailleur</th>
                                            <th>Loyer mensuel</th>
                                            <th>Dépôt de garantie</th>
                                            <th>Charges</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse ($biensDisponibles as $bien)
                                            <tr>
                                                <td>
                                                    <div class="form-check">
                                                        <input class="form-check-input" type="checkbox" name="biens[]" value="{{ $bien->id }}" id="bien{{ $bien->id }}">
                                                    </div>
                                                </td>
                                                <td>
                                                    <label for="bien{{ $bien->id }}" class="mb-0">
                                                        <h6 class="mb-0 fw-medium fs-13">{{ $bien->titre }}</h6>
                                                        <span class="text-muted fs-12">{{ $bien->categorie->nom ?? '' }} — {{ $bien->zone }}</span>
                                                    </label>
                                                </td>
                                                <td>{{ $bien->bailleur->nom_complet }}</td>
                                                <td>
                                                    <input type="number" step="0.01" min="0" class="form-control" style="width: 140px"
                                                        name="conditions[{{ $bien->id }}][loyer_mensuel]" value="{{ $bien->loyer_mensuel }}">
                                                </td>
                                                <td>
                                                    <input type="number" step="0.01" min="0" class="form-control" style="width: 140px"
                                                        name="conditions[{{ $bien->id }}][depot_garantie]" value="0">
                                                </td>
                                                <td>
                                                    <input type="number" step="0.01" min="0" class="form-control" style="width: 120px"
                                                        name="conditions[{{ $bien->id }}][charges]" value="0">
                                                </td>
                                            </tr>
                                        @empty
                                            <tr><td colspan="6" class="text-center text-muted py-5">Aucun bien disponible pour le moment.</td></tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    <div class="d-flex justify-content-end gap-2 mb-6">
                        <a href="{{ route('locative.locations.index') }}" class="btn btn-light">Annuler</a>
                        <button type="submit" class="btn btn-primary">Créer la location</button>
                    </div>
                </div>
            </div>
        </form>

        <div class="modal fade" id="createLocataireModal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
                <div class="modal-content">
                    <form action="{{ route('locative.locataires.store') }}" method="POST">
                        @csrf
                        @include('locative.locataires._formulaire', ['locataire' => null])
                        <div class="modal-footer">
                            <button type="button" class="btn btn-light" data-bs-dismiss="modal">Fermer</button>
                            <button type="submit" class="btn btn-primary">Créer le locataire</button>
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
    @include('locative._form-type-toggle-script')
@endsection
