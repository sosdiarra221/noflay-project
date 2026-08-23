@extends('partials.layouts.master-locative')

@section('title', $contrat->numero.' | Locative')
@section('title-sub', 'Contrats de location')
@section('pagetitle', $contrat->numero)

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
                <div class="d-flex flex-wrap justify-content-between align-items-start gap-3">
                    <div>
                        <h5 class="mb-1">{{ $contrat->numero }} <span class="badge bg-success-subtle text-success text-capitalize ms-1">{{ $contrat->statut }}</span></h5>
                        <p class="text-muted mb-0">
                            {{ $contrat->bien->titre }} — {{ $contrat->location->locataire->nom_complet }} — {{ number_format($contrat->loyer_mensuel, 0, ',', ' ') }} FCFA / mois
                        </p>
                    </div>
                    <div class="d-flex gap-2">
                        <button type="button" class="btn btn-light-info" data-bs-toggle="modal" data-bs-target="#bailContratModal"><i class="bi bi-file-earmark-pdf me-1"></i>Contrat de bail</button>
                        <button type="button" class="btn btn-light-primary" data-bs-toggle="modal" data-bs-target="#editContratModal"><i class="bi bi-pencil-square me-1"></i>Modifier</button>
                        <button type="button" class="btn btn-light-warning" data-bs-toggle="modal" data-bs-target="#genererLoyersModal"><i class="bi bi-calendar-plus me-1"></i>Générer les loyers</button>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-12">
                <div class="mb-6">
                    <ul class="nav nav-pills" role="tablist">
                        <li class="nav-item"><button class="nav-link active" data-bs-toggle="tab" data-bs-target="#infos-pane" type="button">Vue générale</button></li>
                        <li class="nav-item"><button class="nav-link" data-bs-toggle="tab" data-bs-target="#echeances-pane" type="button">Échéances</button></li>
                        <li class="nav-item"><button class="nav-link" data-bs-toggle="tab" data-bs-target="#documents-pane" type="button">Documents</button></li>
                    </ul>
                </div>

                <div class="tab-content">
                    <div class="tab-pane fade show active" id="infos-pane">
                        <div class="row g-4">
                            <div class="col-lg-6">
                                <div class="card">
                                    <div class="card-header"><h6 class="card-action-title mb-0">Contrat</h6></div>
                                    <div class="card-body">
                                        <div class="row mb-3"><div class="col-5 text-muted">Bien</div><div class="col-7 fw-medium">{{ $contrat->bien->titre }}</div></div>
                                        <div class="row mb-3"><div class="col-5 text-muted">Bailleur</div><div class="col-7 fw-medium">{{ $contrat->bailleur->nom_complet }}</div></div>
                                        <div class="row mb-3"><div class="col-5 text-muted">Locataire</div><div class="col-7 fw-medium">{{ $contrat->location->locataire->nom_complet }}</div></div>
                                        <div class="row"><div class="col-5 text-muted">Période</div><div class="col-7 fw-medium">{{ $contrat->date_debut->format('d/m/Y') }} @if ($contrat->date_fin) → {{ $contrat->date_fin->format('d/m/Y') }} @endif</div></div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-6">
                                <div class="card">
                                    <div class="card-header"><h6 class="card-action-title mb-0">Conditions financières</h6></div>
                                    <div class="card-body">
                                        <div class="row mb-3"><div class="col-5 text-muted">Loyer mensuel</div><div class="col-7 fw-medium">{{ number_format($contrat->loyer_mensuel, 0, ',', ' ') }} FCFA</div></div>
                                        <div class="row mb-3"><div class="col-5 text-muted">Charges</div><div class="col-7 fw-medium">{{ number_format($contrat->charges, 0, ',', ' ') }} FCFA</div></div>
                                        <div class="row mb-3"><div class="col-5 text-muted">Dépôt de garantie</div><div class="col-7 fw-medium">{{ number_format($contrat->depot_garantie, 0, ',', ' ') }} FCFA</div></div>
                                        <div class="row"><div class="col-5 text-muted">Jour d'échéance</div><div class="col-7 fw-medium">{{ $contrat->jour_echeance }}</div></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="tab-pane fade" id="echeances-pane">
                        <div class="card">
                            <div class="card-body p-0">
                                <div class="table-box table-responsive">
                                    <table class="table text-nowrap align-middle mb-0">
                                        <thead>
                                            <tr><th>Période</th><th>Échéance</th><th>Attendu</th><th>Payé</th><th>Reste</th><th>Statut</th><th>Actions</th></tr>
                                        </thead>
                                        <tbody>
                                            @forelse ($contrat->echeances->sortBy('date_echeance') as $echeance)
                                                <tr>
                                                    <td>{{ \Carbon\Carbon::createFromDate($echeance->annee, $echeance->mois, 1)->translatedFormat('F Y') }}</td>
                                                    <td>{{ $echeance->date_echeance->format('d/m/Y') }}</td>
                                                    <td>{{ number_format($echeance->montant_attendu, 0, ',', ' ') }} FCFA</td>
                                                    <td>{{ number_format($echeance->montant_paye, 0, ',', ' ') }} FCFA</td>
                                                    <td>{{ number_format(max($echeance->montant_attendu - $echeance->montant_paye, 0), 0, ',', ' ') }} FCFA</td>
                                                    <td>
                                                        @php
                                                            $classesE = ['paye' => 'success', 'partiellement_paye' => 'warning', 'en_retard' => 'danger', 'a_venir' => 'secondary', 'annule' => 'dark'];
                                                        @endphp
                                                        <span class="badge bg-{{ $classesE[$echeance->statut] ?? 'secondary' }}-subtle text-{{ $classesE[$echeance->statut] ?? 'secondary' }} text-capitalize">{{ str_replace('_', ' ', $echeance->statut) }}</span>
                                                    </td>
                                                    <td>
                                                        @php $dernierPaiementValide = $echeance->paiements->where('statut', 'valide')->last(); @endphp
                                                        <div class="hstack gap-2">
                                                            @if ($echeance->statut !== 'paye' && $echeance->statut !== 'annule')
                                                                <button type="button" class="btn btn-light-success btn-sm" data-bs-toggle="modal" data-bs-target="#encaisserModal{{ $echeance->id }}">Encaisser</button>
                                                            @endif
                                                            @if ($dernierPaiementValide)
                                                                <a href="{{ route('locative.paiements.recu', $dernierPaiementValide) }}" class="btn btn-light-info icon-btn-sm"><i class="bi bi-receipt"></i></a>
                                                                @can('locative.operations-sensibles')
                                                                    <button type="button" class="btn btn-light-danger icon-btn-sm" data-bs-toggle="modal" data-bs-target="#annulerPaiementModal{{ $dernierPaiementValide->id }}"><i class="bi bi-x-circle"></i></button>
                                                                @endcan
                                                            @endif
                                                        </div>
                                                    </td>
                                                </tr>
                                            @empty
                                                <tr><td colspan="7" class="text-center text-muted py-5">Aucune échéance générée pour ce contrat.</td></tr>
                                            @endforelse
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="tab-pane fade" id="documents-pane">
                        @include('locative.documents._liste', ['documentable' => $contrat, 'typeDocument' => 'contrat'])
                    </div>
                </div>
            </div>
        </div>

        @foreach ($contrat->echeances->sortBy('date_echeance') as $echeance)
            @if ($echeance->statut !== 'paye' && $echeance->statut !== 'annule')
                <div class="modal fade" id="encaisserModal{{ $echeance->id }}" tabindex="-1" aria-hidden="true">
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
                                                    <option value="{{ $mode->id }}" @selected($contrat->mode_paiement_prefere_id == $mode->id)>{{ $mode->nom }}</option>
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
        @endforeach

        @foreach ($contrat->echeances as $echeance)
            @php $dernierPaiementValide = $echeance->paiements->where('statut', 'valide')->last(); @endphp
            @if ($dernierPaiementValide)
                <div class="modal fade" id="annulerPaiementModal{{ $dernierPaiementValide->id }}" tabindex="-1" aria-hidden="true">
                    <div class="modal-dialog modal-dialog-centered" role="document">
                        <div class="modal-content">
                            <form action="{{ route('locative.paiements.annuler', $dernierPaiementValide) }}" method="POST">
                                @csrf
                                <div class="modal-header">
                                    <h5 class="modal-title">Annuler le paiement {{ $dernierPaiementValide->numero }}</h5>
                                    <button type="button" class="btn-close icon-btn-sm" data-bs-dismiss="modal" aria-label="Close"><i class="ri-close-large-line fw-semibold"></i></button>
                                </div>
                                <div class="modal-body">
                                    <div class="alert alert-warning">
                                        <i class="bi bi-exclamation-triangle me-1"></i>
                                        Le paiement de {{ number_format($dernierPaiementValide->montant, 0, ',', ' ') }} FCFA sera annulé et l'échéance recalculée. Un motif est obligatoire.
                                    </div>
                                    <label class="form-label">Motif de l'annulation<span class="text-danger ms-1">*</span></label>
                                    <textarea class="form-control" name="motif_annulation" rows="2" required></textarea>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Fermer</button>
                                    <button type="submit" class="btn btn-danger">Confirmer l'annulation</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            @endif
        @endforeach

        <!-- Générer les loyers modal -->
        <div class="modal fade" id="genererLoyersModal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered" role="document">
                <div class="modal-content">
                    <form action="{{ route('locative.contrats.generer-loyers', $contrat) }}" method="POST">
                        @csrf
                        <div class="modal-header">
                            <h5 class="modal-title">Générer les loyers</h5>
                            <button type="button" class="btn-close icon-btn-sm" data-bs-dismiss="modal" aria-label="Close"><i class="ri-close-large-line fw-semibold"></i></button>
                        </div>
                        <div class="modal-body">
                            <div class="row g-3">
                                <div class="col-12">
                                    <label class="form-label">Année<span class="text-danger ms-1">*</span></label>
                                    <select class="form-select" name="annee" required>
                                        @for ($annee = now()->year - 1; $annee <= now()->year + 2; $annee++)
                                            <option value="{{ $annee }}" @selected($annee === now()->year)>{{ $annee }}</option>
                                        @endfor
                                    </select>
                                </div>
                                <div class="col-12">
                                    <label class="form-label">Mois<span class="text-danger ms-1">*</span></label>
                                    <div class="row">
                                        @foreach (['Janvier', 'Février', 'Mars', 'Avril', 'Mai', 'Juin', 'Juillet', 'Août', 'Septembre', 'Octobre', 'Novembre', 'Décembre'] as $index => $nomMois)
                                            <div class="col-6">
                                                <div class="form-check">
                                                    <input class="form-check-input" type="checkbox" name="mois[]" value="{{ $index + 1 }}" id="mois{{ $index + 1 }}">
                                                    <label class="form-check-label" for="mois{{ $index + 1 }}">{{ $nomMois }}</label>
                                                </div>
                                            </div>
                                        @endforeach
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

        <!-- Modifier contrat modal -->
        <div class="modal fade" id="editContratModal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
                <div class="modal-content">
                    <form action="{{ route('locative.contrats.update', $contrat) }}" method="POST">
                        @csrf
                        @method('PUT')
                        <div class="modal-header">
                            <h5 class="modal-title">Modifier le contrat</h5>
                            <button type="button" class="btn-close icon-btn-sm" data-bs-dismiss="modal" aria-label="Close"><i class="ri-close-large-line fw-semibold"></i></button>
                        </div>
                        <div class="modal-body">
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label">Date de fin</label>
                                    <input type="date" class="form-control" name="date_fin" value="{{ $contrat->date_fin?->format('Y-m-d') }}">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Statut</label>
                                    <select class="form-select" name="statut">
                                        @foreach (['actif', 'suspendu', 'expire', 'resilie', 'archive'] as $statutOption)
                                            <option value="{{ $statutOption }}" @selected($contrat->statut === $statutOption)>{{ ucfirst($statutOption) }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">Loyer mensuel</label>
                                    <input type="number" step="0.01" min="0" class="form-control" name="loyer_mensuel" value="{{ $contrat->loyer_mensuel }}" required>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">Charges</label>
                                    <input type="number" step="0.01" min="0" class="form-control" name="charges" value="{{ $contrat->charges }}">
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">Dépôt de garantie</label>
                                    <input type="number" step="0.01" min="0" class="form-control" name="depot_garantie" value="{{ $contrat->depot_garantie }}">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Jour d'échéance</label>
                                    <input type="number" min="1" max="28" class="form-control" name="jour_echeance" value="{{ $contrat->jour_echeance }}" required>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Mode de paiement préféré</label>
                                    <select class="form-select" name="mode_paiement_prefere_id">
                                        <option value="">—</option>
                                        @foreach ($modesPaiement as $mode)
                                            <option value="{{ $mode->id }}" @selected($contrat->mode_paiement_prefere_id == $mode->id)>{{ $mode->nom }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-12">
                                    <label class="form-label">Notes</label>
                                    <textarea class="form-control" name="notes" rows="2">{{ $contrat->notes }}</textarea>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-light" data-bs-dismiss="modal">Fermer</button>
                            <button type="submit" class="btn btn-primary">Enregistrer</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Visualiseur du contrat de bail -->
        <div class="modal fade" id="bailContratModal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Contrat de bail — {{ $contrat->numero }}</h5>
                        <button type="button" class="btn-close icon-btn-sm" data-bs-dismiss="modal" aria-label="Close"><i class="ri-close-large-line fw-semibold"></i></button>
                    </div>
                    <div class="modal-body p-0">
                        <iframe src="{{ route('locative.contrats.apercu', $contrat) }}" style="width: 100%; height: 70vh; border: 0;"></iframe>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Fermer</button>
                        <a href="{{ route('locative.contrats.pdf', $contrat) }}" class="btn btn-primary"><i class="bi bi-download me-1"></i>Télécharger en PDF</a>
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
