@extends('partials.layouts.master-documents')

@section('title', 'Modifier '.$modele->name.' | Gestion Document')
@section('title-sub', 'Gestion Document')
@section('pagetitle', $modele->name)

@section('content')
    <div id="layout-wrapper">

        <div class="row mb-4">
            <div class="col-12 d-flex justify-content-between align-items-center flex-wrap gap-2">
                <div>
                    <h5 class="mb-1">{{ $modele->name }} <code class="fs-13">{{ $modele->code }}</code></h5>
                    <p class="text-muted mb-0">
                        Brouillon en cours d'édition : version {{ $version->version }}
                        @if ($modele->activeVersion)
                            — version active actuelle : v{{ $modele->activeVersion->version }} (non modifiée par ce brouillon)
                        @endif
                    </p>
                </div>
                <div class="hstack gap-2">
                    <a href="{{ route('documents.modeles.index') }}" class="btn btn-light">Retour à la liste</a>
                    <a href="{{ route('documents.versions.index', $modele) }}" class="btn btn-light-secondary"><i class="bi bi-clock-history me-1"></i>Historique des versions</a>
                </div>
            </div>
        </div>

        <form id="formVersion" action="{{ route('documents.versions.enregistrer', [$modele, $version]) }}" method="POST">
            @csrf
            <textarea id="contentInput" name="content" class="d-none">{{ $version->content }}</textarea>

            @include('documents.partials.tiptap', ['hiddenInputId' => 'contentInput', 'groupesVariables' => $groupesVariables, 'editorId' => 'tiptapModele'])

            <div class="d-flex justify-content-end gap-2 mt-4">
                <button type="submit" class="btn btn-light-primary" formaction="{{ route('documents.versions.enregistrer', [$modele, $version]) }}">
                    <i class="bi bi-save me-1"></i>Enregistrer brouillon
                </button>
                <button type="submit" class="btn btn-success" formaction="{{ route('documents.versions.publier', [$modele, $version]) }}"
                    onclick="return confirm('Publier cette version ? Elle deviendra la version active du modèle et remplacera l\'ancienne version active (conservée dans l\'historique).');">
                    <i class="bi bi-check-circle me-1"></i>Publier
                </button>
            </div>
        </form>

    </div>
    </main>
@endsection

@section('js')
    <script type="module" src="{{ asset('assets/js/app.js') }}"></script>
@endsection
