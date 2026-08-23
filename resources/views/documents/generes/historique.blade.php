@extends('partials.layouts.master-documents')

@section('title', 'Historique — '.$document->reference.' | Gestion Document')
@section('title-sub', 'Gestion Document')
@section('pagetitle', 'Historique — '.$document->reference)

@section('content')
    <div id="layout-wrapper">

        <div class="row mb-4">
            <div class="col-12 d-flex justify-content-between align-items-center">
                <div>
                    <h5 class="mb-1">{{ $document->title }}</h5>
                    <p class="text-muted mb-0">
                        Modèle d'origine :
                        @if ($document->template)
                            {{ $document->template->name }} (version {{ $document->version->version ?? '?' }} au moment de la génération)
                        @else
                            —
                        @endif
                    </p>
                </div>
                <a href="{{ route('documents.generes.index') }}" class="btn btn-light">Retour à la liste</a>
            </div>
        </div>

        <div class="row">
            <div class="col-lg-8">
                <div class="card">
                    <div class="card-header"><h6 class="mb-0">Chronologie</h6></div>
                    <div class="card-body">
                        <ul class="list-unstyled activity-timeline mb-0">
                            @forelse ($document->revisions as $revision)
                                <li class="pb-4 mb-1 border-bottom">
                                    <div class="d-flex justify-content-between">
                                        <div>
                                            <span class="badge bg-primary-subtle text-primary me-2">{{ $revision->libelleAction() }}</span>
                                            <span class="fw-medium">{{ $revision->user->name ?? 'Système' }}</span>
                                        </div>
                                        <span class="text-muted fs-12">{{ $revision->created_at->format('d/m/Y H:i') }}</span>
                                    </div>
                                    @if ($revision->note)
                                        <p class="text-muted mb-0 mt-1">{{ $revision->note }}</p>
                                    @endif
                                    @if ($revision->changes)
                                        <p class="text-muted mb-0 mt-1 fs-12">
                                            @foreach ($revision->changes as $cle => $valeur)
                                                <span class="me-2"><strong>{{ $cle }}</strong>: {{ is_array($valeur) ? json_encode($valeur) : $valeur }}</span>
                                            @endforeach
                                        </p>
                                    @endif
                                </li>
                            @empty
                                <li class="text-center text-muted py-5">Aucun historique pour ce document.</li>
                            @endforelse
                        </ul>
                    </div>
                </div>
            </div>
            <div class="col-lg-4">
                <div class="card">
                    <div class="card-header"><h6 class="mb-0">Informations</h6></div>
                    <div class="card-body">
                        <div class="row mb-3"><div class="col-5 text-muted">Référence</div><div class="col-7 fw-medium">{{ $document->reference }}</div></div>
                        <div class="row mb-3"><div class="col-5 text-muted">Statut</div><div class="col-7 fw-medium">{{ $document->libelleStatut() }}</div></div>
                        <div class="row mb-3"><div class="col-5 text-muted">Généré le</div><div class="col-7 fw-medium">{{ $document->generated_at?->format('d/m/Y H:i') ?? '—' }}</div></div>
                        <div class="row"><div class="col-5 text-muted">Généré par</div><div class="col-7 fw-medium">{{ $document->generePar->name ?? '—' }}</div></div>
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
