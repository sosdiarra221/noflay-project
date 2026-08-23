@extends('partials.layouts.master-locative')

@section('title', 'Encaissements | Locative')
@section('title-sub', 'Locative')
@section('pagetitle', 'Historique des encaissements')

@section('content')
    <div id="layout-wrapper">

        <div class="row">
            <div class="col-md-6 col-xxl-3">
                <div class="card">
                    <div class="card-body">
                        <h3 class="text-success">{{ number_format($stats['total'], 0, ',', ' ') }} FCFA</h3>
                        <p class="text-muted mb-0">Total encaissé</p>
                    </div>
                </div>
            </div>
            <div class="col-md-6 col-xxl-3">
                <div class="card">
                    <div class="card-body">
                        <h3>{{ $stats['nombre'] }}</h3>
                        <p class="text-muted mb-0">Paiements enregistrés</p>
                    </div>
                </div>
            </div>
            <div class="col-md-6 col-xxl-3">
                <div class="card">
                    <div class="card-body">
                        <h3 class="text-primary">{{ number_format($stats['moyenne'], 0, ',', ' ') }} FCFA</h3>
                        <p class="text-muted mb-0">Montant moyen</p>
                    </div>
                </div>
            </div>
            <div class="col-md-6 col-xxl-3">
                <div class="card">
                    <div class="card-body">
                        <h3 class="text-danger">{{ $stats['annules'] }}</h3>
                        <p class="text-muted mb-0">Paiements annulés</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-12 mb-4">
                <div class="accordion accordion-icon accordion-primary accordion-border-box" id="filtres_encaissements">
                    <div class="accordion-item">
                        <h2 class="accordion-header">
                            <button class="accordion-button" type="button" data-bs-toggle="collapse"
                                data-bs-target="#filtres_encaissements_body" aria-expanded="true">
                                <i class="bi bi-funnel-fill me-2"></i> Filtres
                            </button>
                        </h2>
                        <div id="filtres_encaissements_body" class="accordion-collapse collapse show" data-bs-parent="#filtres_encaissements">
                            <div class="accordion-body py-5">
                                <form method="GET" class="row g-4">
                                    <div class="col-md-3">
                                        <select class="form-select" name="locataire_id">
                                            <option value="">Tous les locataires</option>
                                            @foreach ($locataires as $locataire)
                                                <option value="{{ $locataire->id }}" @selected(request('locataire_id') == $locataire->id)>{{ $locataire->nom_complet }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-md-3">
                                        <select class="form-select" name="mode_paiement_id">
                                            <option value="">Tous les modes</option>
                                            @foreach ($modesPaiement as $mode)
                                                <option value="{{ $mode->id }}" @selected(request('mode_paiement_id') == $mode->id)>{{ $mode->nom }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-md-3">
                                        <select class="form-select" name="periode">
                                            <option value="">Période personnalisée</option>
                                            <option value="aujourdhui" @selected(request('periode') === 'aujourdhui')>Aujourd'hui</option>
                                            <option value="hier" @selected(request('periode') === 'hier')>Hier</option>
                                            <option value="cette_semaine" @selected(request('periode') === 'cette_semaine')>Cette semaine</option>
                                            <option value="ce_mois" @selected(request('periode') === 'ce_mois')>Ce mois</option>
                                            <option value="cette_annee" @selected(request('periode') === 'cette_annee')>Cette année</option>
                                        </select>
                                    </div>
                                    <div class="col-md-3">
                                        &nbsp;
                                    </div>
                                    <div class="col-md-3">
                                        <select class="form-select" name="mois">
                                            <option value="">Tous les mois</option>
                                            @for ($mois = 1; $mois <= 12; $mois++)
                                                <option value="{{ $mois }}" @selected((string) request('mois') === (string) $mois)>{{ ucfirst(\Carbon\Carbon::createFromDate(2026, $mois, 1)->translatedFormat('F')) }}</option>
                                            @endfor
                                        </select>
                                    </div>
                                    <div class="col-md-3">
                                        <select class="form-select" name="annee">
                                            <option value="">Toutes années</option>
                                            @for ($annee = now()->year - 1; $annee <= now()->year + 1; $annee++)
                                                <option value="{{ $annee }}" @selected((string) request('annee') === (string) $annee)>{{ $annee }}</option>
                                            @endfor
                                        </select>
                                    </div>
                                    <div class="col-md-6 d-flex justify-content-end align-items-end gap-2">
                                        <button class="btn btn-light-primary" type="submit"><i class="ri-equalizer-line me-2"></i>Filtrer</button>
                                        <a href="{{ route('locative.encaissements.index') }}" class="btn btn-light-danger">Réinitialiser</a>
                                    </div>
                                </form>
                                <p class="text-muted fs-12 mt-3 mb-0"><i class="bi bi-info-circle me-1"></i>La « Période » (Aujourd'hui, Hier...) prend le pas sur les filtres Mois / Année si elle est renseignée.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-12">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h6 class="mb-0">Encaissements <span class="badge bg-secondary-subtle text-secondary ms-1">{{ $paiements->count() }}</span></h6>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-box table-responsive">
                            <table class="table text-nowrap table-hover align-middle mb-0">
                                <thead>
                                    <tr>
                                        <th>Numéro</th>
                                        <th>Date</th>
                                        <th>Locataire</th>
                                        <th>Bien</th>
                                        <th>Mode</th>
                                        <th>Montant</th>
                                        <th>Statut</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($paiements as $paiement)
                                        <tr>
                                            <td class="fw-medium">{{ $paiement->numero }}</td>
                                            <td>{{ $paiement->date_paiement->format('d/m/Y') }}</td>
                                            <td>{{ $paiement->echeance->contratLocation->location->locataire->nom_complet }}</td>
                                            <td>{{ $paiement->echeance->contratLocation->bien->titre }}</td>
                                            <td>{{ $paiement->modePaiement->nom ?? '—' }}</td>
                                            <td>{{ number_format($paiement->montant, 0, ',', ' ') }} FCFA</td>
                                            <td>
                                                @if ($paiement->statut === 'annule')
                                                    <span class="badge bg-danger-subtle text-danger">Annulé</span>
                                                @else
                                                    <span class="badge bg-success-subtle text-success">Valide</span>
                                                @endif
                                            </td>
                                            <td>
                                                <div class="hstack gap-2">
                                                    <button type="button" class="btn btn-light-warning icon-btn-sm" data-bs-toggle="modal" data-bs-target="#recuModal{{ $paiement->id }}"><i class="bi bi-receipt"></i></button>
                                                    <a href="{{ route('locative.contrats.show', $paiement->echeance->contratLocation) }}" class="btn btn-light-primary icon-btn-sm"><i class="bi bi-arrow-up-right-circle"></i></a>
                                                </div>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr><td colspan="8" class="text-center text-muted py-5">Aucun encaissement ne correspond à ces filtres.</td></tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        @foreach ($paiements as $paiement)
            <div class="modal fade" id="recuModal{{ $paiement->id }}" tabindex="-1" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title">Reçu de paiement — {{ $paiement->numero }}</h5>
                            <button type="button" class="btn-close icon-btn-sm" data-bs-dismiss="modal" aria-label="Close"><i class="ri-close-large-line fw-semibold"></i></button>
                        </div>
                        <div class="modal-body p-0">
                            <iframe src="{{ route('locative.paiements.apercu', $paiement) }}" style="width: 100%; height: 70vh; border: 0;"></iframe>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-light" data-bs-dismiss="modal">Fermer</button>
                            <a href="{{ route('locative.paiements.recu', $paiement) }}" class="btn btn-primary"><i class="bi bi-download me-1"></i>Télécharger en PDF</a>
                        </div>
                    </div>
                </div>
            </div>
        @endforeach

    </div>
    </main>
@endsection

@section('js')
    <script type="module" src="{{ asset('assets/js/app.js') }}"></script>
@endsection
