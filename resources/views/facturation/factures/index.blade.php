@extends('partials.layouts.master-facturation')

@section('title', 'Factures | Facturation')
@section('title-sub', 'Facturation')
@section('pagetitle', 'Factures')

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
                <form method="GET" class="row g-3 align-items-end">
                    <div class="col-md-4">
                        <label class="form-label">Statut</label>
                        <select class="form-select" name="statut" onchange="this.form.submit()">
                            <option value="">Tous statuts</option>
                            @foreach (\App\Models\Facturation\Facture::STATUTS as $valeur => $libelle)
                                <option value="{{ $valeur }}" @selected(request('statut') === $valeur)>{{ $libelle }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-4">
                        <a href="{{ route('facturation.factures.index') }}" class="btn btn-light-danger">Réinitialiser</a>
                    </div>
                </form>
            </div>
        </div>

        <div class="card">
            <div class="card-header">
                <h6 class="mb-0">Factures <span class="badge bg-secondary-subtle text-secondary ms-1">{{ $factures->count() }}</span></h6>
                <p class="text-muted fs-12 mb-0">Les factures sont générées automatiquement lorsqu'un devis passe au statut « Gagné ».</p>
            </div>
            <div class="card-body p-0">
                <div class="table-box table-responsive">
                    <table class="table text-nowrap align-middle mb-0">
                        <thead><tr><th>Numéro</th><th>Client</th><th>Source</th><th>Date</th><th>Total TTC</th><th>Statut</th><th>Actions</th></tr></thead>
                        <tbody>
                            @php $classesStatut = ['emise' => 'info', 'payee' => 'success', 'annulee' => 'danger']; @endphp
                            @forelse ($factures as $facture)
                                <tr>
                                    <td class="fw-medium"><a href="{{ route('facturation.factures.show', $facture) }}" class="text-reset">{{ $facture->numero }}</a></td>
                                    <td>{{ $facture->client->nom_complet ?? '—' }}</td>
                                    <td class="text-muted fs-12">{{ $facture->source ?: '—' }}</td>
                                    <td>{{ $facture->date_facture->format('d/m/Y') }}</td>
                                    <td class="fw-medium">{{ number_format($facture->total_ttc, 0, ',', ' ') }} FCFA</td>
                                    <td><span class="badge bg-{{ $classesStatut[$facture->statut] ?? 'secondary' }}-subtle text-{{ $classesStatut[$facture->statut] ?? 'secondary' }}">{{ $facture->libelleStatut() }}</span></td>
                                    <td>
                                        <a href="{{ route('facturation.factures.show', $facture) }}" class="btn btn-light-success btn-sm" title="Voir la facture"><i class="bi bi-eye me-1"></i>Détail</a>
                                    </td>
                                </tr>
                            @empty
                                <tr><td colspan="7" class="text-center text-muted py-5">Aucune facture pour le moment.</td></tr>
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
    <script type="module" src="{{ asset('assets/js/app.js') }}"></script>
@endsection
