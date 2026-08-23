@extends('partials.layouts.master-locative')

@section('title', 'Locations | Locative')
@section('title-sub', 'Locative')
@section('pagetitle', 'Locations')

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
                <div class="accordion accordion-icon accordion-primary accordion-border-box" id="filtres_locations">
                    <div class="accordion-item">
                        <h2 class="accordion-header">
                            <button class="accordion-button" type="button" data-bs-toggle="collapse"
                                data-bs-target="#filtres_locations_body" aria-expanded="true">
                                <i class="bi bi-funnel-fill me-2"></i> Filtres
                            </button>
                        </h2>
                        <div id="filtres_locations_body" class="accordion-collapse collapse show" data-bs-parent="#filtres_locations">
                            <div class="accordion-body py-5">
                                <form method="GET" class="row g-4">
                                    <div class="col-md-6">
                                        <select class="form-select" name="locataire_id">
                                            <option value="">Tous les locataires</option>
                                            @foreach ($locataires as $locataire)
                                                <option value="{{ $locataire->id }}" @selected(request('locataire_id') == $locataire->id)>{{ $locataire->nom_complet }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-md-6">
                                        <select class="form-select" name="statut">
                                            <option value="">Tous statuts</option>
                                            @foreach (['actif', 'suspendu', 'expire', 'resilie', 'archive'] as $statutOption)
                                                <option value="{{ $statutOption }}" @selected(request('statut') === $statutOption)>{{ str_replace('_', ' ', ucfirst($statutOption)) }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-12 d-flex justify-content-end gap-2">
                                        <button class="btn btn-light-primary" type="submit"><i class="ri-equalizer-line me-2"></i>Filtrer</button>
                                        <a href="{{ route('locative.locations.index') }}" class="btn btn-light-danger">Réinitialiser</a>
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
                        <h6 class="mb-0">Locations <span class="badge bg-secondary-subtle text-secondary ms-1">{{ $locations->count() }}</span></h6>
                        <a href="{{ route('locative.locations.create') }}" class="btn btn-primary">
                            <i class="bi bi-plus-lg me-1"></i>Nouvelle location
                        </a>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-box table-responsive">
                            <table class="table text-nowrap align-middle mb-0">
                                <thead>
                                    <tr>
                                        <th scope="col">Numéro</th>
                                        <th scope="col">Locataire</th>
                                        <th scope="col">Biens</th>
                                        <th scope="col">Loyer total</th>
                                        <th scope="col">Date</th>
                                        <th scope="col">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($locations as $location)
                                        <tr>
                                            <td><a href="{{ route('locative.locations.show', $location) }}" class="fw-medium text-reset">{{ $location->numero }}</a></td>
                                            <td>{{ $location->locataire->nom_complet }}</td>
                                            <td>{{ $location->contrats->count() }}</td>
                                            <td>{{ number_format($location->contrats->sum('loyer_mensuel'), 0, ',', ' ') }} FCFA</td>
                                            <td>{{ $location->created_at->format('d/m/Y') }}</td>
                                            <td>
                                                <div class="hstack gap-2">
                                                    <button type="button" class="btn btn-light-info icon-btn-sm" data-bs-toggle="modal" data-bs-target="#apercuLocationModal{{ $location->id }}"><i class="bi bi-eye"></i></button>
                                                    <a href="{{ route('locative.locations.show', $location) }}" class="btn btn-light-success icon-btn-sm"><i class="bi bi-arrow-up-right-circle"></i></a>
                                                </div>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr><td colspan="6" class="text-center text-muted py-5">Aucune location ne correspond à ces filtres.</td></tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        @foreach ($locations as $location)
            {{-- Aperçu rapide --}}
            <div class="modal fade" id="apercuLocationModal{{ $location->id }}" data-bs-keyboard="false" tabindex="-1" aria-hidden="true">
                <div class="modal-dialog modal-lg" role="document">
                    <div class="modal-content rounded-4 shadow">
                        <div class="modal-header bg-gradient text-white bg-primary">
                            <h5 class="modal-title">{{ $location->numero }}</h5>
                            <button type="button" class="btn-close icon-btn-sm btn-close-white" data-bs-dismiss="modal" aria-label="Close">
                                <i class="ri-close-large-line fw-semibold"></i>
                            </button>
                        </div>
                        <div class="modal-body">
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <div class="d-flex gap-2"><i class="bi bi-person fs-16"></i><p class="text-muted mb-2">Locataire</p></div>
                                    <h6 class="mb-0">{{ $location->locataire->nom_complet }}</h6>
                                </div>
                                <div class="col-md-6">
                                    <div class="d-flex gap-2"><i class="bi bi-calendar fs-16"></i><p class="text-muted mb-2">Créée le</p></div>
                                    <h6 class="mb-0">{{ $location->created_at->format('d/m/Y') }}</h6>
                                </div>
                                <div class="col-md-6">
                                    <div class="d-flex gap-2"><i class="bi bi-cash-coin fs-16"></i><p class="text-muted mb-2">Loyer total mensuel</p></div>
                                    <h6 class="mb-0">{{ number_format($location->contrats->sum('loyer_mensuel'), 0, ',', ' ') }} FCFA</h6>
                                </div>
                                <div class="col-md-6">
                                    <div class="d-flex gap-2"><i class="bi bi-building fs-16"></i><p class="text-muted mb-2">Biens loués</p></div>
                                    <h6 class="mb-0">{{ $location->contrats->count() }}</h6>
                                </div>
                                <div class="col-12">
                                    <hr>
                                    <p class="text-muted mb-2">Détail des biens</p>
                                    <ul class="list-unstyled mb-0">
                                        @foreach ($location->contrats as $contrat)
                                            <li class="d-flex justify-content-between border-bottom py-2">
                                                <span>{{ $contrat->bien->titre ?? '—' }}</span>
                                                <span class="text-muted">{{ number_format($contrat->loyer_mensuel, 0, ',', ' ') }} FCFA/mois</span>
                                            </li>
                                        @endforeach
                                    </ul>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-light" data-bs-dismiss="modal">Fermer</button>
                            <a href="{{ route('locative.locations.show', $location) }}" class="btn btn-primary">Ouvrir la fiche</a>
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
