@extends('partials.layouts.master-finance')

@section('title', $locataire->nom_complet.' | Finance')
@section('title-sub', 'Finance')
@section('pagetitle', $locataire->nom_complet)

@section('content')
    <div id="layout-wrapper">

        <div class="card">
            <div class="card-body d-flex flex-wrap justify-content-between align-items-center gap-3">
                <div>
                    <h5 class="mb-1">{{ $locataire->nom_complet }}</h5>
                    <p class="text-muted fs-12 mb-0">{{ $locataire->numero }} @if ($locataire->telephone) — {{ $locataire->telephone }} @endif</p>
                </div>
                <a href="{{ route('locative.locataires.show', $locataire) }}" class="btn btn-light-primary btn-sm"><i class="bi bi-person me-1"></i>Dossier locatif complet</a>
            </div>
        </div>

        {{-- LOCATAIRE : loyers dus / paiements / paiements partiels / arriérés / solde locatif --}}
        <div class="card">
            <div class="card-header"><h6 class="card-title mb-0 fw-semibold"><i class="bi bi-person-badge me-1"></i>Compte locataire</h6></div>
            <div class="card-body">
                <div class="row g-3 mb-4">
                    <div class="col-md-3">
                        <div class="border rounded-3 p-3 h-100">
                            <h5 class="mb-0">{{ number_format($compteLocataire['loyers_dus'], 0, ',', ' ') }}</h5>
                            <p class="text-muted fs-12 mb-0">Loyers dus (FCFA)</p>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="border rounded-3 p-3 h-100">
                            <h5 class="mb-0 text-success">{{ number_format($compteLocataire['loyers_payes'], 0, ',', ' ') }}</h5>
                            <p class="text-muted fs-12 mb-0">Loyers payés (FCFA)</p>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="border rounded-3 p-3 h-100">
                            <h5 class="mb-0 text-danger">{{ number_format($compteLocataire['arrieres'], 0, ',', ' ') }}</h5>
                            <p class="text-muted fs-12 mb-0">Arriérés (FCFA) — {{ $compteLocataire['nb_partiels'] }} paiement(s) partiel(s)</p>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="border rounded-3 p-3 h-100 {{ $compteLocataire['solde'] > 0 ? 'border-danger' : 'border-success' }}">
                            <h5 class="mb-0 {{ $compteLocataire['solde'] > 0 ? 'text-danger' : 'text-success' }}">{{ number_format($compteLocataire['solde'], 0, ',', ' ') }}</h5>
                            <p class="text-muted fs-12 mb-0">Solde locatif (FCFA)</p>
                        </div>
                    </div>
                </div>

                <h6 class="fw-semibold mb-2">Loyers dus par mois</h6>
                <div class="table-box table-responsive mb-4">
                    <table class="table text-nowrap align-middle mb-0">
                        <thead>
                            <tr><th>Mois</th><th>Bien</th><th>Dû</th><th>Payé</th><th>Arriéré</th><th>Statut</th></tr>
                        </thead>
                        <tbody>
                            @forelse ($echeances as $ligne)
                                <tr>
                                    <td>{{ ucfirst(\Carbon\Carbon::createFromDate($ligne['echeance']->annee, $ligne['echeance']->mois, 1)->translatedFormat('F Y')) }}</td>
                                    <td>{{ $ligne['echeance']->contratLocation->bien->titre ?? '—' }}</td>
                                    <td>{{ number_format($ligne['du'], 0, ',', ' ') }}</td>
                                    <td class="text-success">{{ number_format($ligne['paye'], 0, ',', ' ') }}</td>
                                    <td class="{{ $ligne['arriere'] > 0 ? 'text-danger' : '' }}">{{ $ligne['arriere'] > 0 ? number_format($ligne['arriere'], 0, ',', ' ') : '—' }}</td>
                                    <td>
                                        @if ($ligne['partiel'])
                                            <span class="badge bg-warning-subtle text-warning">Partiel</span>
                                        @elseif ($ligne['arriere'] > 0)
                                            <span class="badge bg-danger-subtle text-danger">Impayé</span>
                                        @else
                                            <span class="badge bg-success-subtle text-success">Payé</span>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr><td colspan="6" class="text-center text-muted py-4">Aucune échéance.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <h6 class="fw-semibold mb-2">Paiements enregistrés</h6>
                <div class="table-box table-responsive">
                    <table class="table text-nowrap align-middle mb-0">
                        <thead>
                            <tr><th>Date</th><th>Numéro</th><th>Type</th><th>Montant</th><th>Mode</th></tr>
                        </thead>
                        <tbody>
                            @forelse ($paiements as $paiement)
                                <tr>
                                    <td>{{ $paiement->date_paiement->format('d/m/Y') }}</td>
                                    <td class="fw-medium">{{ $paiement->numero }}</td>
                                    <td>{{ $paiement->type === 'entree' ? "Entrée / signature" : 'Loyer' }}</td>
                                    <td>{{ number_format($paiement->montant, 0, ',', ' ') }} FCFA</td>
                                    <td>{{ $paiement->modePaiement->nom ?? '—' }}</td>
                                </tr>
                            @empty
                                <tr><td colspan="5" class="text-center text-muted py-4">Aucun paiement.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        {{-- GARANTIE / CAUTION : montant reçu / retenues / motif / justificatifs / montant à restituer --}}
        <div class="card">
            <div class="card-header"><h6 class="card-title mb-0 fw-semibold"><i class="bi bi-shield-lock me-1"></i>Garantie / caution</h6></div>
            <div class="card-body">
                @forelse ($cautions as $caution)
                    <div class="border rounded-3 p-3 mb-3">
                        <div class="d-flex flex-wrap justify-content-between align-items-center mb-2">
                            <h6 class="mb-0">{{ $caution->contratLocation->bien->titre ?? '—' }}</h6>
                            @if ($caution->statut === 'detenue')
                                <span class="badge bg-warning-subtle text-warning">Détenue</span>
                            @elseif ($caution->statut === 'partiellement_restituee')
                                <span class="badge bg-info-subtle text-info">Partiellement restituée</span>
                            @else
                                <span class="badge bg-success-subtle text-success">Restituée</span>
                            @endif
                        </div>
                        <div class="row g-3 fs-13">
                            <div class="col-md-3"><span class="text-muted d-block fs-11">Montant reçu (part bailleur)</span><span class="fw-medium">{{ number_format($caution->part_bailleur, 0, ',', ' ') }} FCFA</span></div>
                            <div class="col-md-3"><span class="text-muted d-block fs-11">Retenues</span><span class="fw-medium text-danger">{{ $caution->montant_retenu > 0 ? number_format($caution->montant_retenu, 0, ',', ' ').' FCFA' : '—' }}</span></div>
                            <div class="col-md-3"><span class="text-muted d-block fs-11">Motif des retenues</span><span class="fw-medium">{{ $caution->motif_retenue ?: '—' }}</span></div>
                            <div class="col-md-3"><span class="text-muted d-block fs-11">Montant à restituer</span><span class="fw-medium text-success">{{ number_format($caution->montantARestituer(), 0, ',', ' ') }} FCFA</span></div>
                        </div>
                    </div>
                    <div class="mb-4">
                        @include('locative.documents._liste', ['documentable' => $caution, 'typeDocument' => 'caution'])
                    </div>
                @empty
                    <p class="text-muted mb-0">Aucune caution enregistrée pour ce locataire.</p>
                @endforelse
            </div>
        </div>

    </div>
    </main>
@endsection

@section('js')
    <script type="module" src="{{ asset('assets/js/app.js') }}"></script>
@endsection
