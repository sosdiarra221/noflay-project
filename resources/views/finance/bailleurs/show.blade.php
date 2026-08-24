@extends('partials.layouts.master-finance')

@section('title', $bailleur->nom_complet.' | Finance')
@section('title-sub', 'Finance')
@section('pagetitle', $bailleur->nom_complet)

@section('content')
    <div id="layout-wrapper">

        <div class="card">
            <div class="card-body d-flex flex-wrap justify-content-between align-items-center gap-3">
                <div>
                    <h5 class="mb-1">{{ $bailleur->nom_complet }}</h5>
                    <p class="text-muted fs-12 mb-0">{{ $bailleur->numero }} @if ($bailleur->telephone) — {{ $bailleur->telephone }} @endif</p>
                </div>
                <a href="{{ route('locative.bailleurs.show', $bailleur) }}" class="btn btn-light-primary btn-sm"><i class="bi bi-person me-1"></i>Dossier locatif complet</a>
            </div>
        </div>

        {{-- BAILLEUR : loyers encaissés / commission agence / travaux / autres dépenses / reversements --}}
        <div class="card">
            <div class="card-header"><h6 class="card-title mb-0 fw-semibold"><i class="bi bi-building me-1"></i>Compte bailleur</h6></div>
            <div class="card-body">
                <div class="row g-3 mb-4">
                    <div class="col-md-2">
                        <div class="border rounded-3 p-3 h-100">
                            <h6 class="mb-0">{{ number_format($compteBailleur['loyers_encaisses'], 0, ',', ' ') }}</h6>
                            <p class="text-muted fs-11 mb-0">Loyers encaissés</p>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="border rounded-3 p-3 h-100">
                            <h6 class="mb-0 text-primary">- {{ number_format($compteBailleur['commission_agence'], 0, ',', ' ') }}</h6>
                            <p class="text-muted fs-11 mb-0">Commission agence</p>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="border rounded-3 p-3 h-100">
                            <h6 class="mb-0 text-danger">- {{ number_format($compteBailleur['travaux_depenses'], 0, ',', ' ') }}</h6>
                            <p class="text-muted fs-11 mb-0">Travaux / dépenses payées</p>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="border rounded-3 p-3 h-100">
                            <h6 class="mb-0 text-warning">{{ number_format($compteBailleur['depenses_en_attente'], 0, ',', ' ') }}</h6>
                            <p class="text-muted fs-11 mb-0">Dépenses en attente</p>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="border rounded-3 p-3 h-100">
                            <h6 class="mb-0 text-info">{{ number_format($compteBailleur['deja_reverse'], 0, ',', ' ') }}</h6>
                            <p class="text-muted fs-11 mb-0">Déjà reversé</p>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="border rounded-3 p-3 h-100 border-success">
                            <h6 class="mb-0 text-success">{{ number_format($compteBailleur['a_reverser'], 0, ',', ' ') }}</h6>
                            <p class="text-muted fs-11 mb-0">Solde à reverser (historique)</p>
                        </div>
                    </div>
                </div>

                @if ($calculMoisCourant)
                    <div class="alert alert-light border fs-13 mb-4">
                        <strong>{{ ucfirst(now()->translatedFormat('F Y')) }}</strong> —
                        Encaissé {{ number_format($calculMoisCourant['encaisse'], 0, ',', ' ') }} FCFA,
                        commission {{ number_format($calculMoisCourant['frais_gestion'], 0, ',', ' ') }} FCFA,
                        dépenses {{ number_format($calculMoisCourant['depenses'], 0, ',', ' ') }} FCFA
                        → net à reverser ce mois : <strong>{{ number_format($calculMoisCourant['net_a_reverser'], 0, ',', ' ') }} FCFA</strong>.
                        <a href="{{ route('finance.reversements.index') }}" class="ms-2">Gérer les reversements</a>
                    </div>
                @endif

                <ul class="nav nav-pills mb-3" role="tablist">
                    <li class="nav-item"><button class="nav-link active" data-bs-toggle="tab" data-bs-target="#paiements-bailleur-pane">Loyers encaissés</button></li>
                    <li class="nav-item"><button class="nav-link" data-bs-toggle="tab" data-bs-target="#depenses-bailleur-pane">Travaux &amp; dépenses</button></li>
                    <li class="nav-item"><button class="nav-link" data-bs-toggle="tab" data-bs-target="#reversements-bailleur-pane">Reversements</button></li>
                </ul>
                <div class="tab-content">
                    <div class="tab-pane fade show active" id="paiements-bailleur-pane">
                        <div class="table-box table-responsive">
                            <table class="table text-nowrap align-middle mb-0">
                                <thead><tr><th>Date</th><th>Numéro</th><th>Bien</th><th>Montant</th><th>Commission</th><th>Net bailleur</th></tr></thead>
                                <tbody>
                                    @forelse ($paiementsLoyer as $paiement)
                                        <tr>
                                            <td>{{ $paiement->date_paiement->format('d/m/Y') }}</td>
                                            <td class="fw-medium">{{ $paiement->numero }}</td>
                                            <td>{{ ($paiement->contratLocation ?? $paiement->echeance?->contratLocation)->bien->titre ?? '—' }}</td>
                                            <td>{{ number_format($paiement->montant, 0, ',', ' ') }} FCFA</td>
                                            <td class="text-primary">{{ number_format($paiement->part_commission_agence ?? 0, 0, ',', ' ') }} FCFA</td>
                                            <td class="text-success">{{ number_format($paiement->part_bailleur ?? 0, 0, ',', ' ') }} FCFA</td>
                                        </tr>
                                    @empty
                                        <tr><td colspan="6" class="text-center text-muted py-4">Aucun encaissement.</td></tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="tab-pane fade" id="depenses-bailleur-pane">
                        <div class="table-box table-responsive">
                            <table class="table text-nowrap align-middle mb-0">
                                <thead><tr><th>Numéro</th><th>Bien</th><th>Catégorie</th><th>Montant</th><th>Statut</th></tr></thead>
                                <tbody>
                                    @forelse ($depenses as $depense)
                                        <tr>
                                            <td><a href="{{ route('finance.depenses.show', $depense) }}" class="fw-medium text-reset">{{ $depense->numero }}</a></td>
                                            <td>{{ $depense->bien->titre ?? '—' }}</td>
                                            <td>{{ $depense->categorie->nom ?? '—' }}</td>
                                            <td>{{ number_format($depense->montantImpute(), 0, ',', ' ') }} FCFA</td>
                                            <td>{{ $depense->libelleStatut() }}</td>
                                        </tr>
                                    @empty
                                        <tr><td colspan="5" class="text-center text-muted py-4">Aucune dépense.</td></tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="tab-pane fade" id="reversements-bailleur-pane">
                        <div class="table-box table-responsive">
                            <table class="table text-nowrap align-middle mb-0">
                                <thead><tr><th>Numéro</th><th>Période</th><th>Net</th><th>Statut</th></tr></thead>
                                <tbody>
                                    @forelse ($reversements as $reversement)
                                        <tr>
                                            <td class="fw-medium">{{ $reversement->numero }}</td>
                                            <td>{{ ucfirst(\Carbon\Carbon::createFromDate($reversement->periode_annee, $reversement->periode_mois, 1)->translatedFormat('F Y')) }}</td>
                                            <td>{{ number_format($reversement->montant_net, 0, ',', ' ') }} FCFA</td>
                                            <td>
                                                @if ($reversement->statut === 'verse')
                                                    <span class="badge bg-success-subtle text-success">Versé</span>
                                                @else
                                                    <span class="badge bg-warning-subtle text-warning">À verser</span>
                                                @endif
                                            </td>
                                        </tr>
                                    @empty
                                        <tr><td colspan="4" class="text-center text-muted py-4">Aucun reversement.</td></tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- AGENCE (aperçu contextuel généré via ce bailleur) --}}
        <div class="card">
            <div class="card-header"><h6 class="card-title mb-0 fw-semibold"><i class="bi bi-briefcase me-1"></i>Revenus agence générés via ce bailleur</h6></div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-md-6">
                        <div class="border rounded-3 p-3 h-100">
                            <h6 class="mb-0">{{ number_format($compteAgence['frais_entree'], 0, ',', ' ') }} FCFA</h6>
                            <p class="text-muted fs-12 mb-0">Frais d'agence à l'entrée (signatures de location)</p>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="border rounded-3 p-3 h-100">
                            <h6 class="mb-0">{{ number_format($compteAgence['commissions'], 0, ',', ' ') }} FCFA</h6>
                            <p class="text-muted fs-12 mb-0">Commissions de gestion cumulées</p>
                        </div>
                    </div>
                </div>
                <p class="text-muted fs-11 mt-3 mb-0">Ces fonds appartiennent à l'agence et ne sont jamais reversés au bailleur. Pour la TVA, les autres revenus et les dépenses propres de l'agence, voir le <a href="{{ route('finance.dashboard') }}">tableau de bord Finance</a>.</p>
            </div>
        </div>

    </div>
    </main>
@endsection

@section('js')
    <script type="module" src="{{ asset('assets/js/app.js') }}"></script>
@endsection
