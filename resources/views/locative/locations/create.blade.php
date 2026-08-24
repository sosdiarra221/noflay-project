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

        <form action="{{ route('locative.locations.store') }}" method="POST" id="formNouvelleLocation">
            @csrf

            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header"><h6 class="card-title mb-0 fw-semibold">1. Type de location</h6></div>
                        <div class="card-body">
                            <p class="text-muted mb-3 fs-12">Détermine le modèle de contrat généré automatiquement (module Gestion Document).</p>
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <div class="form-check border rounded-3 p-3 h-100">
                                        <input class="form-check-input" type="radio" name="type_location" id="typeHabitation" value="habitation" @checked(old('type_location', 'habitation') === 'habitation')>
                                        <label class="form-check-label w-100" for="typeHabitation">
                                            <span class="d-block fw-semibold"><i class="bi bi-house-door me-1"></i>Usage habitation</span>
                                            <span class="text-muted fs-12">Location d'un logement (studio, appartement, villa...)</span>
                                        </label>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-check border rounded-3 p-3 h-100">
                                        <input class="form-check-input" type="radio" name="type_location" id="typeCommercial" value="commercial" @checked(old('type_location') === 'commercial')>
                                        <label class="form-check-label w-100" for="typeCommercial">
                                            <span class="d-block fw-semibold"><i class="bi bi-shop me-1"></i>Usage commercial</span>
                                            <span class="text-muted fs-12">Local commercial, bureau, boutique, entrepôt...</span>
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="card">
                        <div class="card-header"><h6 class="card-title mb-0 fw-semibold">2. Locataire</h6></div>
                        <div class="card-body">
                            <div class="row g-3 align-items-end">
                                <div class="col-md-8">
                                    <label class="form-label">Locataire<span class="text-danger ms-1">*</span></label>
                                    <select class="form-select" name="locataire_id" id="selectLocataire" required>
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
                        <div class="card-header"><h6 class="card-title mb-0 fw-semibold">3. Conditions de location</h6></div>
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
                            <h6 class="card-title mb-0 fw-semibold">4. Biens à louer</h6>
                            <p class="text-muted mb-0 fs-12">Cochez un ou plusieurs biens disponibles. Le loyer proposé est pré-rempli depuis la fiche du bien, modifiable si besoin. Configurez la caution via le bouton dédié.</p>
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
                                            <th>Taxes</th>
                                            <th>Caution / frais d'agence</th>
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
                                                    <div class="form-check">
                                                        <input class="form-check-input" type="checkbox" name="conditions[{{ $bien->id }}][appliquer_tva]" value="1" id="tva{{ $bien->id }}">
                                                        <label class="form-check-label fs-12" for="tva{{ $bien->id }}">TVA</label>
                                                    </div>
                                                    <div class="form-check">
                                                        <input class="form-check-input" type="checkbox" name="conditions[{{ $bien->id }}][appliquer_tom]" value="1" id="tom{{ $bien->id }}">
                                                        <label class="form-check-label fs-12" for="tom{{ $bien->id }}">TOM</label>
                                                    </div>
                                                </td>
                                                <td>
                                                    <input type="hidden" class="champ-depot-garantie" name="conditions[{{ $bien->id }}][depot_garantie]" value="0">
                                                    <input type="hidden" class="champ-depot-part-bailleur" name="conditions[{{ $bien->id }}][depot_garantie_part_bailleur]" value="0">
                                                    <input type="hidden" class="champ-depot-part-agence" name="conditions[{{ $bien->id }}][depot_garantie_part_agence]" value="0">
                                                    <button type="button" class="btn btn-sm btn-light-primary btn-configurer-caution" data-bs-toggle="modal" data-bs-target="#cautionModal{{ $bien->id }}">
                                                        <i class="bi bi-shield-lock me-1"></i>Configurer
                                                    </button>
                                                    <div class="fs-11 text-muted mt-1 resume-caution">Non configurée</div>
                                                </td>
                                                <td>
                                                    <input type="number" step="0.01" min="0" class="form-control" style="width: 120px"
                                                        name="conditions[{{ $bien->id }}][charges]" value="0">
                                                </td>
                                            </tr>
                                        @empty
                                            <tr><td colspan="7" class="text-center text-muted py-5">Aucun bien disponible pour le moment.</td></tr>
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

        @foreach ($biensDisponibles as $bien)
            <div class="modal fade" id="cautionModal{{ $bien->id }}" tabindex="-1" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title">Caution — {{ $bien->titre }}</h5>
                            <button type="button" class="btn-close icon-btn-sm" data-bs-dismiss="modal" aria-label="Close"><i class="ri-close-large-line fw-semibold"></i></button>
                        </div>
                        <div class="modal-body">
                            <p class="text-muted fs-12">Montant encaissé à la signature, réparti entre la caution/garantie conservée pour le bailleur (restituable au locataire, hors dégâts) et les frais d'agence (revenu immédiat de l'agence, non restituables).</p>
                            <div class="row g-3">
                                <div class="col-12">
                                    <label class="form-label">Montant total encaissé à la signature</label>
                                    <input type="number" step="0.01" min="0" class="form-control champ-caution-total" data-bien="{{ $bien->id }}" value="0">
                                </div>
                                <div class="col-6">
                                    <label class="form-label">Part bailleur (caution/garantie)</label>
                                    <input type="number" step="0.01" min="0" class="form-control champ-caution-bailleur" data-bien="{{ $bien->id }}" value="0">
                                </div>
                                <div class="col-6">
                                    <label class="form-label">Part agence (frais d'agence)</label>
                                    <input type="number" step="0.01" min="0" class="form-control champ-caution-agence" data-bien="{{ $bien->id }}" value="0">
                                </div>
                                <div class="col-12">
                                    <div class="alert alert-light border fs-12 mb-0 champ-caution-alerte" data-bien="{{ $bien->id }}">
                                        La part bailleur et la part agence doivent correspondre au total.
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-light" data-bs-dismiss="modal">Annuler</button>
                            <button type="button" class="btn btn-primary btn-valider-caution" data-bien="{{ $bien->id }}" data-bs-dismiss="modal">Valider</button>
                        </div>
                    </div>
                </div>
            </div>
        @endforeach

    </div>
    </main>
@endsection

@section('js')
    <link rel="stylesheet" href="{{ asset('assets/libs/choices.js/public/assets/styles/choices.min.css') }}">
    <script src="{{ asset('assets/libs/choices.js/public/assets/scripts/choices.min.js') }}"></script>
    <script type="module" src="{{ asset('assets/js/app.js') }}"></script>
    @include('locative._form-type-toggle-script')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const selectLocataire = document.getElementById('selectLocataire');
            if (selectLocataire) {
                new Choices(selectLocataire, {
                    searchEnabled: true,
                    itemSelectText: '',
                    placeholderValue: 'Sélectionner un locataire...',
                    searchPlaceholderValue: 'Rechercher un locataire...',
                });
            }

            function formaterFcfa(valeur) {
                return new Intl.NumberFormat('fr-FR').format(Math.round(valeur || 0)) + ' FCFA';
            }

            document.querySelectorAll('.btn-valider-caution').forEach(function (bouton) {
                bouton.addEventListener('click', function () {
                    const bienId = bouton.dataset.bien;
                    const total = parseFloat(document.querySelector(`.champ-caution-total[data-bien="${bienId}"]`).value) || 0;
                    const bailleur = parseFloat(document.querySelector(`.champ-caution-bailleur[data-bien="${bienId}"]`).value) || 0;
                    const agence = parseFloat(document.querySelector(`.champ-caution-agence[data-bien="${bienId}"]`).value) || 0;

                    const ligne = document.getElementById('bien' + bienId).closest('tr');
                    ligne.querySelector('.champ-depot-garantie').value = total;
                    ligne.querySelector('.champ-depot-part-bailleur').value = bailleur;
                    ligne.querySelector('.champ-depot-part-agence').value = agence;

                    const resume = ligne.querySelector('.resume-caution');
                    if (total > 0) {
                        resume.innerHTML = formaterFcfa(total) + '<br>Bailleur : ' + formaterFcfa(bailleur) + ' — Agence : ' + formaterFcfa(agence);
                        if (Math.round(bailleur + agence) !== Math.round(total)) {
                            resume.classList.add('text-danger');
                        } else {
                            resume.classList.remove('text-danger');
                        }
                    } else {
                        resume.textContent = 'Non configurée';
                        resume.classList.remove('text-danger');
                    }
                });
            });

            document.querySelectorAll('.champ-caution-total').forEach(function (champTotal) {
                champTotal.addEventListener('input', function () {
                    const bienId = champTotal.dataset.bien;
                    const champBailleur = document.querySelector(`.champ-caution-bailleur[data-bien="${bienId}"]`);
                    if (champBailleur && ! champBailleur.dataset.touche) {
                        champBailleur.value = champTotal.value;
                        document.querySelector(`.champ-caution-agence[data-bien="${bienId}"]`).value = 0;
                    }
                });
            });

            document.querySelectorAll('.champ-caution-bailleur, .champ-caution-agence').forEach(function (champ) {
                champ.addEventListener('input', function () {
                    champ.dataset.touche = '1';
                });
            });
        });
    </script>
@endsection
