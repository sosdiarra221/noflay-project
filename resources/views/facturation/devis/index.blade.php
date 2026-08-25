@extends('partials.layouts.master-facturation')

@section('title', 'Devis | Facturation')
@section('title-sub', 'Facturation')
@section('pagetitle', 'Devis')

@section('css')
    <link rel="stylesheet" href="{{ asset('assets/libs/choices.js/public/assets/styles/choices.min.css') }}">
@endsection

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
            <div class="card-body">
                <form method="GET" class="row g-3 align-items-end">
                    <div class="col-md-4">
                        <label class="form-label">Statut</label>
                        <select class="form-select" name="statut" onchange="this.form.submit()">
                            <option value="">Tous statuts</option>
                            @foreach (\App\Models\Facturation\Devis::STATUTS as $valeur => $libelle)
                                <option value="{{ $valeur }}" @selected(request('statut') === $valeur)>{{ $libelle }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Client</label>
                        <select class="form-select" name="client_id" onchange="this.form.submit()">
                            <option value="">Tous les clients</option>
                            @foreach ($clients as $client)
                                <option value="{{ $client->id }}" @selected(request('client_id') == $client->id)>{{ $client->nom_complet }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-4 d-flex gap-2">
                        <a href="{{ route('facturation.devis.index') }}" class="btn btn-light-danger">Réinitialiser</a>
                    </div>
                </form>
            </div>
        </div>

        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h6 class="mb-0">Devis <span class="badge bg-secondary-subtle text-secondary ms-1">{{ $devis->count() }}</span></h6>
                <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#creerDevisModal">
                    <i class="bi bi-plus-lg me-1"></i>Nouveau devis
                </button>
            </div>
            <div class="card-body p-0">
                <div class="table-box table-responsive">
                    <table class="table text-nowrap align-middle mb-0">
                        <thead><tr><th>Numéro</th><th>Client</th><th>Date</th><th>Lignes</th><th>Total TTC</th><th>Statut</th><th>Actions</th></tr></thead>
                        <tbody>
                            @php $classesStatut = ['nouveau' => 'secondary', 'en_negociation' => 'warning', 'gagne' => 'success', 'perdu' => 'danger', 'annule' => 'dark']; @endphp
                            @forelse ($devis as $d)
                                <tr>
                                    <td class="fw-medium"><a href="{{ route('facturation.devis.show', $d) }}" class="text-reset">{{ $d->numero }}</a></td>
                                    <td>{{ $d->client->nom_complet ?? '—' }}</td>
                                    <td>{{ $d->date_devis->format('d/m/Y') }}</td>
                                    <td>{{ $d->lignes->count() }}</td>
                                    <td class="fw-medium">{{ number_format($d->total_ttc, 0, ',', ' ') }} FCFA</td>
                                    <td><span class="badge bg-{{ $classesStatut[$d->statut] ?? 'secondary' }}-subtle text-{{ $classesStatut[$d->statut] ?? 'secondary' }}">{{ $d->libelleStatut() }}</span></td>
                                    <td>
                                        <a href="{{ route('facturation.devis.show', $d) }}" class="btn btn-light-success btn-sm" title="Voir le devis"><i class="bi bi-eye me-1"></i>Détail</a>
                                    </td>
                                </tr>
                            @empty
                                <tr><td colspan="7" class="text-center text-muted py-5">Aucun devis pour le moment.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

    </div>

    {{-- Créer un devis --}}
    <div class="modal fade" id="creerDevisModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-xl" role="document">
            <div class="modal-content">
                <form action="{{ route('facturation.devis.store') }}" method="POST" id="formCreerDevis">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title">Nouveau devis</h5>
                        <button type="button" class="btn-close icon-btn-sm" data-bs-dismiss="modal" aria-label="Close"><i class="ri-close-large-line fw-semibold"></i></button>
                    </div>
                    <div class="modal-body">
                        <div class="row g-3 mb-4">
                            <div class="col-md-4">
                                <label class="form-label">Date<span class="text-danger ms-1">*</span></label>
                                <input type="date" class="form-control" name="date_devis" value="{{ now()->format('Y-m-d') }}" required>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Statut<span class="text-danger ms-1">*</span></label>
                                <select class="form-select" name="statut" required>
                                    @foreach (\App\Models\Facturation\Devis::STATUTS as $valeur => $libelle)
                                        <option value="{{ $valeur }}" @selected($valeur === 'nouveau')>{{ $libelle }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label d-block">TVA</label>
                                <div class="d-flex align-items-center gap-2 pt-2">
                                    <div class="form-check form-switch mb-0">
                                        <input class="form-check-input" type="checkbox" name="appliquer_tva" value="1" id="champAppliquerTva">
                                        <label class="form-check-label" for="champAppliquerTva">Activer la TVA</label>
                                    </div>
                                </div>
                                <input type="number" step="0.01" min="0" max="100" class="form-control mt-2 d-none" id="champTauxTva" name="taux_tva" value="{{ (float) ($reglage->taux_tva_defaut ?? 18) }}" placeholder="Taux TVA (%)">
                            </div>
                        </div>

                        <hr>

                        <h6 class="mb-3">Client / Prospect</h6>
                        <div class="row g-3 mb-2">
                            <div class="col-12">
                                <div class="btn-group" role="group">
                                    <input type="radio" class="btn-check" name="mode_client" id="modeClientExistant" checked>
                                    <label class="btn btn-outline-primary btn-sm" for="modeClientExistant">Client existant</label>
                                    <input type="radio" class="btn-check" name="mode_client" id="modeClientNouveau">
                                    <label class="btn btn-outline-primary btn-sm" for="modeClientNouveau">Nouveau client</label>
                                </div>
                            </div>
                        </div>
                        <div class="row g-3 mb-4" id="blocClientExistant">
                            <div class="col-12">
                                <label class="form-label">Sélectionner un client</label>
                                <select class="form-select" name="client_id" id="selectClientDevis">
                                    <option value="">Rechercher un client...</option>
                                    @foreach ($clients as $client)
                                        <option value="{{ $client->id }}">{{ $client->nom_complet }} @if($client->telephone) — {{ $client->telephone }} @endif</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="row g-3 mb-4 d-none" id="blocClientNouveau">
                            <div class="col-md-4">
                                <label class="form-label">Nom complet<span class="text-danger ms-1">*</span></label>
                                <input type="text" class="form-control" name="nouveau_client_nom" id="champNouveauClientNom">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Téléphone</label>
                                <input type="text" class="form-control" name="nouveau_client_telephone">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Email</label>
                                <input type="email" class="form-control" name="nouveau_client_email">
                            </div>
                        </div>

                        <hr>

                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h6 class="mb-0">Lignes du devis</h6>
                            <button type="button" class="btn btn-light-primary btn-sm" id="btnAjouterLigne">
                                <i class="bi bi-plus-lg me-1"></i>Ajouter une ligne
                            </button>
                        </div>
                        <div class="table-box table-responsive mb-3">
                            <table class="table align-middle mb-0" id="tableLignesDevis">
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
                    <div class="modal-footer">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Annuler</button>
                        <button type="submit" class="btn btn-primary"><i class="bi bi-check-circle me-1"></i>Créer le devis</button>
                    </div>
                </form>
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
            new Choices(document.getElementById('selectClientDevis'), {
                searchEnabled: true,
                itemSelectText: '',
                placeholderValue: 'Rechercher un client...',
                searchPlaceholderValue: 'Rechercher...',
            });

            // Bascule client existant / nouveau client
            const modeExistant = document.getElementById('modeClientExistant');
            const modeNouveau = document.getElementById('modeClientNouveau');
            const blocExistant = document.getElementById('blocClientExistant');
            const blocNouveau = document.getElementById('blocClientNouveau');
            const champNouveauNom = document.getElementById('champNouveauClientNom');

            function synchroniserModeClient() {
                const estNouveau = modeNouveau.checked;
                blocExistant.classList.toggle('d-none', estNouveau);
                blocNouveau.classList.toggle('d-none', ! estNouveau);
                champNouveauNom.required = estNouveau;
            }
            modeExistant.addEventListener('change', synchroniserModeClient);
            modeNouveau.addEventListener('change', synchroniserModeClient);
            synchroniserModeClient();

            // Lignes dynamiques (déclaré avant la section TVA car recalculerTotaux() en dépend)
            const corps = document.getElementById('corpsLignesDevis');
            const btnAjouter = document.getElementById('btnAjouterLigne');
            let compteurLigne = 0;

            // TVA
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

            // Réinitialise le formulaire avec une première ligne à chaque ouverture de la modale
            document.getElementById('creerDevisModal').addEventListener('shown.bs.modal', function () {
                if (corps.children.length === 0) {
                    ajouterLigne();
                }
            });

            document.getElementById('formCreerDevis').addEventListener('submit', function (e) {
                if (corps.children.length === 0) {
                    e.preventDefault();
                    alert('Ajoutez au moins une ligne au devis.');
                }
            });
        });
    </script>
@endsection
