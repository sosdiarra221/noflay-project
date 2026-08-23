@extends('partials.layouts.master-locative')

@section('title', 'Biens | Locative')
@section('title-sub', 'Locative')
@section('pagetitle', 'Biens')

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
                <div class="accordion accordion-icon accordion-primary accordion-border-box" id="filtres_biens">
                    <div class="accordion-item">
                        <h2 class="accordion-header">
                            <button class="accordion-button" type="button" data-bs-toggle="collapse"
                                data-bs-target="#filtres_biens_body" aria-expanded="true">
                                <i class="bi bi-funnel-fill me-2"></i> Filtres
                            </button>
                        </h2>
                        <div id="filtres_biens_body" class="accordion-collapse collapse show" data-bs-parent="#filtres_biens">
                            <div class="accordion-body py-5">
                                <form method="GET" class="row g-4">
                                    <div class="col-md-4">
                                        <select class="form-select" name="bailleur_id">
                                            <option value="">Tous les bailleurs</option>
                                            @foreach ($bailleurs as $bailleur)
                                                <option value="{{ $bailleur->id }}" @selected(request('bailleur_id') == $bailleur->id)>{{ $bailleur->nom_complet }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-md-4">
                                        <select class="form-select" name="type_exploitation">
                                            <option value="">Tous types</option>
                                            <option value="location" @selected(request('type_exploitation') === 'location')>Location</option>
                                            <option value="vente" @selected(request('type_exploitation') === 'vente')>Vente</option>
                                        </select>
                                    </div>
                                    <div class="col-md-4">
                                        <select class="form-select" name="statut">
                                            <option value="">Tous statuts</option>
                                            @foreach (array_merge(\App\Models\Bien::STATUTS_LOCATION, \App\Models\Bien::STATUTS_VENTE) as $statutOption)
                                                <option value="{{ $statutOption }}" @selected(request('statut') === $statutOption)>{{ str_replace('_', ' ', ucfirst($statutOption)) }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-12 d-flex justify-content-end gap-2">
                                        <button class="btn btn-light-primary" type="submit"><i class="ri-equalizer-line me-2"></i>Filtrer</button>
                                        <a href="{{ route('locative.biens.index') }}" class="btn btn-light-danger">Réinitialiser</a>
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
                        <h6 class="mb-0">Biens <span class="badge bg-secondary-subtle text-secondary ms-1">{{ $biens->count() }}</span></h6>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-box table-responsive">
                            <table class="table text-nowrap align-middle mb-0">
                                <thead>
                                    <tr>
                                        <th scope="col">Disponibilité</th>
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
                                            <td>
                                                @if ($bien->estDisponible())
                                                    <span class="text-success" title="Disponible"><i class="bi bi-check-circle-fill fs-5"></i></span>
                                                @else
                                                    <span class="text-danger" title="Non disponible"><i class="bi bi-x-circle-fill fs-5"></i></span>
                                                @endif
                                            </td>
                                            <td>
                                                <h6 class="mb-0 fw-medium fs-13">{{ $bien->titre }}</h6>
                                                <span class="text-muted fs-12">{{ $bien->numero }}</span>
                                            </td>
                                            <td>{{ $bien->bailleur->nom_complet }}</td>
                                            <td>{{ $bien->categorie->nom ?? '—' }}</td>
                                            <td class="text-capitalize">{{ $bien->type_exploitation }}</td>
                                            <td>{{ number_format($bien->montantExploitation() ?? 0, 0, ',', ' ') }} FCFA</td>
                                            <td><span class="badge bg-secondary-subtle text-secondary text-capitalize">{{ str_replace('_', ' ', $bien->statut) }}</span></td>
                                            <td>
                                                <div class="hstack gap-2">
                                                    <button type="button" class="btn btn-light-info icon-btn-sm" data-bs-toggle="modal" data-bs-target="#apercuBienModal{{ $bien->id }}"><i class="bi bi-eye"></i></button>
                                                    @if ($bien->gerance)
                                                        <a href="{{ route('locative.gerances.show', $bien->gerance) }}" class="btn btn-light-success icon-btn-sm"><i class="bi bi-arrow-up-right-circle"></i></a>
                                                        <button type="button" class="btn btn-light-primary icon-btn-sm" data-bs-toggle="modal" data-bs-target="#editBienModal{{ $bien->id }}"><i class="bi bi-pencil-square"></i></button>
                                                    @endif
                                                </div>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="8" class="text-center text-muted py-5">Aucun bien ne correspond à ces filtres.</td>
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
            {{-- Aperçu rapide --}}
            <div class="modal fade" id="apercuBienModal{{ $bien->id }}" data-bs-keyboard="false" tabindex="-1" aria-hidden="true">
                <div class="modal-dialog modal-lg" role="document">
                    <div class="modal-content rounded-4 shadow">
                        <div class="modal-header bg-gradient text-white {{ $bien->estDisponible() ? 'bg-success' : 'bg-danger' }}">
                            <h5 class="modal-title">
                                {{ $bien->estDisponible() ? '✓' : '✕' }} {{ $bien->titre }}
                            </h5>
                            <button type="button" class="btn-close icon-btn-sm btn-close-white" data-bs-dismiss="modal" aria-label="Close">
                                <i class="ri-close-large-line fw-semibold"></i>
                            </button>
                        </div>
                        <div class="modal-body">
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <div class="d-flex gap-2"><i class="bi bi-person-badge fs-16"></i><p class="text-muted mb-2">Bailleur</p></div>
                                    <h6 class="mb-0">{{ $bien->bailleur->nom_complet }}</h6>
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
                                    <div class="d-flex gap-2"><i class="bi bi-toggle-on fs-16"></i><p class="text-muted mb-2">Statut</p></div>
                                    <h6 class="mb-0 text-capitalize">{{ str_replace('_', ' ', $bien->statut) }}</h6>
                                </div>
                                <div class="col-md-6">
                                    <div class="d-flex gap-2"><i class="bi bi-arrow-left-right fs-16"></i><p class="text-muted mb-2">Type d'exploitation</p></div>
                                    <h6 class="mb-0 text-capitalize">{{ $bien->type_exploitation }}</h6>
                                </div>
                                <div class="col-md-6">
                                    <div class="d-flex gap-2"><i class="bi bi-cash-coin fs-16"></i><p class="text-muted mb-2">{{ $bien->type_exploitation === 'vente' ? 'Prix de vente' : 'Loyer mensuel' }}</p></div>
                                    <h6 class="mb-0">{{ number_format($bien->montantExploitation() ?? 0, 0, ',', ' ') }} FCFA</h6>
                                </div>
                                @if ($bien->gerance)
                                    <div class="col-md-6">
                                        <div class="d-flex gap-2"><i class="bi bi-file-earmark-text fs-16"></i><p class="text-muted mb-2">Contrat de gérance</p></div>
                                        <h6 class="mb-0">{{ $bien->gerance->numero }}</h6>
                                    </div>
                                @endif
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
                            @if ($bien->gerance)
                                <a href="{{ route('locative.gerances.show', $bien->gerance) }}" class="btn btn-primary">Voir la gérance</a>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            @if ($bien->gerance)
                <div class="modal fade" id="editBienModal{{ $bien->id }}" tabindex="-1" aria-hidden="true">
                    <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
                        <div class="modal-content">
                            <form action="{{ route('locative.biens.update', $bien) }}" method="POST">
                                @csrf
                                @method('PUT')
                                @include('locative.biens._formulaire', ['bien' => $bien, 'gerance' => $bien->gerance, 'categories' => $categories])
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Fermer</button>
                                    <button type="submit" class="btn btn-primary">Enregistrer</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            @endif
        @endforeach

    </div>
    </main>
@endsection

@section('js')
    <script type="module" src="{{ asset('assets/js/app.js') }}"></script>
@endsection
