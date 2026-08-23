@extends('partials.layouts.master-locative')

@section('title', 'Loyers | Locative')
@section('title-sub', 'Locative')
@section('pagetitle', 'Loyers & échéances')

@section('content')
    <div id="layout-wrapper">

        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
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
                                        <select class="form-select" name="statut">
                                            <option value="">Tous statuts</option>
                                            @foreach (['a_venir', 'echu', 'partiellement_paye', 'paye', 'en_retard', 'annule'] as $statutOption)
                                                <option value="{{ $statutOption }}" @selected(request('statut') === $statutOption)>{{ str_replace('_', ' ', ucfirst($statutOption)) }}</option>
                                            @endforeach
                                        </select>
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
                                    <div class="col-12 d-flex justify-content-end gap-2">
                                        <button class="btn btn-light-primary" type="submit"><i class="ri-equalizer-line me-2"></i>Filtrer</button>
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
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h6 class="mb-0">Historique des loyers <span class="badge bg-secondary-subtle text-secondary ms-1">{{ $echeances->count() }}</span></h6>
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
                                                    <button type="button" class="btn btn-light-info icon-btn-sm" data-bs-toggle="modal" data-bs-target="#apercuEcheanceModal{{ $echeance->id }}"><i class="bi bi-eye"></i></button>
                                                    <a href="{{ route('locative.contrats.show', $echeance->contratLocation) }}" class="btn btn-light-success icon-btn-sm"><i class="bi bi-arrow-up-right-circle"></i></a>
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
            {{-- Aperçu rapide --}}
            <div class="modal fade" id="apercuEcheanceModal{{ $echeance->id }}" data-bs-keyboard="false" tabindex="-1" aria-hidden="true">
                <div class="modal-dialog modal-lg" role="document">
                    <div class="modal-content rounded-4 shadow">
                        <div class="modal-header bg-gradient text-white bg-{{ $classesE[$echeance->statut] ?? 'primary' }}">
                            <h5 class="modal-title">
                                {{ ucfirst(\Carbon\Carbon::createFromDate($echeance->annee, $echeance->mois, 1)->translatedFormat('F Y')) }}
                                — {{ $echeance->contratLocation->location->locataire->nom_complet }}
                            </h5>
                            <button type="button" class="btn-close icon-btn-sm btn-close-white" data-bs-dismiss="modal" aria-label="Close">
                                <i class="ri-close-large-line fw-semibold"></i>
                            </button>
                        </div>
                        <div class="modal-body">
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <div class="d-flex gap-2"><i class="bi bi-building fs-16"></i><p class="text-muted mb-2">Bien</p></div>
                                    <h6 class="mb-0">{{ $echeance->contratLocation->bien->titre }}</h6>
                                </div>
                                <div class="col-md-6">
                                    <div class="d-flex gap-2"><i class="bi bi-calendar-event fs-16"></i><p class="text-muted mb-2">Date d'échéance</p></div>
                                    <h6 class="mb-0">{{ $echeance->date_echeance->format('d/m/Y') }}</h6>
                                </div>
                                <div class="col-md-6">
                                    <div class="d-flex gap-2"><i class="bi bi-cash-coin fs-16"></i><p class="text-muted mb-2">Montant attendu</p></div>
                                    <h6 class="mb-0">{{ number_format($echeance->montant_attendu, 0, ',', ' ') }} FCFA</h6>
                                </div>
                                <div class="col-md-6">
                                    <div class="d-flex gap-2"><i class="bi bi-cash-stack fs-16"></i><p class="text-muted mb-2">Montant payé</p></div>
                                    <h6 class="mb-0">{{ number_format($echeance->montant_paye, 0, ',', ' ') }} FCFA</h6>
                                </div>
                                <div class="col-12">
                                    <hr>
                                    <p class="text-muted mb-2">Paiements enregistrés</p>
                                    @forelse ($echeance->paiements as $paiement)
                                        <div class="d-flex justify-content-between border-bottom py-2">
                                            <span>{{ $paiement->date_paiement->format('d/m/Y') }} — {{ $paiement->modePaiement->nom ?? '—' }}</span>
                                            <span class="fw-medium">{{ number_format($paiement->montant, 0, ',', ' ') }} FCFA</span>
                                        </div>
                                    @empty
                                        <p class="text-muted mb-0">Aucun paiement enregistré.</p>
                                    @endforelse
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-light" data-bs-dismiss="modal">Fermer</button>
                            <a href="{{ route('locative.contrats.show', $echeance->contratLocation) }}" class="btn btn-primary">Ouvrir le contrat</a>
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
