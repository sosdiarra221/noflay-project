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
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <form method="GET" class="d-flex flex-wrap gap-3 justify-content-between align-items-center">
                            <h6 class="card-title mb-0 fw-semibold">
                                Biens <span class="badge bg-secondary-subtle text-secondary ms-1">{{ $biens->count() }}</span>
                            </h6>
                            <div class="d-flex flex-wrap gap-2">
                                <select class="form-select" name="bailleur_id" onchange="this.form.submit()">
                                    <option value="">Tous les bailleurs</option>
                                    @foreach ($bailleurs as $bailleur)
                                        <option value="{{ $bailleur->id }}" @selected(request('bailleur_id') == $bailleur->id)>{{ $bailleur->nom_complet }}</option>
                                    @endforeach
                                </select>
                                <select class="form-select" name="type_exploitation" onchange="this.form.submit()">
                                    <option value="">Tous types</option>
                                    <option value="location" @selected(request('type_exploitation') === 'location')>Location</option>
                                    <option value="vente" @selected(request('type_exploitation') === 'vente')>Vente</option>
                                </select>
                                <select class="form-select" name="statut" onchange="this.form.submit()">
                                    <option value="">Tous statuts</option>
                                    @foreach (array_merge(\App\Models\Bien::STATUTS_LOCATION, \App\Models\Bien::STATUTS_VENTE) as $statutOption)
                                        <option value="{{ $statutOption }}" @selected(request('statut') === $statutOption)>{{ str_replace('_', ' ', ucfirst($statutOption)) }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </form>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-box table-responsive">
                            <table class="table text-nowrap align-middle mb-0">
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
                                                    @if ($bien->gerance)
                                                        <a href="{{ route('locative.gerances.show', $bien->gerance) }}" class="btn btn-light-success icon-btn-sm"><i class="bi bi-eye"></i></a>
                                                        <button type="button" class="btn btn-light-primary icon-btn-sm" data-bs-toggle="modal" data-bs-target="#editBienModal{{ $bien->id }}"><i class="bi bi-pencil-square"></i></button>
                                                    @endif
                                                </div>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="7" class="text-center text-muted py-5">Aucun bien ne correspond à ces filtres.</td>
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
