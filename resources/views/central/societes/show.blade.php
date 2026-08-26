@extends('partials.layouts.master-central')

@section('title', ($tenant->nom_pme ?? $tenant->id).' | Espace éditeur')
@section('title-sub', 'Espace éditeur')
@section('pagetitle', $tenant->nom_pme ?? $tenant->id)

@section('content')
    <div id="layout-wrapper">

        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        @if ($tenant->domains->first())
            <p class="mb-3"><a href="http://{{ $tenant->domains->first()->domain }}" target="_blank">{{ $tenant->domains->first()->domain }}</a></p>
        @endif

        <div class="row g-4">
            <div class="col-lg-4">
                <div class="card">
                    <div class="card-header"><h6 class="mb-0">Compte</h6></div>
                    <div class="card-body">
                        <dl class="row mb-0 fs-14">
                            <dt class="col-5 text-muted">Identifiant</dt>
                            <dd class="col-7">{{ $tenant->id }}</dd>
                            <dt class="col-5 text-muted">Plan</dt>
                            <dd class="col-7">{{ $tenant->plan ?? '—' }}</dd>
                            <dt class="col-5 text-muted">Créée le</dt>
                            <dd class="col-7">{{ $tenant->created_at?->format('d/m/Y à H:i') }}</dd>
                            <dt class="col-5 text-muted">Statut</dt>
                            <dd class="col-7">
                                @if ($tenant->statut === 'actif')
                                    <span class="badge bg-success-subtle text-success">Actif</span>
                                @else
                                    <span class="badge bg-danger-subtle text-danger">Suspendu</span>
                                @endif
                            </dd>
                        </dl>

                        <form action="{{ route('central.societes.toggle-statut', $tenant) }}" method="POST" class="mt-3 mb-0">
                            @csrf
                            @if ($tenant->statut === 'actif')
                                <button type="submit" class="btn btn-outline-danger w-100" onclick="return confirm('Suspendre ce compte ? La société ne pourra plus se connecter, y compris à sa page de connexion.')">
                                    <i class="bi bi-pause-circle me-1"></i>Suspendre le compte
                                </button>
                            @else
                                <button type="submit" class="btn btn-outline-success w-100">
                                    <i class="bi bi-play-circle me-1"></i>Réactiver le compte
                                </button>
                            @endif
                        </form>
                    </div>
                </div>

                <div class="card mt-4">
                    <div class="card-header"><h6 class="mb-0">Licence</h6></div>
                    <div class="card-body">
                        @if ($licenceActuelle)
                            <dl class="row mb-0 fs-14">
                                <dt class="col-5 text-muted">Package</dt>
                                <dd class="col-7">{{ $licenceActuelle->package->nom ?? '—' }}</dd>
                                <dt class="col-5 text-muted">Valide jusqu'au</dt>
                                <dd class="col-7">{{ $licenceActuelle->date_fin->format('d/m/Y') }}</dd>
                                <dt class="col-5 text-muted">Statut</dt>
                                <dd class="col-7">
                                    @if ($licenceActuelle->estExpiree())
                                        <span class="badge bg-danger-subtle text-danger">Expirée</span>
                                    @elseif ($licenceActuelle->joursRestants() <= 5)
                                        <span class="badge bg-warning-subtle text-warning">{{ $licenceActuelle->joursRestants() }} j. restants</span>
                                    @else
                                        <span class="badge bg-success-subtle text-success">Valide</span>
                                    @endif
                                </dd>
                            </dl>
                        @else
                            <p class="text-muted mb-0">Aucune licence — la société n'a pas accès à l'application.</p>
                        @endif

                        <button type="button" class="btn btn-primary w-100 mt-3" data-bs-toggle="modal" data-bs-target="#licenceModal">
                            <i class="bi bi-arrow-repeat me-1"></i>Générer / renouveler la licence
                        </button>
                    </div>
                </div>

                @if ($licences->count() > 1)
                    <div class="card mt-4">
                        <div class="card-header"><h6 class="mb-0">Historique des licences</h6></div>
                        <div class="table-responsive">
                            <table class="table table-sm mb-0">
                                <thead class="table-light">
                                    <tr><th>Package</th><th>Période</th><th>Généré par</th></tr>
                                </thead>
                                <tbody>
                                    @foreach ($licences as $licence)
                                        <tr>
                                            <td>{{ $licence->package->nom ?? '—' }}</td>
                                            <td class="fs-12">{{ $licence->date_debut->format('d/m/Y') }} → {{ $licence->date_fin->format('d/m/Y') }}</td>
                                            <td class="fs-12">{{ $licence->genereParAdmin->name ?? '—' }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                @endif
            </div>

            <div class="col-lg-8">
                <div class="card">
                    <div class="card-header">
                        <h6 class="mb-0">Modules accordés</h6>
                        <p class="text-muted fs-12 mb-0">Déterminés par le package de la licence active. Vous pouvez ajuster ponctuellement un module ici sans changer de licence.</p>
                    </div>
                    <div class="card-body">
                        <div class="row g-3">
                            @foreach ($modules as $module)
                                <div class="col-md-6">
                                    <div class="card border h-100 {{ $module->actif ? '' : 'opacity-75' }}">
                                        <div class="card-body d-flex align-items-start justify-content-between">
                                            <div>
                                                <div class="fw-medium"><i class="{{ $module->icone }} me-1"></i>{{ $module->nom }}</div>
                                                <p class="text-muted fs-12 mb-0">{{ $module->description }}</p>
                                            </div>
                                            @if ($module->cle === 'administration')
                                                <span class="badge bg-secondary-subtle text-secondary text-nowrap ms-2">Toujours actif</span>
                                            @else
                                                <form action="{{ route('central.societes.toggle-module', [$tenant, $module->cle]) }}" method="POST" class="ms-2">
                                                    @csrf
                                                    <div class="form-check form-switch mb-0">
                                                        <input class="form-check-input" type="checkbox" role="switch" onchange="this.form.submit()" {{ $module->actif ? 'checked' : '' }} style="width: 2.5em; height: 1.4em;">
                                                    </div>
                                                </form>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>

    <!-- Modal génération de licence -->
    <div class="modal fade" id="licenceModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <form action="{{ route('central.societes.generer-licence', $tenant) }}" method="POST">
                    @csrf
                    <div class="modal-header">
                        <h6 class="modal-title">Générer / renouveler la licence</h6>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <p class="text-muted fs-13">Réactive immédiatement le compte et remplace les modules accordés par ceux du package choisi.</p>
                        <div class="mb-3">
                            <label for="modal_package_id" class="form-label">Package <span class="text-danger">*</span></label>
                            <select class="form-select" id="modal_package_id" name="package_id" required>
                                <option value="">Sélectionner...</option>
                                @foreach ($packages as $package)
                                    <option value="{{ $package->id }}" {{ $licenceActuelle && $licenceActuelle->package_id === $package->id ? 'selected' : '' }}>
                                        {{ $package->nom }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="mb-0">
                            <label for="modal_duree_preset" class="form-label">Durée <span class="text-danger">*</span></label>
                            <select class="form-select mb-2" id="modal_duree_preset">
                                <option value="7">7 jours</option>
                                <option value="30" selected>1 mois</option>
                                <option value="90">3 mois</option>
                                <option value="180">6 mois</option>
                                <option value="365">1 an</option>
                                <option value="autre">Durée personnalisée...</option>
                            </select>
                            <input type="number" class="form-control" id="modal_duree_jours" name="duree_jours" value="30" min="1" required readonly>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Annuler</button>
                        <button type="submit" class="btn btn-primary">Générer la licence</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@section('js')
    <script type="module" src="{{ asset('assets/js/app.js') }}"></script>
    <script>
        const modalDureePreset = document.getElementById('modal_duree_preset');
        const modalDureeJours = document.getElementById('modal_duree_jours');
        modalDureePreset.addEventListener('change', () => {
            if (modalDureePreset.value === 'autre') {
                modalDureeJours.removeAttribute('readonly');
                modalDureeJours.focus();
            } else {
                modalDureeJours.value = modalDureePreset.value;
                modalDureeJours.setAttribute('readonly', 'readonly');
            }
        });
    </script>
@endsection
