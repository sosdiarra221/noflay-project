@extends('partials.layouts.master-finance')

@section('title', 'Comptes bailleurs | Finance')
@section('title-sub', 'Finance')
@section('pagetitle', 'Comptes bailleurs')

@section('content')
    <div id="layout-wrapper">

        <div class="row">
            <div class="col-12 mb-4">
                <div class="card">
                    <div class="card-body">
                        <form method="GET" class="row g-3">
                            <div class="col-md-6">
                                <input type="text" class="form-control" name="q" value="{{ request('q') }}" placeholder="Rechercher un bailleur (nom, numéro)...">
                            </div>
                            <div class="col-md-2">
                                <button type="submit" class="btn btn-primary w-100">Rechercher</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h6 class="mb-0">Bailleurs <span class="badge bg-secondary-subtle text-secondary ms-1">{{ $lignes->count() }}</span></h6>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-box table-responsive">
                            <table class="table text-nowrap align-middle mb-0">
                                <thead>
                                    <tr>
                                        <th>Bailleur</th>
                                        <th>Numéro</th>
                                        <th>Net à reverser (ce mois)</th>
                                        <th></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($lignes as $ligne)
                                        <tr>
                                            <td class="fw-medium">{{ $ligne['bailleur']->nom_complet }}</td>
                                            <td>{{ $ligne['bailleur']->numero }}</td>
                                            <td class="{{ $ligne['net_a_reverser'] > 0 ? 'text-warning' : 'text-muted' }} fw-semibold">{{ number_format($ligne['net_a_reverser'], 0, ',', ' ') }} FCFA</td>
                                            <td><a href="{{ route('finance.bailleurs.show', $ligne['bailleur']) }}" class="btn btn-light-primary icon-btn-sm"><i class="bi bi-eye"></i></a></td>
                                        </tr>
                                    @empty
                                        <tr><td colspan="4" class="text-center text-muted py-5">Aucun bailleur.</td></tr>
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
