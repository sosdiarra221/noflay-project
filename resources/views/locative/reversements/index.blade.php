@extends('partials.layouts.master-locative')

@section('title', 'Reversements | Locative')
@section('title-sub', 'Locative')
@section('pagetitle', 'Reversements')

@section('content')
    <div id="layout-wrapper">

        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <form method="GET" class="d-flex flex-wrap gap-3 justify-content-between align-items-center">
                            <h6 class="card-title mb-0 fw-semibold">
                                Reversements aux bailleurs — {{ $periode->translatedFormat('F Y') }}
                            </h6>
                            <input type="month" class="form-control" name="periode" value="{{ $periode->format('Y-m') }}" onchange="this.form.submit()" style="max-width: 200px">
                        </form>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-box table-responsive">
                            <table class="table text-nowrap align-middle mb-0">
                                <thead>
                                    <tr>
                                        <th>Bailleur</th>
                                        <th>Loyers encaissés</th>
                                        <th>Frais de gestion</th>
                                        <th>Net à reverser</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($reversements as $ligne)
                                        <tr>
                                            <td><a href="{{ route('locative.bailleurs.show', $ligne['bailleur']) }}" class="text-reset fw-medium">{{ $ligne['bailleur']->nom_complet }}</a></td>
                                            <td>{{ number_format($ligne['encaisse'], 0, ',', ' ') }} FCFA</td>
                                            <td class="text-danger">- {{ number_format($ligne['frais_gestion'], 0, ',', ' ') }} FCFA</td>
                                            <td class="fw-semibold">{{ number_format($ligne['net_a_reverser'], 0, ',', ' ') }} FCFA</td>
                                            <td><a href="{{ route('locative.bailleurs.show', $ligne['bailleur']) }}" class="btn btn-light-success icon-btn-sm"><i class="bi bi-eye"></i></a></td>
                                        </tr>
                                    @empty
                                        <tr><td colspan="5" class="text-center text-muted py-5">Aucun encaissement sur cette période.</td></tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <p class="text-muted fs-12">Le calcul déduit les frais de gestion du contrat de gérance (ou de l'override du bien). La TVA, la taxe et le TOM sont configurés par contrat mais aucun taux n'est encore paramétrable — leur montant n'est donc pas déduit ici.</p>
            </div>
        </div>

    </div>
    </main>
@endsection

@section('js')
    <script type="module" src="{{ asset('assets/js/app.js') }}"></script>
@endsection
