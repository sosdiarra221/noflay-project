@extends('partials.layouts.master-locative')

@section('title', 'Encaissements | Locative')
@section('title-sub', 'Locative')
@section('pagetitle', 'Historique des encaissements')

@section('css')
    <link rel="stylesheet" href="{{ asset('assets/libs/choices.js/public/assets/styles/choices.min.css') }}">
@endsection

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
                                <form method="GET" class="row g-4" id="formFiltresEncaissements">
                                    <div class="col-md-3">
                                        <select class="form-select" name="locataire_id" id="filtreLocataireSelect">
                                            <option value="">Tous les locataires</option>
                                            @foreach ($locataires as $locataire)
                                                <option value="{{ $locataire->id }}" @selected(request('locataire_id') == $locataire->id)>{{ $locataire->nom_complet }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-md-3">
                                        <select class="form-select" name="mode_paiement_id" onchange="this.form.submit()">
                                            <option value="">Tous les modes</option>
                                            @foreach ($modesPaiement as $mode)
                                                <option value="{{ $mode->id }}" @selected(request('mode_paiement_id') == $mode->id)>{{ $mode->nom }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-md-3">
                                        <select class="form-select" name="periode" onchange="this.form.submit()">
                                            <option value="">Période rapide...</option>
                                            <option value="aujourdhui" @selected(request('periode') === 'aujourdhui')>Aujourd'hui</option>
                                            <option value="hier" @selected(request('periode') === 'hier')>Hier</option>
                                            <option value="cette_semaine" @selected(request('periode') === 'cette_semaine')>Cette semaine</option>
                                            <option value="ce_mois" @selected(request('periode') === 'ce_mois')>Ce mois</option>
                                            <option value="cette_annee" @selected(request('periode') === 'cette_annee')>Cette année</option>
                                        </select>
                                    </div>
                                    <div class="col-md-3">
                                        <select class="form-select" name="mois_annee" id="filtreMoisAnneeSelect">
                                            <option value="">Tous les mois</option>
                                            @foreach ($periodesDisponibles as $periode)
                                                <option value="{{ $periode['valeur'] }}" @selected(request('mois_annee') === $periode['valeur'])>{{ $periode['libelle'] }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-12 d-flex justify-content-end gap-2">
                                        <a href="{{ route('locative.encaissements.index') }}" class="btn btn-light-danger">Réinitialiser</a>
                                    </div>
                                </form>
                                <p class="text-muted fs-12 mt-3 mb-0"><i class="bi bi-info-circle me-1"></i>Les filtres s'appliquent automatiquement. La « Période » rapide (Aujourd'hui, Hier...) prend le pas sur le filtre Mois / Année si elle est renseignée.</p>
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
                                        @php $contratPaiement = $paiement->contratLocation ?? $paiement->echeance?->contratLocation; @endphp
                                        <tr>
                                            <td class="fw-medium">{{ $paiement->numero }}</td>
                                            <td>{{ $paiement->date_paiement->format('d/m/Y') }}</td>
                                            <td>{{ $contratPaiement?->location?->locataire?->nom_complet ?? '—' }}</td>
                                            <td>{{ $contratPaiement?->bien?->titre ?? '—' }}</td>
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
                                                    @if ($paiement->type !== 'entree')
                                                        <button type="button" class="btn btn-light-warning icon-btn-sm" data-bs-toggle="modal" data-bs-target="#recuModal{{ $paiement->id }}" title="Voir le reçu"><i class="bi bi-receipt"></i></button>
                                                    @endif
                                                    @if ($contratPaiement)
                                                        <a href="{{ route('locative.contrats.show', $contratPaiement) }}" class="btn btn-light-primary icon-btn-sm" title="Voir le contrat"><i class="bi bi-arrow-up-right-circle"></i></a>
                                                    @endif
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

        @foreach ($paiements->where('type', '!=', 'entree') as $paiement)
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
    <script src="{{ asset('assets/libs/choices.js/public/assets/scripts/choices.min.js') }}"></script>
    <script type="module" src="{{ asset('assets/js/app.js') }}"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const form = document.getElementById('formFiltresEncaissements');

            const locataireSelect = new Choices(document.getElementById('filtreLocataireSelect'), {
                searchEnabled: true,
                itemSelectText: '',
                placeholderValue: 'Rechercher un locataire...',
                searchPlaceholderValue: 'Rechercher...',
            });
            document.getElementById('filtreLocataireSelect').addEventListener('change', () => form.submit());

            const moisAnneeSelect = new Choices(document.getElementById('filtreMoisAnneeSelect'), {
                searchEnabled: true,
                itemSelectText: '',
                placeholderValue: 'Rechercher un mois...',
                searchPlaceholderValue: 'Rechercher...',
            });
            document.getElementById('filtreMoisAnneeSelect').addEventListener('change', () => form.submit());
        });
    </script>
@endsection
