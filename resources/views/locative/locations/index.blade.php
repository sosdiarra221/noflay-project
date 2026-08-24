@extends('partials.layouts.master-locative')

@section('title', 'Locations | Locative')
@section('title-sub', 'Locative')
@section('pagetitle', 'Locations')

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
                                <form method="GET" class="row g-4" id="formFiltresLocations">
                                    <div class="col-md-6">
                                        <select class="form-select" name="locataire_id" id="filtreLocataireLocationSelect">
                                            <option value="">Tous les locataires</option>
                                            @foreach ($locataires as $locataire)
                                                <option value="{{ $locataire->id }}" @selected(request('locataire_id') == $locataire->id)>{{ $locataire->nom_complet }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-md-6">
                                        <select class="form-select" name="statut" onchange="this.form.submit()">
                                            <option value="">Tous statuts</option>
                                            @foreach (['actif', 'suspendu', 'expire', 'resilie', 'archive'] as $statutOption)
                                                <option value="{{ $statutOption }}" @selected(request('statut') === $statutOption)>{{ str_replace('_', ' ', ucfirst($statutOption)) }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-12 d-flex justify-content-end gap-2">
                                        <a href="{{ route('locative.locations.index') }}" class="btn btn-light-danger">Réinitialiser</a>
                                    </div>
                                </form>
                                <p class="text-muted fs-12 mt-3 mb-0"><i class="bi bi-info-circle me-1"></i>Les filtres s'appliquent automatiquement.</p>
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
                                                    <a href="{{ route('locative.locations.show', $location) }}" class="btn btn-light-success btn-sm" title="Voir le détail de la location"><i class="bi bi-eye me-1"></i>Détail</a>
                                                    @if ($location->contrats->isNotEmpty())
                                                        <a href="{{ route('locative.contrats.show', $location->contrats->first()) }}" class="btn btn-light-info btn-sm" title="Consulter le contrat"><i class="bi bi-file-earmark-text me-1"></i>Contrat</a>
                                                    @endif
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

    </div>
    </main>
@endsection

@section('js')
    <script src="{{ asset('assets/libs/choices.js/public/assets/scripts/choices.min.js') }}"></script>
    <script type="module" src="{{ asset('assets/js/app.js') }}"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const form = document.getElementById('formFiltresLocations');
            const select = document.getElementById('filtreLocataireLocationSelect');

            new Choices(select, {
                searchEnabled: true,
                itemSelectText: '',
                placeholderValue: 'Rechercher un locataire...',
                searchPlaceholderValue: 'Rechercher...',
            });
            select.addEventListener('change', () => form.submit());
        });
    </script>
@endsection
