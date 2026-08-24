@extends('partials.layouts.master-finance')

@section('title', $depense->numero.' | Finance')
@section('title-sub', 'Finance')
@section('pagetitle', $depense->numero)

@section('content')
    <div id="layout-wrapper">

        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <div class="row">
            <div class="col-xl-8">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h6 class="card-title mb-0 fw-semibold">{{ $depense->numero }}</h6>
                        <div>
                            @php
                                $couleurs = [
                                    'brouillon' => 'light-subtle text-body',
                                    'en_attente_validation' => 'warning-subtle text-warning',
                                    'approuvee' => 'info-subtle text-info',
                                    'refusee' => 'danger-subtle text-danger',
                                    'intervention_en_cours' => 'info-subtle text-info',
                                    'facture_recue' => 'primary-subtle text-primary',
                                    'a_payer' => 'warning-subtle text-warning',
                                    'payee' => 'success-subtle text-success',
                                    'cloturee' => 'success-subtle text-success',
                                ];
                            @endphp
                            <span class="badge bg-{{ $couleurs[$depense->statut] ?? 'light-subtle text-body' }} fs-13">{{ $depense->libelleStatut() }}</span>
                            @if ($depense->urgence)
                                <span class="badge bg-danger-subtle text-danger fs-13"><i class="bi bi-lightning-fill"></i> Urgent</span>
                            @endif
                        </div>
                    </div>
                    <div class="card-body">
                        <ul class="list-unstyled mb-0 fs-13">
                            <li class="d-flex justify-content-between border-bottom py-2">
                                <span class="text-muted">Bien</span>
                                <span class="fw-medium">{{ $depense->bien->titre ?? '—' }}</span>
                            </li>
                            <li class="d-flex justify-content-between border-bottom py-2">
                                <span class="text-muted">Location / contrat</span>
                                <span class="fw-medium">{{ $depense->contratLocation->numero ?? '— (hors location)' }}</span>
                            </li>
                            <li class="d-flex justify-content-between border-bottom py-2">
                                <span class="text-muted">Bailleur</span>
                                <span class="fw-medium">{{ $depense->bailleur->nom_complet ?? '—' }}</span>
                            </li>
                            @if ($depense->locataire)
                                <li class="d-flex justify-content-between border-bottom py-2">
                                    <span class="text-muted">Locataire</span>
                                    <span class="fw-medium">{{ $depense->locataire->nom_complet }}</span>
                                </li>
                            @endif
                            <li class="d-flex justify-content-between border-bottom py-2">
                                <span class="text-muted">Catégorie</span>
                                <span class="fw-medium">{{ $depense->categorie->nom ?? '—' }}</span>
                            </li>
                            <li class="d-flex justify-content-between border-bottom py-2">
                                <span class="text-muted">Description</span>
                                <span class="fw-medium text-end" style="max-width: 60%;">{{ $depense->description }}</span>
                            </li>
                            <li class="d-flex justify-content-between border-bottom py-2">
                                <span class="text-muted">Fournisseur</span>
                                <span class="fw-medium">{{ $depense->fournisseur ?: '—' }}</span>
                            </li>
                            <li class="d-flex justify-content-between border-bottom py-2">
                                <span class="text-muted">Montant estimé</span>
                                <span class="fw-medium">{{ number_format($depense->montant_estime, 0, ',', ' ') }} FCFA</span>
                            </li>
                            <li class="d-flex justify-content-between border-bottom py-2">
                                <span class="text-muted">Montant final</span>
                                <span class="fw-medium">{{ $depense->montant_final ? number_format($depense->montant_final, 0, ',', ' ').' FCFA' : '—' }}</span>
                            </li>
                            <li class="d-flex justify-content-between border-bottom py-2">
                                <span class="text-muted">Qui supporte</span>
                                <span class="fw-medium text-capitalize">{{ $depense->qui_supporte }}</span>
                            </li>
                            <li class="d-flex justify-content-between border-bottom py-2">
                                <span class="text-muted">Responsable financier</span>
                                <span class="fw-medium">{{ $depense->responsableFinancier->name ?? '—' }}</span>
                            </li>
                            <li class="d-flex justify-content-between border-bottom py-2">
                                <span class="text-muted">Créée par</span>
                                <span class="fw-medium">{{ $depense->creePar->name ?? '—' }} le {{ $depense->created_at->format('d/m/Y') }}</span>
                            </li>
                            @if ($depense->date_paiement)
                                <li class="d-flex justify-content-between border-bottom py-2">
                                    <span class="text-muted">Payée le</span>
                                    <span class="fw-medium">{{ $depense->date_paiement->format('d/m/Y') }} — {{ $depense->modePaiement->nom ?? '' }}</span>
                                </li>
                            @endif
                            @if ($depense->commentaire)
                                <li class="d-flex justify-content-between border-bottom py-2">
                                    <span class="text-muted">Commentaire</span>
                                    <span class="fw-medium text-end" style="max-width: 60%;">{{ $depense->commentaire }}</span>
                                </li>
                            @endif
                            @if ($depense->motif_refus)
                                <li class="d-flex justify-content-between py-2">
                                    <span class="text-muted">Motif du refus</span>
                                    <span class="fw-medium text-danger text-end" style="max-width: 60%;">{{ $depense->motif_refus }}</span>
                                </li>
                            @endif
                        </ul>
                    </div>
                </div>

                @include('locative.documents._liste', ['documentable' => $depense, 'typeDocument' => 'depense'])
            </div>

            <div class="col-xl-4">
                @can('finance.gerer')
                    <div class="card">
                        <div class="card-header"><h6 class="card-title mb-0 fw-semibold">Actions</h6></div>
                        <div class="card-body d-flex flex-column gap-2">
                            @if ($depense->statut === 'brouillon')
                                <form action="{{ route('finance.depenses.soumettre', $depense) }}" method="POST">
                                    @csrf
                                    <button type="submit" class="btn btn-primary w-100"><i class="bi bi-send me-1"></i>Soumettre pour validation</button>
                                </form>
                            @endif

                            @if ($depense->statut === 'en_attente_validation')
                                <form action="{{ route('finance.depenses.approuver', $depense) }}" method="POST">
                                    @csrf
                                    <button type="submit" class="btn btn-success w-100"><i class="bi bi-check-circle me-1"></i>Approuver</button>
                                </form>
                                <button type="button" class="btn btn-danger w-100" data-bs-toggle="modal" data-bs-target="#refuserModal"><i class="bi bi-x-circle me-1"></i>Refuser</button>
                            @endif

                            @if ($depense->statut === 'approuvee')
                                <form action="{{ route('finance.depenses.demarrer-intervention', $depense) }}" method="POST">
                                    @csrf
                                    <button type="submit" class="btn btn-info w-100"><i class="bi bi-tools me-1"></i>Démarrer l'intervention</button>
                                </form>
                                <button type="button" class="btn btn-light-primary w-100" data-bs-toggle="modal" data-bs-target="#factureModal"><i class="bi bi-receipt me-1"></i>Facture reçue (sans intervention)</button>
                            @endif

                            @if ($depense->statut === 'intervention_en_cours')
                                <button type="button" class="btn btn-primary w-100" data-bs-toggle="modal" data-bs-target="#factureModal"><i class="bi bi-receipt me-1"></i>Facture reçue</button>
                            @endif

                            @if ($depense->statut === 'facture_recue')
                                <form action="{{ route('finance.depenses.marquer-a-payer', $depense) }}" method="POST">
                                    @csrf
                                    <button type="submit" class="btn btn-warning w-100"><i class="bi bi-cash-stack me-1"></i>Marquer à payer</button>
                                </form>
                            @endif

                            @if ($depense->statut === 'a_payer')
                                <button type="button" class="btn btn-success w-100" data-bs-toggle="modal" data-bs-target="#payerModal"><i class="bi bi-check2-circle me-1"></i>Enregistrer le paiement</button>
                            @endif

                            @if (in_array($depense->statut, ['payee', 'refusee']))
                                <form action="{{ route('finance.depenses.cloturer', $depense) }}" method="POST">
                                    @csrf
                                    <button type="submit" class="btn btn-light-success w-100"><i class="bi bi-lock me-1"></i>Clôturer le dossier</button>
                                </form>
                            @endif

                            @if ($depense->statut === 'cloturee')
                                <p class="text-muted fs-12 mb-0">Dossier clôturé. Aucune action supplémentaire.</p>
                            @endif
                        </div>
                    </div>
                @endcan

                <div class="card">
                    <div class="card-header"><h6 class="card-title mb-0 fw-semibold">Parcours du dossier</h6></div>
                    <div class="card-body">
                        <p class="text-muted fs-12 mb-0">
                            Brouillon → En attente de validation → Approuvée → Intervention en cours → Facture reçue → À payer → Payée → Clôturée.
                            {{ $depense->urgence ? 'Une dépense urgente saute directement à « Approuvée ».' : '' }}
                            Un refus mène directement à « Clôturée ».
                        </p>
                    </div>
                </div>
            </div>
        </div>

    </div>

    <div class="modal fade" id="refuserModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <form action="{{ route('finance.depenses.refuser', $depense) }}" method="POST">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title">Refuser la dépense</h5>
                        <button type="button" class="btn-close icon-btn-sm" data-bs-dismiss="modal" aria-label="Close"><i class="ri-close-large-line fw-semibold"></i></button>
                    </div>
                    <div class="modal-body">
                        <label class="form-label">Motif du refus<span class="text-danger ms-1">*</span></label>
                        <textarea class="form-control" name="motif_refus" rows="3" required></textarea>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Annuler</button>
                        <button type="submit" class="btn btn-danger">Refuser</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="modal fade" id="factureModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <form action="{{ route('finance.depenses.facture-recue', $depense) }}" method="POST">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title">Facture reçue</h5>
                        <button type="button" class="btn-close icon-btn-sm" data-bs-dismiss="modal" aria-label="Close"><i class="ri-close-large-line fw-semibold"></i></button>
                    </div>
                    <div class="modal-body">
                        <label class="form-label">Montant final (FCFA)<span class="text-danger ms-1">*</span></label>
                        <input type="number" step="0.01" min="0" class="form-control" name="montant_final" value="{{ $depense->montant_estime }}" required>
                        <p class="text-muted fs-12 mt-2 mb-0">Pensez à joindre la facture via « Ajouter un document » une fois enregistrée.</p>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Annuler</button>
                        <button type="submit" class="btn btn-primary">Enregistrer</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="modal fade" id="payerModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <form action="{{ route('finance.depenses.payer', $depense) }}" method="POST">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title">Enregistrer le paiement</h5>
                        <button type="button" class="btn-close icon-btn-sm" data-bs-dismiss="modal" aria-label="Close"><i class="ri-close-large-line fw-semibold"></i></button>
                    </div>
                    <div class="modal-body">
                        <p class="text-muted fs-12">Montant : <strong>{{ number_format($depense->montantImpute(), 0, ',', ' ') }} FCFA</strong> — à la charge : <strong class="text-capitalize">{{ $depense->qui_supporte }}</strong>{{ $depense->qui_supporte === 'bailleur' ? ' (sera déduit du prochain reversement)' : '' }}</p>
                        <div class="row g-3">
                            <div class="col-12">
                                <label class="form-label">Mode de paiement<span class="text-danger ms-1">*</span></label>
                                <select class="form-select" name="mode_paiement_id" required>
                                    <option value="">—</option>
                                    @foreach ($modesPaiement as $mode)
                                        <option value="{{ $mode->id }}">{{ $mode->nom }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-12">
                                <label class="form-label">Date de paiement<span class="text-danger ms-1">*</span></label>
                                <input type="date" class="form-control" name="date_paiement" value="{{ now()->format('Y-m-d') }}" required>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Annuler</button>
                        <button type="submit" class="btn btn-success">Confirmer le paiement</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    </main>
@endsection

@section('js')
    <script type="module" src="{{ asset('assets/js/app.js') }}"></script>
@endsection
