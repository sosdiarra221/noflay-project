@php $documents = $documentable->documents()->latest()->get(); @endphp

<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h6 class="card-title mb-0 fw-semibold">Documents <span class="badge bg-secondary-subtle text-secondary ms-1">{{ $documents->count() }}</span></h6>
        @can(in_array($typeDocument, ['depense', 'caution']) ? 'finance.gerer' : 'locative.documents')
            <button type="button" class="btn btn-light-primary btn-sm" data-bs-toggle="modal" data-bs-target="#ajouterDocumentModal{{ $typeDocument }}{{ $documentable->id }}">
                <i class="bi bi-upload me-1"></i>Ajouter un document
            </button>
        @endcan
    </div>
    <div class="card-body p-0">
        <div class="table-box table-responsive">
            <table class="table text-nowrap align-middle mb-0">
                <thead>
                    <tr>
                        <th>Titre</th>
                        <th>Type</th>
                        <th>Fichier</th>
                        <th>Taille</th>
                        <th>Ajouté par</th>
                        <th>Date</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($documents as $document)
                        <tr>
                            <td class="fw-medium">{{ $document->titre ?: '—' }}</td>
                            <td><span class="badge bg-secondary-subtle text-secondary">{{ $document->categorie ?: '—' }}</span></td>
                            <td><i class="bi bi-file-earmark me-1 text-muted"></i>{{ $document->nom_original }}</td>
                            <td>{{ $document->tailleLisible() }}</td>
                            <td>{{ $document->uploadePar->name ?? '—' }}</td>
                            <td>{{ $document->created_at->format('d/m/Y') }}</td>
                            <td>
                                <div class="hstack gap-2">
                                    <button type="button" class="btn btn-light-success icon-btn-sm" title="Voir" onclick="ouvrirApercuDocument({{ Js::from(route('locative.documents.apercu', $document)) }}, {{ Js::from($document->titre ?: $document->nom_original) }}, {{ Js::from($document->estPrevisualisable()) }}, {{ Js::from(route('locative.documents.telecharger', $document)) }})"><i class="bi bi-eye"></i></button>
                                    <a href="{{ route('locative.documents.telecharger', $document) }}" class="btn btn-light-info icon-btn-sm"><i class="bi bi-download"></i></a>
                                    @can(in_array($typeDocument, ['depense', 'caution']) ? 'finance.gerer' : 'locative.documents')
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
                        <tr><td colspan="7" class="text-center text-muted py-5">Aucun document.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

@can(in_array($typeDocument, ['depense', 'caution']) ? 'finance.gerer' : 'locative.documents')
    <div class="modal fade" id="ajouterDocumentModal{{ $typeDocument }}{{ $documentable->id }}" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <form action="{{ route('locative.documents.store', [$typeDocument, $documentable->id]) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title">Ajouter un document</h5>
                        <button type="button" class="btn-close icon-btn-sm" data-bs-dismiss="modal" aria-label="Close"><i class="ri-close-large-line fw-semibold"></i></button>
                    </div>
                    <div class="modal-body">
                        <div class="row g-3">
                            <div class="col-12">
                                <label class="form-label">Titre<span class="text-danger ms-1">*</span></label>
                                <input type="text" class="form-control" name="titre" placeholder="Ex: Bail signé 2026" required>
                            </div>
                            <div class="col-12">
                                <label class="form-label">Type<span class="text-danger ms-1">*</span></label>
                                <select class="form-select" name="categorie" required>
                                    <option value="">Sélectionner...</option>
                                    @foreach (\App\Models\Document::TYPES as $type)
                                        <option value="{{ $type }}">{{ $type }}</option>
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
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Fermer</button>
                        <button type="submit" class="btn btn-primary">Ajouter</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endcan

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
