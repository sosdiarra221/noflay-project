@extends('partials.layouts.master-documents')

@section('title', 'Versions — '.$modele->name.' | Gestion Document')
@section('title-sub', 'Gestion Document')
@section('pagetitle', 'Versions — '.$modele->name)

@section('content')
    <div id="layout-wrapper">

        <div class="row mb-4">
            <div class="col-12 d-flex justify-content-between align-items-center">
                <p class="text-muted mb-0">Historique complet des versions du modèle « {{ $modele->name }} ». Une version publiée n'est jamais modifiée en place.</p>
                <a href="{{ route('documents.modeles.edit', $modele) }}" class="btn btn-primary"><i class="bi bi-pencil-square me-1"></i>Éditer / nouveau brouillon</a>
            </div>
        </div>

        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body p-0">
                        <div class="table-box table-responsive">
                            <table class="table text-nowrap table-hover align-middle mb-0">
                                <thead>
                                    <tr>
                                        <th>Version</th>
                                        <th>Statut</th>
                                        <th>Créée par</th>
                                        <th>Créée le</th>
                                        <th>Publiée le</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($versions as $v)
                                        <tr>
                                            <td class="fw-medium">v{{ $v->version }}</td>
                                            <td>
                                                @if ($v->status === 'active')
                                                    <span class="badge bg-success-subtle text-success">Active</span>
                                                @elseif ($v->status === 'draft')
                                                    <span class="badge bg-warning-subtle text-warning">Brouillon</span>
                                                @elseif ($v->status === 'inactive')
                                                    <span class="badge bg-secondary-subtle text-secondary">Inactive</span>
                                                @else
                                                    <span class="badge bg-danger-subtle text-danger text-capitalize">{{ $v->status }}</span>
                                                @endif
                                            </td>
                                            <td>{{ $v->createdBy->name ?? '—' }}</td>
                                            <td>{{ $v->created_at->format('d/m/Y H:i') }}</td>
                                            <td>{{ $v->published_at?->format('d/m/Y H:i') ?? '—' }}</td>
                                            <td>
                                                <div class="hstack gap-2">
                                                    <a href="{{ route('documents.versions.apercu', [$modele, $v]) }}" target="_blank" class="btn btn-light-info icon-btn-sm" title="Voir"><i class="bi bi-eye"></i></a>
                                                    @if ($v->status !== 'active')
                                                        <form action="{{ route('documents.versions.restaurer', [$modele, $v]) }}" method="POST" class="d-inline">
                                                            @csrf
                                                            <button type="submit" class="btn btn-light-warning icon-btn-sm" title="Restaurer en brouillon" onclick="return confirm('Restaurer cette version comme nouveau brouillon ?');"><i class="bi bi-arrow-counterclockwise"></i></button>
                                                        </form>
                                                    @endif
                                                </div>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr><td colspan="6" class="text-center text-muted py-5">Aucune version.</td></tr>
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
