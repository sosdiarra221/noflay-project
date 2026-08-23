@extends('partials.layouts.master-locative')

@section('title', 'Locations | Locative')
@section('title-sub', 'Locative')
@section('pagetitle', 'Locations')

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
                        <div class="d-flex flex-wrap gap-3 justify-content-between align-items-center">
                            <h6 class="card-title mb-0 fw-semibold">
                                Locations <span class="badge bg-secondary-subtle text-secondary ms-1">{{ $locations->count() }}</span>
                            </h6>
                            <a href="{{ route('locative.locations.create') }}" class="btn btn-light-primary">
                                <i class="bi bi-plus-lg me-1"></i>Nouvelle location
                            </a>
                        </div>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-box table-responsive">
                            <table class="table text-nowrap align-middle mb-0">
                                <thead>
                                    <tr>
                                        <th scope="col">Numéro</th>
                                        <th scope="col">Locataire</th>
                                        <th scope="col">Biens</th>
                                        <th scope="col">Loyer total</th>
                                        <th scope="col">Date</th>
                                        <th scope="col">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($locations as $location)
                                        <tr>
                                            <td><a href="{{ route('locative.locations.show', $location) }}" class="fw-medium text-reset">{{ $location->numero }}</a></td>
                                            <td>{{ $location->locataire->nom_complet }}</td>
                                            <td>{{ $location->contrats->count() }}</td>
                                            <td>{{ number_format($location->contrats->sum('loyer_mensuel'), 0, ',', ' ') }} FCFA</td>
                                            <td>{{ $location->created_at->format('d/m/Y') }}</td>
                                            <td>
                                                <a href="{{ route('locative.locations.show', $location) }}" class="btn btn-light-success icon-btn-sm"><i class="bi bi-eye"></i></a>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr><td colspan="6" class="text-center text-muted py-5">Aucune location pour le moment.</td></tr>
                                    @endforelse
                                </tbody>
                            </table>
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
