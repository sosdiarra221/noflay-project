@extends('partials.layouts.master-locative')

@section('title', 'Gérances | Locative')
@section('title-sub', 'Locative')
@section('pagetitle', 'Contrats de gérance')

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
                                Gérances <span class="badge bg-secondary-subtle text-secondary ms-1">{{ $gerances->count() }}</span>
                            </h6>
                            <a href="{{ route('locative.gerances.create') }}" class="btn btn-light-primary">
                                <i class="bi bi-plus-lg me-1"></i>Nouvelle gérance
                            </a>
                        </div>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-box table-responsive">
                            <table class="table text-nowrap align-middle mb-0">
                                <thead>
                                    <tr>
                                        <th scope="col">Numéro</th>
                                        <th scope="col">Bailleur</th>
                                        <th scope="col">Type</th>
                                        <th scope="col">Période</th>
                                        <th scope="col">Biens</th>
                                        <th scope="col">Statut</th>
                                        <th scope="col">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($gerances as $gerance)
                                        <tr>
                                            <td><a href="{{ route('locative.gerances.show', $gerance) }}" class="fw-medium text-reset">{{ $gerance->numero }}</a></td>
                                            <td>{{ $gerance->bailleur->nom_complet }}</td>
                                            <td class="text-capitalize">{{ str_replace('_', ' ', $gerance->type_gerance) }}</td>
                                            <td>{{ $gerance->date_debut->format('d/m/Y') }} @if ($gerance->date_fin) → {{ $gerance->date_fin->format('d/m/Y') }} @endif</td>
                                            <td>{{ $gerance->biens_count }}</td>
                                            <td>
                                                @php
                                                    $classes = ['actif' => 'success', 'brouillon' => 'secondary', 'en_attente_signature' => 'warning', 'suspendu' => 'warning', 'expire' => 'dark', 'resilie' => 'danger', 'archive' => 'dark'];
                                                @endphp
                                                <span class="badge bg-{{ $classes[$gerance->statut] ?? 'secondary' }}-subtle text-{{ $classes[$gerance->statut] ?? 'secondary' }} text-capitalize">{{ str_replace('_', ' ', $gerance->statut) }}</span>
                                            </td>
                                            <td>
                                                <div class="hstack gap-2">
                                                    <a href="{{ route('locative.gerances.show', $gerance) }}" class="btn btn-light-success icon-btn-sm"><i class="bi bi-eye"></i></a>
                                                    <a href="{{ route('locative.gerances.pdf', $gerance) }}" class="btn btn-light-info icon-btn-sm"><i class="bi bi-file-earmark-pdf"></i></a>
                                                </div>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="7" class="text-center text-muted py-5">Aucun contrat de gérance pour le moment.</td>
                                        </tr>
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
