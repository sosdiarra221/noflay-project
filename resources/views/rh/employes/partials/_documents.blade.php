@can('rh.donnees-sensibles')
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h6 class="card-action-title mb-0">Documents <span class="badge bg-secondary-subtle text-secondary ms-1">{{ $employe->documents->count() }}</span></h6>
            @can('rh.gerer')
                <button type="button" class="btn btn-light-primary btn-sm" data-bs-toggle="modal" data-bs-target="#ajouterDocumentModal">
                    <i class="bi bi-upload me-1"></i>Ajouter un document
                </button>
            @endcan
        </div>
        <div class="card-body p-0">
            <div class="table-box table-responsive">
                <table class="table text-nowrap align-middle mb-0">
                    <thead><tr><th>Type</th><th>Fichier</th><th>Taille</th><th>Ajouté par</th><th>Date</th><th>Actions</th></tr></thead>
                    <tbody>
                        @forelse ($employe->documents as $document)
                            <tr>
                                <td><span class="badge bg-secondary-subtle text-secondary">{{ \App\Http\Controllers\Rh\EmployeDocumentController::TYPES[$document->type_document] ?? $document->type_document }}</span></td>
                                <td><i class="bi bi-file-earmark me-1 text-muted"></i>{{ $document->nom_fichier }}</td>
                                <td>{{ $document->tailleLisible() }}</td>
                                <td>{{ $document->ajoutePar->name ?? '—' }}</td>
                                <td>{{ $document->created_at->format('d/m/Y') }}</td>
                                <td>
                                    <div class="hstack gap-2">
                                        <button type="button" class="btn btn-light-success icon-btn-sm" title="Voir" onclick="ouvrirApercuDocument({{ Js::from(route('rh.employes.documents.apercu', $document)) }}, {{ Js::from($document->nom_fichier) }}, {{ Js::from($document->estPrevisualisable()) }}, {{ Js::from(route('rh.employes.documents.apercu', $document)) }})"><i class="bi bi-eye"></i></button>
                                        @can('rh.gerer')
                                            <form action="{{ route('rh.employes.documents.destroy', $document) }}" method="POST" onsubmit="return confirm('Supprimer ce document ?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-light-danger icon-btn-sm"><i class="ri-delete-bin-line"></i></button>
                                            </form>
                                        @endcan
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="6" class="text-center text-muted py-5">Aucun document enregistré pour cet employé.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@else
    <div class="card">
        <div class="card-body text-center py-8">
            <i class="bi bi-shield-lock text-muted" style="font-size: 2.5rem;"></i>
            <p class="text-muted mt-3 mb-0">Les documents personnels sont des données sensibles réservées à un accès restreint.</p>
        </div>
    </div>
@endcan
