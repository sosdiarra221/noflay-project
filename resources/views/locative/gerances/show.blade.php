@extends('partials.layouts.master-locative')

@section('title', $gerance->numero.' | Locative')
@section('title-sub', 'Gérances')
@section('pagetitle', $gerance->numero)

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
                <div class="d-flex flex-wrap justify-content-between align-items-start gap-3">
                    <div>
                        <h5 class="mb-1">{{ $gerance->numero }}
                            @php
                                $classes = ['actif' => 'success', 'brouillon' => 'secondary', 'en_attente_signature' => 'warning', 'suspendu' => 'warning', 'expire' => 'dark', 'resilie' => 'danger', 'archive' => 'dark'];
                            @endphp
                            <span class="badge bg-{{ $classes[$gerance->statut] ?? 'secondary' }}-subtle text-{{ $classes[$gerance->statut] ?? 'secondary' }} text-capitalize ms-1">{{ str_replace('_', ' ', $gerance->statut) }}</span>
                        </h5>
                        <p class="text-muted mb-0">
                            <a href="{{ route('locative.bailleurs.show', $gerance->bailleur) }}">{{ $gerance->bailleur->nom_complet }}</a>
                            — {{ $gerance->date_debut->format('d/m/Y') }}
                            @if ($gerance->date_fin) → {{ $gerance->date_fin->format('d/m/Y') }} @endif
                        </p>
                    </div>
                    <div class="d-flex gap-2">
                        <a href="{{ route('locative.gerances.pdf', $gerance) }}" class="btn btn-light-info">
                            <i class="bi bi-file-earmark-pdf me-1"></i>PDF
                        </a>
                        <button type="button" class="btn btn-light-primary" data-bs-toggle="modal" data-bs-target="#editGeranceModal">
                            <i class="bi bi-pencil-square me-1"></i>Modifier
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-12">
                <div class="mb-6">
                    <ul class="nav nav-pills" role="tablist">
                        <li class="nav-item"><button class="nav-link active" data-bs-toggle="tab" data-bs-target="#infos-pane" type="button">Vue générale</button></li>
                        <li class="nav-item"><button class="nav-link" data-bs-toggle="tab" data-bs-target="#biens-pane" type="button">Biens</button></li>
                    </ul>
                </div>

                <div class="tab-content">
                    <div class="tab-pane fade show active" id="infos-pane">
                        <div class="row g-4">
                            <div class="col-lg-6">
                                <div class="card">
                                    <div class="card-header"><h6 class="card-action-title mb-0">Conditions financières</h6></div>
                                    <div class="card-body">
                                        <div class="row mb-3"><div class="col-5 text-muted">Frais de gestion</div><div class="col-7 fw-medium">{{ $gerance->frais_gestion_valeur }}{{ $gerance->frais_gestion_mode === 'pourcentage' ? ' %' : ' FCFA' }}</div></div>
                                        <div class="row mb-3"><div class="col-5 text-muted">TVA à la charge</div><div class="col-7 fw-medium text-capitalize">{{ $gerance->tva_charge }}</div></div>
                                        <div class="row mb-3"><div class="col-5 text-muted">Taxe à la charge</div><div class="col-7 fw-medium text-capitalize">{{ $gerance->taxe_charge }}</div></div>
                                        <div class="row"><div class="col-5 text-muted">TOM à la charge</div><div class="col-7 fw-medium text-capitalize">{{ $gerance->tom_charge }}</div></div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-6">
                                <div class="card">
                                    <div class="card-header"><h6 class="card-action-title mb-0">Détails</h6></div>
                                    <div class="card-body">
                                        <div class="row mb-3"><div class="col-5 text-muted">Type de gérance</div><div class="col-7 fw-medium text-capitalize">{{ str_replace('_', ' ', $gerance->type_gerance) }}</div></div>
                                        <div class="row mb-3"><div class="col-5 text-muted">Nombre de biens</div><div class="col-7 fw-medium">{{ $gerance->biens->count() }}</div></div>
                                        <div class="row"><div class="col-5 text-muted">Notes</div><div class="col-7 fw-medium">{{ $gerance->notes ?: '—' }}</div></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="tab-pane fade" id="biens-pane">
                        <div class="card">
                            <div class="card-header">
                                <div class="d-flex justify-content-between align-items-center">
                                    <h6 class="card-title mb-0 fw-semibold">Biens du contrat <span class="badge bg-secondary-subtle text-secondary ms-1">{{ $gerance->biens->count() }}</span></h6>
                                    <button type="button" class="btn btn-light-primary" data-bs-toggle="modal" data-bs-target="#createBienModal">
                                        <i class="bi bi-plus-lg me-1"></i>Ajouter un bien
                                    </button>
                                </div>
                            </div>
                            <div class="card-body p-0">
                                <div class="table-box table-responsive">
                                    <table class="table text-nowrap align-middle mb-0">
                                        <thead>
                                            <tr><th>Bien</th><th>Catégorie</th><th>Type</th><th>Montant</th><th>Statut</th><th>Actions</th></tr>
                                        </thead>
                                        <tbody>
                                            @forelse ($gerance->biens as $bien)
                                                <tr>
                                                    <td>{{ $bien->titre }}</td>
                                                    <td>{{ $bien->categorie->nom ?? '—' }}</td>
                                                    <td class="text-capitalize">{{ $bien->type_exploitation }}</td>
                                                    <td>{{ number_format($bien->montantExploitation() ?? 0, 0, ',', ' ') }} FCFA</td>
                                                    <td><span class="badge bg-secondary-subtle text-secondary text-capitalize">{{ str_replace('_', ' ', $bien->statut) }}</span></td>
                                                    <td>
                                                        <div class="hstack gap-2">
                                                            <button type="button" class="btn btn-light-primary icon-btn-sm" data-bs-toggle="modal" data-bs-target="#editBienModal{{ $bien->id }}"><i class="bi bi-pencil-square"></i></button>
                                                            <button type="button" class="btn btn-light-danger icon-btn-sm" data-bs-toggle="modal" data-bs-target="#deleteBienModal{{ $bien->id }}"><i class="ri-delete-bin-line"></i></button>
                                                        </div>
                                                    </td>
                                                </tr>
                                            @empty
                                                <tr><td colspan="6" class="text-center text-muted py-5">Aucun bien rattaché à cette gérance.</td></tr>
                                            @endforelse
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        @foreach ($gerance->biens as $bien)
            <div class="modal fade" id="editBienModal{{ $bien->id }}" tabindex="-1" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
                    <div class="modal-content">
                        <form action="{{ route('locative.biens.update', $bien) }}" method="POST">
                            @csrf
                            @method('PUT')
                            @include('locative.biens._formulaire', ['bien' => $bien, 'gerance' => $gerance, 'categories' => $categories])
                            <div class="modal-footer">
                                <button type="button" class="btn btn-light" data-bs-dismiss="modal">Fermer</button>
                                <button type="submit" class="btn btn-primary">Enregistrer</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <div class="modal fade" id="deleteBienModal{{ $bien->id }}" tabindex="-1" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered" role="document">
                    <div class="modal-content">
                        <form action="{{ route('locative.biens.destroy', $bien) }}" method="POST">
                            @csrf
                            @method('DELETE')
                            <div class="modal-header">
                                <h5 class="modal-title">Supprimer le bien</h5>
                                <button type="button" class="btn-close icon-btn-sm" data-bs-dismiss="modal" aria-label="Close"><i class="ri-close-large-line fw-semibold"></i></button>
                            </div>
                            <div class="modal-body">
                                <label class="form-label">Motif de suppression<span class="text-danger ms-1">*</span></label>
                                <textarea class="form-control" name="motif_suppression" rows="2" required></textarea>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-light" data-bs-dismiss="modal">Annuler</button>
                                <button type="submit" class="btn btn-danger">Confirmer</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        @endforeach

        <!-- Create Bien Modal -->
        <div class="modal fade" id="createBienModal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
                <div class="modal-content">
                    <form action="{{ route('locative.biens.store') }}" method="POST">
                        @csrf
                        @include('locative.biens._formulaire', ['bien' => null, 'gerance' => $gerance, 'categories' => $categories])
                        <div class="modal-footer">
                            <button type="button" class="btn btn-light" data-bs-dismiss="modal">Fermer</button>
                            <button type="submit" class="btn btn-primary">Ajouter</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Edit Gerance Modal -->
        <div class="modal fade" id="editGeranceModal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
                <div class="modal-content">
                    <form action="{{ route('locative.gerances.update', $gerance) }}" method="POST">
                        @csrf
                        @method('PUT')
                        <div class="modal-header">
                            <h5 class="modal-title">Modifier le contrat de gérance</h5>
                            <button type="button" class="btn-close icon-btn-sm" data-bs-dismiss="modal" aria-label="Close"><i class="ri-close-large-line fw-semibold"></i></button>
                        </div>
                        <div class="modal-body">
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label">Date de début</label>
                                    <input type="date" class="form-control" name="date_debut" value="{{ $gerance->date_debut->format('Y-m-d') }}" required>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Date de fin</label>
                                    <input type="date" class="form-control" name="date_fin" value="{{ $gerance->date_fin?->format('Y-m-d') }}">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Type de gérance</label>
                                    <select class="form-select" name="type_gerance">
                                        <option value="gestion_locative" @selected($gerance->type_gerance === 'gestion_locative')>Gestion locative</option>
                                        <option value="gestion_vente" @selected($gerance->type_gerance === 'gestion_vente')>Gestion vente</option>
                                        <option value="gestion_locative_vente" @selected($gerance->type_gerance === 'gestion_locative_vente')>Gestion locative + vente</option>
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Statut</label>
                                    <select class="form-select" name="statut">
                                        @foreach (['brouillon', 'en_attente_signature', 'actif', 'suspendu', 'expire', 'resilie', 'archive'] as $statutOption)
                                            <option value="{{ $statutOption }}" @selected($gerance->statut === $statutOption)>{{ str_replace('_', ' ', ucfirst($statutOption)) }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">Mode de frais</label>
                                    <select class="form-select" name="frais_gestion_mode">
                                        <option value="pourcentage" @selected($gerance->frais_gestion_mode === 'pourcentage')>Pourcentage</option>
                                        <option value="montant_fixe" @selected($gerance->frais_gestion_mode === 'montant_fixe')>Montant fixe</option>
                                    </select>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">Valeur des frais</label>
                                    <input type="number" step="0.01" min="0" class="form-control" name="frais_gestion_valeur" value="{{ $gerance->frais_gestion_valeur }}">
                                </div>
                                <div class="col-md-4"></div>
                                <div class="col-md-4">
                                    <label class="form-label">TVA à la charge</label>
                                    <select class="form-select" name="tva_charge">
                                        <option value="bailleur" @selected($gerance->tva_charge === 'bailleur')>Bailleur</option>
                                        <option value="agence" @selected($gerance->tva_charge === 'agence')>Agence</option>
                                    </select>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">Taxe à la charge</label>
                                    <select class="form-select" name="taxe_charge">
                                        <option value="bailleur" @selected($gerance->taxe_charge === 'bailleur')>Bailleur</option>
                                        <option value="agence" @selected($gerance->taxe_charge === 'agence')>Agence</option>
                                    </select>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">TOM à la charge</label>
                                    <select class="form-select" name="tom_charge">
                                        <option value="bailleur" @selected($gerance->tom_charge === 'bailleur')>Bailleur</option>
                                        <option value="agence" @selected($gerance->tom_charge === 'agence')>Agence</option>
                                    </select>
                                </div>
                                <div class="col-12">
                                    <label class="form-label">Notes</label>
                                    <textarea class="form-control" name="notes" rows="2">{{ $gerance->notes }}</textarea>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-light" data-bs-dismiss="modal">Fermer</button>
                            <button type="submit" class="btn btn-primary">Enregistrer</button>
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
