@extends('partials.layouts.master-locative')

@section('title', 'Versements aux bailleurs | Locative')
@section('title-sub', 'Locative')
@section('pagetitle', 'Versements aux bailleurs')

@section('css')
    <link rel="stylesheet" href="{{ asset('assets/libs/choices.js/public/assets/styles/choices.min.css') }}">
@endsection

@section('content')
    <div id="layout-wrapper">

        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif
        @if ($errors->any())
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <ul class="mb-0 ps-3">
                    @foreach ($errors->all() as $erreur)
                        <li>{{ $erreur }}</li>
                    @endforeach
                </ul>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <div class="card">
            <div class="card-body">
                <form method="GET" class="row g-3 align-items-end">
                    <div class="col-md-8">
                        <label class="form-label">Sélectionner un bailleur</label>
                        <select class="form-select" name="bailleur_id" id="selectBailleurVersement">
                            <option value="">Rechercher un bailleur...</option>
                            @foreach ($bailleurs as $b)
                                <option value="{{ $b->id }}" @selected($bailleur && $bailleur->id === $b->id)>{{ $b->nom_complet }} — {{ $b->numero }}</option>
                            @endforeach
                        </select>
                    </div>
                    @if ($bailleur)
                        <div class="col-md-4">
                            <a href="{{ route('locative.versements.index') }}" class="btn btn-light-danger w-100">Changer de bailleur</a>
                        </div>
                    @endif
                </form>
            </div>
        </div>

        @if (! $bailleur)
            <div class="card">
                <div class="card-body text-center py-8">
                    <i class="bi bi-search text-muted" style="font-size: 2.5rem;"></i>
                    <p class="text-muted mt-3 mb-0">Sélectionnez un bailleur ci-dessus pour afficher automatiquement sa fiche financière et enregistrer un versement.</p>
                </div>
            </div>
        @else
            {{-- Fiche de versement, présentée sous forme de facture --}}
            <div class="card overflow-hidden">
                <div class="p-5" style="background: linear-gradient(135deg, #0b2e59, #14487d);">
                    <div class="d-flex flex-wrap justify-content-between align-items-start gap-4">
                        <div>
                            <h4 class="mb-1 text-white">{{ $reglage->nom_societe ?: config('app.name') }}</h4>
                            <p class="mb-0 text-white-50 fs-13">{{ $reglage->adresse ?: '—' }}</p>
                            <p class="mb-0 text-white-50 fs-13">{{ $reglage->telephone ?: '' }} {{ $reglage->email ? '· '.$reglage->email : '' }}</p>
                        </div>
                        <div class="text-md-end">
                            <h5 class="mb-1 text-white text-uppercase">Fiche de versement bailleur</h5>
                            <p class="mb-0 text-white-50 fs-13">Édité le {{ now()->versionLongue() }}</p>
                            <p class="mb-0 text-white-50 fs-13">Période en cours : {{ ucfirst(now()->translatedFormat('F Y')) }}</p>
                        </div>
                    </div>
                </div>
                <div class="card-body p-5">
                    <div class="row g-4 mb-5">
                        <div class="col-md-6">
                            <p class="text-muted fs-11 text-uppercase mb-1">Bailleur</p>
                            <h5 class="mb-1"><a href="{{ route('locative.bailleurs.show', $bailleur) }}" class="text-reset">{{ $bailleur->nom_complet }}</a></h5>
                            <p class="text-muted fs-12 mb-0">{{ $bailleur->numero }} @if ($bailleur->telephone) — {{ $bailleur->telephone }} @endif</p>
                        </div>
                        <div class="col-md-6 text-md-end">
                            <p class="text-muted fs-11 text-uppercase mb-1">Solde net à verser</p>
                            <h2 class="mb-0 {{ $compte['a_reverser'] > 0 ? 'text-success' : 'text-muted' }}">{{ number_format($compte['a_reverser'], 0, ',', ' ') }} FCFA</h2>
                            @if ($compte['arriere_anterieur'] > 0)
                                <span class="badge bg-danger-subtle text-danger">dont {{ number_format($compte['arriere_anterieur'], 0, ',', ' ') }} FCFA d'arriéré</span>
                            @endif
                        </div>
                    </div>

                    <div class="table-box table-responsive mb-4">
                        <table class="table align-middle mb-0">
                            <thead>
                                <tr><th>Désignation</th><th class="text-end">Montant (FCFA)</th></tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>Loyers encaissés pour son compte</td>
                                    <td class="text-end">+ {{ number_format($compte['loyers_encaisses'], 0, ',', ' ') }}</td>
                                </tr>
                                <tr>
                                    <td>Commission de gestion de l'agence</td>
                                    <td class="text-end text-primary">- {{ number_format($compte['commission_agence'], 0, ',', ' ') }}</td>
                                </tr>
                                <tr>
                                    <td>Travaux / dépenses mis à sa charge</td>
                                    <td class="text-end text-danger">- {{ number_format($compte['travaux_depenses'], 0, ',', ' ') }}</td>
                                </tr>
                                <tr>
                                    <td>Déjà versé (versements + reversements antérieurs)</td>
                                    <td class="text-end text-info">- {{ number_format($compte['deja_reverse'], 0, ',', ' ') }}</td>
                                </tr>
                                <tr class="table-active">
                                    <td class="fw-semibold">Solde net à verser</td>
                                    <td class="text-end fw-semibold fs-15">{{ number_format($compte['a_reverser'], 0, ',', ' ') }}</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <div class="d-flex flex-wrap justify-content-between align-items-center gap-3">
                        <p class="text-muted fs-12 mb-0">
                            Dont {{ number_format($compte['du_mois_courant'], 0, ',', ' ') }} FCFA au titre du mois en cours
                            @if ($compte['arriere_anterieur'] > 0)
                                et <span class="text-danger fw-medium">{{ number_format($compte['arriere_anterieur'], 0, ',', ' ') }} FCFA</span> d'arriéré non versé les mois précédents.
                            @else
                                et aucun arriéré des mois précédents.
                            @endif
                        </p>
                        <button type="button" class="btn btn-primary flex-shrink-0" data-bs-toggle="modal" data-bs-target="#payerVersementModal">
                            <i class="bi bi-cash-coin me-1"></i>Faire le paiement
                        </button>
                    </div>
                </div>
            </div>

            <div class="row g-4">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header"><h6 class="mb-0">Contrats — TVA / TOM appliquées</h6></div>
                        <div class="card-body p-0">
                            <div class="table-box table-responsive">
                                <table class="table text-nowrap align-middle mb-0">
                                    <thead><tr><th>Bien</th><th>Loyer mensuel</th><th>TVA</th><th>TOM</th><th>Statut</th></tr></thead>
                                    <tbody>
                                        @forelse ($contratsAvecTaxes as $contrat)
                                            <tr>
                                                <td>{{ $contrat->bien->titre ?? '—' }}</td>
                                                <td>{{ number_format($contrat->loyer_mensuel, 0, ',', ' ') }} FCFA</td>
                                                <td>
                                                    @if ($contrat->appliquer_tva)
                                                        <span class="badge bg-primary-subtle text-primary">Activée</span>
                                                    @else
                                                        <span class="badge bg-light-subtle text-body">Non activée</span>
                                                    @endif
                                                </td>
                                                <td>
                                                    @if ($contrat->appliquer_tom)
                                                        <span class="badge bg-secondary-subtle text-secondary">Activée</span>
                                                    @else
                                                        <span class="badge bg-light-subtle text-body">Non activée</span>
                                                    @endif
                                                </td>
                                                <td><span class="badge bg-secondary-subtle text-secondary text-capitalize">{{ str_replace('_', ' ', $contrat->statut) }}</span></td>
                                            </tr>
                                        @empty
                                            <tr><td colspan="5" class="text-center text-muted py-5">Aucun contrat pour ce bailleur.</td></tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    <div class="card">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h6 class="mb-0">Dépenses à sa charge</h6>
                            <span class="fw-medium text-danger">Total payé : {{ number_format($compte['travaux_depenses'], 0, ',', ' ') }} FCFA</span>
                        </div>
                        <div class="card-body p-0">
                            <div class="table-box table-responsive">
                                <table class="table text-nowrap align-middle mb-0">
                                    <thead><tr><th>Numéro</th><th>Bien</th><th>Catégorie</th><th>Montant</th><th>Statut</th></tr></thead>
                                    <tbody>
                                        @forelse ($depenses as $depense)
                                            <tr>
                                                <td class="fw-medium">
                                                    @can('finance.consulter')
                                                        <a href="{{ route('finance.depenses.show', $depense) }}" class="text-reset">{{ $depense->numero }}</a>
                                                    @else
                                                        {{ $depense->numero }}
                                                    @endcan
                                                </td>
                                                <td>{{ $depense->bien->titre ?? '—' }}</td>
                                                <td>{{ $depense->categorie->nom ?? '—' }}</td>
                                                <td>{{ number_format($depense->montantImpute(), 0, ',', ' ') }} FCFA</td>
                                                <td>{{ $depense->libelleStatut() }}</td>
                                            </tr>
                                        @empty
                                            <tr><td colspan="5" class="text-center text-muted py-5">Aucune dépense pour ce bailleur.</td></tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    <div class="card">
                        <div class="card-header"><h6 class="mb-0">Historique des versements <span class="badge bg-secondary-subtle text-secondary ms-1">{{ $versements->count() }}</span></h6></div>
                        <div class="card-body p-0">
                            <div class="table-box table-responsive">
                                <table class="table text-nowrap align-middle mb-0">
                                    <thead><tr><th>Numéro</th><th>Date</th><th>Type</th><th>Montant</th><th>Mode</th><th>Effectué par</th><th></th></tr></thead>
                                    <tbody>
                                        @forelse ($versements as $versement)
                                            <tr>
                                                <td class="fw-medium">{{ $versement->numero }}</td>
                                                <td>{{ $versement->date_versement->format('d/m/Y') }}</td>
                                                <td>
                                                    @if ($versement->type === 'avance')
                                                        <span class="badge bg-info-subtle text-info">Avance</span>
                                                    @else
                                                        <span class="badge bg-success-subtle text-success">Normal</span>
                                                    @endif
                                                </td>
                                                <td class="fw-medium">{{ number_format($versement->montant, 0, ',', ' ') }} FCFA</td>
                                                <td>{{ $versement->modePaiement->nom ?? '—' }}</td>
                                                <td>{{ $versement->effectuePar->name ?? '—' }}</td>
                                                <td>
                                                    @can('locative.operations-sensibles')
                                                        <button type="button" class="btn btn-light-danger icon-btn-sm" data-bs-toggle="modal" data-bs-target="#supprimerVersementModal{{ $versement->id }}"><i class="ri-delete-bin-line"></i></button>
                                                    @endcan
                                                </td>
                                            </tr>
                                        @empty
                                            <tr><td colspan="7" class="text-center text-muted py-5">Aucun versement enregistré pour ce bailleur.</td></tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

            </div>

            {{-- Faire le paiement --}}
            <div class="modal fade" id="payerVersementModal" tabindex="-1" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered" role="document">
                    <div class="modal-content">
                        <form action="{{ route('locative.versements.store') }}" method="POST">
                            @csrf
                            <input type="hidden" name="bailleur_id" value="{{ $bailleur->id }}">
                            <div class="modal-header">
                                <h5 class="modal-title">Faire le paiement — {{ $bailleur->nom_complet }}</h5>
                                <button type="button" class="btn-close icon-btn-sm" data-bs-dismiss="modal" aria-label="Close"><i class="ri-close-large-line fw-semibold"></i></button>
                            </div>
                            <div class="modal-body">
                                <div class="row g-3">
                                    <div class="col-12">
                                        <label class="form-label">Montant payé (FCFA)<span class="text-danger ms-1">*</span></label>
                                        <input type="number" step="0.01" min="0.01" class="form-control" name="montant" value="{{ old('montant', $compte['a_reverser'] > 0 ? $compte['a_reverser'] : '') }}" required>
                                        <div class="fs-11 text-muted mt-1">Solde net à verser : {{ number_format($compte['a_reverser'], 0, ',', ' ') }} FCFA. Vous pouvez indiquer un montant inférieur (paiement partiel / avance).</div>
                                    </div>
                                    <div class="col-12">
                                        <label class="form-label">Type<span class="text-danger ms-1">*</span></label>
                                        <select class="form-select" name="type" required>
                                            <option value="normal">Versement normal</option>
                                            <option value="avance">Avance</option>
                                        </select>
                                    </div>
                                    <div class="col-12">
                                        <label class="form-label">Date du versement<span class="text-danger ms-1">*</span></label>
                                        <input type="date" class="form-control" name="date_versement" value="{{ now()->format('Y-m-d') }}" required>
                                    </div>
                                    <div class="col-12">
                                        <label class="form-label">Mode de paiement<span class="text-danger ms-1">*</span></label>
                                        <select class="form-select" name="mode_paiement_id" required>
                                            <option value="">Sélectionner...</option>
                                            @foreach ($modesPaiement as $mode)
                                                <option value="{{ $mode->id }}">{{ $mode->nom }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-12">
                                        <label class="form-label">Référence</label>
                                        <input type="text" class="form-control" name="reference" placeholder="Ex: VIR-XXXXXXXX">
                                    </div>
                                    <div class="col-12">
                                        <label class="form-label">Notes</label>
                                        <textarea class="form-control" name="notes" rows="2"></textarea>
                                    </div>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-light" data-bs-dismiss="modal">Annuler</button>
                                <button type="submit" class="btn btn-primary"><i class="bi bi-check-circle me-1"></i>Confirmer le paiement</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            @foreach ($versements as $versement)
                <div class="modal fade" id="supprimerVersementModal{{ $versement->id }}" tabindex="-1" aria-hidden="true">
                    <div class="modal-dialog modal-dialog-centered" role="document">
                        <div class="modal-content">
                            <form action="{{ route('locative.versements.destroy', $versement) }}" method="POST">
                                @csrf
                                @method('DELETE')
                                <div class="modal-header">
                                    <h5 class="modal-title">Supprimer le versement {{ $versement->numero }}</h5>
                                    <button type="button" class="btn-close icon-btn-sm" data-bs-dismiss="modal" aria-label="Close"><i class="ri-close-large-line fw-semibold"></i></button>
                                </div>
                                <div class="modal-body">
                                    <p class="text-muted">Ce versement de <strong>{{ number_format($versement->montant, 0, ',', ' ') }} FCFA</strong> sera retiré du solde déjà versé au bailleur.</p>
                                    <label class="form-label">Motif de suppression<span class="text-danger ms-1">*</span></label>
                                    <textarea class="form-control" name="motif_suppression" rows="2" required></textarea>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Annuler</button>
                                    <button type="submit" class="btn btn-danger">Confirmer la suppression</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            @endforeach
        @endif

    </div>
    </main>
@endsection

@section('js')
    <script src="{{ asset('assets/libs/choices.js/public/assets/scripts/choices.min.js') }}"></script>
    <script type="module" src="{{ asset('assets/js/app.js') }}"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const select = document.getElementById('selectBailleurVersement');
            new Choices(select, {
                searchEnabled: true,
                itemSelectText: '',
                placeholderValue: 'Rechercher un bailleur...',
                searchPlaceholderValue: 'Rechercher...',
            });
            select.addEventListener('change', () => select.closest('form').submit());
        });
    </script>
@endsection
