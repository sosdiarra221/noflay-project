@extends('partials.layouts.master-finance')

@section('title', 'Reversements | Finance')
@section('title-sub', 'Finance')
@section('pagetitle', 'Reversements aux bailleurs')

@section('content')
    <div id="layout-wrapper">

        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <div class="row">
            <div class="col-12 mb-4">
                <div class="card">
                    <div class="card-body">
                        <form method="GET" class="row g-3 align-items-end">
                            <div class="col-md-4">
                                <label class="form-label">Période</label>
                                <input type="month" class="form-control" name="periode" value="{{ $periode->format('Y-m') }}" onchange="this.form.submit()">
                            </div>
                            <div class="col-md-8 d-flex justify-content-end gap-2">
                                <a href="{{ route('finance.reversements.historique') }}" class="btn btn-light-secondary"><i class="bi bi-clock-history me-1"></i>Historique des reversements</a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h6 class="mb-0">Reversements à effectuer — {{ ucfirst($periode->translatedFormat('F Y')) }} <span class="badge bg-secondary-subtle text-secondary ms-1">{{ $lignes->count() }}</span></h6>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-box table-responsive">
                            <table class="table text-nowrap align-middle mb-0">
                                <thead>
                                    <tr>
                                        <th>Bailleur</th>
                                        <th>Encaissé</th>
                                        <th>Frais de gestion</th>
                                        <th>Net à reverser</th>
                                        <th>Statut</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($lignes as $ligne)
                                        <tr>
                                            <td>{{ $ligne['bailleur']->nom_complet }}</td>
                                            <td>{{ number_format($ligne['encaisse'], 0, ',', ' ') }} FCFA</td>
                                            <td>{{ number_format($ligne['frais_gestion'], 0, ',', ' ') }} FCFA</td>
                                            <td class="fw-medium">{{ number_format($ligne['net_a_reverser'], 0, ',', ' ') }} FCFA</td>
                                            <td>
                                                @if ($ligne['reversement'] && $ligne['reversement']->statut === 'verse')
                                                    <span class="badge bg-success-subtle text-success">Versé le {{ $ligne['reversement']->date_versement->format('d/m/Y') }}</span>
                                                @else
                                                    <span class="badge bg-warning-subtle text-warning">À verser</span>
                                                @endif
                                            </td>
                                            <td>
                                                @can('finance.gerer')
                                                    @if (! ($ligne['reversement'] && $ligne['reversement']->statut === 'verse'))
                                                        <button type="button" class="btn btn-light-success btn-sm" data-bs-toggle="modal" data-bs-target="#marquerVerseModal{{ $ligne['bailleur']->id }}">
                                                            <i class="bi bi-check-circle me-1"></i>Marquer comme versé
                                                        </button>
                                                    @else
                                                        <a href="{{ route('finance.reversements.bordereau', $ligne['reversement']) }}" class="btn btn-light-info btn-sm" target="_blank"><i class="bi bi-receipt me-1"></i>Bordereau</a>
                                                    @endif
                                                @endcan
                                            </td>
                                        </tr>
                                    @empty
                                        <tr><td colspan="6" class="text-center text-muted py-5">Aucun encaissement pour cette période.</td></tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        @can('finance.gerer')
            @foreach ($lignes as $ligne)
                @if (! ($ligne['reversement'] && $ligne['reversement']->statut === 'verse'))
                    <div class="modal fade" id="marquerVerseModal{{ $ligne['bailleur']->id }}" tabindex="-1" aria-hidden="true">
                        <div class="modal-dialog modal-dialog-centered" role="document">
                            <div class="modal-content">
                                <form action="{{ route('finance.reversements.marquer-verse') }}" method="POST">
                                    @csrf
                                    <input type="hidden" name="bailleur_id" value="{{ $ligne['bailleur']->id }}">
                                    <input type="hidden" name="periode_annee" value="{{ $periode->year }}">
                                    <input type="hidden" name="periode_mois" value="{{ $periode->month }}">
                                    <input type="hidden" name="montant_encaisse" value="{{ $ligne['encaisse'] }}">
                                    <input type="hidden" name="montant_frais_gestion" value="{{ $ligne['frais_gestion'] }}">
                                    <input type="hidden" name="montant_net" value="{{ $ligne['net_a_reverser'] }}">
                                    <div class="modal-header">
                                        <h5 class="modal-title">Marquer comme versé — {{ $ligne['bailleur']->nom_complet }}</h5>
                                        <button type="button" class="btn-close icon-btn-sm" data-bs-dismiss="modal" aria-label="Close"><i class="ri-close-large-line fw-semibold"></i></button>
                                    </div>
                                    <div class="modal-body">
                                        <p class="text-muted">Montant net à reverser : <strong>{{ number_format($ligne['net_a_reverser'], 0, ',', ' ') }} FCFA</strong></p>
                                        <div class="row g-3">
                                            <div class="col-12">
                                                <label class="form-label">Date du versement<span class="text-danger ms-1">*</span></label>
                                                <input type="date" class="form-control" name="date_versement" value="{{ now()->format('Y-m-d') }}" required>
                                            </div>
                                            <div class="col-12">
                                                <label class="form-label">Mode de paiement</label>
                                                <select class="form-select" name="mode_paiement_id">
                                                    <option value="">—</option>
                                                    @foreach (\App\Models\ModePaiement::where('actif', true)->orderBy('nom')->get() as $mode)
                                                        <option value="{{ $mode->id }}">{{ $mode->nom }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                            <div class="col-12">
                                                <label class="form-label">Référence</label>
                                                <input type="text" class="form-control" name="reference" placeholder="Ex: VIR-XXXXXXXX">
                                            </div>
                                            <div class="col-12">
                                                <label class="form-label">Notes</label>
                                                <textarea class="form-control" name="notes" rows="2"></textarea>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Fermer</button>
                                        <button type="submit" class="btn btn-primary">Confirmer le versement</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                @endif
            @endforeach
        @endcan

    </div>
    </main>
@endsection

@section('js')
    <script type="module" src="{{ asset('assets/js/app.js') }}"></script>
@endsection
