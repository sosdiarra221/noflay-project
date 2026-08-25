@extends('partials.layouts.master-facturation')

@section('title', 'Nouveau devis | Facturation')
@section('title-sub', 'Devis')
@section('pagetitle', 'Créer')

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

        <form action="{{ route('facturation.devis.store') }}" method="POST" id="formCreerDevis">
            @csrf
            <input type="hidden" name="mode_client" id="champModeClient">
            <input type="hidden" name="client_id" id="champClientId">
            <input type="hidden" name="prospect_id" id="champProspectId">
            <input type="hidden" name="nouveau_client_nom" id="champNouveauNomCache">
            <input type="hidden" name="nouveau_client_telephone" id="champNouveauTelCache">
            <input type="hidden" name="nouveau_client_email" id="champNouveauEmailCache">

            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h6 class="card-title mb-0 fw-semibold"><i class="bi bi-file-earmark-plus me-2"></i>Nouveau devis</h6>
                    <a href="{{ route('facturation.devis.index') }}" class="btn btn-light-light text-body icon-btn" title="Retour"><i class="bi bi-arrow-left"></i></a>
                </div>
                <div class="card-body">
                    <div class="row g-4 border-bottom border-dashed pb-4 mb-4">
                        <div class="col-md-6 col-xl-4">
                            <label class="form-label">Client / Prospect<span class="text-danger ms-1">*</span></label>
                            <div class="border rounded p-3" id="carteClientChoisi">
                                <p class="text-muted mb-2 fs-13" id="texteAucunClient">Aucun client sélectionné.</p>
                                <div id="resumeClientChoisi" class="d-none">
                                    <h6 class="mb-1" id="resumeClientNom"></h6>
                                    <p class="text-muted fs-12 mb-0" id="resumeClientContact"></p>
                                    <span class="badge bg-info-subtle text-info mt-1 d-none" id="badgeClientProspect">Depuis un prospect</span>
                                    <span class="badge bg-success-subtle text-success mt-1 d-none" id="badgeClientNouveau">Nouveau client</span>
                                </div>
                                <button type="button" class="btn btn-light-primary btn-sm mt-2" data-bs-toggle="modal" data-bs-target="#choixClientModal">
                                    <i class="bi bi-person-plus me-1"></i><span id="libelleBoutonChoix">Choisir un client / prospect</span>
                                </button>
                            </div>
                        </div>
                        <div class="col-md-6 col-xl-3">
                            <div class="row g-3">
                                <div class="col-12">
                                    <label class="form-label">Date<span class="text-danger ms-1">*</span></label>
                                    <input type="date" class="form-control" name="date_devis" value="{{ now()->format('Y-m-d') }}" required>
                                </div>
                                <div class="col-12">
                                    <label class="form-label">Statut<span class="text-danger ms-1">*</span></label>
                                    <select class="form-select" name="statut" id="selectStatutDevis" required>
                                        @foreach (\App\Models\Facturation\Devis::STATUTS as $valeur => $libelle)
                                            <option value="{{ $valeur }}" @selected($valeur === 'nouveau')>{{ $libelle }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6 col-xl-3">
                            <label class="form-label d-block">TVA</label>
                            <div class="form-check form-switch mb-0">
                                <input class="form-check-input" type="checkbox" name="appliquer_tva" value="1" id="champAppliquerTva">
                                <label class="form-check-label" for="champAppliquerTva">Activer la TVA</label>
                            </div>
                            <input type="number" step="0.01" min="0" max="100" class="form-control mt-2 d-none" id="champTauxTva" name="taux_tva" value="{{ (float) ($reglage->taux_tva_defaut ?? 18) }}" placeholder="Taux TVA (%)">
                        </div>
                    </div>

                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h6 class="mb-0">Lignes du devis</h6>
                        <button type="button" class="btn btn-light-primary btn-sm" id="btnAjouterLigne">
                            <i class="bi bi-plus-lg me-1"></i>Ajouter une ligne
                        </button>
                    </div>
                    <div class="table-box table-responsive mb-3">
                        <table class="table text-nowrap align-middle mb-0" id="tableLignesDevis">
                            <thead>
                                <tr>
                                    <th style="min-width: 220px;">Désignation</th>
                                    <th style="width: 110px;">Quantité</th>
                                    <th style="width: 150px;">Prix unitaire</th>
                                    <th style="width: 150px;">Total</th>
                                    <th style="width: 40px;"></th>
                                </tr>
                            </thead>
                            <tbody id="corpsLignesDevis"></tbody>
                        </table>
                    </div>

                    <div class="d-flex justify-content-end">
                        <div style="min-width: 300px;">
                            <div class="d-flex justify-content-between py-1">
                                <span class="text-muted">Sous-total HT</span>
                                <span class="fw-medium" id="affSousTotal">0 FCFA</span>
                            </div>
                            <div class="d-flex justify-content-between py-1" id="ligneAffTva">
                                <span class="text-muted">TVA</span>
                                <span class="fw-medium" id="affTva">0 FCFA</span>
                            </div>
                            <div class="d-flex justify-content-between py-2 border-top mt-1">
                                <span class="fw-semibold">Total TTC</span>
                                <span class="fw-semibold fs-15" id="affTotalTtc">0 FCFA</span>
                            </div>
                        </div>
                    </div>

                    <div class="mt-3">
                        <label class="form-label">Notes</label>
                        <textarea class="form-control" name="notes" rows="2"></textarea>
                    </div>
                </div>
            </div>

            <div class="mb-8 d-flex justify-content-end gap-3">
                <a href="{{ route('facturation.devis.index') }}" class="btn btn-light-primary">Annuler</a>
                <button type="submit" class="btn btn-primary"><i class="bi bi-check-circle me-1"></i>Créer le devis</button>
            </div>
        </form>

    </div>

    {{-- Choix du client / prospect --}}
    <div class="modal fade" id="choixClientModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Client / Prospect du devis</h5>
                    <button type="button" class="btn-close icon-btn-sm" data-bs-dismiss="modal" aria-label="Close"><i class="ri-close-large-line fw-semibold"></i></button>
                </div>
                <div class="modal-body">
                    <div class="btn-group mb-4" role="group">
                        <input type="radio" class="btn-check" name="mode_choix" id="choixModeExistant" checked>
                        <label class="btn btn-outline-primary btn-sm" for="choixModeExistant">Client existant</label>
                        <input type="radio" class="btn-check" name="mode_choix" id="choixModeProspect">
                        <label class="btn btn-outline-primary btn-sm" for="choixModeProspect">Prospect</label>
                        <input type="radio" class="btn-check" name="mode_choix" id="choixModeNouveau">
                        <label class="btn btn-outline-primary btn-sm" for="choixModeNouveau">Nouveau client</label>
                    </div>

                    <div id="blocChoixExistant">
                        <label class="form-label">Sélectionner un client</label>
                        <select class="form-select" id="selectChoixClient">
                            <option value="">Rechercher un client...</option>
                            @foreach ($clients as $client)
                                <option value="{{ $client->id }}" data-nom="{{ $client->nom_complet }}" data-contact="{{ $client->telephone ?: $client->email }}">
                                    {{ $client->nom_complet }} @if($client->telephone) — {{ $client->telephone }} @endif
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div id="blocChoixProspect" class="d-none">
                        <label class="form-label">Sélectionner un prospect</label>
                        <select class="form-select" id="selectChoixProspect">
                            <option value="">Rechercher un prospect...</option>
                            @foreach ($prospects as $prospect)
                                <option value="{{ $prospect->id }}" data-nom="{{ $prospect->nom_complet }}" data-contact="{{ $prospect->telephone ?: $prospect->email }}">
                                    {{ $prospect->nom_complet }} @if($prospect->telephone) — {{ $prospect->telephone }} @endif
                                </option>
                            @endforeach
                        </select>
                        <p class="text-muted fs-12 mt-2 mb-0"><i class="bi bi-info-circle me-1"></i>Une fiche client sera automatiquement liée à ce prospect pour la facturation.</p>
                    </div>

                    <div id="blocChoixNouveau" class="d-none">
                        <div class="row g-3">
                            <div class="col-md-4">
                                <label class="form-label">Nom complet<span class="text-danger ms-1">*</span></label>
                                <input type="text" class="form-control" id="champChoixNouveauNom">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Téléphone</label>
                                <input type="text" class="form-control" id="champChoixNouveauTel">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Email</label>
                                <input type="email" class="form-control" id="champChoixNouveauEmail">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Annuler</button>
                    <button type="button" class="btn btn-primary" id="btnValiderChoixClient">Valider la sélection</button>
                </div>
            </div>
        </div>
    </div>

    </main>
@endsection

@section('js')
    <script src="{{ asset('assets/libs/choices.js/public/assets/scripts/choices.min.js') }}"></script>
    <script type="module" src="{{ asset('assets/js/app.js') }}"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            new Choices(document.getElementById('selectStatutDevis'), { searchEnabled: false, itemSelectText: '' });
            const choicesClient = new Choices(document.getElementById('selectChoixClient'), {
                searchEnabled: true, itemSelectText: '', placeholderValue: 'Rechercher un client...', searchPlaceholderValue: 'Rechercher...',
            });
            const choicesProspect = new Choices(document.getElementById('selectChoixProspect'), {
                searchEnabled: true, itemSelectText: '', placeholderValue: 'Rechercher un prospect...', searchPlaceholderValue: 'Rechercher...',
            });

            // Bascule entre les 3 modes de sélection dans la modale
            const modeExistant = document.getElementById('choixModeExistant');
            const modeProspect = document.getElementById('choixModeProspect');
            const modeNouveau = document.getElementById('choixModeNouveau');
            const blocExistant = document.getElementById('blocChoixExistant');
            const blocProspect = document.getElementById('blocChoixProspect');
            const blocNouveau = document.getElementById('blocChoixNouveau');

            function synchroniserModeChoix() {
                blocExistant.classList.toggle('d-none', ! modeExistant.checked);
                blocProspect.classList.toggle('d-none', ! modeProspect.checked);
                blocNouveau.classList.toggle('d-none', ! modeNouveau.checked);
            }
            [modeExistant, modeProspect, modeNouveau].forEach(r => r.addEventListener('change', synchroniserModeChoix));
            synchroniserModeChoix();

            // Valide la sélection courante de la modale : remplit les champs cachés du formulaire
            // principal et affiche un résumé, sans jamais soumettre la modale elle-même.
            document.getElementById('btnValiderChoixClient').addEventListener('click', function () {
                const texteAucun = document.getElementById('texteAucunClient');
                const resume = document.getElementById('resumeClientChoisi');
                const resumeNom = document.getElementById('resumeClientNom');
                const resumeContact = document.getElementById('resumeClientContact');
                const badgeProspect = document.getElementById('badgeClientProspect');
                const badgeNouveau = document.getElementById('badgeClientNouveau');
                const libelleBouton = document.getElementById('libelleBoutonChoix');

                document.getElementById('champClientId').value = '';
                document.getElementById('champProspectId').value = '';
                document.getElementById('champNouveauNomCache').value = '';
                document.getElementById('champNouveauTelCache').value = '';
                document.getElementById('champNouveauEmailCache').value = '';
                badgeProspect.classList.add('d-none');
                badgeNouveau.classList.add('d-none');

                let nom = '', contact = '';

                if (modeExistant.checked) {
                    const option = choicesClient.passedElement.element.selectedOptions[0];
                    if (! option || ! option.value) { alert('Sélectionnez un client.'); return; }
                    document.getElementById('champModeClient').value = 'existant';
                    document.getElementById('champClientId').value = option.value;
                    nom = option.dataset.nom;
                    contact = option.dataset.contact || '—';
                } else if (modeProspect.checked) {
                    const option = choicesProspect.passedElement.element.selectedOptions[0];
                    if (! option || ! option.value) { alert('Sélectionnez un prospect.'); return; }
                    document.getElementById('champModeClient').value = 'prospect';
                    document.getElementById('champProspectId').value = option.value;
                    nom = option.dataset.nom;
                    contact = option.dataset.contact || '—';
                    badgeProspect.classList.remove('d-none');
                } else {
                    const nomSaisi = document.getElementById('champChoixNouveauNom').value.trim();
                    if (! nomSaisi) { alert('Renseignez le nom du nouveau client.'); return; }
                    document.getElementById('champModeClient').value = 'nouveau';
                    document.getElementById('champNouveauNomCache').value = nomSaisi;
                    document.getElementById('champNouveauTelCache').value = document.getElementById('champChoixNouveauTel').value;
                    document.getElementById('champNouveauEmailCache').value = document.getElementById('champChoixNouveauEmail').value;
                    nom = nomSaisi;
                    contact = document.getElementById('champChoixNouveauTel').value || document.getElementById('champChoixNouveauEmail').value || '—';
                    badgeNouveau.classList.remove('d-none');
                }

                resumeNom.textContent = nom;
                resumeContact.textContent = contact;
                texteAucun.classList.add('d-none');
                resume.classList.remove('d-none');
                libelleBouton.textContent = 'Modifier le client / prospect';

                bootstrap.Modal.getInstance(document.getElementById('choixClientModal')).hide();
            });

            // Lignes dynamiques
            const corps = document.getElementById('corpsLignesDevis');
            const btnAjouter = document.getElementById('btnAjouterLigne');
            let compteurLigne = 0;

            const champTva = document.getElementById('champAppliquerTva');
            const champTauxTva = document.getElementById('champTauxTva');
            const ligneAffTva = document.getElementById('ligneAffTva');
            function synchroniserTva() {
                champTauxTva.classList.toggle('d-none', ! champTva.checked);
                ligneAffTva.classList.toggle('d-none', ! champTva.checked);
                recalculerTotaux();
            }
            champTva.addEventListener('change', synchroniserTva);
            champTauxTva.addEventListener('input', recalculerTotaux);

            function formaterFcfa(valeur) {
                return new Intl.NumberFormat('fr-FR').format(Math.round(valeur)) + ' FCFA';
            }

            function recalculerTotaux() {
                let sousTotal = 0;
                corps.querySelectorAll('tr').forEach(function (ligne) {
                    const quantite = parseFloat(ligne.querySelector('.champ-quantite').value) || 0;
                    const prixUnitaire = parseFloat(ligne.querySelector('.champ-prix').value) || 0;
                    const total = quantite * prixUnitaire;
                    ligne.querySelector('.affichage-total-ligne').textContent = formaterFcfa(total);
                    sousTotal += total;
                });

                const tauxTva = champTva.checked ? (parseFloat(champTauxTva.value) || 0) : 0;
                const montantTva = champTva.checked ? sousTotal * tauxTva / 100 : 0;
                const totalTtc = sousTotal + montantTva;

                document.getElementById('affSousTotal').textContent = formaterFcfa(sousTotal);
                document.getElementById('affTva').textContent = formaterFcfa(montantTva);
                document.getElementById('affTotalTtc').textContent = formaterFcfa(totalTtc);
            }

            function ajouterLigne() {
                const index = compteurLigne++;
                const ligne = document.createElement('tr');
                ligne.innerHTML = `
                    <td><input type="text" class="form-control" name="lignes[${index}][designation]" required></td>
                    <td><input type="number" step="0.01" min="0.01" class="form-control champ-quantite" name="lignes[${index}][quantite]" value="1" required></td>
                    <td><input type="number" step="0.01" min="0" class="form-control champ-prix" name="lignes[${index}][prix_unitaire]" value="0" required></td>
                    <td class="fw-medium affichage-total-ligne">0 FCFA</td>
                    <td><button type="button" class="btn btn-light-danger icon-btn-sm btn-supprimer-ligne"><i class="ri-delete-bin-line"></i></button></td>
                `;
                corps.appendChild(ligne);

                ligne.querySelector('.champ-quantite').addEventListener('input', recalculerTotaux);
                ligne.querySelector('.champ-prix').addEventListener('input', recalculerTotaux);
                ligne.querySelector('.btn-supprimer-ligne').addEventListener('click', function () {
                    ligne.remove();
                    recalculerTotaux();
                });

                recalculerTotaux();
            }

            btnAjouter.addEventListener('click', ajouterLigne);
            synchroniserTva();
            ajouterLigne();

            document.getElementById('formCreerDevis').addEventListener('submit', function (e) {
                if (! document.getElementById('champModeClient').value) {
                    e.preventDefault();
                    alert('Choisissez un client ou un prospect avant de créer le devis.');
                    return;
                }
                if (corps.children.length === 0) {
                    e.preventDefault();
                    alert('Ajoutez au moins une ligne au devis.');
                }
            });
        });
    </script>
@endsection
