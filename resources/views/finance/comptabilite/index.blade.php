@extends('partials.layouts.master-finance')

@section('title', 'Comptabilité générale | Finance')
@section('title-sub', 'Finance')
@section('pagetitle', 'Comptabilité générale')

@section('content')
    <div id="layout-wrapper">

        <div class="row g-3 mb-1">
            <div class="col-md-6 col-xl-3">
                <div class="card border">
                    <div class="card-body">
                        <div class="d-flex gap-4 border-bottom pb-5 mb-5">
                            <div class="h-50px w-50px bg-success text-white d-flex align-items-center justify-content-center rounded fs-3">
                                <i class="bi bi-arrow-down-circle"></i>
                            </div>
                            <div>
                                <h5 class="mb-2">{{ number_format($stats['total_encaisse'], 0, ',', ' ') }} FCFA</h5>
                                <p class="text-muted mb-0 fs-12">Encaissé sur la période</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-6 col-xl-3">
                <div class="card border">
                    <div class="card-body">
                        <div class="d-flex gap-4 border-bottom pb-5 mb-5">
                            <div class="h-50px w-50px bg-danger text-white d-flex align-items-center justify-content-center rounded fs-3">
                                <i class="bi bi-arrow-up-circle"></i>
                            </div>
                            <div>
                                <h5 class="mb-2">{{ number_format($stats['total_decaisse'], 0, ',', ' ') }} FCFA</h5>
                                <p class="text-muted mb-0 fs-12">Décaissé sur la période</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-6 col-xl-3">
                <div class="card border">
                    <div class="card-body">
                        <div class="d-flex gap-4 border-bottom pb-5 mb-5">
                            <div class="h-50px w-50px bg-{{ $stats['solde_periode'] >= 0 ? 'primary' : 'warning' }} text-white d-flex align-items-center justify-content-center rounded fs-3">
                                <i class="bi bi-calculator"></i>
                            </div>
                            <div>
                                <h5 class="mb-2">{{ number_format($stats['solde_periode'], 0, ',', ' ') }} FCFA</h5>
                                <p class="text-muted mb-0 fs-12">Solde de la période</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-6 col-xl-3">
                <div class="card border bg-dark-subtle">
                    <div class="card-body">
                        <div class="d-flex gap-4 border-bottom pb-5 mb-5">
                            <div class="h-50px w-50px bg-dark text-white d-flex align-items-center justify-content-center rounded fs-3">
                                <i class="bi bi-safe"></i>
                            </div>
                            <div>
                                <h5 class="mb-2">{{ number_format($stats['solde_global'], 0, ',', ' ') }} FCFA</h5>
                                <p class="text-muted mb-0 fs-12">Solde de trésorerie global (tout historique)</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="card">
            <div class="card-header">
                <form action="{{ route('finance.comptabilite.index') }}" method="GET" class="row g-3 align-items-end">
                    <div class="col-md-2">
                        <label class="form-label">Du</label>
                        <input type="date" class="form-control" name="debut" value="{{ $debut->format('Y-m-d') }}">
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">Au</label>
                        <input type="date" class="form-control" name="fin" value="{{ $fin->format('Y-m-d') }}">
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">Type</label>
                        <select class="form-select" name="type">
                            <option value="">Tous</option>
                            <option value="encaissement" @selected(request('type') === 'encaissement')>Encaissements</option>
                            <option value="decaissement" @selected(request('type') === 'decaissement')>Décaissements</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">Module</label>
                        <select class="form-select" name="module">
                            <option value="">Tous</option>
                            @foreach ($modules as $module)
                                <option value="{{ $module }}" @selected(request('module') === $module)>{{ $module }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2">
                        <button type="submit" class="btn btn-primary w-100">Filtrer</button>
                    </div>
                    <div class="col-md-2">
                        <a href="{{ route('finance.comptabilite.rapport', request()->only('debut', 'fin')) }}" target="_blank" class="btn btn-outline-secondary w-100">
                            <i class="bi bi-printer me-1"></i>Imprimer
                        </a>
                    </div>
                </form>
            </div>
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Date</th>
                            <th>Type</th>
                            <th>Source</th>
                            <th>Module</th>
                            <th>Référence</th>
                            <th class="text-end">Montant</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($mouvements as $mouvement)
                            <tr>
                                <td>{{ $mouvement->date->format('d/m/Y') }}</td>
                                <td>
                                    @if ($mouvement->type === 'encaissement')
                                        <span class="badge bg-success-subtle text-success">Encaissement</span>
                                    @else
                                        <span class="badge bg-danger-subtle text-danger">Décaissement</span>
                                    @endif
                                </td>
                                <td>{{ $mouvement->source }}</td>
                                <td><span class="badge bg-light text-dark border">{{ $mouvement->module }}</span></td>
                                <td>{{ $mouvement->reference }}</td>
                                <td class="text-end fw-medium">{{ number_format($mouvement->montant, 0, ',', ' ') }} FCFA</td>
                                <td class="text-end">
                                    <button type="button" class="btn btn-sm btn-outline-secondary"
                                        onclick="ouvrirDetailComptabilite('{{ $mouvement->lien['type'] }}', {{ $mouvement->lien['id'] }})">
                                        Détail
                                    </button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center text-muted py-4">Aucun mouvement sur la période sélectionnée.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

    </div>

    <div class="modal fade" id="detailComptabiliteModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h6 class="modal-title">Détail du mouvement</h6>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body" id="detailComptabiliteBody">
                    <div class="text-center text-muted py-4">Chargement...</div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('js')
    <script type="module" src="{{ asset('assets/js/app.js') }}"></script>
    <script>
        function ouvrirDetailComptabilite(type, id) {
            const modalEl = document.getElementById('detailComptabiliteModal');
            const body = document.getElementById('detailComptabiliteBody');
            body.innerHTML = '<div class="text-center text-muted py-4">Chargement...</div>';
            new bootstrap.Modal(modalEl).show();

            fetch(`{{ url('finance/comptabilite/detail') }}/${type}/${id}`)
                .then(r => r.text())
                .then(html => { body.innerHTML = html; })
                .catch(() => { body.innerHTML = '<div class="text-center text-danger py-4">Impossible de charger le détail.</div>'; });
        }
    </script>
@endsection
