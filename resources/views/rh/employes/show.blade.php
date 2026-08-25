@extends('partials.layouts.master-rh')

@section('title', $employe->nom_complet.' | RH')
@section('title-sub', 'Employés')
@section('pagetitle', $employe->nom_complet)

@section('content')
    <div id="layout-wrapper">

        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <div class="card overflow-hidden">
            <div class="mt-2">
                <div class="card-body p-5">
                    <div class="d-flex flex-wrap align-items-start gap-5">
                        <div class="flex-shrink-0">
                            <img src="{{ $employe->photo ? asset('storage/'.$employe->photo) : asset('assets/images/avatar/avatar-10.jpg') }}" class="rounded-circle border border-4 border-white shadow-lg" width="110" height="110" style="object-fit: cover;">
                        </div>
                        <div class="flex-grow-1">
                            <div class="mb-2">
                                <h5 class="mb-1">{{ $employe->nom_complet }}
                                    @if ($employe->statut === 'actif')
                                        <span class="badge bg-success-subtle text-success ms-1">Actif</span>
                                    @else
                                        <span class="badge bg-secondary-subtle text-secondary ms-1">Sortie</span>
                                    @endif
                                </h5>
                                <p class="text-muted fs-12 mb-0">{{ $employe->matricule }} — {{ $employe->poste->nom ?? '—' }} ({{ $employe->libelleCategorieFonction() }})</p>
                            </div>
                            <div class="d-flex flex-wrap gap-3">
                                <span class="badge bg-secondary-subtle text-secondary fs-13 px-3 py-2"><i class="bi bi-diagram-3 me-1"></i>{{ $employe->departement->nom ?? '—' }}</span>
                                <span class="badge bg-secondary-subtle text-secondary fs-13 px-3 py-2"><i class="bi bi-geo-alt me-1"></i>{{ $employe->sites->pluck('nom')->implode(', ') ?: 'Aucun site' }}</span>
                                <span class="badge bg-info-subtle text-info fs-13 px-3 py-2"><i class="bi bi-calendar-check me-1"></i>{{ number_format($employe->solde_conges, 1) }} j de congés</span>
                            </div>
                        </div>
                        @can('rh.gerer')
                            <div class="flex-shrink-0 d-flex flex-wrap gap-2">
                                <a href="{{ route('rh.employes.edit', $employe) }}" class="btn btn-light-primary"><i class="bi bi-pencil-square me-1"></i>Modifier</a>
                                <button type="button" class="btn btn-light-success" data-bs-toggle="modal" data-bs-target="#nouveauContratModal"><i class="bi bi-file-earmark-plus me-1"></i>Nouveau contrat</button>
                                @if ($employe->statut === 'actif')
                                    <button type="button" class="btn btn-light-danger" data-bs-toggle="modal" data-bs-target="#archiverModal"><i class="bi bi-box-arrow-right me-1"></i>Archiver</button>
                                @else
                                    <form action="{{ route('rh.employes.reactiver', $employe) }}" method="POST">
                                        @csrf
                                        <button type="submit" class="btn btn-light-success"><i class="bi bi-arrow-counterclockwise me-1"></i>Réactiver</button>
                                    </form>
                                @endif
                            </div>
                        @endcan
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-12">
                <div class="mb-6">
                    <ul class="nav nav-pills" role="tablist">
                        <li class="nav-item"><button class="nav-link active" data-bs-toggle="tab" data-bs-target="#infos-pane" type="button">Vue générale</button></li>
                        <li class="nav-item"><button class="nav-link" data-bs-toggle="tab" data-bs-target="#contrats-pane" type="button">Contrats <span class="badge bg-secondary-subtle text-secondary ms-1">{{ $employe->contrats->count() }}</span></button></li>
                        <li class="nav-item"><button class="nav-link" data-bs-toggle="tab" data-bs-target="#affectations-pane" type="button">Affectations <span class="badge bg-secondary-subtle text-secondary ms-1">{{ $employe->affectations->count() }}</span></button></li>
                        <li class="nav-item"><button class="nav-link" data-bs-toggle="tab" data-bs-target="#documents-pane" type="button">Documents <span class="badge bg-secondary-subtle text-secondary ms-1">{{ $employe->documents->count() }}</span></button></li>
                    </ul>
                </div>

                <div class="tab-content">
                    <div class="tab-pane fade show active" id="infos-pane">
                        <div class="row g-4">
                            <div class="col-lg-6">
                                <div class="card">
                                    <div class="card-header"><h6 class="card-action-title mb-0">Contact</h6></div>
                                    <div class="card-body">
                                        <div class="row mb-3"><div class="col-5 text-muted">Téléphone</div><div class="col-7 fw-medium">{{ $employe->telephone ?: '—' }}</div></div>
                                        <div class="row mb-3"><div class="col-5 text-muted">WhatsApp</div><div class="col-7 fw-medium">{{ $employe->whatsapp ?: '—' }}</div></div>
                                        <div class="row mb-3"><div class="col-5 text-muted">Email</div><div class="col-7 fw-medium">{{ $employe->email ?: '—' }}</div></div>
                                        <div class="row"><div class="col-5 text-muted">Adresse</div><div class="col-7 fw-medium">{{ $employe->adresse ?: '—' }}</div></div>
                                    </div>
                                </div>
                                <div class="card">
                                    <div class="card-header"><h6 class="card-action-title mb-0">Diplômes</h6></div>
                                    <div class="card-body">
                                        @forelse ($employe->diplomes as $diplome)
                                            <div class="d-flex justify-content-between border-bottom py-2">
                                                <span>{{ $diplome->intitule }} @if($diplome->niveau) <span class="text-muted fs-12">({{ $diplome->niveau }})</span> @endif</span>
                                                <span class="text-muted">{{ $diplome->annee_obtention ?: '—' }}</span>
                                            </div>
                                        @empty
                                            <p class="text-muted fs-12 mb-0">Aucun diplôme enregistré.</p>
                                        @endforelse
                                    </div>
                                </div>
                                <div class="card">
                                    <div class="card-header"><h6 class="card-action-title mb-0">Langues</h6></div>
                                    <div class="card-body">
                                        <div class="row mb-3"><div class="col-5 text-muted">Langues parlées</div><div class="col-7 fw-medium">{{ $employe->langues_parlees ?: '—' }}</div></div>
                                        <div class="row"><div class="col-5 text-muted">Langues lues</div><div class="col-7 fw-medium">{{ $employe->langues_lues ?: '—' }}</div></div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-6">
                                @can('rh.donnees-sensibles')
                                    <div class="card">
                                        <div class="card-header"><h6 class="card-action-title mb-0"><i class="bi bi-shield-lock me-1"></i>État civil &amp; identité <span class="badge bg-warning-subtle text-warning ms-1 fs-11">Données sensibles</span></h6></div>
                                        <div class="card-body">
                                            <div class="row mb-3"><div class="col-5 text-muted">Sexe</div><div class="col-7 fw-medium text-capitalize">{{ $employe->sexe ?: '—' }}</div></div>
                                            <div class="row mb-3"><div class="col-5 text-muted">Date de naissance</div><div class="col-7 fw-medium">{{ $employe->date_naissance?->format('d/m/Y') ?: '—' }}</div></div>
                                            <div class="row mb-3"><div class="col-5 text-muted">Lieu de naissance</div><div class="col-7 fw-medium">{{ $employe->lieu_naissance ?: '—' }}</div></div>
                                            <div class="row mb-3"><div class="col-5 text-muted">Situation matrimoniale</div><div class="col-7 fw-medium">{{ \App\Models\Rh\Employe::SITUATIONS_MATRIMONIALES[$employe->situation_matrimoniale] ?? '—' }}</div></div>
                                            <div class="row mb-3"><div class="col-5 text-muted">Pièce d'identité</div><div class="col-7 fw-medium">{{ $employe->libellePieceIdentite() ?: '—' }} {{ $employe->piece_identite_numero }}</div></div>
                                            <div class="row"><div class="col-5 text-muted">Aptitudes</div><div class="col-7">
                                                <span class="badge {{ $employe->permis_conduire ? 'bg-success-subtle text-success' : 'bg-secondary-subtle text-secondary' }} me-1">Permis {{ $employe->permis_conduire ? '✓' : '✗' }}</span>
                                                <span class="badge {{ $employe->arts_martiaux ? 'bg-success-subtle text-success' : 'bg-secondary-subtle text-secondary' }} me-1">Arts martiaux {{ $employe->arts_martiaux ? '✓' : '✗' }}</span>
                                                <span class="badge {{ $employe->service_militaire ? 'bg-success-subtle text-success' : 'bg-secondary-subtle text-secondary' }}">Service militaire {{ $employe->service_militaire ? '✓' : '✗' }}</span>
                                            </div></div>
                                        </div>
                                    </div>
                                    <div class="card">
                                        <div class="card-header"><h6 class="card-action-title mb-0"><i class="bi bi-shield-lock me-1"></i>Coordonnées bancaires <span class="badge bg-warning-subtle text-warning ms-1 fs-11">Données sensibles</span></h6></div>
                                        <div class="card-body">
                                            <div class="row mb-3"><div class="col-5 text-muted">Banque</div><div class="col-7 fw-medium">{{ $employe->banque ?: '—' }}</div></div>
                                            <div class="row"><div class="col-5 text-muted">Compte / RIB</div><div class="col-7 fw-medium">{{ $employe->compte_bancaire ?: '—' }}</div></div>
                                        </div>
                                    </div>
                                    <div class="card">
                                        <div class="card-header"><h6 class="card-action-title mb-0"><i class="bi bi-shield-lock me-1"></i>Situation familiale <span class="badge bg-warning-subtle text-warning ms-1 fs-11">Données sensibles</span></h6></div>
                                        <div class="card-body">
                                            <p class="text-muted fs-12 mb-2">Épouse(s)</p>
                                            @forelse ($employe->epouses as $epouse)
                                                <div class="d-flex justify-content-between border-bottom py-2">
                                                    <span>{{ $epouse->nom_complet }}</span>
                                                    <span class="text-muted">{{ $epouse->telephone ?: '—' }}</span>
                                                </div>
                                            @empty
                                                <p class="text-muted fs-12">Aucune épouse enregistrée.</p>
                                            @endforelse
                                            <p class="text-muted fs-12 mb-2 mt-3">Enfant(s)</p>
                                            @forelse ($employe->enfants as $enfant)
                                                <div class="d-flex justify-content-between border-bottom py-2">
                                                    <span>{{ $enfant->nom_complet }}</span>
                                                    <span class="text-muted">{{ $enfant->date_naissance?->format('d/m/Y') ?: '—' }}</span>
                                                </div>
                                            @empty
                                                <p class="text-muted fs-12 mb-0">Aucun enfant enregistré.</p>
                                            @endforelse
                                        </div>
                                    </div>
                                    <div class="card">
                                        <div class="card-header"><h6 class="card-action-title mb-0">Contact d'urgence</h6></div>
                                        <div class="card-body">
                                            <div class="row mb-3"><div class="col-5 text-muted">Nom</div><div class="col-7 fw-medium">{{ $employe->personne_urgence_nom ?: '—' }}</div></div>
                                            <div class="row mb-3"><div class="col-5 text-muted">Téléphone</div><div class="col-7 fw-medium">{{ $employe->personne_urgence_telephone ?: '—' }}</div></div>
                                            <div class="row"><div class="col-5 text-muted">Lien de parenté</div><div class="col-7 fw-medium">{{ $employe->personne_urgence_lien ?: '—' }}</div></div>
                                        </div>
                                    </div>
                                @else
                                    <div class="card">
                                        <div class="card-body text-center py-8">
                                            <i class="bi bi-shield-lock text-muted" style="font-size: 2.5rem;"></i>
                                            <p class="text-muted mt-3 mb-0">L'identité complète, le RIB et la situation familiale sont des données sensibles réservées à un accès restreint.</p>
                                        </div>
                                    </div>
                                @endcan
                            </div>
                        </div>
                        @if ($employe->notes)
                            <div class="card">
                                <div class="card-header"><h6 class="card-action-title mb-0">Notes</h6></div>
                                <div class="card-body"><p class="mb-0">{{ $employe->notes }}</p></div>
                            </div>
                        @endif
                    </div>

                    <div class="tab-pane fade" id="contrats-pane">
                        <div class="card">
                            <div class="card-body p-0">
                                <div class="table-box table-responsive">
                                    <table class="table text-nowrap align-middle mb-0">
                                        <thead><tr><th>Numéro</th><th>Type</th><th>Début</th><th>Fin prévue</th><th>Fin réelle</th><th>Montant</th><th>État</th><th>Actions</th></tr></thead>
                                        <tbody>
                                            @forelse ($employe->contrats as $contrat)
                                                <tr>
                                                    <td class="fw-medium">{{ $contrat->numero }}</td>
                                                    <td>{{ $contrat->libelleType() }}</td>
                                                    <td>{{ $contrat->date_debut->format('d/m/Y') }}</td>
                                                    <td>{{ $contrat->date_prevu_fin?->format('d/m/Y') ?: '—' }}</td>
                                                    <td>{{ $contrat->date_fin?->format('d/m/Y') ?: '—' }}</td>
                                                    <td>{{ $contrat->montant ? number_format($contrat->montant, 0, ',', ' ').' FCFA' : '—' }}</td>
                                                    <td>
                                                        @if ($contrat->etat === 'actif')
                                                            <span class="badge bg-success-subtle text-success">Actif</span>
                                                        @else
                                                            <span class="badge bg-secondary-subtle text-secondary">Clôturé</span>
                                                        @endif
                                                    </td>
                                                    <td>
                                                        @can('rh.gerer')
                                                            @if ($contrat->etat === 'actif')
                                                                <div class="hstack gap-2">
                                                                    <button type="button" class="btn btn-light-primary btn-sm" data-bs-toggle="modal" data-bs-target="#renouvelerContratModal{{ $contrat->id }}">Renouveler</button>
                                                                    <button type="button" class="btn btn-light-danger btn-sm" data-bs-toggle="modal" data-bs-target="#cloturerContratModal{{ $contrat->id }}">Clôturer</button>
                                                                </div>
                                                            @endif
                                                        @endcan
                                                        <div class="hstack gap-2">
                                                            @if ($contrat->document)
                                                                <button type="button" class="btn btn-light-info icon-btn-sm" title="Voir le document signé" onclick="ouvrirApercuDocument({{ Js::from(route('rh.contrats.document.apercu', $contrat)) }}, {{ Js::from('Contrat '.$contrat->numero) }}, true, {{ Js::from(route('rh.contrats.document.apercu', $contrat)) }})"><i class="bi bi-file-earmark-check"></i></button>
                                                            @endif
                                                            <button type="button" class="btn btn-light-secondary icon-btn-sm" title="Contrat en PDF" onclick="ouvrirApercuDocument({{ Js::from(route('rh.contrats.pdf.apercu', $contrat)) }}, {{ Js::from('Contrat '.$contrat->numero) }}, true, {{ Js::from(route('rh.contrats.pdf', $contrat)) }})"><i class="bi bi-file-earmark-pdf"></i></button>
                                                        </div>
                                                    </td>
                                                </tr>
                                            @empty
                                                <tr><td colspan="8" class="text-center text-muted py-5">Aucun contrat enregistré pour cet employé.</td></tr>
                                            @endforelse
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="tab-pane fade" id="affectations-pane">
                        <div class="card">
                            <div class="card-header d-flex justify-content-between align-items-center">
                                <h6 class="card-action-title mb-0">Historique des transferts</h6>
                                @can('rh.gerer')
                                    <a href="{{ route('rh.affectations.index') }}" class="btn btn-light-primary btn-sm"><i class="bi bi-geo-alt me-1"></i>Affecter à un site</a>
                                @endcan
                            </div>
                            <div class="card-body p-0">
                                <div class="table-box table-responsive">
                                    <table class="table text-nowrap align-middle mb-0">
                                        <thead><tr><th>Date</th><th>Ancien département</th><th>Nouveau département</th><th>Anciens sites</th><th>Nouveaux sites</th><th>Motif</th></tr></thead>
                                        <tbody>
                                            @forelse ($employe->affectations as $affectation)
                                                <tr>
                                                    <td>{{ $affectation->date_affectation->format('d/m/Y') }}</td>
                                                    <td>{{ $affectation->ancienDepartement->nom ?? '—' }}</td>
                                                    <td class="fw-medium">{{ $affectation->nouveauDepartement->nom ?? '—' }}</td>
                                                    <td class="text-muted fs-12">{{ $affectation->anciens_sites ?: '—' }}</td>
                                                    <td class="fw-medium">{{ $affectation->nouveaux_sites ?: '—' }}</td>
                                                    <td class="text-muted fs-12">{{ $affectation->motif ?: '—' }}</td>
                                                </tr>
                                            @empty
                                                <tr><td colspan="6" class="text-center text-muted py-5">Aucun transfert enregistré — l'employé n'a jamais changé de département ou de site.</td></tr>
                                            @endforelse
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="tab-pane fade" id="documents-pane">
                        @can('rh.donnees-sensibles')
                            <div class="card">
                                <div class="card-header d-flex justify-content-between align-items-center">
                                    <h6 class="card-action-title mb-0">Documents <span class="badge bg-secondary-subtle text-secondary ms-1">{{ $employe->documents->count() }}</span></h6>
                                    @can('rh.gerer')
                                        <button type="button" class="btn btn-light-primary btn-sm" data-bs-toggle="modal" data-bs-target="#ajouterDocumentModal">
                                            <i class="bi bi-upload me-1"></i>Ajouter un document
                                        </button>
                                    @endcan
                                </div>
                                <div class="card-body p-0">
                                    <div class="table-box table-responsive">
                                        <table class="table text-nowrap align-middle mb-0">
                                            <thead><tr><th>Type</th><th>Fichier</th><th>Taille</th><th>Ajouté par</th><th>Date</th><th>Actions</th></tr></thead>
                                            <tbody>
                                                @forelse ($employe->documents as $document)
                                                    <tr>
                                                        <td><span class="badge bg-secondary-subtle text-secondary">{{ \App\Http\Controllers\Rh\EmployeDocumentController::TYPES[$document->type_document] ?? $document->type_document }}</span></td>
                                                        <td><i class="bi bi-file-earmark me-1 text-muted"></i>{{ $document->nom_fichier }}</td>
                                                        <td>{{ $document->tailleLisible() }}</td>
                                                        <td>{{ $document->ajoutePar->name ?? '—' }}</td>
                                                        <td>{{ $document->created_at->format('d/m/Y') }}</td>
                                                        <td>
                                                            <div class="hstack gap-2">
                                                                <button type="button" class="btn btn-light-success icon-btn-sm" title="Voir" onclick="ouvrirApercuDocument({{ Js::from(route('rh.employes.documents.apercu', $document)) }}, {{ Js::from($document->nom_fichier) }}, {{ Js::from($document->estPrevisualisable()) }}, {{ Js::from(route('rh.employes.documents.apercu', $document)) }})"><i class="bi bi-eye"></i></button>
                                                                @can('rh.gerer')
                                                                    <form action="{{ route('rh.employes.documents.destroy', $document) }}" method="POST" onsubmit="return confirm('Supprimer ce document ?');">
                                                                        @csrf
                                                                        @method('DELETE')
                                                                        <button type="submit" class="btn btn-light-danger icon-btn-sm"><i class="ri-delete-bin-line"></i></button>
                                                                    </form>
                                                                @endcan
                                                            </div>
                                                        </td>
                                                    </tr>
                                                @empty
                                                    <tr><td colspan="6" class="text-center text-muted py-5">Aucun document enregistré pour cet employé.</td></tr>
                                                @endforelse
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        @else
                            <div class="card">
                                <div class="card-body text-center py-8">
                                    <i class="bi bi-shield-lock text-muted" style="font-size: 2.5rem;"></i>
                                    <p class="text-muted mt-3 mb-0">Les documents personnels sont des données sensibles réservées à un accès restreint.</p>
                                </div>
                            </div>
                        @endcan
                    </div>
                </div>
            </div>
        </div>

    </div>

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

    </main>
@endsection

@section('js')
    <script type="module" src="{{ asset('assets/js/app.js') }}"></script>
    <script>
        function ouvrirApercuDocument(urlApercu, titre, previsualisable, urlTelecharger) {
            document.getElementById('apercuDocumentTitre').textContent = titre;
            document.getElementById('apercuDocumentTelecharger').href = urlTelecharger;

            const iframe = document.getElementById('apercuDocumentFrame');
            const indisponible = document.getElementById('apercuDocumentIndisponible');
            if (previsualisable) {
                iframe.src = urlApercu;
                iframe.classList.remove('d-none');
                indisponible.classList.add('d-none');
            } else {
                iframe.src = '';
                iframe.classList.add('d-none');
                indisponible.classList.remove('d-none');
            }

            bootstrap.Modal.getOrCreateInstance(document.getElementById('apercuDocumentModal')).show();
        }
    </script>
@endsection
