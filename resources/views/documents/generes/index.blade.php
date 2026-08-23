@extends('partials.layouts.master-documents')

@section('title', 'Documents générés | Gestion Document')
@section('title-sub', 'Gestion Document')
@section('pagetitle', 'Documents générés')

@section('css')
    <link rel="stylesheet" href="{{ asset('assets/libs/choices.js/public/assets/styles/choices.min.css') }}">
@endsection

@section('content')
    <div id="layout-wrapper">

        <div class="row">
            <div class="col-12 mb-4">
                <div class="accordion accordion-icon accordion-primary accordion-border-box" id="filtres_documents">
                    <div class="accordion-item">
                        <h2 class="accordion-header">
                            <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#filtres_documents_body" aria-expanded="true">
                                <i class="bi bi-funnel-fill me-2"></i> Filtres
                            </button>
                        </h2>
                        <div id="filtres_documents_body" class="accordion-collapse collapse show" data-bs-parent="#filtres_documents">
                            <div class="accordion-body py-5">
                                <form method="GET" class="row g-4" id="formFiltresDocuments">
                                    <div class="col-md-3">
                                        <select class="form-select" name="document_template_id" id="filtreModeleSelect">
                                            <option value="">Tous les modèles</option>
                                            @foreach ($modeles as $modele)
                                                <option value="{{ $modele->id }}" @selected(request('document_template_id') == $modele->id)>{{ $modele->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-md-3">
                                        <select class="form-select" name="type" onchange="this.form.submit()">
                                            <option value="">Tous les types</option>
                                            @foreach ($types as $code => $type)
                                                <option value="{{ $code }}" @selected(request('type') === $code)>{{ $type['libelle'] }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-md-3">
                                        <select class="form-select" name="statut" onchange="this.form.submit()">
                                            <option value="">Tous les statuts</option>
                                            @foreach (\App\Models\Documents\Document::LIBELLES_STATUT as $code => $libelle)
                                                <option value="{{ $code }}" @selected(request('statut') === $code)>{{ $libelle }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-md-3">
                                        <input type="text" name="recherche" class="form-control" placeholder="Rechercher une référence, un titre..." value="{{ request('recherche') }}" id="filtreRecherche">
                                    </div>
                                    <div class="col-12 d-flex justify-content-end gap-2">
                                        <a href="{{ route('documents.generes.index') }}" class="btn btn-light-danger">Réinitialiser</a>
                                    </div>
                                </form>
                                <p class="text-muted fs-12 mt-3 mb-0"><i class="bi bi-info-circle me-1"></i>Les filtres s'appliquent automatiquement.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-12">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h6 class="mb-0">Documents générés <span class="badge bg-secondary-subtle text-secondary ms-1">{{ $documents->count() }}</span></h6>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-box table-responsive">
                            <table class="table text-nowrap table-hover align-middle mb-0">
                                <thead>
                                    <tr>
                                        <th>Référence</th>
                                        <th>Type / Modèle</th>
                                        <th>Version</th>
                                        <th>Rattaché à</th>
                                        <th>Statut</th>
                                        <th>Généré le</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($documents as $document)
                                        <tr>
                                            <td class="fw-medium">{{ $document->reference }}</td>
                                            <td>{{ $document->template->name ?? \App\Services\Documents\DocumentType::libelle($document->type) }}</td>
                                            <td>{{ $document->version ? 'v'.$document->version->version : '—' }}</td>
                                            <td>
                                                @if ($document->documentable)
                                                    {{ class_basename($document->documentable) === 'ContratLocation' ? 'Location' : 'Gérance' }}
                                                    {{ $document->documentable->numero ?? $document->documentable_id }}
                                                @else
                                                    —
                                                @endif
                                            </td>
                                            <td><span class="badge bg-primary-subtle text-primary">{{ $document->libelleStatut() }}</span></td>
                                            <td>{{ $document->generated_at?->format('d/m/Y') ?? '—' }}</td>
                                            <td>
                                                <div class="hstack gap-2">
                                                    <button type="button" class="btn btn-light-info icon-btn-sm" data-bs-toggle="modal" data-bs-target="#voirDocModal{{ $document->id }}" title="Voir"><i class="bi bi-eye"></i></button>
                                                    <a href="{{ route('documents.generes.edit', $document) }}" class="btn btn-light-primary icon-btn-sm" title="Modifier"><i class="bi bi-pencil-square"></i></a>
                                                    <a href="{{ route('documents.generes.telecharger', $document) }}" class="btn btn-light-warning icon-btn-sm" title="Télécharger en PDF"><i class="bi bi-file-earmark-pdf"></i></a>
                                                    <a href="{{ route('documents.generes.historique', $document) }}" class="btn btn-light-secondary icon-btn-sm" title="Historique"><i class="bi bi-clock-history"></i></a>
                                                </div>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr><td colspan="7" class="text-center text-muted py-5">Aucun document généré ne correspond à ces filtres.</td></tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        @foreach ($documents as $document)
            <div class="modal fade" id="voirDocModal{{ $document->id }}" tabindex="-1" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title">{{ $document->title }} — {{ $document->reference }}</h5>
                            <button type="button" class="btn-close icon-btn-sm" data-bs-dismiss="modal" aria-label="Close"><i class="ri-close-large-line fw-semibold"></i></button>
                        </div>
                        <div class="modal-body p-0">
                            <iframe src="{{ route('documents.generes.apercu', $document) }}" style="width: 100%; height: 70vh; border: 0;"></iframe>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-light" data-bs-dismiss="modal">Fermer</button>
                            <a href="{{ route('documents.generes.telecharger', $document) }}" class="btn btn-primary"><i class="bi bi-download me-1"></i>Télécharger en PDF</a>
                        </div>
                    </div>
                </div>
            </div>
        @endforeach

    </div>
    </main>
@endsection

@section('js')
    <script src="{{ asset('assets/libs/choices.js/public/assets/scripts/choices.min.js') }}"></script>
    <script type="module" src="{{ asset('assets/js/app.js') }}"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const form = document.getElementById('formFiltresDocuments');

            const modeleSelect = new Choices(document.getElementById('filtreModeleSelect'), {
                searchEnabled: true,
                itemSelectText: '',
                placeholderValue: 'Rechercher un modèle...',
                searchPlaceholderValue: 'Rechercher...',
            });
            document.getElementById('filtreModeleSelect').addEventListener('change', () => form.submit());

            let timeoutRecherche;
            document.getElementById('filtreRecherche').addEventListener('input', function () {
                clearTimeout(timeoutRecherche);
                timeoutRecherche = setTimeout(() => form.submit(), 500);
            });
        });
    </script>
@endsection
