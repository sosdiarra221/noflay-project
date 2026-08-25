@extends('partials.layouts.master-rh')

@section('title', 'Sites | RH')
@section('title-sub', 'RH')
@section('pagetitle', 'Sites')

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
            <div class="card-header d-flex justify-content-between align-items-center">
                <h6 class="mb-0">Sites <span class="badge bg-secondary-subtle text-secondary ms-1">{{ $sites->count() }}</span></h6>
                <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#creerSiteModal">
                    <i class="bi bi-plus-lg me-1"></i>Nouveau site
                </button>
            </div>
            <div class="card-body p-0">
                <div class="table-box table-responsive">
                    <table class="table text-nowrap align-middle mb-0">
                        <thead><tr><th>Nom</th><th>Client</th><th>Adresse</th><th>Employés affectés</th><th>Statut</th><th></th></tr></thead>
                        <tbody>
                            @forelse ($sites as $site)
                                <tr>
                                    <td class="fw-medium">{{ $site->nom }}</td>
                                    <td>{{ $site->client->nom_complet ?? '—' }}</td>
                                    <td>{{ $site->adresse ?: '—' }}</td>
                                    <td>{{ $site->employes_count }}</td>
                                    <td>
                                        @if ($site->actif)
                                            <span class="badge bg-success-subtle text-success">Actif</span>
                                        @else
                                            <span class="badge bg-secondary-subtle text-secondary">Inactif</span>
                                        @endif
                                    </td>
                                    <td>
                                        <button type="button" class="btn btn-light-primary icon-btn-sm" data-bs-toggle="modal" data-bs-target="#modifierSiteModal{{ $site->id }}"><i class="bi bi-pencil-square"></i></button>
                                    </td>
                                </tr>
                            @empty
                                <tr><td colspan="6" class="text-center text-muted py-5">Aucun site enregistré.</td></tr>
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

    @foreach ($sites as $site)
        <div class="modal fade" id="modifierSiteModal{{ $site->id }}" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered" role="document">
                <div class="modal-content">
                    <form action="{{ route('rh.sites.update', $site) }}" method="POST">
                        @csrf
                        @method('PUT')
                        <div class="modal-header">
                            <h5 class="modal-title">Modifier {{ $site->nom }}</h5>
                            <button type="button" class="btn-close icon-btn-sm" data-bs-dismiss="modal" aria-label="Close"><i class="ri-close-large-line fw-semibold"></i></button>
                        </div>
                        <div class="modal-body">
                            <div class="mb-3">
                                <label class="form-label">Nom<span class="text-danger ms-1">*</span></label>
                                <input type="text" class="form-control" name="nom" value="{{ $site->nom }}" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Client <span class="text-muted fs-11">(un client peut avoir plusieurs sites)</span></label>
                                <select class="form-select select-client-site" name="client_id">
                                    <option value="">Aucun (site interne à l'agence)</option>
                                    @foreach ($clients as $client)
                                        <option value="{{ $client->id }}" @selected($site->client_id === $client->id)>{{ $client->nom_complet }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Adresse</label>
                                <input type="text" class="form-control" name="adresse" value="{{ $site->adresse }}">
                            </div>
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" name="actif" value="1" id="actifSite{{ $site->id }}" @checked($site->actif)>
                                <label class="form-check-label" for="actifSite{{ $site->id }}">Site actif</label>
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
            document.querySelectorAll('.select-client-site').forEach(function (select) {
                new Choices(select, { searchEnabled: true, itemSelectText: '', searchPlaceholderValue: 'Rechercher un client...' });
            });
        });
    </script>
@endsection
