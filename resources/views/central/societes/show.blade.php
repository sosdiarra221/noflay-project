@extends('central.layout')

@section('title', ($tenant->nom_pme ?? $tenant->id).' | Espace éditeur')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="mb-1">{{ $tenant->nom_pme ?? $tenant->id }}</h4>
            @if ($tenant->domains->first())
                <a href="http://{{ $tenant->domains->first()->domain }}" target="_blank" class="fs-13">{{ $tenant->domains->first()->domain }}</a>
            @endif
        </div>
        <a href="{{ route('central.societes.index') }}" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left me-1"></i>Retour
        </a>
    </div>

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
        </div>

        <div class="col-lg-8">
            <div class="card">
                <div class="card-header">
                    <h6 class="mb-0">Modules souscrits</h6>
                    <p class="text-muted fs-12 mb-0">Activez ou désactivez l'accès de cette société à chaque module, selon son abonnement.</p>
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
@endsection
