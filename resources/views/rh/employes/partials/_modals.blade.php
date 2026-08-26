{{-- Aperçu d'un document (iframe pour PDF/images, sinon repli sur le téléchargement) --}}
<div class="modal fade" id="apercuDocumentModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="apercuDocumentTitre">Document</h5>
                <button type="button" class="btn-close icon-btn-sm" data-bs-dismiss="modal" aria-label="Close"><i class="ri-close-large-line fw-semibold"></i></button>
            </div>
            <div class="modal-body p-0">
                <iframe id="apercuDocumentFrame" src="" style="width: 100%; height: 70vh; border: 0;" class="d-none"></iframe>
                <div id="apercuDocumentIndisponible" class="text-center text-muted py-8 d-none">
                    <i class="bi bi-file-earmark-x" style="font-size: 2.5rem;"></i>
                    <p class="mt-3 mb-0">Aperçu non disponible pour ce type de fichier — téléchargez-le pour le consulter.</p>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-light" data-bs-dismiss="modal">Fermer</button>
                <a id="apercuDocumentTelecharger" href="#" class="btn btn-primary"><i class="bi bi-download me-1"></i>Télécharger</a>
            </div>
        </div>
    </div>
</div>

@can('rh.gerer')
    {{-- Ajuster le solde de congé --}}
    <div class="modal fade" id="ajusterSoldeModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <form action="{{ route('rh.employes.solde-conges', $employe) }}" method="POST">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title">Ajuster le solde de congé</h5>
                        <button type="button" class="btn-close icon-btn-sm" data-bs-dismiss="modal" aria-label="Close"><i class="ri-close-large-line fw-semibold"></i></button>
                    </div>
                    <div class="modal-body">
                        <p class="text-muted fs-12">Solde actuel : <strong>{{ number_format($employe->solde_conges, 1) }} j</strong>. Saisissez une valeur positive pour créditer, négative pour débiter.</p>
                        <div class="mb-3">
                            <label class="form-label">Ajustement (jours)<span class="text-danger ms-1">*</span></label>
                            <input type="number" step="0.5" class="form-control" name="ajustement" placeholder="Ex : 3 ou -2" required>
                        </div>
                        <div class="mb-0">
                            <label class="form-label">Motif<span class="text-danger ms-1">*</span></label>
                            <textarea class="form-control" name="motif" rows="2" placeholder="Ex : régularisation, erreur de saisie..." required></textarea>
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

    {{-- Nouveau contrat --}}
    <div class="modal fade" id="nouveauContratModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <form action="{{ route('rh.contrats.store', $employe) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title">Nouveau contrat — {{ $employe->nom_complet }}</h5>
                        <button type="button" class="btn-close icon-btn-sm" data-bs-dismiss="modal" aria-label="Close"><i class="ri-close-large-line fw-semibold"></i></button>
                    </div>
                    <div class="modal-body">
                        <div class="row g-3">
                            <div class="col-12">
                                <label class="form-label">Type de contrat<span class="text-danger ms-1">*</span></label>
                                <select class="form-select" name="type_contrat" required>
                                    @foreach (\App\Models\Rh\ContratTravail::TYPES as $valeur => $libelle)
                                        <option value="{{ $valeur }}">{{ $libelle }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Date de début<span class="text-danger ms-1">*</span></label>
                                <input type="date" class="form-control" name="date_debut" value="{{ now()->format('Y-m-d') }}" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Date de fin prévue</label>
                                <input type="date" class="form-control" name="date_prevu_fin">
                            </div>
                            <div class="col-12">
                                <label class="form-label">Montant (salaire)</label>
                                <input type="number" step="0.01" min="0" class="form-control" name="montant">
                            </div>
                            <div class="col-12">
                                <label class="form-label">Document contractuel</label>
                                <input type="file" class="form-control" name="document" accept=".pdf,.jpg,.jpeg,.png">
                            </div>
                            <div class="col-12">
                                <label class="form-label">Motif / observations</label>
                                <textarea class="form-control" name="motif" rows="2"></textarea>
                            </div>
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

    @foreach ($employe->contrats as $contrat)
        @if ($contrat->etat === 'actif')
            {{-- Renouveler --}}
            <div class="modal fade" id="renouvelerContratModal{{ $contrat->id }}" tabindex="-1" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered" role="document">
                    <div class="modal-content">
                        <form action="{{ route('rh.contrats.renouveler', $contrat) }}" method="POST">
                            @csrf
                            <div class="modal-header">
                                <h5 class="modal-title">Renouveler le contrat {{ $contrat->numero }}</h5>
                                <button type="button" class="btn-close icon-btn-sm" data-bs-dismiss="modal" aria-label="Close"><i class="ri-close-large-line fw-semibold"></i></button>
                            </div>
                            <div class="modal-body">
                                <p class="text-muted">Un nouveau contrat sera créé, l'ancien passera au statut « Clôturé ». L'historique de renouvellement est conservé.</p>
                                <div class="row g-3">
                                    <div class="col-12">
                                        <label class="form-label">Type de contrat<span class="text-danger ms-1">*</span></label>
                                        <select class="form-select" name="type_contrat" required>
                                            @foreach (\App\Models\Rh\ContratTravail::TYPES as $valeur => $libelle)
                                                <option value="{{ $valeur }}" @selected($valeur === $contrat->type_contrat)>{{ $libelle }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">Nouvelle date de début<span class="text-danger ms-1">*</span></label>
                                        <input type="date" class="form-control" name="date_debut" value="{{ ($contrat->date_prevu_fin ?? now())->copy()->addDay()->format('Y-m-d') }}" required>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">Nouvelle date de fin prévue</label>
                                        <input type="date" class="form-control" name="date_prevu_fin">
                                    </div>
                                    <div class="col-12">
                                        <label class="form-label">Montant</label>
                                        <input type="number" step="0.01" min="0" class="form-control" name="montant" value="{{ $contrat->montant }}">
                                    </div>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-light" data-bs-dismiss="modal">Annuler</button>
                                <button type="submit" class="btn btn-primary">Renouveler</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            {{-- Clôturer --}}
            <div class="modal fade" id="cloturerContratModal{{ $contrat->id }}" tabindex="-1" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered" role="document">
                    <div class="modal-content">
                        <form action="{{ route('rh.contrats.cloturer', $contrat) }}" method="POST">
                            @csrf
                            <div class="modal-header">
                                <h5 class="modal-title">Clôturer le contrat {{ $contrat->numero }}</h5>
                                <button type="button" class="btn-close icon-btn-sm" data-bs-dismiss="modal" aria-label="Close"><i class="ri-close-large-line fw-semibold"></i></button>
                            </div>
                            <div class="modal-body">
                                <div class="row g-3">
                                    <div class="col-12">
                                        <label class="form-label">Date de fin effective<span class="text-danger ms-1">*</span></label>
                                        <input type="date" class="form-control" name="date_fin" value="{{ now()->format('Y-m-d') }}" required>
                                    </div>
                                    <div class="col-12">
                                        <label class="form-label">Motif<span class="text-danger ms-1">*</span></label>
                                        <textarea class="form-control" name="motif" rows="2" required></textarea>
                                    </div>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-light" data-bs-dismiss="modal">Annuler</button>
                                <button type="submit" class="btn btn-danger">Clôturer</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        @endif
    @endforeach

    {{-- Archiver --}}
    <div class="modal fade" id="archiverModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <form action="{{ route('rh.employes.archiver', $employe) }}" method="POST">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title">Archiver {{ $employe->nom_complet }}</h5>
                        <button type="button" class="btn-close icon-btn-sm" data-bs-dismiss="modal" aria-label="Close"><i class="ri-close-large-line fw-semibold"></i></button>
                    </div>
                    <div class="modal-body">
                        <div class="alert alert-warning">
                            <i class="bi bi-exclamation-triangle me-1"></i>
                            L'employé passe au statut « Sortie ». Son dossier est conservé — aucune suppression physique.
                        </div>
                        <div class="row g-3">
                            <div class="col-12">
                                <label class="form-label">Date de sortie<span class="text-danger ms-1">*</span></label>
                                <input type="date" class="form-control" name="date_sortie" value="{{ now()->format('Y-m-d') }}" required>
                            </div>
                            <div class="col-12">
                                <label class="form-label">Motif<span class="text-danger ms-1">*</span></label>
                                <textarea class="form-control" name="motif_sortie" rows="2" required></textarea>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Annuler</button>
                        <button type="submit" class="btn btn-danger">Confirmer l'archivage</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- Ajouter un document --}}
    <div class="modal fade" id="ajouterDocumentModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <form action="{{ route('rh.employes.documents.store', $employe) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title">Ajouter un document — {{ $employe->nom_complet }}</h5>
                        <button type="button" class="btn-close icon-btn-sm" data-bs-dismiss="modal" aria-label="Close"><i class="ri-close-large-line fw-semibold"></i></button>
                    </div>
                    <div class="modal-body">
                        <div class="row g-3">
                            <div class="col-12">
                                <label class="form-label">Type de document<span class="text-danger ms-1">*</span></label>
                                <select class="form-select" name="type_document" required>
                                    <option value="">Sélectionner...</option>
                                    @foreach (\App\Http\Controllers\Rh\EmployeDocumentController::TYPES as $valeur => $libelle)
                                        <option value="{{ $valeur }}">{{ $libelle }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-12">
                                <label class="form-label">Fichier<span class="text-danger ms-1">*</span></label>
                                <input type="file" class="form-control" name="fichier" required>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Annuler</button>
                        <button type="submit" class="btn btn-primary">Ajouter</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endcan
