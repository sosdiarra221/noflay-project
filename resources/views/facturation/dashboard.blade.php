@extends('partials.layouts.master-facturation')

@section('title', 'Tableau de bord | Facturation')
@section('title-sub', 'Facturation')
@section('pagetitle', 'Tableau de bord')

@section('content')
    <div id="layout-wrapper">

        <div class="row g-3 mb-1">
            <div class="col-md-6 col-xl-3">
                <div class="card border">
                    <div class="card-body">
                        <div class="d-flex gap-4 border-bottom pb-5 mb-5">
                            <div class="h-50px w-50px bg-primary text-white d-flex align-items-center justify-content-center rounded fs-3">
                                <i class="bi bi-receipt"></i>
                            </div>
                            <div>
                                <h5 class="mb-2">{{ $stats['total_devis'] }}</h5>
                                <p class="text-muted mb-0 fs-12">Devis créés</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-6 col-xl-3">
                <div class="card border">
                    <div class="card-body">
                        <div class="d-flex gap-4 border-bottom pb-5 mb-5">
                            <div class="h-50px w-50px bg-info text-white d-flex align-items-center justify-content-center rounded fs-3">
                                <i class="bi bi-cash-stack"></i>
                            </div>
                            <div>
                                <h5 class="mb-2">{{ number_format($stats['montant_total_ttc'], 0, ',', ' ') }}</h5>
                                <p class="text-muted mb-0 fs-12">Montant total TTC (FCFA)</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-6 col-xl-3">
                <div class="card border">
                    <div class="card-body">
                        <div class="d-flex gap-4 border-bottom pb-5 mb-5">
                            <div class="h-50px w-50px bg-success text-white d-flex align-items-center justify-content-center rounded fs-3">
                                <i class="bi bi-check-circle"></i>
                            </div>
                            <div>
                                <h5 class="mb-2">{{ $stats['gagne'] }}</h5>
                                <p class="text-muted mb-0 fs-12">Devis gagnés</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-6 col-xl-3">
                <div class="card border">
                    <div class="card-body">
                        <div class="d-flex gap-4 border-bottom pb-5 mb-5">
                            <div class="h-50px w-50px bg-warning text-white d-flex align-items-center justify-content-center rounded fs-3">
                                <i class="bi bi-hourglass-split"></i>
                            </div>
                            <div>
                                <h5 class="mb-2">{{ $stats['en_cours'] }}</h5>
                                <p class="text-muted mb-0 fs-12">En cours (nouveau / négociation)</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row g-4">
            <div class="col-12">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h6 class="mb-0">Derniers devis</h6>
                        <a href="{{ route('facturation.devis.index') }}" class="btn btn-primary btn-sm">
                            <i class="bi bi-plus-lg me-1"></i>Voir tous les devis
                        </a>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-box table-responsive">
                            <table class="table text-nowrap align-middle mb-0">
                                <thead><tr><th>Numéro</th><th>Client</th><th>Date</th><th>Total TTC</th><th>Statut</th><th></th></tr></thead>
                                <tbody>
                                    @php $classesStatut = ['nouveau' => 'secondary', 'en_negociation' => 'warning', 'gagne' => 'success', 'perdu' => 'danger', 'annule' => 'dark']; @endphp
                                    @forelse ($derniersDevis as $devis)
                                        <tr>
                                            <td class="fw-medium">{{ $devis->numero }}</td>
                                            <td>{{ $devis->client->nom_complet ?? '—' }}</td>
                                            <td>{{ $devis->date_devis->format('d/m/Y') }}</td>
                                            <td>{{ number_format($devis->total_ttc, 0, ',', ' ') }} FCFA</td>
                                            <td><span class="badge bg-{{ $classesStatut[$devis->statut] ?? 'secondary' }}-subtle text-{{ $classesStatut[$devis->statut] ?? 'secondary' }}">{{ $devis->libelleStatut() }}</span></td>
                                            <td><a href="{{ route('facturation.devis.show', $devis) }}" class="btn btn-light-success btn-sm"><i class="bi bi-eye me-1"></i>Ouvrir</a></td>
                                        </tr>
                                    @empty
                                        <tr><td colspan="6" class="text-center text-muted py-5">Aucun devis créé pour le moment.</td></tr>
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
