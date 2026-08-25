@extends('partials.layouts.master-facturation')

@section('title', 'Devis | Facturation')
@section('title-sub', 'Facturation')
@section('pagetitle', 'Devis')

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

        <div class="row">
            <div class="col-md-6 col-xl-3">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex align-items-start pb-4 gap-4 border-bottom border-dashed">
                            <div class="flex-grow-1">
                                <h2>{{ $stats['total'] }}</h2>
                                <h6 class="fw-medium mb-0">Total devis</h6>
                            </div>
                            <div class="flex-shrink-0 h-56px w-56px bg-light-subtle d-flex justify-content-center align-items-center rounded text-muted fs-3">
                                <i class="bi bi-file-earmark-text"></i>
                            </div>
                        </div>
                        <p class="fs-12 pt-4 mb-0 text-muted text-center">Tous statuts confondus</p>
                    </div>
                </div>
            </div>
            <div class="col-md-6 col-xl-3">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex align-items-start pb-4 gap-4 border-bottom border-dashed">
                            <div class="flex-grow-1">
                                <h2>{{ $stats['en_negociation'] }}</h2>
                                <h6 class="fw-medium mb-0">En négociation</h6>
                            </div>
                            <div class="flex-shrink-0 h-56px w-56px bg-warning-subtle d-flex justify-content-center align-items-center rounded text-warning fs-3">
                                <i class="bi bi-hourglass-split"></i>
                            </div>
                        </div>
                        <p class="fs-12 pt-4 mb-0 text-muted text-center">En cours de discussion</p>
                    </div>
                </div>
            </div>
            <div class="col-md-6 col-xl-3">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex align-items-start pb-4 gap-4 border-bottom border-dashed">
                            <div class="flex-grow-1">
                                <h2>{{ $stats['gagne'] }}</h2>
                                <h6 class="fw-medium mb-0">Gagnés</h6>
                            </div>
                            <div class="flex-shrink-0 h-56px w-56px bg-success-subtle d-flex justify-content-center align-items-center rounded text-success fs-3">
                                <i class="bi bi-patch-check"></i>
                            </div>
                        </div>
                        <p class="fs-12 pt-4 mb-0 text-muted text-center">Convertis en facture</p>
                    </div>
                </div>
            </div>
            <div class="col-md-6 col-xl-3">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex align-items-start pb-4 gap-4 border-bottom border-dashed">
                            <div class="flex-grow-1">
                                <h2>{{ $stats['perdu'] }}</h2>
                                <h6 class="fw-medium mb-0">Perdus</h6>
                            </div>
                            <div class="flex-shrink-0 h-56px w-56px bg-danger-subtle d-flex justify-content-center align-items-center rounded text-danger fs-3">
                                <i class="bi bi-x-circle"></i>
                            </div>
                        </div>
                        <p class="fs-12 pt-4 mb-0 text-muted text-center">Non convertis</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="card">
            <div class="card-header d-flex flex-wrap gap-3 justify-content-between align-items-center">
                <h6 class="mb-0">Devis <span class="badge bg-secondary-subtle text-secondary ms-1">{{ $devis->count() }}</span></h6>
                <form method="GET" class="d-flex flex-wrap gap-2 align-items-center" id="formFiltresDevis">
                    <div class="form-icon">
                        <input type="text" class="form-control form-control-icon" name="recherche" value="{{ request('recherche') }}" placeholder="Numéro ou client...">
                        <i class="ri-search-2-line text-muted"></i>
                    </div>
                    <select class="form-select" name="statut" id="selectFiltreStatut" onchange="document.getElementById('formFiltresDevis').submit()">
                        <option value="">Tous statuts</option>
                        @foreach (\App\Models\Facturation\Devis::STATUTS as $valeur => $libelle)
                            <option value="{{ $valeur }}" @selected(request('statut') === $valeur)>{{ $libelle }}</option>
                        @endforeach
                    </select>
                    <select class="form-select" name="client_id" id="selectFiltreClient" onchange="document.getElementById('formFiltresDevis').submit()">
                        <option value="">Tous les clients</option>
                        @foreach ($clients as $client)
                            <option value="{{ $client->id }}" @selected(request('client_id') == $client->id)>{{ $client->nom_complet }}</option>
                        @endforeach
                    </select>
                    @if (request()->anyFilled(['statut', 'client_id', 'recherche']))
                        <a href="{{ route('facturation.devis.index') }}" class="btn btn-light-danger icon-btn" title="Réinitialiser"><i class="bi bi-x-lg"></i></a>
                    @endif
                    <a href="{{ route('facturation.devis.create') }}" class="btn btn-primary"><i class="bi bi-plus-lg me-1"></i>Nouveau devis</a>
                </form>
            </div>
            <div class="card-body p-0">
                <div class="table-box table-responsive">
                    <table class="table text-nowrap align-middle mb-0">
                        <thead><tr><th>Numéro</th><th>Client</th><th>Date</th><th>Lignes</th><th>Total TTC</th><th>Statut</th><th>Actions</th></tr></thead>
                        <tbody>
                            @php $classesStatut = ['nouveau' => 'secondary', 'en_negociation' => 'warning', 'gagne' => 'success', 'perdu' => 'danger', 'annule' => 'dark']; @endphp
                            @forelse ($devis as $d)
                                <tr>
                                    <td class="fw-medium"><a href="{{ route('facturation.devis.show', $d) }}" class="text-reset">{{ $d->numero }}</a></td>
                                    <td>{{ $d->client->nom_complet ?? '—' }}</td>
                                    <td>{{ $d->date_devis->format('d/m/Y') }}</td>
                                    <td>{{ $d->lignes->count() }}</td>
                                    <td class="fw-medium">{{ number_format($d->total_ttc, 0, ',', ' ') }} FCFA</td>
                                    <td><span class="badge bg-{{ $classesStatut[$d->statut] ?? 'secondary' }}-subtle text-{{ $classesStatut[$d->statut] ?? 'secondary' }}">{{ $d->libelleStatut() }}</span></td>
                                    <td>
                                        <a href="{{ route('facturation.devis.show', $d) }}" class="btn btn-light-success btn-sm" title="Voir le devis"><i class="bi bi-eye me-1"></i>Détail</a>
                                    </td>
                                </tr>
                            @empty
                                <tr><td colspan="7" class="text-center text-muted py-5">Aucun devis ne correspond à ces filtres.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
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
            new Choices(document.getElementById('selectFiltreStatut'), { searchEnabled: false, itemSelectText: '' });
            new Choices(document.getElementById('selectFiltreClient'), { searchEnabled: true, itemSelectText: '', searchPlaceholderValue: 'Rechercher...' });

            // Filtre "recherche" auto-synchronisé avec le tableau : la liste se ré-interroge
            // sans clic sur un bouton, avec un léger délai pour ne pas soumettre à chaque frappe.
            let minuteur;
            document.querySelector('input[name="recherche"]').addEventListener('input', function () {
                clearTimeout(minuteur);
                minuteur = setTimeout(() => document.getElementById('formFiltresDevis').submit(), 500);
            });
        });
    </script>
@endsection
