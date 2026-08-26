@extends('partials.layouts.master-central')

@section('title', 'Nouvelle société | Espace éditeur')
@section('title-sub', 'Espace éditeur')
@section('pagetitle', 'Nouvelle société')

@section('content')
    <div id="layout-wrapper">

        @if ($errors->any())
            <div class="alert alert-danger">
                <ul class="mb-0">
                    @foreach ($errors->all() as $erreur)
                        <li>{{ $erreur }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        @if ($packages->isEmpty())
            <div class="alert alert-warning">
                Aucun package actif n'existe encore. <a href="{{ route('central.packages.index') }}">Créez-en un</a> avant de pouvoir créer une société.
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
                            <div class="mb-0">
                                <label for="nom_pme" class="form-label">Nom de la société <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="nom_pme" name="nom_pme" value="{{ old('nom_pme') }}" required>
                            </div>
                        </div>
                    </div>

                    <div class="card mt-4">
                        <div class="card-header">
                            <h6 class="mb-0">Premier accès</h6>
                            <p class="text-muted fs-12 mb-0">Ce compte administrateur permettra à la société de se connecter et de créer ses propres utilisateurs.</p>
                        </div>
                        <div class="card-body">
                            <div class="mb-3">
                                <label for="utilisateur_nom" class="form-label">Nom <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="utilisateur_nom" name="utilisateur_nom" value="{{ old('utilisateur_nom') }}" required>
                            </div>
                            <div class="mb-3">
                                <label for="utilisateur_email" class="form-label">Email <span class="text-danger">*</span></label>
                                <input type="email" class="form-control" id="utilisateur_email" name="utilisateur_email" value="{{ old('utilisateur_email') }}" required>
                            </div>
                            <div class="mb-0">
                                <label for="utilisateur_password" class="form-label">Mot de passe <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="utilisateur_password" name="utilisateur_password" minlength="8" required>
                                <div class="form-text">Communiquez-le à la société — il ne sera plus affiché ensuite.</div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-lg-6">
                    <div class="card">
                        <div class="card-header">
                            <h6 class="mb-0">Package &amp; licence</h6>
                            <p class="text-muted fs-12 mb-0">Le package choisi détermine les modules accordés. « Direction &amp; Administration » est toujours inclus.</p>
                        </div>
                        <div class="card-body">
                            <div class="mb-3">
                                <label for="package_id" class="form-label">Package <span class="text-danger">*</span></label>
                                <select class="form-select" id="package_id" name="package_id" required>
                                    <option value="">Sélectionner...</option>
                                    @foreach ($packages as $package)
                                        <option value="{{ $package->id }}" {{ old('package_id') == $package->id ? 'selected' : '' }}>
                                            {{ $package->nom }} ({{ count($package->modules) }} module{{ count($package->modules) > 1 ? 's' : '' }} + Administration)
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="mb-0">
                                <label for="duree_preset" class="form-label">Durée de la licence <span class="text-danger">*</span></label>
                                <select class="form-select mb-2" id="duree_preset">
                                    <option value="7">7 jours</option>
                                    <option value="30" selected>1 mois</option>
                                    <option value="90">3 mois</option>
                                    <option value="180">6 mois</option>
                                    <option value="365">1 an</option>
                                    <option value="autre">Durée personnalisée...</option>
                                </select>
                                <input type="number" class="form-control" id="duree_jours" name="duree_jours" value="30" min="1" required>
                                <div class="form-text">Nombre de jours d'accès. Un avertissement s'affichera chez la société 5 jours avant l'échéance.</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="mt-4">
                <button type="submit" class="btn btn-primary" {{ $packages->isEmpty() ? 'disabled' : '' }}>
                    <i class="bi bi-check-lg me-1"></i>Créer la société
                </button>
            </div>
            <p class="text-muted fs-12 mt-2">La création provisionne une base de données dédiée, exécute les migrations et le paramétrage initial — cela peut prendre quelques secondes.</p>
        </form>

    </div>
@endsection

@section('js')
    <script type="module" src="{{ asset('assets/js/app.js') }}"></script>
    <script>
        const dureePreset = document.getElementById('duree_preset');
        const dureeJours = document.getElementById('duree_jours');
        dureePreset.addEventListener('change', () => {
            if (dureePreset.value === 'autre') {
                dureeJours.removeAttribute('readonly');
                dureeJours.focus();
            } else {
                dureeJours.value = dureePreset.value;
                dureeJours.setAttribute('readonly', 'readonly');
            }
        });
        dureeJours.setAttribute('readonly', 'readonly');
    </script>
@endsection
