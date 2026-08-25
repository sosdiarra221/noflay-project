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

        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-start">
                <div>
                    <h6 class="mb-0">Employés <span class="badge bg-secondary-subtle text-secondary ms-1">{{ $employes->count() }}</span></h6>
                    <p class="text-muted fs-12 mb-0">Sélectionnez un employé pour l'affecter à un ou plusieurs sites. L'historique des transferts est conservé.</p>
                </div>
                <button type="button" class="btn btn-light-primary btn-sm text-nowrap" data-bs-toggle="modal" data-bs-target="#creerSiteModal">
                    <i class="bi bi-plus-lg me-1"></i>Nouveau site
                </button>
            </div>
            <div class="card-body p-0">
                <div class="table-box table-responsive">
                    <table class="table text-nowrap align-middle mb-0">
                        <thead><tr><th>Matricule</th><th>Nom</th><th>Poste</th><th>Département</th><th>Site(s) actuel(s)</th><th></th></tr></thead>
                        <tbody>
                            @forelse ($employes as $employe)
                                <tr>
                                    <td class="fw-medium">{{ $employe->matricule }}</td>
                                    <td>{{ $employe->nom_complet }}</td>
                                    <td>{{ $employe->poste->nom ?? '—' }}</td>
                                    <td>{{ $employe->departement->nom ?? '—' }}</td>
                                    <td>{{ $employe->sites->pluck('nom')->implode(', ') ?: 'Aucun site' }}</td>
                                    <td>
                                        <button type="button" class="btn btn-light-primary btn-sm" data-bs-toggle="modal" data-bs-target="#affecterModal{{ $employe->id }}">
                                            <i class="bi bi-geo-alt me-1"></i>Affecter
                                        </button>
                                    </td>
                                </tr>
                            @empty
                                <tr><td colspan="6" class="text-center text-muted py-5">Aucun employé actif.</td></tr>
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

    @foreach ($employes as $employe)
        <div class="modal fade" id="affecterModal{{ $employe->id }}" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered" role="document">
                <div class="modal-content">
                    <form action="{{ route('rh.affectations.store', $employe) }}" method="POST">
                        @csrf
                        <div class="modal-header">
                            <h5 class="modal-title">Affecter {{ $employe->nom_complet }}</h5>
                            <button type="button" class="btn-close icon-btn-sm" data-bs-dismiss="modal" aria-label="Close"><i class="ri-close-large-line fw-semibold"></i></button>
                        </div>
                        <div class="modal-body">
                            <div class="mb-3">
                                <label class="form-label">Site(s)</label>
                                <select class="form-select select-sites-affectation" name="sites[]" multiple>
                                    @foreach ($sites as $site)
                                        <option value="{{ $site->id }}" @selected($employe->sites->contains($site->id))>{{ $site->nom }} @if($site->client) — {{ $site->client->nom_complet }} @endif</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="mb-0">
                                <label class="form-label">Motif</label>
                                <input type="text" class="form-control" name="motif" placeholder="Ex : réaffectation, besoin opérationnel...">
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-light" data-bs-dismiss="modal">Annuler</button>
                            <button type="submit" class="btn btn-primary">Enregistrer</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endforeach

    </main>
@endsection

@section('js')
    <script src="{{ asset('assets/libs/choices.js/public/assets/scripts/choices.min.js') }}"></script>
    <script type="module" src="{{ asset('assets/js/app.js') }}"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            document.querySelectorAll('.select-sites-affectation').forEach(function (select) {
                new Choices(select, { removeItemButton: true, placeholderValue: 'Sélectionner un ou plusieurs sites...' });
            });
            document.querySelectorAll('.select-client-site').forEach(function (select) {
                new Choices(select, { searchEnabled: true, itemSelectText: '', searchPlaceholderValue: 'Rechercher un client...' });
            });
        });
    </script>
@endsection
