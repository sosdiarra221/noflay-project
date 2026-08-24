@extends('partials.layouts.master-locative')

@section('title', 'Loyers | Locative')
@section('title-sub', 'Locative')
@section('pagetitle', 'Loyers & échéances')

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

        <div class="row">
            <div class="col-12 mb-4">
                <div class="accordion accordion-icon accordion-primary accordion-border-box" id="filtres_loyers">
                    <div class="accordion-item">
                        <h2 class="accordion-header">
                            <button class="accordion-button" type="button" data-bs-toggle="collapse"
                                data-bs-target="#filtres_loyers_body" aria-expanded="true">
                                <i class="bi bi-funnel-fill me-2"></i> Filtres
                            </button>
                        </h2>
                        <div id="filtres_loyers_body" class="accordion-collapse collapse show" data-bs-parent="#filtres_loyers">
                            <div class="accordion-body py-5">
                                <form method="GET" class="row g-4" id="formFiltresLoyers">
                                    <div class="col-md-4">
                                        <select class="form-select" name="locataire_id" id="filtreLocataireSelect">
                                            <option value="">Tous les locataires</option>
                                            @foreach ($locataires as $locataire)
                                                <option value="{{ $locataire->id }}" @selected(request('locataire_id') == $locataire->id)>{{ $locataire->nom_complet }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-md-4">
                                        <select class="form-select" name="statut" onchange="this.form.submit()">
                                            <option value="">Tous statuts</option>
                                            @foreach (['a_venir', 'echu', 'partiellement_paye', 'paye', 'en_retard', 'annule'] as $statutOption)
                                                <option value="{{ $statutOption }}" @selected(request('statut') === $statutOption)>{{ str_replace('_', ' ', ucfirst($statutOption)) }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-md-4">
                                        <select class="form-select" name="mois_annee" id="filtreMoisAnneeSelect">
                                            <option value="">Tous les mois</option>
                                            @foreach ($periodesDisponibles as $periode)
                                                <option value="{{ $periode['valeur'] }}" @selected(request('mois_annee') === $periode['valeur'])>{{ $periode['libelle'] }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-12 d-flex justify-content-end gap-2">
                                        <a href="{{ route('locative.echeances.index') }}" class="btn btn-light-danger">Réinitialiser</a>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-12">
                <div class="card">
                    <div class="card-header d-flex flex-wrap justify-content-between align-items-center gap-3">
                        <h6 class="mb-0">Historique des loyers <span class="badge bg-secondary-subtle text-secondary ms-1">{{ $echeances->count() }}</span></h6>
                        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#genererLoyersModal">
                            <i class="bi bi-calendar-plus me-1"></i>Générer les loyers
                        </button>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-box table-responsive">
                            <table class="table text-nowrap align-middle mb-0">
                                <thead>
                                    <tr>
                                        <th>Payé</th>
                                        <th>Période</th>
                                        <th>Locataire</th>
                                        <th>Bien</th>
                                        <th>Attendu</th>
                                        <th>Payé</th>
                                        <th>Statut</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @php $classesE = ['paye' => 'success', 'partiellement_paye' => 'warning', 'en_retard' => 'danger', 'a_venir' => 'secondary', 'echu' => 'danger', 'annule' => 'dark']; @endphp
                                    @forelse ($echeances as $echeance)
                                        <tr>
                                            <td>
                                                @if ($echeance->statut === 'paye')
                                                    <span class="text-success" title="Payé"><i class="bi bi-check-circle-fill fs-5"></i></span>
                                                @elseif ($echeance->statut === 'partiellement_paye')
                                                    <span class="text-warning" title="Partiellement payé"><i class="bi bi-dash-circle-fill fs-5"></i></span>
                                                @else
                                                    <span class="text-danger" title="Non payé"><i class="bi bi-x-circle-fill fs-5"></i></span>
                                                @endif
                                            </td>
                                            <td>{{ \Carbon\Carbon::createFromDate($echeance->annee, $echeance->mois, 1)->translatedFormat('F Y') }}</td>
                                            <td>{{ $echeance->contratLocation->location->locataire->nom_complet }}</td>
                                            <td>{{ $echeance->contratLocation->bien->titre }}</td>
                                            <td>{{ number_format($echeance->montant_attendu, 0, ',', ' ') }} FCFA</td>
                                            <td>{{ number_format($echeance->montant_paye, 0, ',', ' ') }} FCFA</td>
                                            <td>
                                                <span class="badge bg-{{ $classesE[$echeance->statut] ?? 'secondary' }}-subtle text-{{ $classesE[$echeance->statut] ?? 'secondary' }} text-capitalize">{{ str_replace('_', ' ', $echeance->statut) }}</span>
                                            </td>
                                            <td>
                                                <div class="hstack gap-2">
                                                    @if ($echeance->statut !== 'paye' && $echeance->statut !== 'annule')
                                                        <button type="button" class="btn btn-light-success btn-sm" data-bs-toggle="modal" data-bs-target="#encaisserEcheanceModal{{ $echeance->id }}" title="Encaisser le loyer">
                                                            <i class="bi bi-cash-coin me-1"></i>Encaisser
                                                        </button>
                                                    @endif
                                                    @if ($echeance->montant_paye > 0)
                                                        <button type="button" class="btn btn-light-warning btn-sm" data-bs-toggle="modal" data-bs-target="#quittanceModal{{ $echeance->id }}" title="Voir la quittance">
                                                            <i class="bi bi-eye me-1"></i>Aperçu
                                                        </button>
                                                    @else
                                                        <button type="button" class="btn btn-light-secondary btn-sm" disabled title="Aucun paiement enregistré pour cette échéance">
                                                            <i class="bi bi-eye me-1"></i>Aperçu
                                                        </button>
                                                    @endif
                                                </div>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr><td colspan="8" class="text-center text-muted py-5">Aucune échéance ne correspond à ces filtres.</td></tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        @foreach ($echeances as $echeance)
            {{-- Encaisser --}}
            @if ($echeance->statut !== 'paye' && $echeance->statut !== 'annule')
                <div class="modal fade" id="encaisserEcheanceModal{{ $echeance->id }}" tabindex="-1" aria-hidden="true">
                    <div class="modal-dialog modal-dialog-centered" role="document">
                        <div class="modal-content">
                            <form action="{{ route('locative.echeances.encaisser', $echeance) }}" method="POST">
                                @csrf
                                <div class="modal-header">
                                    <h5 class="modal-title">Encaisser — {{ \Carbon\Carbon::createFromDate($echeance->annee, $echeance->mois, 1)->translatedFormat('F Y') }}</h5>
                                    <button type="button" class="btn-close icon-btn-sm" data-bs-dismiss="modal" aria-label="Close"><i class="ri-close-large-line fw-semibold"></i></button>
                                </div>
                                <div class="modal-body">
                                    <p class="text-muted">Montant attendu : <strong>{{ number_format($echeance->montant_attendu, 0, ',', ' ') }} FCFA</strong> — déjà payé : {{ number_format($echeance->montant_paye, 0, ',', ' ') }} FCFA</p>
                                    <div class="row g-3">
                                        <div class="col-12">
                                            <label class="form-label">Montant payé<span class="text-danger ms-1">*</span></label>
                                            <input type="number" step="0.01" min="0.01" class="form-control" name="montant" value="{{ max($echeance->montant_attendu - $echeance->montant_paye, 0) }}" required>
                                        </div>
                                        <div class="col-12">
                                            <label class="form-label">Mode de paiement<span class="text-danger ms-1">*</span></label>
                                            <select class="form-select" name="mode_paiement_id" required>
                                                <option value="">Sélectionner...</option>
                                                @foreach (\App\Models\ModePaiement::where('actif', true)->orderBy('nom')->get() as $mode)
                                                    <option value="{{ $mode->id }}" @selected($echeance->contratLocation->mode_paiement_prefere_id == $mode->id)>{{ $mode->nom }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="col-12">
                                            <label class="form-label">Date du paiement<span class="text-danger ms-1">*</span></label>
                                            <input type="date" class="form-control" name="date_paiement" value="{{ now()->format('Y-m-d') }}" required>
                                        </div>
                                        <div class="col-12">
                                            <label class="form-label">Référence</label>
                                            <input type="text" class="form-control" name="reference" placeholder="Ex: WAV-XXXXXXXX">
                                        </div>
                                        <div class="col-12">
                                            <label class="form-label">Note</label>
                                            <textarea class="form-control" name="note" rows="2"></textarea>
                                        </div>
                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Fermer</button>
                                    <button type="submit" class="btn btn-primary">Encaisser</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            @endif

            {{-- Quittance --}}
            @if ($echeance->montant_paye > 0)
                <div class="modal fade" id="quittanceModal{{ $echeance->id }}" tabindex="-1" aria-hidden="true">
                    <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title">Quittance de loyer — {{ \Carbon\Carbon::createFromDate($echeance->annee, $echeance->mois, 1)->translatedFormat('F Y') }}</h5>
                                <button type="button" class="btn-close icon-btn-sm" data-bs-dismiss="modal" aria-label="Close"><i class="ri-close-large-line fw-semibold"></i></button>
                            </div>
                            <div class="modal-body p-0">
                                <iframe src="{{ route('locative.echeances.quittance', $echeance) }}" style="width: 100%; height: 70vh; border: 0;"></iframe>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-light" data-bs-dismiss="modal">Fermer</button>
                                <a href="{{ route('locative.echeances.quittance-telecharger', $echeance) }}" class="btn btn-primary"><i class="bi bi-download me-1"></i>Télécharger en PDF</a>
                            </div>
                        </div>
                    </div>
                </div>
            @endif
        @endforeach

        {{-- Générer les loyers --}}
        <div class="modal fade" id="genererLoyersModal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered" role="document">
                <div class="modal-content">
                    <form action="{{ route('locative.echeances.generer') }}" method="POST">
                        @csrf
                        <div class="modal-header">
                            <h5 class="modal-title">Générer les loyers</h5>
                            <button type="button" class="btn-close icon-btn-sm" data-bs-dismiss="modal" aria-label="Close"><i class="ri-close-large-line fw-semibold"></i></button>
                        </div>
                        <div class="modal-body">
                            <div class="row g-3">
                                <div class="col-12">
                                    <label class="form-label">Contrat de location<span class="text-danger ms-1">*</span></label>
                                    <select class="form-select" name="contrat_location_id" id="genererContratSelect" required>
                                        <option value="">Sélectionner un contrat...</option>
                                        @foreach ($contrats as $contrat)
                                            <option value="{{ $contrat->id }}">{{ $contrat->location->locataire->nom_complet }} — {{ $contrat->bien->titre }} ({{ $contrat->numero }})</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-12">
                                    <label class="form-label">Année<span class="text-danger ms-1">*</span></label>
                                    <select class="form-select" name="annee" required>
                                        @for ($annee = now()->year - 1; $annee <= now()->year + 2; $annee++)
                                            <option value="{{ $annee }}" @selected($annee === now()->year)>{{ $annee }}</option>
                                        @endfor
                                    </select>
                                </div>
                                <div class="col-12">
                                    <label class="form-label">Mois à générer<span class="text-danger ms-1">*</span></label>
                                    <div class="row g-2">
                                        @for ($mois = 1; $mois <= 12; $mois++)
                                            <div class="col-4">
                                                <div class="form-check">
                                                    <input class="form-check-input" type="checkbox" name="mois[]" value="{{ $mois }}" id="moisGenerer{{ $mois }}">
                                                    <label class="form-check-label" for="moisGenerer{{ $mois }}">{{ ucfirst(\Carbon\Carbon::createFromDate(2026, $mois, 1)->translatedFormat('F')) }}</label>
                                                </div>
                                            </div>
                                        @endfor
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-light" data-bs-dismiss="modal">Fermer</button>
                            <button type="submit" class="btn btn-primary">Générer</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

    </div>
    </main>
@endsection

@section('js')
    <script src="{{ asset('assets/libs/choices.js/public/assets/scripts/choices.min.js') }}"></script>
    <script type="module" src="{{ asset('assets/js/app.js') }}"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const contratSelect = document.getElementById('genererContratSelect');
            if (contratSelect) {
                new Choices(contratSelect, {
                    searchEnabled: true,
                    itemSelectText: '',
                    placeholderValue: 'Rechercher un contrat...',
                    searchPlaceholderValue: 'Rechercher...',
                });
            }

            const form = document.getElementById('formFiltresLoyers');

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
