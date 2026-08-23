@extends('partials.layouts.master-locative')

@section('title', $bailleur->nom_complet.' | Locative')
@section('title-sub', 'Bailleurs')
@section('pagetitle', $bailleur->nom_complet)

@section('content')
    <div id="layout-wrapper">

        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <div class="card overflow-hidden">
            <div class="card-body h-176px"
                style="background-image: url('assets/images/background.png');background-repeat: no-repeat;background-position: right;">
            </div>
            <div class="mt-2">
                <div class="card-body p-5">
                    <div class="d-flex flex-wrap align-items-start gap-5">
                        <div class="mt-n12 flex-shrink-0">
                            <div class="h-128px w-128px border border-4 border-white shadow-lg bg-primary-subtle text-primary d-flex align-items-center justify-content-center fs-1">
                                <i class="bi bi-person-badge"></i>
                            </div>
                        </div>
                        <div class="flex-grow-1">
                            <div class="mb-2">
                                <h5 class="mb-1">{{ $bailleur->nom_complet }}
                                    @if ($bailleur->statut === 'actif')
                                        <span class="badge bg-success-subtle text-success ms-1">Bailleur actif</span>
                                    @else
                                        <span class="badge bg-light-subtle text-body ms-1">Inactif</span>
                                    @endif
                                </h5>
                                <p class="text-muted fs-12 mb-0">{{ $bailleur->numero }} — {{ ucfirst($bailleur->type) }}</p>
                            </div>
                            <div class="d-flex flex-wrap gap-3">
                                <span class="badge bg-secondary-subtle text-secondary fs-13 px-3 py-2">{{ $bailleur->biens->count() }} Biens</span>
                                <span class="badge bg-secondary-subtle text-secondary fs-13 px-3 py-2">{{ $bailleur->gerances->count() }} Gérances</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-12">
                <div class="mb-6">
                    <ul class="nav nav-pills" id="bailleurTab" role="tablist">
                        <li class="nav-item" role="presentation">
                            <button class="nav-link active" data-bs-toggle="tab" data-bs-target="#infos-tab-pane" type="button">Vue générale</button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" data-bs-toggle="tab" data-bs-target="#gerances-tab-pane" type="button">Gérances</button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" data-bs-toggle="tab" data-bs-target="#biens-tab-pane" type="button">Biens</button>
                        </li>
                    </ul>
                </div>

                <div class="tab-content">
                    <div class="tab-pane fade show active" id="infos-tab-pane">
                        <div class="row g-4">
                            <div class="col-lg-6">
                                <div class="card">
                                    <div class="card-header"><h6 class="card-action-title mb-0">Coordonnées</h6></div>
                                    <div class="card-body">
                                        <div class="row mb-3"><div class="col-4 text-muted">Téléphone</div><div class="col-8 fw-medium">{{ $bailleur->telephone ?: '—' }}</div></div>
                                        <div class="row mb-3"><div class="col-4 text-muted">WhatsApp</div><div class="col-8 fw-medium">{{ $bailleur->whatsapp ?: '—' }}</div></div>
                                        <div class="row mb-3"><div class="col-4 text-muted">Email</div><div class="col-8 fw-medium">{{ $bailleur->email ?: '—' }}</div></div>
                                        <div class="row"><div class="col-4 text-muted">Adresse</div><div class="col-8 fw-medium">{{ $bailleur->adresse ?: '—' }}</div></div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-6">
                                <div class="card">
                                    <div class="card-header"><h6 class="card-action-title mb-0">Identité</h6></div>
                                    <div class="card-body">
                                        <div class="row mb-3"><div class="col-4 text-muted">Pièce d'identité</div><div class="col-8 fw-medium">{{ $bailleur->piece_identite_type ?: '—' }} {{ $bailleur->piece_identite_numero }}</div></div>
                                        <div class="row mb-3"><div class="col-4 text-muted">NINEA</div><div class="col-8 fw-medium">{{ $bailleur->ninea ?: '—' }}</div></div>
                                        <div class="row"><div class="col-4 text-muted">Coordonnées de paiement</div><div class="col-8 fw-medium">{{ $bailleur->coordonnees_paiement ?: '—' }}</div></div>
                                    </div>
                                </div>
                                @if ($bailleur->notes)
                                    <div class="card">
                                        <div class="card-header"><h6 class="card-action-title mb-0">Notes</h6></div>
                                        <div class="card-body"><p class="mb-0">{{ $bailleur->notes }}</p></div>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>

                    <div class="tab-pane fade" id="gerances-tab-pane">
                        <div class="card">
                            <div class="card-body p-0">
                                <div class="table-box table-responsive">
                                    <table class="table text-nowrap align-middle mb-0">
                                        <thead><tr><th>Numéro</th><th>Type</th><th>Période</th><th>Statut</th><th></th></tr></thead>
                                        <tbody>
                                            @forelse ($bailleur->gerances as $gerance)
                                                <tr>
                                                    <td>{{ $gerance->numero }}</td>
                                                    <td class="text-capitalize">{{ str_replace('_', ' ', $gerance->type_gerance) }}</td>
                                                    <td>{{ optional($gerance->date_debut)->format('d/m/Y') }} @if($gerance->date_fin) → {{ $gerance->date_fin->format('d/m/Y') }} @endif</td>
                                                    <td><span class="badge bg-secondary-subtle text-secondary text-capitalize">{{ str_replace('_', ' ', $gerance->statut) }}</span></td>
                                                    <td><a href="{{ route('locative.gerances.show', $gerance) }}" class="btn btn-light-success icon-btn-sm"><i class="bi bi-eye"></i></a></td>
                                                </tr>
                                            @empty
                                                <tr><td colspan="5" class="text-center text-muted py-5">Aucune gérance pour ce bailleur.</td></tr>
                                            @endforelse
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="tab-pane fade" id="biens-tab-pane">
                        <div class="card">
                            <div class="card-body p-0">
                                <div class="table-box table-responsive">
                                    <table class="table text-nowrap align-middle mb-0">
                                        <thead><tr><th>Bien</th><th>Catégorie</th><th>Exploitation</th><th>Statut</th><th></th></tr></thead>
                                        <tbody>
                                            @forelse ($bailleur->biens as $bien)
                                                <tr>
                                                    <td>{{ $bien->titre }}</td>
                                                    <td>{{ $bien->categorie->nom ?? '—' }}</td>
                                                    <td class="text-capitalize">{{ $bien->type_exploitation }}</td>
                                                    <td><span class="badge bg-secondary-subtle text-secondary text-capitalize">{{ str_replace('_', ' ', $bien->statut) }}</span></td>
                                                    <td></td>
                                                </tr>
                                            @empty
                                                <tr><td colspan="5" class="text-center text-muted py-5">Aucun bien pour ce bailleur.</td></tr>
                                            @endforelse
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
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
