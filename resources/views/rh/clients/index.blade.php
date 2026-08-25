@extends('partials.layouts.master-rh')

@section('title', 'Clients | RH')
@section('title-sub', 'RH')
@section('pagetitle', 'Clients')

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

        <div class="row g-3 mb-1">
            <div class="col-md-6 col-xl-3">
                <div class="card border">
                    <div class="card-body">
                        <div class="d-flex gap-4 border-bottom pb-5 mb-5">
                            <div class="h-50px w-50px bg-primary text-white d-flex align-items-center justify-content-center rounded fs-3"><i class="bi bi-building"></i></div>
                            <div><h5 class="mb-2">{{ $stats['total'] }}</h5><p class="text-muted mb-0 fs-12">Clients</p></div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-6 col-xl-3">
                <div class="card border">
                    <div class="card-body">
                        <div class="d-flex gap-4 border-bottom pb-5 mb-5">
                            <div class="h-50px w-50px bg-success text-white d-flex align-items-center justify-content-center rounded fs-3"><i class="bi bi-geo-alt"></i></div>
                            <div><h5 class="mb-2">{{ $stats['avec_sites'] }}</h5><p class="text-muted mb-0 fs-12">Clients avec site(s)</p></div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-6 col-xl-3">
                <div class="card border">
                    <div class="card-body">
                        <div class="d-flex gap-4 border-bottom pb-5 mb-5">
                            <div class="h-50px w-50px bg-secondary text-white d-flex align-items-center justify-content-center rounded fs-3"><i class="bi bi-slash-circle"></i></div>
                            <div><h5 class="mb-2">{{ $stats['sans_site'] }}</h5><p class="text-muted mb-0 fs-12">Sans site lié</p></div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-6 col-xl-3">
                <div class="card border">
                    <div class="card-body">
                        <div class="d-flex gap-4 border-bottom pb-5 mb-5">
                            <div class="h-50px w-50px bg-info text-white d-flex align-items-center justify-content-center rounded fs-3"><i class="bi bi-diagram-3"></i></div>
                            <div><h5 class="mb-2">{{ $stats['total_sites'] }}</h5><p class="text-muted mb-0 fs-12">Sites liés au total</p></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="card">
            <div class="card-body">
                <form method="GET" class="row g-3 align-items-end">
                    <div class="col-md-4">
                        <label class="form-label">Client</label>
                        <select class="form-select" name="client_id" id="selectFiltreClient" onchange="this.form.submit()">
                            <option value="">Tous</option>
                            @foreach ($tousClients as $client)
                                <option value="{{ $client->id }}" @selected(request('client_id') == $client->id)>{{ $client->nom_complet }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3">
                        <a href="{{ route('rh.clients.index') }}" class="btn btn-light-danger">Réinitialiser</a>
                    </div>
                </form>
            </div>
        </div>

        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <div>
                    <h6 class="mb-0">Clients <span class="badge bg-secondary-subtle text-secondary ms-1">{{ $clients->count() }}</span></h6>
                    <p class="text-muted fs-12 mb-0">Ce sont les mêmes clients que ceux liés aux sites (un client peut avoir plusieurs sites).</p>
                </div>
                <button type="button" class="btn btn-primary text-nowrap" data-bs-toggle="modal" data-bs-target="#creerClientModal">
                    <i class="bi bi-plus-lg me-1"></i>Nouveau client
                </button>
            </div>
            <div class="card-body p-0">
                <div class="table-box table-responsive">
                    <table class="table text-nowrap align-middle mb-0">
                        <thead><tr><th>Nom</th><th>Téléphone</th><th>Email</th><th>Sites liés</th></tr></thead>
                        <tbody>
                            @forelse ($clients as $client)
                                <tr>
                                    <td class="fw-medium">{{ $client->nom_complet }}</td>
                                    <td>{{ $client->telephone ?: '—' }}</td>
                                    <td>{{ $client->email ?: '—' }}</td>
                                    <td><span class="badge bg-secondary-subtle text-secondary">{{ $client->sites_count }}</span></td>
                                </tr>
                            @empty
                                <tr><td colspan="4" class="text-center text-muted py-5">Aucun client ne correspond à ce filtre.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

    </div>

    <div class="modal fade" id="creerClientModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <form action="{{ route('rh.clients.store') }}" method="POST">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title">Nouveau client</h5>
                        <button type="button" class="btn-close icon-btn-sm" data-bs-dismiss="modal" aria-label="Close"><i class="ri-close-large-line fw-semibold"></i></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">Nom complet<span class="text-danger ms-1">*</span></label>
                            <input type="text" class="form-control" name="nom_complet" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Téléphone</label>
                            <input type="text" class="form-control" name="telephone">
                        </div>
                        <div class="mb-0">
                            <label class="form-label">Email</label>
                            <input type="email" class="form-control" name="email">
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
            new Choices(document.getElementById('selectFiltreClient'), { searchEnabled: true, itemSelectText: '', searchPlaceholderValue: 'Rechercher un client...' });
        });
    </script>
@endsection
