@extends('partials.layouts.master-documents')

@section('title', 'Modèles de documents | Gestion Document')
@section('title-sub', 'Gestion Document')
@section('pagetitle', 'Modèles de documents')

@section('content')
    <div id="layout-wrapper">

        <div class="row">
            <div class="col-12 mb-4 d-flex justify-content-end">
                <a href="{{ route('documents.modeles.create') }}" class="btn btn-primary"><i class="bi bi-plus-lg me-1"></i>Nouveau modèle</a>
            </div>
            <div class="col-12">
                <div class="card">
                    <div class="card-header"><h6 class="mb-0">Modèles <span class="badge bg-secondary-subtle text-secondary ms-1">{{ $modeles->count() }}</span></h6></div>
                    <div class="card-body p-0">
                        <div class="table-box table-responsive">
                            <table class="table text-nowrap table-hover align-middle mb-0">
                                <thead>
                                    <tr>
                                        <th>Nom</th>
                                        <th>Code</th>
                                        <th>Catégorie</th>
                                        <th>Version active</th>
                                        <th>Statut</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($modeles as $modele)
                                        <tr>
                                            <td class="fw-medium">{{ $modele->name }}</td>
                                            <td><code>{{ $modele->code }}</code></td>
                                            <td>{{ $modele->category ?: '—' }}</td>
                                            <td>
                                                @if ($modele->activeVersion)
                                                    v{{ $modele->activeVersion->version }}
                                                @else
                                                    <span class="text-muted">Aucune</span>
                                                @endif
                                                <span class="text-muted fs-11">({{ $modele->versions_count }} version(s))</span>
                                            </td>
                                            <td>
                                                @if ($modele->status === 'active')
                                                    <span class="badge bg-success-subtle text-success">Actif</span>
                                                @elseif ($modele->status === 'draft')
                                                    <span class="badge bg-warning-subtle text-warning">Brouillon</span>
                                                @elseif ($modele->status === 'archived')
                                                    <span class="badge bg-danger-subtle text-danger">Archivé</span>
                                                @else
                                                    <span class="badge bg-secondary-subtle text-secondary text-capitalize">{{ $modele->status }}</span>
                                                @endif
                                            </td>
                                            <td>
                                                <div class="hstack gap-2">
                                                    <a href="{{ route('documents.modeles.edit', $modele) }}" class="btn btn-light-primary icon-btn-sm" title="Modifier"><i class="bi bi-pencil-square"></i></a>
                                                    @if ($modele->activeVersion)
                                                        <a href="{{ route('documents.modeles.apercu', $modele) }}" target="_blank" class="btn btn-light-info icon-btn-sm" title="Aperçu"><i class="bi bi-eye"></i></a>
                                                    @endif
                                                    <a href="{{ route('documents.versions.index', $modele) }}" class="btn btn-light-secondary icon-btn-sm" title="Versions"><i class="bi bi-clock-history"></i></a>
                                                    <form action="{{ route('documents.modeles.dupliquer', $modele) }}" method="POST" class="d-inline">
                                                        @csrf
                                                        <button type="submit" class="btn btn-light-warning icon-btn-sm" title="Dupliquer"><i class="bi bi-copy"></i></button>
                                                    </form>
                                                    <button type="button" class="btn btn-light-danger icon-btn-sm" data-bs-toggle="modal" data-bs-target="#archiverModal{{ $modele->id }}" title="Archiver"><i class="bi bi-archive"></i></button>
                                                </div>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr><td colspan="6" class="text-center text-muted py-5">Aucun modèle pour le moment.</td></tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        @foreach ($modeles as $modele)
            <div class="modal fade" id="archiverModal{{ $modele->id }}" tabindex="-1" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered" role="document">
                    <form action="{{ route('documents.modeles.destroy', $modele) }}" method="POST">
                        @csrf
                        @method('DELETE')
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title">Archiver « {{ $modele->name }} »</h5>
                                <button type="button" class="btn-close icon-btn-sm" data-bs-dismiss="modal" aria-label="Close"><i class="ri-close-large-line fw-semibold"></i></button>
                            </div>
                            <div class="modal-body">
                                <p class="text-muted">Ce modèle sera archivé (les documents déjà générés à partir de lui ne sont pas affectés).</p>
                                <div class="mb-3">
                                    <label class="form-label">Motif <span class="text-danger">*</span></label>
                                    <input type="text" name="motif_suppression" class="form-control" required maxlength="255">
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-light" data-bs-dismiss="modal">Annuler</button>
                                <button type="submit" class="btn btn-danger">Archiver</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        @endforeach

    </div>
    </main>
@endsection

@section('js')
    <script type="module" src="{{ asset('assets/js/app.js') }}"></script>
@endsection
