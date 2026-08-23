@extends('partials.layouts.master-documents')

@section('title', 'Nouveau modèle | Gestion Document')
@section('title-sub', 'Gestion Document')
@section('pagetitle', 'Nouveau modèle de document')

@section('content')
    <div id="layout-wrapper">

        <div class="row justify-content-center">
            <div class="col-lg-7">
                <div class="card">
                    <div class="card-header"><h6 class="mb-0">Informations du modèle</h6></div>
                    <div class="card-body">
                        <form action="{{ route('documents.modeles.store') }}" method="POST">
                            @csrf
                            <div class="mb-3">
                                <label class="form-label">Nom <span class="text-danger">*</span></label>
                                <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name') }}" required maxlength="180">
                                @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Code <span class="text-danger">*</span></label>
                                <input type="text" name="code" class="form-control text-uppercase @error('code') is-invalid @enderror" value="{{ old('code') }}" required maxlength="80" placeholder="EX: LOCATION_HABITATION" style="text-transform:uppercase">
                                <div class="form-text">Lettres majuscules, chiffres et underscores uniquement. Utilisé par le moteur pour retrouver le modèle actif d'un type de document.</div>
                                @error('code')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Catégorie</label>
                                <input type="text" name="category" list="categoriesConnues" class="form-control" value="{{ old('category') }}" maxlength="80" placeholder="EX: LOCATION, MANDAT...">
                                <datalist id="categoriesConnues">
                                    @foreach (collect($types)->pluck('categorie')->unique() as $categorie)
                                        <option value="{{ $categorie }}">
                                    @endforeach
                                </datalist>
                            </div>
                            <div class="mb-4">
                                <label class="form-label">Description</label>
                                <textarea name="description" class="form-control" rows="3">{{ old('description') }}</textarea>
                            </div>
                            <div class="d-flex justify-content-end gap-2">
                                <a href="{{ route('documents.modeles.index') }}" class="btn btn-light">Annuler</a>
                                <button type="submit" class="btn btn-primary">Créer et rédiger le contenu</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

    </div>
    </main>
@endsection
