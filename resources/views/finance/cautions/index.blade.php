@extends('partials.layouts.master-finance')

@section('title', 'Cautions / garanties | Finance')
@section('title-sub', 'Finance')
@section('pagetitle', 'Cautions / garanties')

@section('css')
    <link rel="stylesheet" href="{{ asset('assets/libs/choices.js/public/assets/styles/choices.min.css') }}">
@endsection

@section('content')
    <div id="layout-wrapper">

        <div class="row">
            <div class="col-md-3">
                <div class="card">
                    <div class="card-body">
                        <h3 class="text-warning">{{ number_format($stats['total_detenu'], 0, ',', ' ') }}</h3>
                        <p class="text-muted mb-0">Caution détenue (FCFA)</p>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card">
                    <div class="card-body">
                        <h3 class="text-danger">{{ number_format($stats['total_retenu'], 0, ',', ' ') }}</h3>
                        <p class="text-muted mb-0">Total retenu pour dégâts (FCFA)</p>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card">
                    <div class="card-body">
                        <h3 class="text-success">{{ number_format($stats['total_restitue'], 0, ',', ' ') }}</h3>
                        <p class="text-muted mb-0">Total restitué aux locataires (FCFA)</p>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card">
                    <div class="card-body">
                        <h3>{{ $stats['nombre_actives'] }}</h3>
                        <p class="text-muted mb-0">Cautions actives</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-12 mb-4">
                <div class="accordion accordion-icon accordion-primary accordion-border-box" id="filtres_cautions">
                    <div class="accordion-item">
                        <h2 class="accordion-header">
                            <button class="accordion-button" type="button" data-bs-toggle="collapse"
                                data-bs-target="#filtres_cautions_body" aria-expanded="true">
                                <i class="bi bi-funnel-fill me-2"></i> Filtres
                            </button>
                        </h2>
                        <div id="filtres_cautions_body" class="accordion-collapse collapse show" data-bs-parent="#filtres_cautions">
                            <div class="accordion-body py-5">
                                <form method="GET" class="row g-4" id="formFiltresCautions">
                                    <div class="col-md-6">
                                        <select class="form-select" name="bailleur_id" id="filtreBailleurCautionSelect">
                                            <option value="">Tous les bailleurs</option>
                                            @foreach ($bailleurs as $bailleur)
                                                <option value="{{ $bailleur->id }}" @selected(request('bailleur_id') == $bailleur->id)>{{ $bailleur->nom_complet }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-md-6">
                                        <select class="form-select" name="statut" onchange="this.form.submit()">
                                            <option value="">Tous les statuts</option>
                                            <option value="detenue" @selected(request('statut') === 'detenue')>Détenue</option>
                                            <option value="partiellement_restituee" @selected(request('statut') === 'partiellement_restituee')>Partiellement restituée</option>
                                            <option value="restituee" @selected(request('statut') === 'restituee')>Restituée</option>
                                        </select>
                                    </div>
                                    <div class="col-12 d-flex justify-content-end gap-2">
                                        <a href="{{ route('finance.cautions.index') }}" class="btn btn-light-danger">Réinitialiser</a>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h6 class="mb-0">Cautions <span class="badge bg-secondary-subtle text-secondary ms-1">{{ $cautions->count() }}</span></h6>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-box table-responsive">
                            <table class="table text-nowrap align-middle mb-0">
                                <thead>
                                    <tr>
                                        <th>Bien</th>
                                        <th>Bailleur</th>
                                        <th>Locataire</th>
                                        <th>Montant total signature</th>
                                        <th>Part bailleur (caution)</th>
                                        <th>Part agence (frais)</th>
                                        <th>Retenu</th>
                                        <th>À restituer</th>
                                        <th>Statut</th>
                                        <th></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($cautions as $caution)
                                        <tr>
                                            <td>{{ $caution->contratLocation->bien->titre ?? '—' }}</td>
                                            <td>{{ $caution->contratLocation->bailleur->nom_complet ?? '—' }}</td>
                                            <td>{{ $caution->contratLocation->location->locataire->nom_complet ?? '—' }}</td>
                                            <td>{{ number_format($caution->montant_total, 0, ',', ' ') }} FCFA</td>
                                            <td class="text-warning">{{ number_format($caution->part_bailleur, 0, ',', ' ') }} FCFA</td>
                                            <td class="text-primary">{{ number_format($caution->part_agence, 0, ',', ' ') }} FCFA</td>
                                            <td class="text-danger">{{ $caution->montant_retenu > 0 ? number_format($caution->montant_retenu, 0, ',', ' ').' FCFA' : '—' }}</td>
                                            <td class="fw-semibold">{{ number_format($caution->montantARestituer(), 0, ',', ' ') }} FCFA</td>
                                            <td>
                                                @if ($caution->statut === 'detenue')
                                                    <span class="badge bg-warning-subtle text-warning">Détenue</span>
                                                @elseif ($caution->statut === 'partiellement_restituee')
                                                    <span class="badge bg-info-subtle text-info">Partiellement restituée</span>
                                                @else
                                                    <span class="badge bg-success-subtle text-success">Restituée</span>
                                                @endif
                                            </td>
                                            <td>
                                                @if ($caution->statut !== 'restituee')
                                                    <button type="button" class="btn btn-sm btn-light-primary" data-bs-toggle="modal" data-bs-target="#restituerModal{{ $caution->id }}">
                                                        <i class="bi bi-arrow-return-left me-1"></i>Restituer
                                                    </button>
                                                @endif
                                            </td>
                                        </tr>
                                    @empty
                                        <tr><td colspan="10" class="text-center text-muted py-5">Aucune caution enregistrée pour ces filtres.</td></tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>

    @foreach ($cautions as $caution)
        @if ($caution->statut !== 'restituee')
            <div class="modal fade" id="restituerModal{{ $caution->id }}" tabindex="-1" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered" role="document">
                    <div class="modal-content">
                        <form action="{{ route('finance.cautions.restituer', $caution) }}" method="POST">
                            @csrf
                            <div class="modal-header">
                                <h5 class="modal-title">Restitution de caution — {{ $caution->contratLocation->bien->titre ?? '' }}</h5>
                                <button type="button" class="btn-close icon-btn-sm" data-bs-dismiss="modal" aria-label="Close"><i class="ri-close-large-line fw-semibold"></i></button>
                            </div>
                            <div class="modal-body">
                                <p class="text-muted fs-12">Caution détenue (part bailleur) : <strong>{{ number_format($caution->part_bailleur, 0, ',', ' ') }} FCFA</strong>. Toute retenue doit être justifiée par l'état des lieux, les factures/devis ou les dispositions du contrat.</p>
                                <div class="row g-3">
                                    <div class="col-12">
                                        <label class="form-label">Montant retenu pour dégâts</label>
                                        <input type="number" step="0.01" min="0" max="{{ $caution->part_bailleur }}" class="form-control" name="montant_retenu" value="0">
                                    </div>
                                    <div class="col-12">
                                        <label class="form-label">Motif de la retenue</label>
                                        <textarea class="form-control" name="motif_retenue" rows="2" placeholder="Ex. réparations murales suite à état des lieux de sortie..."></textarea>
                                    </div>
                                    <div class="col-12">
                                        <label class="form-label">Date de restitution<span class="text-danger ms-1">*</span></label>
                                        <input type="date" class="form-control" name="date_restitution" value="{{ now()->format('Y-m-d') }}" required>
                                    </div>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-light" data-bs-dismiss="modal">Annuler</button>
                                <button type="submit" class="btn btn-primary">Confirmer la restitution</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        @endif
    @endforeach
    </main>
@endsection

@section('js')
    <script src="{{ asset('assets/libs/choices.js/public/assets/scripts/choices.min.js') }}"></script>
    <script type="module" src="{{ asset('assets/js/app.js') }}"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const form = document.getElementById('formFiltresCautions');
            new Choices(document.getElementById('filtreBailleurCautionSelect'), {
                searchEnabled: true,
                itemSelectText: '',
                placeholderValue: 'Rechercher un bailleur...',
                searchPlaceholderValue: 'Rechercher...',
            });
            document.getElementById('filtreBailleurCautionSelect').addEventListener('change', () => form.submit());
        });
    </script>
@endsection
