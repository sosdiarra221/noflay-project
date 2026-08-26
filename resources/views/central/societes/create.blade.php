@extends('central.layout')

@section('title', 'Nouvelle société | Espace éditeur')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="mb-0">Nouvelle société</h4>
        <a href="{{ route('central.societes.index') }}" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left me-1"></i>Retour
        </a>
    </div>

    @if ($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach ($errors->all() as $erreur)
                    <li>{{ $erreur }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('central.societes.store') }}" method="POST">
        @csrf
        <div class="row g-4">
            <div class="col-lg-6">
                <div class="card">
                    <div class="card-header"><h6 class="mb-0">Informations de la société</h6></div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label for="id" class="form-label">Identifiant (sous-domaine) <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <input type="text" class="form-control" id="id" name="id" value="{{ old('id') }}"
                                    placeholder="acme" pattern="[a-zA-Z0-9_-]+" required>
                                <span class="input-group-text">.{{ config('app.tenant_domain_suffix') }}</span>
                            </div>
                            <div class="form-text">Lettres, chiffres, tirets uniquement. Ne pourra plus être modifié après création.</div>
                        </div>
                        <div class="mb-3">
                            <label for="nom_pme" class="form-label">Nom de la société <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="nom_pme" name="nom_pme" value="{{ old('nom_pme') }}" required>
                        </div>
                        <div class="mb-0">
                            <label for="plan" class="form-label">Plan / abonnement</label>
                            <input type="text" class="form-control" id="plan" name="plan" value="{{ old('plan') }}" placeholder="ex: Standard, Premium...">
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-6">
                <div class="card">
                    <div class="card-header">
                        <h6 class="mb-0">Modules souscrits</h6>
                        <p class="text-muted fs-12 mb-0">Cochez les modules inclus dans l'abonnement de cette société. « Direction &amp; Administration » est toujours inclus.</p>
                    </div>
                    <div class="card-body">
                        <div class="form-check mb-2">
                            <input class="form-check-input" type="checkbox" checked disabled>
                            <label class="form-check-label text-muted">Direction &amp; Administration (toujours actif)</label>
                        </div>
                        @foreach ($catalogue as $cle => $meta)
                            @continue($cle === 'administration')
                            <div class="form-check mb-2">
                                <input class="form-check-input" type="checkbox" id="module-{{ $cle }}" name="modules[]" value="{{ $cle }}" {{ in_array($cle, old('modules', [])) ? 'checked' : '' }}>
                                <label class="form-check-label" for="module-{{ $cle }}">
                                    <i class="{{ $meta['icone'] }} me-1"></i>{{ $meta['nom'] }}
                                </label>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>

        <div class="mt-4">
            <button type="submit" class="btn btn-primary">
                <i class="bi bi-check-lg me-1"></i>Créer la société
            </button>
        </div>
        <p class="text-muted fs-12 mt-2">La création provisionne une base de données dédiée et exécute automatiquement les migrations — cela peut prendre quelques secondes.</p>
    </form>
@endsection
