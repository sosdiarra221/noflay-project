@extends('partials.layouts.master-commercial')

@section('title', $prospect->nom_complet.' | Commercial')
@section('title-sub', 'Prospection')
@section('pagetitle', $prospect->nom_complet)

@section('content')

    <div id="layout-wrapper">

        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        @php $classesS = ['non_traite' => 'secondary', 'en_cours' => 'warning', 'gagne' => 'success', 'perdu' => 'danger', 'annule' => 'dark']; @endphp

        <div class="row">
            <div class="col-md-6 col-xl-3">
                <div class="card card-h-100">
                    <div class="card-header d-flex align-items-center gap-3">
                        <h5 class="card-title mb-0 flex-grow-1">{{ $prospect->nom_complet }}</h5>
                        <span class="badge bg-{{ $classesS[$prospect->statut] }}-subtle text-{{ $classesS[$prospect->statut] }} text-capitalize">{{ str_replace('_', ' ', $prospect->statut) }}</span>
                    </div>
                    <div class="card-body">
                        <div class="d-flex justify-content-between gap-2">
                            <h6><i class="bi bi-hash me-2"></i>Numéro</h6>
                            <div class="text-end"><p>{{ $prospect->numero }}</p></div>
                        </div>
                        <div class="d-flex justify-content-between gap-2 mt-3">
                            <h6><i class="bi bi-telephone me-2"></i>Téléphone</h6>
                            <div class="text-end"><p>{{ $prospect->telephone }}</p></div>
                        </div>
                        <div class="d-flex justify-content-between gap-2 mt-3">
                            <h6 class="mb-0"><i class="bi bi-envelope me-2"></i>Email</h6>
                            <div class="text-end"><p class="mb-0">{{ $prospect->email ?: '—' }}</p></div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-6 col-xl-3">
                <div class="card card-h-100">
                    <div class="card-header d-flex align-items-center gap-3">
                        <h5 class="card-title mb-0 flex-grow-1">Demande</h5>
                        <div class="avatar-sm bg-primary-subtle text-primary d-flex justify-content-center align-items-center rounded-2"><i class="bi bi-tag"></i></div>
                    </div>
                    <div class="card-body">
                        <div class="d-flex justify-content-between gap-2">
                            <h6><i class="bi bi-bookmark me-2"></i>Type</h6>
                            <div class="text-end"><p>{{ $prospect->typeDemande->nom ?? '—' }}</p></div>
                        </div>
                        <div class="d-flex justify-content-between gap-2 mt-3">
                            <h6><i class="bi bi-signpost me-2"></i>Source</h6>
                            <div class="text-end"><p>{{ $prospect->source->nom ?? '—' }}</p></div>
                        </div>
                        <div class="d-flex justify-content-between gap-2 mt-3">
                            <h6 class="mb-0"><i class="bi bi-person-badge me-2"></i>Responsable</h6>
                            <div class="text-end"><p class="mb-0">{{ $prospect->commercial->name ?? '—' }}</p></div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-6 col-xl-3">
                <div class="card card-h-100">
                    <div class="card-header d-flex align-items-center gap-3">
                        <h5 class="card-title mb-0 flex-grow-1">Budget</h5>
                        <div class="avatar-sm bg-primary-subtle text-primary d-flex justify-content-center align-items-center rounded-2"><i class="bi bi-cash-coin"></i></div>
                    </div>
                    <div class="card-body">
                        <div class="d-flex justify-content-between gap-2">
                            <h6><i class="bi bi-arrow-down me-2"></i>Minimum</h6>
                            <div class="text-end"><p>{{ number_format($prospect->budget_min ?? 0, 0, ',', ' ') }} {{ $prospect->devise }}</p></div>
                        </div>
                        <div class="d-flex justify-content-between gap-2 mt-3">
                            <h6 class="mb-0"><i class="bi bi-arrow-up me-2"></i>Maximum</h6>
                            <div class="text-end"><p class="mb-0">{{ number_format($prospect->budget_max ?? 0, 0, ',', ' ') }} {{ $prospect->devise }}</p></div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-6 col-xl-3">
                <div class="card">
                    <div class="card-header d-flex align-items-center gap-3">
                        <h5 class="card-title mb-0 flex-grow-1">Actions rapides</h5>
                    </div>
                    <div class="card-body d-flex flex-column gap-2">
                        <button type="button" class="btn btn-light-primary btn-sm" data-bs-toggle="modal" data-bs-target="#statutModal"><i class="bi bi-arrow-repeat me-1"></i>Changer statut</button>
                        <button type="button" class="btn btn-light-success btn-sm" data-bs-toggle="modal" data-bs-target="#activiteModal"><i class="bi bi-journal-plus me-1"></i>Ajouter une activité</button>
                        @if ($prospect->statut === 'gagne' && ! $prospect->converti_en)
                            <form action="{{ route('commercial.prospects.convertir-location', $prospect) }}" method="POST">
                                @csrf
                                <button type="submit" class="btn btn-success btn-sm w-100"><i class="bi bi-arrow-right-circle me-1"></i>Convertir en location</button>
                            </form>
                        @elseif ($prospect->converti_en)
                            <span class="badge bg-success-subtle text-success">Converti en {{ $prospect->converti_en }} le {{ $prospect->converti_le->format('d/m/Y') }}</span>
                        @endif
                        @can('commercial.operations-sensibles')
                            <button type="button" class="btn btn-light-danger btn-sm" data-bs-toggle="modal" data-bs-target="#supprimerModal"><i class="bi bi-trash3 me-1"></i>Supprimer</button>
                        @endcan
                    </div>
                </div>
            </div>

            <div class="col-xl-9">
                @if ($prospect->besoin)
                    <div class="card">
                        <div class="card-header"><h5 class="card-title mb-0">Besoin exprimé</h5></div>
                        <div class="card-body"><p class="mb-0">{{ $prospect->besoin }}</p></div>
                    </div>
                @endif

                <div class="card">
                    <div class="card-header">
                        <div class="d-flex align-items-center">
                            <h5 class="card-title flex-grow-1 mb-0">Activités ({{ $prospect->activites->count() }})</h5>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="table-box">
                            <table class="table table-borderless">
                                <thead class="border-bottom">
                                    <tr><th>Type</th><th>Objet</th><th>Description</th><th>Date</th><th>Par</th></tr>
                                </thead>
                                <tbody>
                                    @forelse ($prospect->activites as $activite)
                                        <tr>
                                            <td class="text-capitalize">{{ str_replace('_', ' ', $activite->type) }}</td>
                                            <td>{{ $activite->objet }}</td>
                                            <td class="text-muted">{{ $activite->description ?: '—' }}</td>
                                            <td>{{ $activite->date_activite->format('d/m/Y H:i') }}</td>
                                            <td>{{ $activite->utilisateur->name ?? '—' }}</td>
                                        </tr>
                                    @empty
                                        <tr><td colspan="5" class="text-center text-muted py-5">Aucune activité enregistrée.</td></tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-3">
                <div class="card card-h-100">
                    <div class="card-header">
                        <div class="d-sm-flex align-items-center">
                            <h5 class="card-title flex-grow-1 mb-0">Historique</h5>
                            <div class="avatar-sm bg-primary-subtle text-primary d-flex justify-content-center align-items-center rounded-2"><i class="bi bi-clock-history"></i></div>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="timeline2 profile-timeline">
                            <ul>
                                @forelse ($prospect->historiqueStatuts as $historique)
                                    <li class="card border-0 box">
                                        <span class="h-28px w-28px d-flex justify-content-center align-items-center text-white"><i class="bi bi-arrow-repeat"></i></span>
                                        <div class="title">{{ $historique->created_at->versionLongue() }} à {{ $historique->created_at->format('H:i') }}
                                            <span class="badge bg-{{ $classesS[$historique->nouveau_statut] ?? 'secondary' }} float-end text-capitalize">{{ str_replace('_', ' ', $historique->nouveau_statut) }}</span>
                                        </div>
                                        <div class="sub-title">
                                            @if ($historique->ancien_statut)
                                                {{ str_replace('_', ' ', ucfirst($historique->ancien_statut)) }} → {{ str_replace('_', ' ', ucfirst($historique->nouveau_statut)) }}
                                            @else
                                                Prospect créé
                                            @endif
                                        </div>
                                        <div class="info text-muted">{{ $historique->commentaire }} — {{ $historique->utilisateur->name ?? 'Système' }}</div>
                                    </li>
                                @empty
                                    <li class="text-muted text-center py-3">Aucun historique.</li>
                                @endforelse
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div><!--End container-fluid-->
    </main><!--End app-wrapper-->

    <!-- Changer statut modal -->
    <div class="modal fade" id="statutModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <form action="{{ route('commercial.prospects.changer-statut', $prospect) }}" method="POST">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title">Changer le statut</h5>
                        <button type="button" class="btn-close icon-btn-sm" data-bs-dismiss="modal" aria-label="Close"><i class="ri-close-large-line fw-semibold"></i></button>
                    </div>
                    <div class="modal-body">
                        <p class="text-muted">Statut actuel : <strong class="text-capitalize">{{ str_replace('_', ' ', $prospect->statut) }}</strong></p>
                        <div class="row g-3">
                            <div class="col-12">
                                <label class="form-label">Nouveau statut<span class="text-danger ms-1">*</span></label>
                                <select class="form-select" name="nouveau_statut" required>
                                    @foreach (\App\Models\Commercial\Prospect::STATUTS as $statutOption)
                                        <option value="{{ $statutOption }}" @selected($prospect->statut === $statutOption)>{{ str_replace('_', ' ', ucfirst($statutOption)) }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-12">
                                <label class="form-label">Pourquoi ce changement ?</label>
                                <textarea class="form-control" name="commentaire" rows="3" placeholder="Aucune information renseignée si laissé vide"></textarea>
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

    <!-- Ajouter activité modal -->
    <div class="modal fade" id="activiteModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <form action="{{ route('commercial.activites.store', $prospect) }}" method="POST">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title">Ajouter une activité</h5>
                        <button type="button" class="btn-close icon-btn-sm" data-bs-dismiss="modal" aria-label="Close"><i class="ri-close-large-line fw-semibold"></i></button>
                    </div>
                    <div class="modal-body">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label">Type<span class="text-danger ms-1">*</span></label>
                                <select class="form-select" name="type" required>
                                    @foreach (['appel' => 'Appel', 'email' => 'Email', 'whatsapp' => 'WhatsApp', 'sms' => 'SMS', 'visite' => 'Visite', 'rendez_vous' => 'Rendez-vous', 'note' => 'Note', 'relance' => 'Relance', 'document' => 'Envoi de document', 'autre' => 'Autre'] as $valeur => $libelle)
                                        <option value="{{ $valeur }}">{{ $libelle }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Date</label>
                                <input type="datetime-local" class="form-control" name="date_activite" value="{{ now()->format('Y-m-d\TH:i') }}">
                            </div>
                            <div class="col-12">
                                <label class="form-label">Objet<span class="text-danger ms-1">*</span></label>
                                <input type="text" class="form-control" name="objet" placeholder="Ex: Appel effectué" required>
                            </div>
                            <div class="col-12">
                                <label class="form-label">Description</label>
                                <textarea class="form-control" name="description" rows="3"></textarea>
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
    <!-- Supprimer prospect modal -->
    <div class="modal fade" id="supprimerModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <form action="{{ route('commercial.prospects.destroy', $prospect) }}" method="POST">
                    @csrf
                    @method('DELETE')
                    <div class="modal-header">
                        <h5 class="modal-title">Supprimer le prospect</h5>
                        <button type="button" class="btn-close icon-btn-sm" data-bs-dismiss="modal" aria-label="Close"><i class="ri-close-large-line fw-semibold"></i></button>
                    </div>
                    <div class="modal-body">
                        <div class="alert alert-warning"><i class="bi bi-exclamation-triangle me-1"></i>Cette opération archive le prospect. Un motif est obligatoire.</div>
                        <label class="form-label">Motif de suppression<span class="text-danger ms-1">*</span></label>
                        <textarea class="form-control" name="motif_suppression" rows="2" required></textarea>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Annuler</button>
                        <button type="submit" class="btn btn-danger">Confirmer la suppression</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@section('js')
    <script type="module" src="{{ asset('assets/js/app.js') }}"></script>
@endsection
