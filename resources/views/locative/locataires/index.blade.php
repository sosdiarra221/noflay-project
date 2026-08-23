@extends('partials.layouts.master-locative')

@section('title', 'Locataires | Locative')
@section('title-sub', 'Locative')
@section('pagetitle', 'Locataires')

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
                <div class="accordion accordion-icon accordion-primary accordion-border-box" id="filtres_locataires">
                    <div class="accordion-item">
                        <h2 class="accordion-header">
                            <button class="accordion-button" type="button" data-bs-toggle="collapse"
                                data-bs-target="#filtres_locataires_body" aria-expanded="true">
                                <i class="bi bi-funnel-fill me-2"></i> Filtres
                            </button>
                        </h2>
                        <div id="filtres_locataires_body" class="accordion-collapse collapse show" data-bs-parent="#filtres_locataires">
                            <div class="accordion-body py-5">
                                <form method="GET" class="row g-4">
                                    <div class="col-md-4">
                                        <input type="text" class="form-control" name="recherche" value="{{ request('recherche') }}" placeholder="Nom, téléphone, email...">
                                    </div>
                                    <div class="col-md-4">
                                        <select class="form-select" name="type_locataire">
                                            <option value="">Tous types</option>
                                            <option value="particulier" @selected(request('type_locataire') === 'particulier')>Particulier</option>
                                            <option value="entreprise" @selected(request('type_locataire') === 'entreprise')>Entreprise</option>
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
                                        <a href="{{ route('locative.locataires.index') }}" class="btn btn-light-danger">Réinitialiser</a>
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
                        <h6 class="mb-0">Locataires <span class="badge bg-secondary-subtle text-secondary ms-1">{{ $locataires->count() }}</span></h6>
                        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createLocataireModal">
                            <i class="bi bi-plus-lg me-1"></i>Nouveau locataire
                        </button>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-box table-responsive">
                            <table class="table text-nowrap align-middle mb-0">
                                <thead>
                                    <tr>
                                        <th scope="col">Locataire</th>
                                        <th scope="col">Contact</th>
                                        <th scope="col">Locations</th>
                                        <th scope="col">Statut</th>
                                        <th scope="col">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($locataires as $locataire)
                                        <tr>
                                            <td>
                                                <div class="d-flex align-items-center gap-3">
                                                    <div class="flex-shrink-0 h-32px w-32px bg-primary-subtle d-flex justify-content-center align-items-center rounded-pill text-primary fs-6">
                                                        <i class="bi bi-person"></i>
                                                    </div>
                                                    <div>
                                                        <h6 class="mb-0 fw-medium fs-13">{{ $locataire->nom_complet }}</h6>
                                                        <span class="text-muted fs-12">{{ $locataire->numero }}</span>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                @if ($locataire->telephone)<i class="bi bi-telephone me-1 text-muted"></i>{{ $locataire->telephone }}<br>@endif
                                                @if ($locataire->email)<i class="bi bi-envelope me-1 text-muted"></i>{{ $locataire->email }}@endif
                                            </td>
                                            <td>{{ $locataire->locations_count }}</td>
                                            <td>
                                                @if ($locataire->statut === 'actif')
                                                    <span class="badge bg-success-subtle text-success">Actif</span>
                                                @else
                                                    <span class="badge bg-light-subtle text-body">Inactif</span>
                                                @endif
                                            </td>
                                            <td>
                                                <div class="hstack gap-2">
                                                    <button type="button" class="btn btn-light-info icon-btn-sm" data-bs-toggle="modal" data-bs-target="#apercuLocataireModal{{ $locataire->id }}"><i class="bi bi-eye"></i></button>
                                                    <a href="{{ route('locative.locataires.show', $locataire) }}" class="btn btn-light-success icon-btn-sm"><i class="bi bi-arrow-up-right-circle"></i></a>
                                                    <button type="button" class="btn btn-light-primary icon-btn-sm" data-bs-toggle="modal" data-bs-target="#editLocataireModal{{ $locataire->id }}"><i class="bi bi-pencil-square"></i></button>
                                                    <button type="button" class="btn btn-light-danger icon-btn-sm" data-bs-toggle="modal" data-bs-target="#deleteLocataireModal{{ $locataire->id }}"><i class="ri-delete-bin-line"></i></button>
                                                </div>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr><td colspan="5" class="text-center text-muted py-5">Aucun locataire ne correspond à ces filtres.</td></tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        @foreach ($locataires as $locataire)
            {{-- Aperçu rapide --}}
            <div class="modal fade" id="apercuLocataireModal{{ $locataire->id }}" data-bs-keyboard="false" tabindex="-1" aria-hidden="true">
                <div class="modal-dialog modal-lg" role="document">
                    <div class="modal-content rounded-4 shadow">
                        <div class="modal-header bg-gradient text-white bg-primary">
                            <h5 class="modal-title">{{ $locataire->nom_complet }}</h5>
                            <button type="button" class="btn-close icon-btn-sm btn-close-white" data-bs-dismiss="modal" aria-label="Close">
                                <i class="ri-close-large-line fw-semibold"></i>
                            </button>
                        </div>
                        <div class="modal-body">
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <div class="d-flex gap-2"><i class="bi bi-tag fs-16"></i><p class="text-muted mb-2">Type</p></div>
                                    <h6 class="mb-0 text-capitalize">{{ $locataire->type_locataire }}</h6>
                                </div>
                                <div class="col-md-6">
                                    <div class="d-flex gap-2"><i class="bi bi-toggle-on fs-16"></i><p class="text-muted mb-2">Statut</p></div>
                                    <h6 class="mb-0 text-capitalize">{{ $locataire->statut }}</h6>
                                </div>
                                <div class="col-md-6">
                                    <div class="d-flex gap-2"><i class="bi bi-telephone fs-16"></i><p class="text-muted mb-2">Téléphone</p></div>
                                    <h6 class="mb-0">{{ $locataire->telephone ?: '—' }}</h6>
                                </div>
                                <div class="col-md-6">
                                    <div class="d-flex gap-2"><i class="bi bi-whatsapp fs-16"></i><p class="text-muted mb-2">WhatsApp</p></div>
                                    <h6 class="mb-0">{{ $locataire->whatsapp ?: '—' }}</h6>
                                </div>
                                <div class="col-md-6">
                                    <div class="d-flex gap-2"><i class="bi bi-envelope fs-16"></i><p class="text-muted mb-2">Email</p></div>
                                    <h6 class="mb-0">{{ $locataire->email ?: '—' }}</h6>
                                </div>
                                <div class="col-md-6">
                                    <div class="d-flex gap-2"><i class="bi bi-geo-alt fs-16"></i><p class="text-muted mb-2">Adresse</p></div>
                                    <h6 class="mb-0">{{ $locataire->adresse ?: '—' }}</h6>
                                </div>
                                <div class="col-md-6">
                                    <div class="d-flex gap-2"><i class="bi bi-card-text fs-16"></i><p class="text-muted mb-2">Pièce d'identité</p></div>
                                    <h6 class="mb-0">{{ $locataire->piece_identite_type ? $locataire->piece_identite_type.' — '.$locataire->piece_identite_numero : '—' }}</h6>
                                </div>
                                <div class="col-md-6">
                                    <div class="d-flex gap-2"><i class="bi bi-house-door fs-16"></i><p class="text-muted mb-2">Locations</p></div>
                                    <h6 class="mb-0">{{ $locataire->locations_count }}</h6>
                                </div>
                                @if ($locataire->notes)
                                    <div class="col-12">
                                        <hr>
                                        <p class="text-muted mb-2">Notes</p>
                                        <p class="mb-0">{{ $locataire->notes }}</p>
                                    </div>
                                @endif
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-light" data-bs-dismiss="modal">Fermer</button>
                            <a href="{{ route('locative.locataires.show', $locataire) }}" class="btn btn-primary">Ouvrir le dossier</a>
                        </div>
                    </div>
                </div>
            </div>

            <div class="modal fade" id="editLocataireModal{{ $locataire->id }}" tabindex="-1" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
                    <div class="modal-content">
                        <form action="{{ route('locative.locataires.update', $locataire) }}" method="POST">
                            @csrf
                            @method('PUT')
                            @include('locative.locataires._formulaire', ['locataire' => $locataire])
                            <div class="modal-footer">
                                <button type="button" class="btn btn-light" data-bs-dismiss="modal">Fermer</button>
                                <button type="submit" class="btn btn-primary">Enregistrer</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <div class="modal fade" id="deleteLocataireModal{{ $locataire->id }}" tabindex="-1" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered" role="document">
                    <div class="modal-content">
                        <form action="{{ route('locative.locataires.destroy', $locataire) }}" method="POST">
                            @csrf
                            @method('DELETE')
                            <div class="modal-header">
                                <h5 class="modal-title">Supprimer le locataire</h5>
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

    </div>
    </main>
@endsection

@section('js')
    <script type="module" src="{{ asset('assets/js/app.js') }}"></script>
    @include('locative._form-type-toggle-script')
@endsection
