@extends('partials.layouts.master-finance')

@section('title', 'Nouvelle dépense | Finance')
@section('title-sub', 'Finance')
@section('pagetitle', 'Nouvelle dépense')

@section('css')
    <link rel="stylesheet" href="{{ asset('assets/libs/choices.js/public/assets/styles/choices.min.css') }}">
@endsection

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

        <form action="{{ route('finance.depenses.store') }}" method="POST" id="formDepense">
            @csrf

            <div class="card">
                <div class="card-header"><h6 class="card-title mb-0 fw-semibold">Bien &amp; location concernés</h6></div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">Bien<span class="text-danger ms-1">*</span></label>
                            <select class="form-select" name="bien_id" id="selectBienDepense" required>
                                <option value="">Sélectionner un bien...</option>
                                @foreach ($biens as $bien)
                                    <option value="{{ $bien->id }}">{{ $bien->titre }} — {{ $bien->bailleur->nom_complet ?? '' }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Location / contrat concerné (facultatif)</label>
                            <select class="form-select" name="contrat_location_id" id="selectContratDepense">
                                <option value="">— Aucun (dépense hors location) —</option>
                            </select>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card">
                <div class="card-header"><h6 class="card-title mb-0 fw-semibold">Détails de la dépense</h6></div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">Catégorie<span class="text-danger ms-1">*</span></label>
                            <select class="form-select" name="categorie_depense_id" required>
                                <option value="">Sélectionner...</option>
                                @foreach ($categories as $categorie)
                                    <option value="{{ $categorie->id }}">{{ $categorie->nom }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Fournisseur</label>
                            <input type="text" class="form-control" name="fournisseur" placeholder="Ex: Plombier X">
                        </div>
                        <div class="col-12">
                            <label class="form-label">Description<span class="text-danger ms-1">*</span></label>
                            <textarea class="form-control" name="description" rows="2" placeholder="Ex: Réparation fuite évier" required></textarea>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Montant estimé (FCFA)<span class="text-danger ms-1">*</span></label>
                            <input type="number" step="0.01" min="0" class="form-control" name="montant_estime" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Qui supporte cette dépense ?<span class="text-danger ms-1">*</span></label>
                            <select class="form-select" name="qui_supporte" required>
                                <option value="bailleur">Bailleur</option>
                                <option value="locataire">Locataire</option>
                                <option value="agence">Agence</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Responsable financier</label>
                            <select class="form-select" name="responsable_financier_id">
                                <option value="">—</option>
                                @foreach ($responsables as $responsable)
                                    <option value="{{ $responsable->id }}">{{ $responsable->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-12">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="urgence" value="1" id="urgenceDepense">
                                <label class="form-check-label" for="urgenceDepense">Dépense urgente (ignore l'étape de validation, passe directement à « Approuvée »)</label>
                            </div>
                        </div>
                        <div class="col-12">
                            <label class="form-label">Commentaire</label>
                            <textarea class="form-control" name="commentaire" rows="2"></textarea>
                        </div>
                    </div>
                </div>
            </div>

            <div class="d-flex justify-content-end gap-2 mb-6">
                <a href="{{ route('finance.depenses.index') }}" class="btn btn-light">Annuler</a>
                <button type="submit" class="btn btn-light-secondary" onclick="document.getElementById('champAction').value='brouillon'">Enregistrer comme brouillon</button>
                <button type="submit" class="btn btn-primary" onclick="document.getElementById('champAction').value='soumettre'">Soumettre pour validation</button>
            </div>
            <input type="hidden" name="action" id="champAction" value="soumettre">
        </form>

    </div>
    </main>
@endsection

@section('js')
    <script src="{{ asset('assets/libs/choices.js/public/assets/scripts/choices.min.js') }}"></script>
    <script type="module" src="{{ asset('assets/js/app.js') }}"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const contratsParBien = {!! json_encode($contratsParBien) !!};

            new Choices(document.getElementById('selectBienDepense'), {
                searchEnabled: true,
                itemSelectText: '',
                placeholderValue: 'Sélectionner un bien...',
                searchPlaceholderValue: 'Rechercher...',
            });

            const selectContrat = document.getElementById('selectContratDepense');

            document.getElementById('selectBienDepense').addEventListener('change', function (e) {
                const contrats = contratsParBien[e.target.value] || [];
                selectContrat.innerHTML = '<option value="">— Aucun (dépense hors location) —</option>';
                contrats.forEach(function (contrat) {
                    const option = document.createElement('option');
                    option.value = contrat.id;
                    option.textContent = contrat.numero + (contrat.locataire ? ' — ' + contrat.locataire : '');
                    selectContrat.appendChild(option);
                });
            });
        });
    </script>
@endsection
