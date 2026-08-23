@extends('partials.layouts.master-commercial')

@section('title', 'Tableau de bord | Commercial')
@section('title-sub', 'Commercial')
@section('pagetitle', 'Tableau de bord')

@section('content')

    <div id="layout-wrapper">

        <div class="row">
            <div class="col-md-6 col-xxl-2">
                <div class="card">
                    <div class="card-body">
                        <h3>{{ $kpis['prospects'] }}</h3>
                        <p class="text-muted mb-0">Prospects</p>
                    </div>
                </div>
            </div>
            <div class="col-md-6 col-xxl-2">
                <div class="card">
                    <div class="card-body">
                        <h3 class="text-secondary">{{ $kpis['a_traiter'] }}</h3>
                        <p class="text-muted mb-0">À traiter</p>
                    </div>
                </div>
            </div>
            <div class="col-md-6 col-xxl-2">
                <div class="card">
                    <div class="card-body">
                        <h3 class="text-warning">{{ $kpis['en_cours'] }}</h3>
                        <p class="text-muted mb-0">En cours</p>
                    </div>
                </div>
            </div>
            <div class="col-md-6 col-xxl-2">
                <div class="card">
                    <div class="card-body">
                        <h3 class="text-success">{{ $kpis['gagnes'] }}</h3>
                        <p class="text-muted mb-0">Gagnés</p>
                    </div>
                </div>
            </div>
            <div class="col-md-6 col-xxl-2">
                <div class="card">
                    <div class="card-body">
                        <h3 class="text-danger">{{ $kpis['perdus'] }}</h3>
                        <p class="text-muted mb-0">Perdus</p>
                    </div>
                </div>
            </div>
            <div class="col-md-6 col-xxl-2">
                <div class="card">
                    <div class="card-body">
                        <h3 class="text-dark">{{ $kpis['annules'] }}</h3>
                        <p class="text-muted mb-0">Annulés</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-xl-4">
                <div class="card">
                    <div class="card-header"><h6 class="mb-0">Taux de conversion</h6></div>
                    <div class="card-body">
                        <h2 class="mb-2">{{ $tauxConversion }} %</h2>
                        <div class="progress progress-sm mb-3" role="progressbar" aria-valuenow="{{ $tauxConversion }}" aria-valuemin="0" aria-valuemax="100">
                            <div class="progress-bar bg-success" style="width: {{ $tauxConversion }}%"></div>
                        </div>
                        <p class="text-muted mb-0">{{ $kpis['gagnes'] }} gagnés sur {{ $kpis['prospects'] }} prospects</p>
                    </div>
                </div>
                <div class="card">
                    <div class="card-header"><h6 class="mb-0">Prospects par source</h6></div>
                    <div class="card-body">
                        @forelse ($parSource as $source)
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <span class="text-muted">{{ $source->nom }}</span>
                                <span class="fw-semibold">{{ $source->prospects_count }}</span>
                            </div>
                        @empty
                            <p class="text-muted mb-0">Aucune donnée pour le moment.</p>
                        @endforelse
                    </div>
                </div>
                @if ($sansActivite > 0)
                    <div class="alert alert-warning">
                        <i class="bi bi-exclamation-triangle me-1"></i>
                        <strong>{{ $sansActivite }}</strong> prospect(s) sans activité depuis 7 jours.
                        <a href="{{ route('commercial.prospects.index') }}" class="alert-link">Voir</a>
                    </div>
                @endif
            </div>
            <div class="col-xl-8">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h6 class="mb-0">Prospects récents</h6>
                        <a href="{{ route('commercial.prospects.index') }}" class="btn btn-outline-light text-muted btn-sm">Voir tout<i class="bi bi-arrow-right ms-1"></i></a>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-box table-responsive">
                            <table class="table text-nowrap align-middle mb-0">
                                <thead><tr><th>Prospect</th><th>Demande</th><th>Source</th><th>Statut</th><th></th></tr></thead>
                                <tbody>
                                    @forelse ($prospectsRecents as $prospect)
                                        @php
                                            $classesS = ['non_traite' => 'secondary', 'en_cours' => 'warning', 'gagne' => 'success', 'perdu' => 'danger', 'annule' => 'dark'];
                                        @endphp
                                        <tr>
                                            <td>{{ $prospect->nom_complet }}</td>
                                            <td>{{ $prospect->typeDemande->nom ?? '—' }}</td>
                                            <td>{{ $prospect->source->nom ?? '—' }}</td>
                                            <td><span class="badge bg-{{ $classesS[$prospect->statut] }}-subtle text-{{ $classesS[$prospect->statut] }} text-capitalize">{{ str_replace('_', ' ', $prospect->statut) }}</span></td>
                                            <td><a href="{{ route('commercial.prospects.show', $prospect) }}" class="btn btn-light-success icon-btn-sm"><i class="bi bi-eye"></i></a></td>
                                        </tr>
                                    @empty
                                        <tr><td colspan="5" class="text-center text-muted py-5">Aucun prospect pour le moment.</td></tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <div class="card">
                    <div class="card-header"><h6 class="mb-0">Activités récentes</h6></div>
                    <div class="card-body p-0">
                        <div class="table-box table-responsive">
                            <table class="table text-nowrap align-middle mb-0">
                                <thead><tr><th>Date</th><th>Type</th><th>Prospect</th><th>Objet</th><th>Par</th></tr></thead>
                                <tbody>
                                    @forelse ($activitesRecentes as $activite)
                                        <tr>
                                            <td>{{ $activite->date_activite->format('d/m/Y H:i') }}</td>
                                            <td class="text-capitalize">{{ str_replace('_', ' ', $activite->type) }}</td>
                                            <td>{{ $activite->prospect->nom_complet }}</td>
                                            <td>{{ $activite->objet }}</td>
                                            <td>{{ $activite->utilisateur->name ?? '—' }}</td>
                                        </tr>
                                    @empty
                                        <tr><td colspan="5" class="text-center text-muted py-5">Aucune activité enregistrée.</td></tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
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
