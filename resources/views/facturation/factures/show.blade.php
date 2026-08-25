@extends('partials.layouts.master-facturation')

@section('title', $facture->numero.' | Facturation')
@section('title-sub', 'Factures')
@section('pagetitle', $facture->numero)

@section('content')
    <div id="layout-wrapper">

        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        @php $classesStatut = ['emise' => 'info', 'payee' => 'success', 'annulee' => 'danger']; @endphp

        <div class="card">
            <div class="card-body">
                <div class="d-flex flex-wrap justify-content-between align-items-start gap-3">
                    <div>
                        <h5 class="mb-1">{{ $facture->numero }} <span class="badge bg-{{ $classesStatut[$facture->statut] ?? 'secondary' }}-subtle text-{{ $classesStatut[$facture->statut] ?? 'secondary' }} ms-1">{{ $facture->libelleStatut() }}</span></h5>
                        <p class="text-muted mb-0">
                            {{ $facture->client->nom_complet }} — {{ $facture->date_facture->format('d/m/Y') }} — <strong>{{ number_format($facture->total_ttc, 0, ',', ' ') }} FCFA</strong>
                        </p>
                        @if ($facture->source)
                            <p class="text-muted fs-12 mb-0">
                                <i class="bi bi-arrow-return-right me-1"></i>Source :
                                @if ($facture->devisSource)
                                    <a href="{{ route('facturation.devis.show', $facture->devisSource) }}">{{ $facture->source }}</a>
                                @else
                                    {{ $facture->source }}
                                @endif
                            </p>
                        @endif
                    </div>
                    <div class="d-flex flex-wrap gap-2">
                        <button type="button" class="btn btn-light-info" data-bs-toggle="modal" data-bs-target="#facturePdfModal"><i class="bi bi-file-earmark-pdf me-1"></i>Aperçu / PDF</button>
                        <button type="button" class="btn btn-light-primary" data-bs-toggle="modal" data-bs-target="#statutFactureModal"><i class="bi bi-arrow-repeat me-1"></i>Changer le statut</button>
                    </div>
                </div>
            </div>
        </div>

        <div class="row g-4">
            <div class="col-lg-4">
                <div class="card">
                    <div class="card-header"><h6 class="card-action-title mb-0">Client / Prospect</h6></div>
                    <div class="card-body">
                        <div class="row mb-3"><div class="col-5 text-muted">Nom complet</div><div class="col-7 fw-medium">{{ $facture->client->nom_complet }}</div></div>
                        <div class="row mb-3"><div class="col-5 text-muted">Téléphone</div><div class="col-7 fw-medium">{{ $facture->client->telephone ?: '—' }}</div></div>
                        <div class="row"><div class="col-5 text-muted">Email</div><div class="col-7 fw-medium">{{ $facture->client->email ?: '—' }}</div></div>
                    </div>
                </div>
                <div class="card">
                    <div class="card-header"><h6 class="card-action-title mb-0">Informations</h6></div>
                    <div class="card-body">
                        <div class="row mb-3"><div class="col-5 text-muted">Date</div><div class="col-7 fw-medium">{{ $facture->date_facture->format('d/m/Y') }}</div></div>
                        <div class="row mb-3"><div class="col-5 text-muted">TVA</div><div class="col-7 fw-medium">{{ $facture->appliquer_tva ? number_format((float) $facture->taux_tva, 2, ',', ' ').' %' : 'Non activée' }}</div></div>
                        <div class="row"><div class="col-5 text-muted">Créée par</div><div class="col-7 fw-medium">{{ $facture->creePar->name ?? '—' }}</div></div>
                    </div>
                </div>
            </div>
            <div class="col-lg-8">
                <div class="card">
                    <div class="card-header"><h6 class="mb-0">Lignes de la facture <span class="badge bg-secondary-subtle text-secondary ms-1">{{ $facture->lignes->count() }}</span></h6></div>
                    <div class="card-body p-0">
                        <div class="table-box table-responsive">
                            <table class="table align-middle mb-0">
                                <thead><tr><th>Désignation</th><th class="text-end">Quantité</th><th class="text-end">Prix unitaire</th><th class="text-end">Total</th></tr></thead>
                                <tbody>
                                    @foreach ($facture->lignes as $ligne)
                                        <tr>
                                            <td>{{ $ligne->designation }}</td>
                                            <td class="text-end">{{ number_format($ligne->quantite, 2, ',', ' ') }}</td>
                                            <td class="text-end">{{ number_format($ligne->prix_unitaire, 0, ',', ' ') }} FCFA</td>
                                            <td class="text-end fw-medium">{{ number_format($ligne->total, 0, ',', ' ') }} FCFA</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        <div class="p-4 d-flex justify-content-end">
                            <div style="min-width: 300px;">
                                <div class="d-flex justify-content-between py-1"><span class="text-muted">Sous-total HT</span><span class="fw-medium">{{ number_format($facture->sous_total_ht, 0, ',', ' ') }} FCFA</span></div>
                                @if ($facture->appliquer_tva)
                                    <div class="d-flex justify-content-between py-1"><span class="text-muted">TVA ({{ (float) $facture->taux_tva }} %)</span><span class="fw-medium">{{ number_format($facture->montant_tva, 0, ',', ' ') }} FCFA</span></div>
                                @endif
                                <div class="d-flex justify-content-between py-2 border-top mt-1"><span class="fw-semibold">Total TTC</span><span class="fw-semibold fs-15 text-primary">{{ number_format($facture->total_ttc, 0, ',', ' ') }} FCFA</span></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>

    {{-- Aperçu / PDF --}}
    <div class="modal fade" id="facturePdfModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Facture — {{ $facture->numero }}</h5>
                    <button type="button" class="btn-close icon-btn-sm" data-bs-dismiss="modal" aria-label="Close"><i class="ri-close-large-line fw-semibold"></i></button>
                </div>
                <div class="modal-body p-0">
                    <iframe src="{{ route('facturation.factures.apercu', $facture) }}" style="width: 100%; height: 70vh; border: 0;"></iframe>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Fermer</button>
                    <a href="{{ route('facturation.factures.pdf', $facture) }}" class="btn btn-primary"><i class="bi bi-download me-1"></i>Télécharger en PDF</a>
                </div>
            </div>
        </div>
    </div>

    {{-- Changer le statut --}}
    <div class="modal fade" id="statutFactureModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <form action="{{ route('facturation.factures.update', $facture) }}" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="modal-header">
                        <h5 class="modal-title">Changer le statut de la facture</h5>
                        <button type="button" class="btn-close icon-btn-sm" data-bs-dismiss="modal" aria-label="Close"><i class="ri-close-large-line fw-semibold"></i></button>
                    </div>
                    <div class="modal-body">
                        <label class="form-label">Statut<span class="text-danger ms-1">*</span></label>
                        <select class="form-select" name="statut" required>
                            @foreach (\App\Models\Facturation\Facture::STATUTS as $valeur => $libelle)
                                <option value="{{ $valeur }}" @selected($facture->statut === $valeur)>{{ $libelle }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Annuler</button>
                        <button type="submit" class="btn btn-primary">Enregistrer</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    </main>
@endsection

@section('js')
    <script type="module" src="{{ asset('assets/js/app.js') }}"></script>
@endsection
