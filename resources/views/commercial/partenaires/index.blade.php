@extends('partials.layouts.master-commercial')

@section('title', 'Partenaires | Commercial')
@section('title-sub', 'Commercial')
@section('pagetitle', 'Partenaires')

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
                <div class="accordion accordion-icon accordion-primary accordion-border-box" id="filtres_partenaires">
                    <div class="accordion-item">
                        <h2 class="accordion-header">
                            <button class="accordion-button" type="button" data-bs-toggle="collapse"
                                data-bs-target="#filtres_partenaires_body" aria-expanded="true">
                                <i class="bi bi-funnel-fill me-2"></i> Filtres
                            </button>
                        </h2>
                        <div id="filtres_partenaires_body" class="accordion-collapse collapse show"
                            data-bs-parent="#filtres_partenaires">
                            <div class="accordion-body py-5">
                                <form method="GET" class="row g-4">
                                    <div class="col-md-4">
                                        <input type="text" class="form-control" name="recherche" value="{{ request('recherche') }}" placeholder="Nom, téléphone, email...">
                                    </div>
                                    <div class="col-md-4">
                                        <select class="form-select" name="type">
                                            <option value="">Tous types</option>
                                            @foreach (\App\Models\Commercial\Partenaire::TYPES as $type)
                                                <option value="{{ $type }}" @selected(request('type') === $type)>{{ str_replace('_', ' ', ucfirst($type)) }}</option>
                                            @endforeach
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
                                        <a href="{{ route('commercial.partenaires') }}" class="btn btn-light-danger">Réinitialiser</a>
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
                        <h6 class="mb-0">Partenaires <span class="badge bg-secondary-subtle text-secondary ms-1">{{ $partenaires->count() }}</span></h6>
                        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#creerPartenaireModal">
                            <i class="bi bi-plus-lg me-1"></i>Nouveau partenaire
                        </button>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-box table-responsive">
                            <table class="table text-nowrap align-middle mb-0">
                                <thead>
                                    <tr>
                                        <th scope="col">Partenaire</th>
                                        <th scope="col">Type</th>
                                        <th scope="col">Contact</th>
                                        <th scope="col">Commission</th>
                                        <th scope="col">Prospects apportés</th>
                                        <th scope="col">Statut</th>
                                        <th scope="col">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($partenaires as $partenaire)
                                        <tr>
                                            <td>
                                                <h6 class="mb-1">{{ $partenaire->nom }}</h6>
                                                <p class="mb-0 fs-12 text-muted">{{ $partenaire->numero }}</p>
                                            </td>
                                            <td class="text-capitalize">{{ str_replace('_', ' ', $partenaire->type) }}</td>
                                            <td>
                                                @if ($partenaire->contact_nom)
                                                    {{ $partenaire->contact_nom }}<br>
                                                @endif
                                                @if ($partenaire->telephone)
                                                    <i class="bi bi-telephone me-1 text-muted"></i>{{ $partenaire->telephone }}
                                                @endif
                                            </td>
                                            <td>{{ $partenaire->commission_pourcentage ? $partenaire->commission_pourcentage.' %' : '—' }}</td>
                                            <td>{{ $partenaire->prospects_count }}</td>
                                            <td>
                                                @if ($partenaire->statut === 'actif')
                                                    <span class="badge bg-success-subtle text-success">Actif</span>
                                                @else
                                                    <span class="badge bg-light-subtle text-body">Inactif</span>
                                                @endif
                                            </td>
                                            <td>
                                                <div class="hstack gap-2">
                                                    <button type="button" class="btn btn-light-primary icon-btn-sm" data-bs-toggle="modal" data-bs-target="#editPartenaireModal{{ $partenaire->id }}">
                                                        <i class="bi bi-pencil-square"></i>
                                                    </button>
                                                    <button type="button" class="btn btn-light-danger icon-btn-sm" data-bs-toggle="modal" data-bs-target="#deletePartenaireModal{{ $partenaire->id }}">
                                                        <i class="ri-delete-bin-line"></i>
                                                    </button>
                                                </div>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr><td colspan="7" class="text-center text-muted py-5">Aucun partenaire ne correspond à ces filtres.</td></tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        @foreach ($partenaires as $partenaire)
            {{-- Modal édition --}}
            <div class="modal fade" id="editPartenaireModal{{ $partenaire->id }}" tabindex="-1" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
                    <div class="modal-content">
                        <form action="{{ route('commercial.partenaires.update', $partenaire) }}" method="POST">
                            @csrf
                            @method('PUT')
                            @include('commercial.partenaires._formulaire', ['partenaire' => $partenaire])
                            <div class="modal-footer">
                                <button type="button" class="btn btn-light" data-bs-dismiss="modal">Fermer</button>
                                <button type="submit" class="btn btn-primary">Enregistrer</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            {{-- Modal suppression --}}
            <div class="modal fade" id="deletePartenaireModal{{ $partenaire->id }}" tabindex="-1" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered" role="document">
                    <div class="modal-content">
                        <form action="{{ route('commercial.partenaires.destroy', $partenaire) }}" method="POST">
                            @csrf
                            @method('DELETE')
                            <div class="modal-header">
                                <h5 class="modal-title">Supprimer le partenaire</h5>
                                <button type="button" class="btn-close icon-btn-sm" data-bs-dismiss="modal" aria-label="Close">
                                    <i class="ri-close-large-line fw-semibold"></i>
                                </button>
                            </div>
                            <div class="modal-body">
                                <div class="alert alert-warning">
                                    <i class="bi bi-exclamation-triangle me-1"></i>
                                    Cette opération archive le partenaire <strong>{{ $partenaire->nom }}</strong>. Un motif est obligatoire.
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
        <div class="modal fade" id="creerPartenaireModal" data-bs-keyboard="false" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
                <div class="modal-content">
                    <form action="{{ route('commercial.partenaires.store') }}" method="POST">
                        @csrf
                        @include('commercial.partenaires._formulaire', ['partenaire' => null])
                        <div class="modal-footer">
                            <button type="button" class="btn btn-light" data-bs-dismiss="modal">Fermer</button>
                            <button type="submit" class="btn btn-primary">Créer le partenaire</button>
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
