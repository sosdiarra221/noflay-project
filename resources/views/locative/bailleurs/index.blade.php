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
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <div class="d-flex flex-wrap gap-3 justify-content-between align-items-center">
                            <h6 class="card-title mb-0 fw-semibold">
                                Bailleurs <span class="badge bg-secondary-subtle text-secondary ms-1">{{ $bailleurs->count() }}</span>
                            </h6>
                            <button type="button" class="btn btn-light-primary" data-bs-toggle="modal"
                                data-bs-target="#createBailleurModal">
                                <i class="bi bi-plus-lg me-1"></i>Nouveau bailleur
                            </button>
                        </div>
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
                                                <a href="{{ route('locative.bailleurs.show', $bailleur) }}" class="text-reset">
                                                    <div class="d-flex align-items-center gap-3">
                                                        <div class="flex-shrink-0 h-32px w-32px bg-primary-subtle d-flex justify-content-center align-items-center rounded-pill text-primary fs-6">
                                                            <i class="bi bi-person-badge"></i>
                                                        </div>
                                                        <div>
                                                            <h6 class="mb-0 fw-medium fs-13">{{ $bailleur->nom_complet }}</h6>
                                                            <span class="text-muted fs-12">{{ $bailleur->numero }}</span>
                                                        </div>
                                                    </div>
                                                </a>
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
                                                    <a href="{{ route('locative.bailleurs.show', $bailleur) }}"
                                                        class="btn btn-light-success icon-btn-sm">
                                                        <i class="bi bi-eye"></i>
                                                    </a>
                                                    <button type="button" class="btn btn-light-primary icon-btn-sm"
                                                        data-bs-toggle="modal"
                                                        data-bs-target="#editBailleurModal{{ $bailleur->id }}">
                                                        <i class="bi bi-pencil-square"></i>
                                                    </button>
                                                    <button type="button" class="btn btn-light-danger icon-btn-sm"
                                                        data-bs-toggle="modal"
                                                        data-bs-target="#deleteBailleurModal{{ $bailleur->id }}">
                                                        <i class="ri-delete-bin-line"></i>
                                                    </button>
                                                </div>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="7" class="text-center text-muted py-5">Aucun bailleur enregistré.</td>
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
