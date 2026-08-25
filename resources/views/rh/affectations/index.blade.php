@extends('partials.layouts.master-rh')

@section('title', 'Affectations | RH')
@section('title-sub', 'RH')
@section('pagetitle', 'Affectations')

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

        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-start">
                <div>
                    <h6 class="mb-0">Sites <span class="badge bg-secondary-subtle text-secondary ms-1">{{ $sites->count() }}</span></h6>
                    <p class="text-muted fs-12 mb-0">Affectez un ou plusieurs employés à un site. L'historique des transferts est conservé.</p>
                </div>
                <div class="d-flex gap-2 text-nowrap">
                    <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#nouvelleAffectationModal">
                        <i class="bi bi-signpost-split me-1"></i>Nouvelle Affectation
                    </button>
                    <button type="button" class="btn btn-light-primary btn-sm" data-bs-toggle="modal" data-bs-target="#creerSiteModal">
                        <i class="bi bi-plus-lg me-1"></i>Nouveau site
                    </button>
                </div>
            </div>
            <div class="card-body p-0">
                <div class="table-box table-responsive">
                    <table class="table text-nowrap align-middle mb-0">
                        <thead><tr><th>Nom</th><th>Client</th><th>Employés affectés</th><th>Dernière affectation</th><th>Statut</th><th></th></tr></thead>
                        <tbody>
                            @forelse ($sites as $site)
                                <tr>
                                    <td class="fw-medium">{{ $site->nom }}</td>
                                    <td>{{ $site->client->nom_complet ?? '—' }}</td>
                                    <td><span class="badge bg-secondary-subtle text-secondary">{{ $site->employes_count }}</span></td>
                                    <td>{{ $site->derniere_affectation ? \Illuminate\Support\Carbon::parse($site->derniere_affectation)->format('d/m/Y') : '—' }}</td>
                                    <td>
                                        @if ($site->actif)
                                            <span class="badge bg-success-subtle text-success">Actif</span>
                                        @else
                                            <span class="badge bg-secondary-subtle text-secondary">Inactif</span>
                                        @endif
                                    </td>
                                    <td><a href="{{ route('rh.sites.show', $site) }}" class="btn btn-light-success btn-sm"><i class="bi bi-eye me-1"></i>Consulter</a></td>
                                </tr>
                            @empty
                                <tr><td colspan="6" class="text-center text-muted py-5">Aucun site enregistré.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="card">
            <div class="card-header"><h6 class="mb-0">Derniers transferts</h6></div>
            <div class="card-body p-0">
                <div class="table-box table-responsive">
                    <table class="table text-nowrap align-middle mb-0">
                        <thead><tr><th>Date</th><th>Employé</th><th>Anciens sites</th><th>Nouveaux sites</th><th>Motif</th></tr></thead>
                        <tbody>
                            @forelse ($historique as $affectation)
                                <tr>
                                    <td>{{ $affectation->date_affectation->format('d/m/Y') }}</td>
                                    <td class="fw-medium">{{ $affectation->employe->nom_complet ?? '—' }}</td>
                                    <td class="text-muted fs-12">{{ $affectation->anciens_sites ?: '—' }}</td>
                                    <td class="fw-medium">{{ $affectation->nouveaux_sites ?: '—' }}</td>
                                    <td class="text-muted fs-12">{{ $affectation->motif ?: '—' }}</td>
                                </tr>
                            @empty
                                <tr><td colspan="5" class="text-center text-muted py-5">Aucune affectation enregistrée.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

    </div>

    {{-- Nouvelle Affectation --}}
    <div class="modal fade" id="nouvelleAffectationModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-xl" role="document">
            <div class="modal-content">
                <form action="{{ route('rh.affectations.store') }}" method="POST">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title">Nouvelle affectation</h5>
                        <button type="button" class="btn-close icon-btn-sm" data-bs-dismiss="modal" aria-label="Close"><i class="ri-close-large-line fw-semibold"></i></button>
                    </div>
                    <div class="modal-body">
                        <div class="row g-4">
                            <div class="col-lg-7">
                                <div class="mb-3">
                                    <label class="form-label">Employé(s)<span class="text-danger ms-1">*</span></label>
                                    <select class="form-select select-employes-affectation" name="employes[]" multiple required>
                                        @foreach ($employes as $employe)
                                            <option value="{{ $employe->id }}">{{ $employe->nom_complet }} ({{ $employe->matricule }}) — {{ $employe->poste->nom ?? '—' }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Site<span class="text-danger ms-1">*</span></label>
                                    <select class="form-select select-site-affectation" name="site_id" required>
                                        <option value="">Sélectionner...</option>
                                        @foreach ($sites as $site)
                                            <option value="{{ $site->id }}">{{ $site->nom }} @if($site->client) — {{ $site->client->nom_complet }} @endif</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="mb-0">
                                    <label class="form-label">Motif</label>
                                    <input type="text" class="form-control" name="motif" placeholder="Ex : nouvelle mission, renfort...">
                                </div>
                            </div>
                            <div class="col-lg-5">
                                <label class="form-label">Aperçu des employés sélectionnés</label>
                                <div id="apercuEmployesSelectionnes" class="bg-light-subtle rounded p-3" style="max-height: 360px; overflow-y: auto;">
                                    <p class="text-muted fs-12 text-center py-5 mb-0">Sélectionnez un ou plusieurs employés pour voir leurs informations ici.</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Annuler</button>
                        <button type="submit" class="btn btn-primary">Affecter</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- Nouveau site --}}
    <div class="modal fade" id="creerSiteModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <form action="{{ route('rh.sites.store') }}" method="POST">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title">Nouveau site</h5>
                        <button type="button" class="btn-close icon-btn-sm" data-bs-dismiss="modal" aria-label="Close"><i class="ri-close-large-line fw-semibold"></i></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">Nom<span class="text-danger ms-1">*</span></label>
                            <input type="text" class="form-control" name="nom" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Client <span class="text-muted fs-11">(un client peut avoir plusieurs sites)</span></label>
                            <select class="form-select select-client-site" name="client_id">
                                <option value="">Aucun (site interne à l'agence)</option>
                                @foreach ($clients as $client)
                                    <option value="{{ $client->id }}">{{ $client->nom_complet }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="mb-0">
                            <label class="form-label">Adresse</label>
                            <input type="text" class="form-control" name="adresse">
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Annuler</button>
                        <button type="submit" class="btn btn-primary">Créer</button>
                    </div>
                </form>
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
            document.querySelectorAll('.select-site-affectation').forEach(function (select) {
                new Choices(select, { searchEnabled: true, itemSelectText: '' });
            });
            document.querySelectorAll('.select-client-site').forEach(function (select) {
                new Choices(select, { searchEnabled: true, itemSelectText: '', searchPlaceholderValue: 'Rechercher un client...' });
            });

            const employesData = {!! json_encode($employes->map(fn ($e) => [
                'id' => (string) $e->id,
                'nom' => $e->nom_complet,
                'matricule' => $e->matricule,
                'poste' => $e->poste->nom ?? '—',
                'departement' => $e->departement->nom ?? '—',
                'sites' => $e->sites->pluck('nom')->implode(', ') ?: 'Aucun site',
                'contrat' => $e->contratActif ? $e->contratActif->libelleType() : 'Sans contrat actif',
            ])) !!};

            const selectEmployes = document.querySelector('.select-employes-affectation');
            const apercu = document.getElementById('apercuEmployesSelectionnes');

            function actualiserApercuEmployes() {
                const idsSelectionnes = Array.from(selectEmployes.selectedOptions).map(o => o.value);
                if (idsSelectionnes.length === 0) {
                    apercu.innerHTML = '<p class="text-muted fs-12 text-center py-5 mb-0">Sélectionnez un ou plusieurs employés pour voir leurs informations ici.</p>';
                    return;
                }
                apercu.innerHTML = idsSelectionnes.map(function (id) {
                    const e = employesData.find(function (x) { return x.id === id; });
                    if (!e) return '';
                    return '<div class="border rounded p-2 mb-2 bg-white">'
                        + '<div class="fw-medium">' + e.nom + ' <span class="text-muted fs-11">(' + e.matricule + ')</span></div>'
                        + '<div class="fs-12 text-muted">' + e.poste + ' — ' + e.departement + '</div>'
                        + '<div class="fs-12 text-muted">Site(s) actuel(s) : ' + e.sites + '</div>'
                        + '<div class="fs-12 text-muted">Contrat : ' + e.contrat + '</div>'
                        + '</div>';
                }).join('');
            }

            new Choices(selectEmployes, { removeItemButton: true, placeholderValue: 'Sélectionner un ou plusieurs employés...', searchPlaceholderValue: 'Rechercher...' });
            selectEmployes.addEventListener('change', actualiserApercuEmployes);
        });
    </script>
@endsection
