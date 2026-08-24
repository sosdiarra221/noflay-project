@extends('partials.layouts.master-finance')

@section('title', 'Comptes locataires | Finance')
@section('title-sub', 'Finance')
@section('pagetitle', 'Comptes locataires')

@section('content')
    <div id="layout-wrapper">

        <div class="row">
            <div class="col-12 mb-4">
                <div class="card">
                    <div class="card-body">
                        <form method="GET" class="row g-3">
                            <div class="col-md-6">
                                <input type="text" class="form-control" name="q" value="{{ request('q') }}" placeholder="Rechercher un locataire (nom, numéro)...">
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
                        <h6 class="mb-0">Locataires <span class="badge bg-secondary-subtle text-secondary ms-1">{{ $locataires->count() }}</span></h6>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-box table-responsive">
                            <table class="table text-nowrap align-middle mb-0">
                                <thead>
                                    <tr>
                                        <th>Locataire</th>
                                        <th>Numéro</th>
                                        <th>Loyers dus</th>
                                        <th>Loyers payés</th>
                                        <th>Solde</th>
                                        <th></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($locataires as $ligne)
                                        @php $solde = $ligne['du'] - $ligne['paye']; @endphp
                                        <tr>
                                            <td class="fw-medium">{{ $ligne['locataire']->nom_complet }}</td>
                                            <td>{{ $ligne['locataire']->numero }}</td>
                                            <td>{{ number_format($ligne['du'], 0, ',', ' ') }} FCFA</td>
                                            <td class="text-success">{{ number_format($ligne['paye'], 0, ',', ' ') }} FCFA</td>
                                            <td class="{{ $solde > 0 ? 'text-danger' : 'text-success' }} fw-semibold">{{ number_format($solde, 0, ',', ' ') }} FCFA</td>
                                            <td><a href="{{ route('finance.locataires.show', $ligne['locataire']) }}" class="btn btn-light-primary icon-btn-sm"><i class="bi bi-eye"></i></a></td>
                                        </tr>
                                    @empty
                                        <tr><td colspan="6" class="text-center text-muted py-5">Aucun locataire.</td></tr>
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
