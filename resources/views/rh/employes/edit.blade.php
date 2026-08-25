@extends('partials.layouts.master-rh')

@section('title', 'Modifier '.$employe->nom_complet.' | RH')
@section('title-sub', 'Employés')
@section('pagetitle', 'Modifier '.$employe->nom_complet)

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

        <form action="{{ route('rh.employes.update', $employe) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')

            <div class="row g-4">
                <div class="col-lg-4">
                    <div class="card">
                        <div class="card-header"><h6 class="card-action-title mb-0">Photo</h6></div>
                        <div class="card-body text-center">
                            <img id="apercuPhoto" src="{{ $employe->photo ? asset('storage/'.$employe->photo) : asset('assets/images/avatar/avatar-10.jpg') }}" class="rounded-circle mb-3" width="120" height="120" style="object-fit: cover;">
                            <input type="file" class="form-control" name="photo" accept="image/*" id="champPhoto">
                        </div>
                    </div>
                    <div class="card">
                        <div class="card-header"><h6 class="card-action-title mb-0">Poste &amp; rattachement</h6></div>
                        <div class="card-body">
                            <div class="mb-3">
                                <div class="d-flex justify-content-between align-items-center mb-1">
                                    <label class="form-label mb-0">Poste<span class="text-danger ms-1">*</span></label>
                                    <a href="{{ route('rh.postes.index') }}" target="_blank" class="fs-11">Gérer les postes</a>
                                </div>
                                <select class="form-select" name="poste_id" id="selectPoste" required>
                                    @foreach ($postes as $poste)
                                        <option value="{{ $poste->id }}" @selected(old('poste_id', $employe->poste_id) == $poste->id)>{{ $poste->nom }}</option>
                                    @endforeach
                                    @if ($employe->poste && ! $postes->contains('id', $employe->poste_id))
                                        <option value="{{ $employe->poste->id }}" selected>{{ $employe->poste->nom }} (inactif)</option>
                                    @endif
                                </select>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Fonction<span class="text-danger ms-1">*</span></label>
                                <select class="form-select" name="categorie_fonction" required>
                                    @foreach (\App\Models\Rh\Employe::CATEGORIES_FONCTION as $valeur => $libelle)
                                        <option value="{{ $valeur }}" @selected(old('categorie_fonction', $employe->categorie_fonction) === $valeur)>{{ $libelle }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Département<span class="text-danger ms-1">*</span></label>
                                <select class="form-select" name="departement_id" required>
                                    @foreach ($departements as $departement)
                                        <option value="{{ $departement->id }}" @selected(old('departement_id', $employe->departement_id) == $departement->id)>{{ $departement->nom }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Site(s)</label>
                                <select class="form-select" name="sites[]" id="selectSites" multiple>
                                    @foreach ($sites as $site)
                                        <option value="{{ $site->id }}" @selected($employe->sites->contains($site->id))>{{ $site->nom }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="mb-0">
                                <label class="form-label">Motif du transfert <span class="text-muted fs-11">(si département/site modifié)</span></label>
                                <input type="text" class="form-control" name="motif_affectation" placeholder="Ex : réaffectation, besoin opérationnel...">
                            </div>
                        </div>
                    </div>
                    <div class="card">
                        <div class="card-header"><h6 class="card-action-title mb-0">Emploi</h6></div>
                        <div class="card-body">
                            <label class="form-label">Date d'embauche<span class="text-danger ms-1">*</span></label>
                            <input type="date" class="form-control" name="date_embauche" value="{{ old('date_embauche', $employe->date_embauche->format('Y-m-d')) }}" required>
                            <p class="text-muted fs-11 mt-2 mb-0">Matricule : <strong>{{ $employe->matricule }}</strong> — Solde de congés : <strong>{{ number_format($employe->solde_conges, 1) }} j</strong></p>
                        </div>
                    </div>
                </div>

                <div class="col-lg-8">
                    <div class="card">
                        <div class="card-header"><h6 class="card-action-title mb-0">État civil</h6></div>
                        <div class="card-body">
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label">Nom<span class="text-danger ms-1">*</span></label>
                                    <input type="text" class="form-control" name="nom" value="{{ old('nom', $employe->nom) }}" required>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Prénom<span class="text-danger ms-1">*</span></label>
                                    <input type="text" class="form-control" name="prenom" value="{{ old('prenom', $employe->prenom) }}" required>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">Sexe</label>
                                    <select class="form-select" name="sexe">
                                        <option value="">—</option>
                                        <option value="homme" @selected(old('sexe', $employe->sexe) === 'homme')>Homme</option>
                                        <option value="femme" @selected(old('sexe', $employe->sexe) === 'femme')>Femme</option>
                                    </select>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">Date de naissance</label>
                                    <input type="date" class="form-control" name="date_naissance" value="{{ old('date_naissance', $employe->date_naissance?->format('Y-m-d')) }}">
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">Lieu de naissance</label>
                                    <input type="text" class="form-control" name="lieu_naissance" value="{{ old('lieu_naissance', $employe->lieu_naissance) }}">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Situation matrimoniale</label>
                                    <select class="form-select" name="situation_matrimoniale">
                                        <option value="">—</option>
                                        @foreach (\App\Models\Rh\Employe::SITUATIONS_MATRIMONIALES as $valeur => $libelle)
                                            <option value="{{ $valeur }}" @selected(old('situation_matrimoniale', $employe->situation_matrimoniale) === $valeur)>{{ $libelle }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label">Pièce d'identité</label>
                                    <input type="text" class="form-control" name="piece_identite_type" value="{{ old('piece_identite_type', $employe->piece_identite_type) }}">
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label">Numéro</label>
                                    <input type="text" class="form-control" name="piece_identite_numero" value="{{ old('piece_identite_numero', $employe->piece_identite_numero) }}">
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="card">
                        <div class="card-header"><h6 class="card-action-title mb-0">Contact</h6></div>
                        <div class="card-body">
                            <div class="row g-3">
                                <div class="col-md-4"><label class="form-label">Téléphone</label><input type="text" class="form-control" name="telephone" value="{{ old('telephone', $employe->telephone) }}"></div>
                                <div class="col-md-4"><label class="form-label">WhatsApp</label><input type="text" class="form-control" name="whatsapp" value="{{ old('whatsapp', $employe->whatsapp) }}"></div>
                                <div class="col-md-4"><label class="form-label">Email</label><input type="email" class="form-control" name="email" value="{{ old('email', $employe->email) }}"></div>
                                <div class="col-12"><label class="form-label">Adresse</label><input type="text" class="form-control" name="adresse" value="{{ old('adresse', $employe->adresse) }}"></div>
                            </div>
                        </div>
                    </div>

                    <div class="card">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h6 class="card-action-title mb-0">Situation familiale — Épouse(s)</h6>
                            <button type="button" class="btn btn-light-primary btn-sm" id="btnAjouterEpouse"><i class="bi bi-plus-lg me-1"></i>Ajouter</button>
                        </div>
                        <div class="card-body">
                            <div class="table-box table-responsive">
                                <table class="table align-middle mb-0">
                                    <thead><tr><th>Nom complet</th><th style="width: 220px;">Téléphone</th><th style="width: 40px;"></th></tr></thead>
                                    <tbody id="corpsEpouses">
                                        @foreach ($employe->epouses as $index => $epouse)
                                            <tr>
                                                <td><input type="text" class="form-control" name="epouses[{{ $index }}][nom_complet]" value="{{ $epouse->nom_complet }}" required></td>
                                                <td><input type="text" class="form-control" name="epouses[{{ $index }}][telephone]" value="{{ $epouse->telephone }}"></td>
                                                <td><button type="button" class="btn btn-light-danger icon-btn-sm btn-supprimer-ligne"><i class="ri-delete-bin-line"></i></button></td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                            <p class="text-muted fs-11 mb-0 {{ $employe->epouses->isNotEmpty() ? 'd-none' : '' }}" id="messageAucuneEpouse">Aucune épouse ajoutée.</p>
                        </div>
                    </div>

                    <div class="card">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h6 class="card-action-title mb-0">Situation familiale — Enfant(s)</h6>
                            <button type="button" class="btn btn-light-primary btn-sm" id="btnAjouterEnfant"><i class="bi bi-plus-lg me-1"></i>Ajouter</button>
                        </div>
                        <div class="card-body">
                            <div class="table-box table-responsive">
                                <table class="table align-middle mb-0">
                                    <thead><tr><th>Nom complet</th><th style="width: 180px;">Date de naissance</th><th style="width: 180px;">Téléphone</th><th style="width: 40px;"></th></tr></thead>
                                    <tbody id="corpsEnfants">
                                        @foreach ($employe->enfants as $index => $enfant)
                                            <tr>
                                                <td><input type="text" class="form-control" name="enfants[{{ $index }}][nom_complet]" value="{{ $enfant->nom_complet }}" required></td>
                                                <td><input type="date" class="form-control" name="enfants[{{ $index }}][date_naissance]" value="{{ $enfant->date_naissance?->format('Y-m-d') }}"></td>
                                                <td><input type="text" class="form-control" name="enfants[{{ $index }}][telephone]" value="{{ $enfant->telephone }}"></td>
                                                <td><button type="button" class="btn btn-light-danger icon-btn-sm btn-supprimer-ligne"><i class="ri-delete-bin-line"></i></button></td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                            <p class="text-muted fs-11 mb-0 {{ $employe->enfants->isNotEmpty() ? 'd-none' : '' }}" id="messageAucunEnfant">Aucun enfant ajouté.</p>
                        </div>
                    </div>

                    <div class="card">
                        <div class="card-header"><h6 class="card-action-title mb-0">Diplômes &amp; langues</h6></div>
                        <div class="card-body">
                            <div class="row g-3">
                                <div class="col-md-6"><label class="form-label">Niveau d'étude</label><input type="text" class="form-control" name="niveau_etude" value="{{ old('niveau_etude', $employe->niveau_etude) }}"></div>
                                <div class="col-md-6"><label class="form-label">Intitulé du diplôme</label><input type="text" class="form-control" name="intitule_diplome" value="{{ old('intitule_diplome', $employe->intitule_diplome) }}"></div>
                                <div class="col-md-6"><label class="form-label">Langues parlées</label><input type="text" class="form-control" name="langues_parlees" value="{{ old('langues_parlees', $employe->langues_parlees) }}"></div>
                                <div class="col-md-6"><label class="form-label">Langues lues/écrites</label><input type="text" class="form-control" name="langues_lues" value="{{ old('langues_lues', $employe->langues_lues) }}"></div>
                            </div>
                        </div>
                    </div>

                    <div class="card">
                        <div class="card-header"><h6 class="card-action-title mb-0">Coordonnées bancaires</h6></div>
                        <div class="card-body">
                            <div class="row g-3">
                                <div class="col-md-6"><label class="form-label">Banque</label><input type="text" class="form-control" name="banque" value="{{ old('banque', $employe->banque) }}"></div>
                                <div class="col-md-6"><label class="form-label">Compte / RIB</label><input type="text" class="form-control" name="compte_bancaire" value="{{ old('compte_bancaire', $employe->compte_bancaire) }}"></div>
                            </div>
                        </div>
                    </div>

                    <div class="card">
                        <div class="card-header"><h6 class="card-action-title mb-0">Personne à contacter en cas d'urgence</h6></div>
                        <div class="card-body">
                            <div class="row g-3">
                                <div class="col-md-4"><label class="form-label">Nom</label><input type="text" class="form-control" name="personne_urgence_nom" value="{{ old('personne_urgence_nom', $employe->personne_urgence_nom) }}"></div>
                                <div class="col-md-4"><label class="form-label">Téléphone</label><input type="text" class="form-control" name="personne_urgence_telephone" value="{{ old('personne_urgence_telephone', $employe->personne_urgence_telephone) }}"></div>
                                <div class="col-md-4"><label class="form-label">Lien de parenté</label><input type="text" class="form-control" name="personne_urgence_lien" value="{{ old('personne_urgence_lien', $employe->personne_urgence_lien) }}"></div>
                            </div>
                        </div>
                    </div>

                    <div class="card">
                        <div class="card-header"><h6 class="card-action-title mb-0">Notes</h6></div>
                        <div class="card-body">
                            <textarea class="form-control" name="notes" rows="3">{{ old('notes', $employe->notes) }}</textarea>
                        </div>
                    </div>

                    <div class="d-flex justify-content-end gap-2 mb-6">
                        <a href="{{ route('rh.employes.show', $employe) }}" class="btn btn-light">Annuler</a>
                        <button type="submit" class="btn btn-primary"><i class="bi bi-check-circle me-1"></i>Enregistrer</button>
                    </div>
                </div>
            </div>
        </form>

    </div>
    </main>
@endsection

@section('js')
    <script src="{{ asset('assets/libs/choices.js/public/assets/scripts/choices.min.js') }}"></script>
    <script type="module" src="{{ asset('assets/js/app.js') }}"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            new Choices(document.getElementById('selectSites'), {
                removeItemButton: true,
                placeholderValue: 'Sélectionner un ou plusieurs sites...',
            });
            new Choices(document.getElementById('selectPoste'), { searchEnabled: true, itemSelectText: '' });

            document.getElementById('champPhoto').addEventListener('change', function (e) {
                const fichier = e.target.files[0];
                if (fichier) {
                    document.getElementById('apercuPhoto').src = URL.createObjectURL(fichier);
                }
            });

            let compteurEpouse = {{ $employe->epouses->count() }};
            let compteurEnfant = {{ $employe->enfants->count() }};

            const corpsEpouses = document.getElementById('corpsEpouses');
            const messageAucuneEpouse = document.getElementById('messageAucuneEpouse');
            const corpsEnfants = document.getElementById('corpsEnfants');
            const messageAucunEnfant = document.getElementById('messageAucunEnfant');

            function brancherSuppression(ligne, callback) {
                ligne.querySelector('.btn-supprimer-ligne').addEventListener('click', function () {
                    ligne.remove();
                    callback();
                });
            }

            document.querySelectorAll('#corpsEpouses .btn-supprimer-ligne').forEach(function (bouton) {
                bouton.addEventListener('click', function () {
                    bouton.closest('tr').remove();
                    messageAucuneEpouse.classList.toggle('d-none', corpsEpouses.children.length > 0);
                });
            });
            document.querySelectorAll('#corpsEnfants .btn-supprimer-ligne').forEach(function (bouton) {
                bouton.addEventListener('click', function () {
                    bouton.closest('tr').remove();
                    messageAucunEnfant.classList.toggle('d-none', corpsEnfants.children.length > 0);
                });
            });

            document.getElementById('btnAjouterEpouse').addEventListener('click', function () {
                const index = compteurEpouse++;
                const ligne = document.createElement('tr');
                ligne.innerHTML = `
                    <td><input type="text" class="form-control" name="epouses[${index}][nom_complet]" required></td>
                    <td><input type="text" class="form-control" name="epouses[${index}][telephone]"></td>
                    <td><button type="button" class="btn btn-light-danger icon-btn-sm btn-supprimer-ligne"><i class="ri-delete-bin-line"></i></button></td>
                `;
                corpsEpouses.appendChild(ligne);
                brancherSuppression(ligne, () => messageAucuneEpouse.classList.toggle('d-none', corpsEpouses.children.length > 0));
                messageAucuneEpouse.classList.add('d-none');
            });

            document.getElementById('btnAjouterEnfant').addEventListener('click', function () {
                const index = compteurEnfant++;
                const ligne = document.createElement('tr');
                ligne.innerHTML = `
                    <td><input type="text" class="form-control" name="enfants[${index}][nom_complet]" required></td>
                    <td><input type="date" class="form-control" name="enfants[${index}][date_naissance]"></td>
                    <td><input type="text" class="form-control" name="enfants[${index}][telephone]"></td>
                    <td><button type="button" class="btn btn-light-danger icon-btn-sm btn-supprimer-ligne"><i class="ri-delete-bin-line"></i></button></td>
                `;
                corpsEnfants.appendChild(ligne);
                brancherSuppression(ligne, () => messageAucunEnfant.classList.toggle('d-none', corpsEnfants.children.length > 0));
                messageAucunEnfant.classList.add('d-none');
            });
        });
    </script>
@endsection
