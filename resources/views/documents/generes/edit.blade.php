@extends('partials.layouts.master-documents')

@section('title', 'Modifier '.$document->reference.' | Gestion Document')
@section('title-sub', 'Gestion Document')
@section('pagetitle', $document->reference)

@section('content')
    <div id="layout-wrapper">

        <div class="row mb-4">
            <div class="col-12 d-flex justify-content-between align-items-center flex-wrap gap-2">
                <div>
                    <h5 class="mb-1">{{ $document->title }} <code class="fs-13">{{ $document->reference }}</code></h5>
                    <p class="text-muted mb-0">
                        Ce document est désormais indépendant de son modèle d'origine : toute modification ici n'affecte que ce document et crée une entrée d'historique.
                    </p>
                </div>
                <div class="hstack gap-2">
                    <a href="{{ route('documents.generes.index') }}" class="btn btn-light">Retour à la liste</a>
                    <a href="{{ route('documents.generes.historique', $document) }}" class="btn btn-light-secondary"><i class="bi bi-clock-history me-1"></i>Historique</a>
                </div>
            </div>
        </div>

        <form id="formDocument" action="{{ route('documents.generes.update', $document) }}" method="POST">
            @csrf
            @method('PUT')
            <textarea id="contentInput" name="content" class="d-none">{{ $document->content }}</textarea>

            @include('documents.partials.tiptap', ['hiddenInputId' => 'contentInput', 'groupesVariables' => $groupesVariables, 'editorId' => 'tiptapDocument'])

            <div class="row mt-4">
                <div class="col-lg-4">
                    <label class="form-label">Statut du document</label>
                    <select name="status" class="form-select">
                        @foreach (\App\Models\Documents\Document::LIBELLES_STATUT as $code => $libelle)
                            <option value="{{ $code }}" @selected($document->status === $code)>{{ $libelle }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-lg-8">
                    <label class="form-label">Note sur cette modification (facultatif)</label>
                    <input type="text" name="note" class="form-control" maxlength="255" placeholder="Ex: correction d'une clause à la demande du bailleur...">
                </div>
            </div>

            <div class="d-flex justify-content-end gap-2 mt-4">
                <button type="submit" class="btn btn-primary"><i class="bi bi-save me-1"></i>Enregistrer les modifications</button>
            </div>
        </form>

    </div>
    </main>
@endsection

@section('js')
    <script type="module" src="{{ asset('assets/js/app.js') }}"></script>
@endsection
