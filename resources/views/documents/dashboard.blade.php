@extends('partials.layouts.master-documents')

@section('title', 'Tableau de bord | Gestion Document')
@section('title-sub', 'Gestion Document')
@section('pagetitle', "Vue d'ensemble")

@section('content')
    <div id="layout-wrapper">

        <div class="row">
            <div class="col-md-6 col-xxl-3">
                <div class="card">
                    <div class="card-body">
                        <h3>{{ $stats['modeles_actifs'] }}</h3>
                        <p class="text-muted mb-0">Modèles actifs</p>
                    </div>
                </div>
            </div>
            <div class="col-md-6 col-xxl-3">
                <div class="card">
                    <div class="card-body">
                        <h3>{{ $stats['modeles_total'] }}</h3>
                        <p class="text-muted mb-0">Modèles au total</p>
                    </div>
                </div>
            </div>
            <div class="col-md-6 col-xxl-3">
                <div class="card">
                    <div class="card-body">
                        <h3 class="text-primary">{{ $stats['documents_generes'] }}</h3>
                        <p class="text-muted mb-0">Documents générés</p>
                    </div>
                </div>
            </div>
            <div class="col-md-6 col-xxl-3">
                <div class="card">
                    <div class="card-body">
                        <h3 class="text-success">{{ $stats['documents_ce_mois'] }}</h3>
                        <p class="text-muted mb-0">Générés ce mois-ci</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-lg-7">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h6 class="mb-0">Derniers documents générés</h6>
                        <a href="{{ route('documents.generes.index') }}" class="btn btn-sm btn-light-primary">Tout voir</a>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-box table-responsive">
                            <table class="table text-nowrap table-hover align-middle mb-0">
                                <thead>
                                    <tr>
                                        <th>Référence</th>
                                        <th>Type</th>
                                        <th>Rattaché à</th>
                                        <th>Statut</th>
                                        <th>Généré le</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($derniersDocuments as $document)
                                        <tr>
                                            <td class="fw-medium">{{ $document->reference }}</td>
                                            <td>{{ \App\Services\Documents\DocumentType::libelle($document->type) }}</td>
                                            <td>
                                                @if ($document->documentable)
                                                    {{ class_basename($document->documentable) }} — {{ $document->documentable->numero ?? $document->documentable_id }}
                                                @else
                                                    —
                                                @endif
                                            </td>
                                            <td><span class="badge bg-primary-subtle text-primary">{{ $document->libelleStatut() }}</span></td>
                                            <td>{{ $document->generated_at?->format('d/m/Y') ?? '—' }}</td>
                                        </tr>
                                    @empty
                                        <tr><td colspan="5" class="text-center text-muted py-5">Aucun document généré pour le moment.</td></tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-5">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h6 class="mb-0">Modèles de documents</h6>
                        <a href="{{ route('documents.modeles.create') }}" class="btn btn-sm btn-primary"><i class="bi bi-plus-lg me-1"></i>Nouveau modèle</a>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-box table-responsive">
                            <table class="table text-nowrap table-hover align-middle mb-0">
                                <thead>
                                    <tr>
                                        <th>Nom</th>
                                        <th>Statut</th>
                                        <th>Utilisé</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($modeles as $modele)
                                        <tr>
                                            <td><a href="{{ route('documents.modeles.edit', $modele) }}" class="fw-medium">{{ $modele->name }}</a></td>
                                            <td>
                                                @if ($modele->status === 'active')
                                                    <span class="badge bg-success-subtle text-success">Actif</span>
                                                @elseif ($modele->status === 'draft')
                                                    <span class="badge bg-warning-subtle text-warning">Brouillon</span>
                                                @else
                                                    <span class="badge bg-secondary-subtle text-secondary text-capitalize">{{ $modele->status }}</span>
                                                @endif
                                            </td>
                                            <td>{{ $modele->documents_count }} fois</td>
                                        </tr>
                                    @empty
                                        <tr><td colspan="3" class="text-center text-muted py-5">Aucun modèle pour le moment.</td></tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>
    </main>
@endsection

@section('js')
    <script type="module" src="{{ asset('assets/js/app.js') }}"></script>
@endsection
