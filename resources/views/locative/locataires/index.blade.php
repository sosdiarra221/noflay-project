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
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <div class="d-flex flex-wrap gap-3 justify-content-between align-items-center">
                            <h6 class="card-title mb-0 fw-semibold">
                                Locataires <span class="badge bg-secondary-subtle text-secondary ms-1">{{ $locataires->count() }}</span>
                            </h6>
                            <button type="button" class="btn btn-light-primary" data-bs-toggle="modal" data-bs-target="#createLocataireModal">
                                <i class="bi bi-plus-lg me-1"></i>Nouveau locataire
                            </button>
                        </div>
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
                                                    <button type="button" class="btn btn-light-primary icon-btn-sm" data-bs-toggle="modal" data-bs-target="#editLocataireModal{{ $locataire->id }}"><i class="bi bi-pencil-square"></i></button>
                                                    <button type="button" class="btn btn-light-danger icon-btn-sm" data-bs-toggle="modal" data-bs-target="#deleteLocataireModal{{ $locataire->id }}"><i class="ri-delete-bin-line"></i></button>
                                                </div>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr><td colspan="5" class="text-center text-muted py-5">Aucun locataire enregistré.</td></tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        @foreach ($locataires as $locataire)
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
@endsection
