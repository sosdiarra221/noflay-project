@extends('partials.layouts.master-commercial')

@section('title', 'Biens disponibles | Commercial')
@section('title-sub', 'Commercial')
@section('pagetitle', 'Biens disponibles')

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
                <div class="accordion accordion-icon accordion-primary accordion-border-box" id="filtres_biens_disponibles">
                    <div class="accordion-item">
                        <h2 class="accordion-header">
                            <button class="accordion-button" type="button" data-bs-toggle="collapse"
                                data-bs-target="#filtres_biens_disponibles_body" aria-expanded="true">
                                <i class="bi bi-funnel-fill me-2"></i> Filtres
                            </button>
                        </h2>
                        <div id="filtres_biens_disponibles_body" class="accordion-collapse collapse show"
                            data-bs-parent="#filtres_biens_disponibles">
                            <div class="accordion-body py-5">
                                <form method="GET" class="row g-4" id="formFiltresBiensDisponibles">
                                    <div class="col-md-3">
                                        <input type="text" class="form-control" name="recherche" value="{{ request('recherche') }}" placeholder="Titre, adresse, zone..." onchange="this.form.submit()">
                                    </div>
                                    <div class="col-md-3">
                                        <select class="form-select" name="categorie_bien_id" onchange="this.form.submit()">
                                            <option value="">Toutes catégories</option>
                                            @foreach ($categories as $categorie)
                                                <option value="{{ $categorie->id }}" @selected(request('categorie_bien_id') == $categorie->id)>{{ $categorie->nom }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-md-3">
                                        <select class="form-select" name="type_exploitation" onchange="this.form.submit()">
                                            <option value="">Tous types</option>
                                            <option value="location" @selected(request('type_exploitation') === 'location')>À louer</option>
                                            <option value="vente" @selected(request('type_exploitation') === 'vente')>À vendre</option>
                                        </select>
                                    </div>
                                    <div class="col-md-3">
                                        <select class="form-select" name="bailleur_id" id="filtreBailleurSelect">
                                            <option value="">Tous les bailleurs</option>
                                            @foreach ($bailleurs as $bailleur)
                                                <option value="{{ $bailleur->id }}" @selected(request('bailleur_id') == $bailleur->id)>{{ $bailleur->nom_complet }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-12 d-flex justify-content-end gap-2">
                                        <a href="{{ route('commercial.biens-disponibles') }}" class="btn btn-light-danger">Réinitialiser</a>
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
                    <div class="card-header d-flex flex-wrap justify-content-between align-items-center gap-3">
                        <h6 class="mb-0">Biens disponibles <span class="badge bg-secondary-subtle text-secondary ms-1">{{ $biens->count() }}</span></h6>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-box table-responsive">
                            <table class="table text-nowrap table-hover align-middle mb-0">
                                <thead>
                                    <tr>
                                        <th scope="col">Bien</th>
                                        <th scope="col">Bailleur</th>
                                        <th scope="col">Catégorie</th>
                                        <th scope="col">Type</th>
                                        <th scope="col">Montant</th>
                                        <th scope="col">Statut</th>
                                        <th scope="col">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($biens as $bien)
                                        <tr>
                                            <td class="d-flex align-items-center gap-3">
                                                <div class="avatar-md rounded-pill bg-light-primary d-flex align-items-center justify-content-center flex-shrink-0">
                                                    <i class="bi bi-house-door fs-18 text-primary"></i>
                                                </div>
                                                <div>
                                                    <h6 class="mb-1">{{ $bien->titre }}</h6>
                                                    <p class="fs-12 mb-0 text-muted">{{ $bien->numero }}</p>
                                                </div>
                                            </td>
                                            <td>{{ $bien->bailleur->nom_complet ?? '—' }}</td>
                                            <td><span class="badge bg-secondary-subtle text-secondary">{{ $bien->categorie->nom ?? '—' }}</span></td>
                                            <td>
                                                @if ($bien->type_exploitation === 'vente')
                                                    <span class="badge bg-info-subtle text-info">À vendre</span>
                                                @else
                                                    <span class="badge bg-primary-subtle text-primary">À louer</span>
                                                @endif
                                            </td>
                                            <td class="fw-medium">{{ number_format($bien->montantExploitation() ?? 0, 0, ',', ' ') }} FCFA</td>
                                            <td><span class="badge bg-success-subtle text-success text-capitalize">{{ str_replace('_', ' ', $bien->statut) }}</span></td>
                                            <td>
                                                <button type="button" class="btn btn-light-info icon-btn-sm" data-bs-toggle="modal" data-bs-target="#apercuBienDisponibleModal{{ $bien->id }}">
                                                    <i class="bi bi-eye"></i>
                                                </button>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="7" class="text-center text-muted py-5">Aucun bien disponible ne correspond à ces filtres.</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        @foreach ($biens as $bien)
            {{-- Aperçu rapide (lecture seule) --}}
            <div class="modal fade" id="apercuBienDisponibleModal{{ $bien->id }}" data-bs-keyboard="false" tabindex="-1" aria-hidden="true">
                <div class="modal-dialog modal-lg" role="document">
                    <div class="modal-content rounded-4 shadow">
                        <div class="modal-header bg-gradient text-white bg-primary">
                            <h5 class="modal-title">{{ $bien->titre }}</h5>
                            <button type="button" class="btn-close icon-btn-sm btn-close-white" data-bs-dismiss="modal" aria-label="Close">
                                <i class="ri-close-large-line fw-semibold"></i>
                            </button>
                        </div>
                        <div class="modal-body">
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <div class="d-flex gap-2"><i class="bi bi-person-badge fs-16"></i><p class="text-muted mb-2">Bailleur</p></div>
                                    <h6 class="mb-0">{{ $bien->bailleur->nom_complet ?? '—' }}</h6>
                                </div>
                                <div class="col-md-6">
                                    <div class="d-flex gap-2"><i class="bi bi-telephone fs-16"></i><p class="text-muted mb-2">Téléphone du bailleur</p></div>
                                    <h6 class="mb-0">{{ $bien->bailleur->telephone ?? '—' }}</h6>
                                </div>
                                <div class="col-md-6">
                                    <div class="d-flex gap-2"><i class="bi bi-tag fs-16"></i><p class="text-muted mb-2">Catégorie</p></div>
                                    <h6 class="mb-0">{{ $bien->categorie->nom ?? '—' }}</h6>
                                </div>
                                <div class="col-md-6">
                                    <div class="d-flex gap-2"><i class="bi bi-geo-alt fs-16"></i><p class="text-muted mb-2">Adresse</p></div>
                                    <h6 class="mb-0">{{ $bien->adresse ?: '—' }} @if ($bien->zone) — {{ $bien->zone }} @endif</h6>
                                </div>
                                <div class="col-md-6">
                                    <div class="d-flex gap-2"><i class="bi bi-arrow-left-right fs-16"></i><p class="text-muted mb-2">Type d'exploitation</p></div>
                                    <h6 class="mb-0">{{ $bien->type_exploitation === 'vente' ? 'À vendre' : 'À louer' }}</h6>
                                </div>
                                <div class="col-md-6">
                                    <div class="d-flex gap-2"><i class="bi bi-cash-coin fs-16"></i><p class="text-muted mb-2">{{ $bien->type_exploitation === 'vente' ? 'Prix de vente' : 'Loyer mensuel' }}</p></div>
                                    <h6 class="mb-0">{{ number_format($bien->montantExploitation() ?? 0, 0, ',', ' ') }} FCFA</h6>
                                </div>
                                <div class="col-md-6">
                                    <div class="d-flex gap-2"><i class="bi bi-toggle-on fs-16"></i><p class="text-muted mb-2">Statut</p></div>
                                    <h6 class="mb-0 text-capitalize">{{ str_replace('_', ' ', $bien->statut) }}</h6>
                                </div>
                                @if ($bien->description)
                                    <div class="col-12">
                                        <hr>
                                        <p class="text-muted mb-2">Description</p>
                                        <p class="mb-0">{{ $bien->description }}</p>
                                    </div>
                                @endif
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-light" data-bs-dismiss="modal">Fermer</button>
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
            const form = document.getElementById('formFiltresBiensDisponibles');

            const bailleurSelect = new Choices(document.getElementById('filtreBailleurSelect'), {
                searchEnabled: true,
                itemSelectText: '',
                placeholderValue: 'Rechercher un bailleur...',
                searchPlaceholderValue: 'Rechercher...',
            });
            document.getElementById('filtreBailleurSelect').addEventListener('change', () => form.submit());
        });
    </script>
@endsection
