@extends('partials.layouts.master-locative')

@section('title', 'Bailleurs | Locative')
@section('title-sub', 'Locative')
@section('pagetitle', 'Bailleurs')

@section('content')
    <div id="layout-wrapper">

        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <div class="row">
            <div class="col-12 mb-4">
                <div class="accordion accordion-icon accordion-primary accordion-border-box" id="filtres_bailleurs">
                    <div class="accordion-item">
                        <h2 class="accordion-header">
                            <button class="accordion-button" type="button" data-bs-toggle="collapse"
                                data-bs-target="#filtres_bailleurs_body" aria-expanded="true">
                                <i class="bi bi-funnel-fill me-2"></i> Filtres
                            </button>
                        </h2>
                        <div id="filtres_bailleurs_body" class="accordion-collapse collapse show" data-bs-parent="#filtres_bailleurs">
                            <div class="accordion-body py-5">
                                <form method="GET" class="row g-4">
                                    <div class="col-md-4">
                                        <input type="text" class="form-control" name="recherche" value="{{ request('recherche') }}" placeholder="Nom, téléphone, email...">
                                    </div>
                                    <div class="col-md-4">
                                        <select class="form-select" name="type">
                                            <option value="">Tous types</option>
                                            <option value="particulier" @selected(request('type') === 'particulier')>Particulier</option>
                                            <option value="entreprise" @selected(request('type') === 'entreprise')>Entreprise</option>
                                        </select>
                                    </div>
                                    <div class="col-md-4">
                                        <select class="form-select" name="statut">
                                            <option value="">Tous statuts</option>
                                            <option value="actif" @selected(request('statut') === 'actif')>Actif</option>
                                            <option value="inactif" @selected(request('statut') === 'inactif')>Inactif</option>
                                        </select>
                                    </div>
                                    <div class="col-12 d-flex justify-content-end gap-2">
                                        <button class="btn btn-light-primary" type="submit"><i class="ri-equalizer-line me-2"></i>Filtrer</button>
                                        <a href="{{ route('locative.bailleurs.index') }}" class="btn btn-light-danger">Réinitialiser</a>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-12">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h6 class="mb-0">Bailleurs <span class="badge bg-secondary-subtle text-secondary ms-1">{{ $bailleurs->count() }}</span></h6>
                        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createBailleurModal">
                            <i class="bi bi-plus-lg me-1"></i>Nouveau bailleur
                        </button>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-box table-responsive">
                            <table class="table text-nowrap align-middle mb-0">
                                <thead>
                                    <tr>
                                        <th scope="col">Bailleur</th>
                                        <th scope="col">Type</th>
                                        <th scope="col">Contact</th>
                                        <th scope="col">Biens</th>
                                        <th scope="col">Gérances</th>
                                        <th scope="col">Statut</th>
                                        <th scope="col">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($bailleurs as $bailleur)
                                        <tr>
                                            <td>
                                                <div class="d-flex align-items-center gap-3">
                                                    <div class="flex-shrink-0 h-32px w-32px bg-primary-subtle d-flex justify-content-center align-items-center rounded-pill text-primary fs-6">
                                                        <i class="bi bi-person-badge"></i>
                                                    </div>
                                                    <div>
                                                        <h6 class="mb-0 fw-medium fs-13">{{ $bailleur->nom_complet }}</h6>
                                                        <span class="text-muted fs-12">{{ $bailleur->numero }}</span>
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="text-capitalize">{{ $bailleur->type }}</td>
                                            <td>
                                                @if ($bailleur->telephone)
                                                    <i class="bi bi-telephone me-1 text-muted"></i>{{ $bailleur->telephone }}<br>
                                                @endif
                                                @if ($bailleur->email)
                                                    <i class="bi bi-envelope me-1 text-muted"></i>{{ $bailleur->email }}
                                                @endif
                                            </td>
                                            <td>{{ $bailleur->biens_count }}</td>
                                            <td>{{ $bailleur->gerances_count }}</td>
                                            <td>
                                                @if ($bailleur->statut === 'actif')
                                                    <span class="badge bg-success-subtle text-success">Actif</span>
                                                @else
                                                    <span class="badge bg-light-subtle text-body">Inactif</span>
                                                @endif
                                            </td>
                                            <td>
                                                <div class="hstack gap-2">
                                                    <button type="button" class="btn btn-light-info icon-btn-sm" data-bs-toggle="modal" data-bs-target="#apercuBailleurModal{{ $bailleur->id }}"><i class="bi bi-eye"></i></button>
                                                    <a href="{{ route('locative.bailleurs.show', $bailleur) }}" class="btn btn-light-success icon-btn-sm"><i class="bi bi-arrow-up-right-circle"></i></a>
                                                    <button type="button" class="btn btn-light-primary icon-btn-sm" data-bs-toggle="modal" data-bs-target="#editBailleurModal{{ $bailleur->id }}"><i class="bi bi-pencil-square"></i></button>
                                                    <button type="button" class="btn btn-light-danger icon-btn-sm" data-bs-toggle="modal" data-bs-target="#deleteBailleurModal{{ $bailleur->id }}"><i class="ri-delete-bin-line"></i></button>
                                                </div>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="7" class="text-center text-muted py-5">Aucun bailleur ne correspond à ces filtres.</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        @foreach ($bailleurs as $bailleur)
            {{-- Aperçu rapide --}}
            <div class="modal fade" id="apercuBailleurModal{{ $bailleur->id }}" data-bs-keyboard="false" tabindex="-1" aria-hidden="true">
                <div class="modal-dialog modal-lg" role="document">
                    <div class="modal-content rounded-4 shadow">
                        <div class="modal-header bg-gradient text-white bg-primary">
                            <h5 class="modal-title">{{ $bailleur->nom_complet }}</h5>
                            <button type="button" class="btn-close icon-btn-sm btn-close-white" data-bs-dismiss="modal" aria-label="Close">
                                <i class="ri-close-large-line fw-semibold"></i>
                            </button>
                        </div>
                        <div class="modal-body">
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <div class="d-flex gap-2"><i class="bi bi-tag fs-16"></i><p class="text-muted mb-2">Type</p></div>
                                    <h6 class="mb-0 text-capitalize">{{ $bailleur->type }}</h6>
                                </div>
                                <div class="col-md-6">
                                    <div class="d-flex gap-2"><i class="bi bi-toggle-on fs-16"></i><p class="text-muted mb-2">Statut</p></div>
                                    <h6 class="mb-0 text-capitalize">{{ $bailleur->statut }}</h6>
                                </div>
                                <div class="col-md-6">
                                    <div class="d-flex gap-2"><i class="bi bi-telephone fs-16"></i><p class="text-muted mb-2">Téléphone</p></div>
                                    <h6 class="mb-0">{{ $bailleur->telephone ?: '—' }}</h6>
                                </div>
                                <div class="col-md-6">
                                    <div class="d-flex gap-2"><i class="bi bi-envelope fs-16"></i><p class="text-muted mb-2">Email</p></div>
                                    <h6 class="mb-0">{{ $bailleur->email ?: '—' }}</h6>
                                </div>
                                <div class="col-md-6">
                                    <div class="d-flex gap-2"><i class="bi bi-geo-alt fs-16"></i><p class="text-muted mb-2">Adresse</p></div>
                                    <h6 class="mb-0">{{ $bailleur->adresse ?: '—' }}</h6>
                                </div>
                                <div class="col-md-6">
                                    <div class="d-flex gap-2"><i class="bi bi-card-text fs-16"></i><p class="text-muted mb-2">Pièce d'identité</p></div>
                                    <h6 class="mb-0">{{ $bailleur->piece_identite_type ? $bailleur->piece_identite_type.' — '.$bailleur->piece_identite_numero : '—' }}</h6>
                                </div>
                                <div class="col-md-6">
                                    <div class="d-flex gap-2"><i class="bi bi-file-earmark-text fs-16"></i><p class="text-muted mb-2">Gérances</p></div>
                                    <h6 class="mb-0">{{ $bailleur->gerances_count }}</h6>
                                </div>
                                <div class="col-md-6">
                                    <div class="d-flex gap-2"><i class="bi bi-building fs-16"></i><p class="text-muted mb-2">Biens</p></div>
                                    <h6 class="mb-0">{{ $bailleur->biens_count }}</h6>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-light" data-bs-dismiss="modal">Fermer</button>
                            <a href="{{ route('locative.bailleurs.show', $bailleur) }}" class="btn btn-primary">Ouvrir la fiche</a>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Modal édition --}}
            <div class="modal fade" id="editBailleurModal{{ $bailleur->id }}" tabindex="-1" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
                    <div class="modal-content">
                        <form action="{{ route('locative.bailleurs.update', $bailleur) }}" method="POST">
                            @csrf
                            @method('PUT')
                            @include('locative.bailleurs._formulaire', ['bailleur' => $bailleur])
                            <div class="modal-footer">
                                <button type="button" class="btn btn-light" data-bs-dismiss="modal">Fermer</button>
                                <button type="submit" class="btn btn-primary">Enregistrer</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            {{-- Modal suppression --}}
            <div class="modal fade" id="deleteBailleurModal{{ $bailleur->id }}" tabindex="-1" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered" role="document">
                    <div class="modal-content">
                        <form action="{{ route('locative.bailleurs.destroy', $bailleur) }}" method="POST">
                            @csrf
                            @method('DELETE')
                            <div class="modal-header">
                                <h5 class="modal-title">Supprimer le bailleur</h5>
                                <button type="button" class="btn-close icon-btn-sm" data-bs-dismiss="modal" aria-label="Close">
                                    <i class="ri-close-large-line fw-semibold"></i>
                                </button>
                            </div>
                            <div class="modal-body">
                                <div class="alert alert-warning">
                                    <i class="bi bi-exclamation-triangle me-1"></i>
                                    Cette opération archive le bailleur <strong>{{ $bailleur->nom_complet }}</strong>. Un motif est obligatoire.
                                </div>
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
        @endforeach

        {{-- Modal création --}}
        <div class="modal fade" id="createBailleurModal" data-bs-keyboard="false" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
                <div class="modal-content">
                    <form action="{{ route('locative.bailleurs.store') }}" method="POST">
                        @csrf
                        @include('locative.bailleurs._formulaire', ['bailleur' => null])
                        <div class="modal-footer">
                            <button type="button" class="btn btn-light" data-bs-dismiss="modal">Fermer</button>
                            <button type="submit" class="btn btn-primary">Créer le bailleur</button>
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
