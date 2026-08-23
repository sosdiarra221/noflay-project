@extends('partials.layouts.master-commercial')

@section('title', 'Prospection | Commercial')
@section('title-sub', 'Commercial')
@section('pagetitle', 'Prospection')

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
                <div class="accordion accordion-icon accordion-primary accordion-border-box" id="filtres_prospection">
                    <div class="accordion-item">
                        <h2 class="accordion-header">
                            <button class="accordion-button" type="button" data-bs-toggle="collapse"
                                data-bs-target="#filtres_prospection_body" aria-expanded="true">
                                <i class="bi bi-funnel-fill me-2"></i> Filtres
                            </button>
                        </h2>
                        <div id="filtres_prospection_body" class="accordion-collapse collapse show"
                            data-bs-parent="#filtres_prospection">
                            <div class="accordion-body py-5">
                                <form method="GET" class="row g-4">
                                    <div class="col-md-3">
                                        <input type="text" class="form-control" name="recherche" value="{{ request('recherche') }}" placeholder="Nom, téléphone, email...">
                                    </div>
                                    <div class="col-md-3">
                                        <select class="form-select" name="type_demande_id">
                                            <option value="">Tous types</option>
                                            @foreach ($typesDemande as $type)
                                                <option value="{{ $type->id }}" @selected(request('type_demande_id') == $type->id)>{{ $type->nom }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-md-3">
                                        <select class="form-select" name="source_id">
                                            <option value="">Toutes sources</option>
                                            @foreach ($sources as $source)
                                                <option value="{{ $source->id }}" @selected(request('source_id') == $source->id)>{{ $source->nom }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-md-3">
                                        <select class="form-select" name="statut">
                                            <option value="">Tous statuts</option>
                                            @foreach (\App\Models\Commercial\Prospect::STATUTS as $statutOption)
                                                <option value="{{ $statutOption }}" @selected(request('statut') === $statutOption)>{{ str_replace('_', ' ', ucfirst($statutOption)) }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-12 d-flex justify-content-end gap-2">
                                        <button class="btn btn-light-primary" type="submit"><i class="ri-equalizer-line me-2"></i>Filtrer</button>
                                        <a href="{{ route('commercial.prospects.index') }}" class="btn btn-light-danger">Réinitialiser</a>
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
                        <h6 class="mb-0">Prospects <span class="badge bg-secondary-subtle text-secondary ms-1">{{ $prospects->count() }}</span></h6>
                        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#creerProspectModal">
                            <i class="bi bi-plus-lg me-1"></i>Nouvelle prospection
                        </button>
                    </div>
                    <div class="card-body">
                        <div class="table-box table-responsive">
                            <table class="table text-nowrap align-middle">
                                <thead>
                                    <tr>
                                        <th scope="col">Prospect</th>
                                        <th scope="col">Demande</th>
                                        <th scope="col">Budget</th>
                                        <th scope="col">Source</th>
                                        <th scope="col">Statut</th>
                                        <th scope="col">Dernière activité</th>
                                        <th scope="col">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @php $classesS = ['non_traite' => 'secondary', 'en_cours' => 'warning', 'gagne' => 'success', 'perdu' => 'danger', 'annule' => 'dark']; @endphp
                                    @forelse ($prospects as $prospect)
                                        <tr>
                                            <td>
                                                <h6 class="mb-1">{{ $prospect->nom_complet }}</h6>
                                                <p class="mb-0 fs-12 text-muted">{{ $prospect->numero }} — {{ $prospect->telephone }}</p>
                                            </td>
                                            <td>{{ $prospect->typeDemande->nom ?? '—' }}</td>
                                            <td>
                                                @if ($prospect->budget_min || $prospect->budget_max)
                                                    {{ number_format($prospect->budget_min ?? 0, 0, ',', ' ') }} - {{ number_format($prospect->budget_max ?? 0, 0, ',', ' ') }} {{ $prospect->devise }}
                                                @else
                                                    —
                                                @endif
                                            </td>
                                            <td>{{ $prospect->source->nom ?? '—' }}</td>
                                            <td><span class="badge bg-{{ $classesS[$prospect->statut] }}-subtle text-{{ $classesS[$prospect->statut] }} text-capitalize">{{ str_replace('_', ' ', $prospect->statut) }}</span></td>
                                            <td>{{ optional($prospect->activites->first())->date_activite?->diffForHumans() ?? '—' }}</td>
                                            <td>
                                                <div class="hstack gap-2">
                                                    <button type="button" class="btn btn-light-info icon-btn-sm" data-bs-toggle="modal" data-bs-target="#apercuModal{{ $prospect->id }}"><i class="bi bi-eye"></i></button>
                                                    <a href="{{ route('commercial.prospects.show', $prospect) }}" class="btn btn-light-primary icon-btn-sm"><i class="bi bi-arrow-up-right-circle"></i></a>
                                                </div>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr><td colspan="7" class="text-center text-muted py-5">Aucun prospect ne correspond à ces filtres.</td></tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Extra Large Modal : Nouvelle prospection -->
        <div class="modal fade" id="creerProspectModal" data-bs-keyboard="false" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-xl" role="document">
                <div class="modal-content">
                    <form action="{{ route('commercial.prospects.store') }}" method="POST">
                        @csrf
                        @include('commercial.prospects._formulaire')
                    </form>
                </div>
            </div>
        </div>

        @foreach ($prospects as $prospect)
            <!-- Aperçu rapide (pattern popovers/tooltips modal) -->
            <div class="modal fade" id="apercuModal{{ $prospect->id }}" data-bs-keyboard="false" tabindex="-1" aria-hidden="true">
                <div class="modal-dialog modal-lg" role="document">
                    <div class="modal-content rounded-4 shadow">
                        <div class="modal-header bg-gradient text-white bg-primary">
                            <h5 class="modal-title">{{ $prospect->nom_complet }}</h5>
                            <button type="button" class="btn-close icon-btn-sm btn-close-white" data-bs-dismiss="modal" aria-label="Close">
                                <i class="ri-close-large-line fw-semibold"></i>
                            </button>
                        </div>
                        <div class="modal-body">
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <div class="d-flex gap-2"><i class="bi bi-telephone fs-16"></i><p class="text-muted mb-2">Téléphone</p></div>
                                    <h6 class="mb-0">{{ $prospect->telephone }}</h6>
                                </div>
                                <div class="col-md-6">
                                    <div class="d-flex gap-2"><i class="bi bi-envelope fs-16"></i><p class="text-muted mb-2">Email</p></div>
                                    <h6 class="mb-0">{{ $prospect->email ?: '—' }}</h6>
                                </div>
                                <div class="col-md-6">
                                    <div class="d-flex gap-2"><i class="bi bi-geo-alt fs-16"></i><p class="text-muted mb-2">Adresse</p></div>
                                    <h6 class="mb-0">{{ $prospect->adresse ?: '—' }}</h6>
                                </div>
                                <div class="col-md-6">
                                    <div class="d-flex gap-2"><i class="bi bi-tag fs-16"></i><p class="text-muted mb-2">Type de demande</p></div>
                                    <h6 class="mb-0">{{ $prospect->typeDemande->nom ?? '—' }}</h6>
                                </div>
                                <div class="col-md-6">
                                    <div class="d-flex gap-2"><i class="bi bi-cash-coin fs-16"></i><p class="text-muted mb-2">Budget</p></div>
                                    <h6 class="mb-0">
                                        @if ($prospect->budget_min || $prospect->budget_max)
                                            {{ number_format($prospect->budget_min ?? 0, 0, ',', ' ') }} - {{ number_format($prospect->budget_max ?? 0, 0, ',', ' ') }} {{ $prospect->devise }}
                                        @else — @endif
                                    </h6>
                                </div>
                                <div class="col-md-6">
                                    <div class="d-flex gap-2"><i class="bi bi-signpost fs-16"></i><p class="text-muted mb-2">Source</p></div>
                                    <h6 class="mb-0">{{ $prospect->source->nom ?? '—' }}</h6>
                                </div>
                                @if ($prospect->besoin)
                                    <div class="col-12">
                                        <hr>
                                        <p class="text-muted mb-2">Besoin exprimé</p>
                                        <p class="mb-0">{{ $prospect->besoin }}</p>
                                    </div>
                                @endif
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-light" data-bs-dismiss="modal">Fermer</button>
                            <a href="{{ route('commercial.prospects.show', $prospect) }}" class="btn btn-primary">Ouvrir la fiche</a>
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
