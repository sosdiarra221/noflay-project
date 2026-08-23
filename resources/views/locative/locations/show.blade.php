@extends('partials.layouts.master-locative')

@section('title', $location->numero.' | Locative')
@section('title-sub', 'Locations')
@section('pagetitle', $location->numero)

@section('content')
    <div id="layout-wrapper">

        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <div class="card">
            <div class="card-body">
                <h5 class="mb-1">{{ $location->numero }}</h5>
                <p class="text-muted mb-0">
                    Locataire : <a href="javascript:void(0)">{{ $location->locataire->nom_complet }}</a> — {{ $location->locataire->telephone }}
                </p>
            </div>
        </div>

        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h6 class="card-title mb-0 fw-semibold">Contrats générés ({{ $location->contrats->count() }})</h6>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-box table-responsive">
                            <table class="table text-nowrap align-middle mb-0">
                                <thead>
                                    <tr>
                                        <th>Contrat</th>
                                        <th>Bien</th>
                                        <th>Loyer</th>
                                        <th>Période</th>
                                        <th>Échéances</th>
                                        <th>Statut</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($location->contrats as $contrat)
                                        <tr>
                                            <td>{{ $contrat->numero }}</td>
                                            <td>{{ $contrat->bien->titre }}</td>
                                            <td>{{ number_format($contrat->loyer_mensuel, 0, ',', ' ') }} FCFA</td>
                                            <td>{{ $contrat->date_debut->format('d/m/Y') }} @if ($contrat->date_fin) → {{ $contrat->date_fin->format('d/m/Y') }} @endif</td>
                                            <td>{{ $contrat->echeances->count() }}</td>
                                            <td><span class="badge bg-success-subtle text-success text-capitalize">{{ $contrat->statut }}</span></td>
                                            <td>
                                                <div class="hstack gap-2">
                                                    <a href="{{ route('locative.contrats.show', $contrat) }}" class="btn btn-light-success icon-btn-sm"><i class="bi bi-eye"></i></a>
                                                    <a href="{{ route('locative.contrats.pdf', $contrat) }}" class="btn btn-light-info icon-btn-sm"><i class="bi bi-file-earmark-pdf"></i></a>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
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
