@extends('partials.layouts.master-locative')

@section('title', 'Nouvelle gérance | Locative')
@section('title-sub', 'Gérances')
@section('pagetitle', 'Nouvelle gérance')

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

        <div class="row">
            <div class="col-lg-8 offset-lg-2">
                <div class="card">
                    <div class="card-header">
                        <h6 class="card-title mb-0 fw-semibold">Nouveau contrat de gérance</h6>
                    </div>
                    <div class="card-body p-6">
                        <form action="{{ route('locative.gerances.store') }}" method="POST">
                            @csrf
                            <div class="row g-4">
                                <div class="col-12">
                                    <label class="form-label">Bailleur<span class="text-danger ms-1">*</span></label>
                                    <div class="input-group">
                                        <select class="form-select" name="bailleur_id" required>
                                            <option value="">Sélectionner un bailleur...</option>
                                            @foreach ($bailleurs as $bailleur)
                                                <option value="{{ $bailleur->id }}" @selected(old('bailleur_id', session('bailleur_cree_id')) == $bailleur->id)>
                                                    {{ $bailleur->nom_complet }} — {{ $bailleur->numero }}
                                                </option>
                                            @endforeach
                                        </select>
                                        <button type="button" class="btn btn-light-primary" data-bs-toggle="modal" data-bs-target="#createBailleurModal">
                                            <i class="bi bi-plus-lg"></i>
                                        </button>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">Date de début<span class="text-danger ms-1">*</span></label>
                                    <input type="date" class="form-control" name="date_debut" value="{{ old('date_debut', now()->format('Y-m-d')) }}" required>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">Date de fin</label>
                                    <input type="date" class="form-control" name="date_fin" value="{{ old('date_fin') }}">
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">Type de gérance<span class="text-danger ms-1">*</span></label>
                                    <select class="form-select" name="type_gerance" required>
                                        <option value="gestion_locative">Gestion locative</option>
                                        <option value="gestion_vente">Gestion vente</option>
                                        <option value="gestion_locative_vente">Gestion locative + vente</option>
                                    </select>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">Mode de frais de gestion<span class="text-danger ms-1">*</span></label>
                                    <select class="form-select" name="frais_gestion_mode" required>
                                        <option value="pourcentage">Pourcentage</option>
                                        <option value="montant_fixe">Montant fixe</option>
                                    </select>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">Valeur des frais<span class="text-danger ms-1">*</span></label>
                                    <input type="number" step="0.01" min="0" class="form-control" name="frais_gestion_valeur" value="{{ old('frais_gestion_valeur', 10) }}" required>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">Statut</label>
                                    <select class="form-select" name="statut">
                                        <option value="brouillon">Brouillon</option>
                                        <option value="en_attente_signature">En attente de signature</option>
                                        <option value="actif" selected>Actif</option>
                                    </select>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">TVA à la charge de</label>
                                    <select class="form-select" name="tva_charge">
                                        <option value="bailleur">Bailleur</option>
                                        <option value="agence">Agence</option>
                                    </select>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">Taxe à la charge de</label>
                                    <select class="form-select" name="taxe_charge">
                                        <option value="bailleur">Bailleur</option>
                                        <option value="agence">Agence</option>
                                    </select>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">TOM à la charge de</label>
                                    <select class="form-select" name="tom_charge">
                                        <option value="bailleur">Bailleur</option>
                                        <option value="agence">Agence</option>
                                    </select>
                                </div>
                                <div class="col-12">
                                    <label class="form-label">Notes</label>
                                    <textarea class="form-control" name="notes" rows="2">{{ old('notes') }}</textarea>
                                </div>
                                <div class="col-12 d-flex justify-content-end gap-2">
                                    <a href="{{ route('locative.gerances.index') }}" class="btn btn-light">Annuler</a>
                                    <button type="submit" class="btn btn-primary">Créer la gérance</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <!-- Create Bailleur Modal -->
        <div class="modal fade" id="createBailleurModal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
                <div class="modal-content">
                    <form action="{{ route('locative.bailleurs.store') }}" method="POST">
                        @csrf
                        @include('locative.bailleurs._formulaire', ['bailleur' => null])
                        <div class="modal-footer">
                            <button type="button" class="btn btn-light" data-bs-dismiss="modal">Fermer</button>
                            <button type="submit" class="btn btn-primary">Créer le bailleur</button>
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
