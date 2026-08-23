@php $documents = $documentable->documents()->latest()->get(); @endphp

<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h6 class="card-title mb-0 fw-semibold">Documents <span class="badge bg-secondary-subtle text-secondary ms-1">{{ $documents->count() }}</span></h6>
        @can('locative.documents')
            <form action="{{ route('locative.documents.store', [$typeDocument, $documentable->id]) }}" method="POST" enctype="multipart/form-data" class="d-flex gap-2">
                @csrf
                <input type="text" name="categorie" class="form-control form-control-sm" placeholder="Catégorie (optionnel)" style="width: 160px">
                <input type="file" name="fichier" class="form-control form-control-sm" required>
                <button type="submit" class="btn btn-light-primary btn-sm text-nowrap"><i class="bi bi-upload me-1"></i>Ajouter</button>
            </form>
        @endcan
    </div>
    <div class="card-body p-0">
        <div class="table-box table-responsive">
            <table class="table text-nowrap align-middle mb-0">
                <thead>
                    <tr>
                        <th>Fichier</th>
                        <th>Catégorie</th>
                        <th>Taille</th>
                        <th>Ajouté par</th>
                        <th>Date</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($documents as $document)
                        <tr>
                            <td><i class="bi bi-file-earmark me-1 text-muted"></i>{{ $document->nom_original }}</td>
                            <td>{{ $document->categorie ?: '—' }}</td>
                            <td>{{ $document->tailleLisible() }}</td>
                            <td>{{ $document->uploadePar->name ?? '—' }}</td>
                            <td>{{ $document->created_at->format('d/m/Y') }}</td>
                            <td>
                                <div class="hstack gap-2">
                                    <a href="{{ route('locative.documents.telecharger', $document) }}" class="btn btn-light-info icon-btn-sm"><i class="bi bi-download"></i></a>
                                    @can('locative.documents')
                                        <form action="{{ route('locative.documents.destroy', $document) }}" method="POST" onsubmit="return confirm('Supprimer ce document ?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-light-danger icon-btn-sm"><i class="ri-delete-bin-line"></i></button>
                                        </form>
                                    @endcan
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="6" class="text-center text-muted py-5">Aucun document.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
