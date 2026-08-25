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

        <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-1">
            <a href="{{ route('facturation.factures.index') }}" class="text-muted fs-13"><i class="bi bi-arrow-left me-1"></i>Retour aux factures</a>
            <div class="d-flex flex-wrap gap-2">
                <button type="button" class="btn btn-light-info" data-bs-toggle="modal" data-bs-target="#facturePdfModal"><i class="bi bi-file-earmark-pdf me-1"></i>Aperçu / PDF</button>
                <button type="button" class="btn btn-light-primary" data-bs-toggle="modal" data-bs-target="#statutFactureModal"><i class="bi bi-arrow-repeat me-1"></i>Changer le statut</button>
            </div>
        </div>

        <div class="card">
            <div class="card-body">
                <div class="row justify-content-between mb-8">
                    <div class="col-6">
                        <h4 class="mb-1 text-primary">{{ $reglage->nom_societe ?? config('app.name') }}</h4>
                        <p class="text-muted fs-12 mb-0">{{ $reglage->adresse ?? '' }}</p>
                        <p class="text-muted fs-12 mb-0">{{ $reglage->telephone ?? '' }} @if($reglage->email) — {{ $reglage->email }} @endif</p>
                    </div>
                    <div class="col-5 col-md-3 text-end">
                        <span class="badge bg-{{ $classesStatut[$facture->statut] ?? 'secondary' }} mb-2">{{ $facture->libelleStatut() }}</span>
                        <h5 class="mb-0">Facture {{ $facture->numero }}</h5>
                    </div>
                </div>

                <div class="row g-5 border-bottom border-dashed py-4">
                    <div class="col-md-5">
                        <h6 class="mb-3 text-muted text-uppercase fs-11">Client / Prospect</h6>
                        <p class="mb-1 fw-semibold fs-15">{{ $facture->client->nom_complet }}</p>
                        <p class="mb-1"><i class="bi bi-telephone me-1 text-muted"></i>{{ $facture->client->telephone ?: '—' }}</p>
                        <p class="mb-0"><i class="bi bi-envelope me-1 text-muted"></i>{{ $facture->client->email ?: '—' }}</p>
                        @if ($facture->client->prospect)
                            <span class="badge bg-info-subtle text-info mt-2"><i class="bi bi-link-45deg me-1"></i>Issu du prospect {{ $facture->client->prospect->numero }}</span>
                        @endif
                    </div>
                    <div class="col-md-4">
                        <h6 class="mb-3 text-muted text-uppercase fs-11">Détails de la facture</h6>
                        <p class="mb-1"><span class="text-muted">Date d'émission :</span> <span class="fw-medium">{{ $facture->date_facture->format('d/m/Y') }}</span></p>
                        <p class="mb-1"><span class="text-muted">TVA :</span> <span class="fw-medium">{{ $facture->appliquer_tva ? number_format((float) $facture->taux_tva, 2, ',', ' ').' %' : 'Non activée' }}</span></p>
                        <p class="mb-1"><span class="text-muted">Créée par :</span> <span class="fw-medium">{{ $facture->creePar->name ?? '—' }}</span></p>
                        @if ($facture->source)
                            <p class="mb-0"><span class="text-muted">Source :</span>
                                @if ($facture->devisSource)
                                    <a href="{{ route('facturation.devis.show', $facture->devisSource) }}" class="fw-medium">{{ $facture->source }}</a>
                                @else
                                    <span class="fw-medium">{{ $facture->source }}</span>
                                @endif
                            </p>
                        @endif
                    </div>
                    <div class="col-md-3 text-md-end">
                        <h6 class="mb-3 text-muted text-uppercase fs-11">Montant</h6>
                        <h3 class="text-primary mb-0">{{ number_format($facture->total_ttc, 0, ',', ' ') }}</h3>
                        <p class="text-muted mb-0">FCFA TTC</p>
                    </div>
                </div>

                <div class="py-4">
                    <h6 class="mb-3">Détail des prestations</h6>
                    <div class="table-responsive">
                        <table class="table text-nowrap table-borderless mb-0">
                            <thead>
                                <tr class="border-bottom">
                                    <th class="w-50px">No.</th>
                                    <th>Désignation</th>
                                    <th class="text-end">Quantité</th>
                                    <th class="text-end">Prix unitaire</th>
                                    <th class="text-end">Total</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($facture->lignes as $ligne)
                                    <tr>
                                        <td class="text-muted">{{ $loop->iteration }}.</td>
                                        <td class="fw-medium">{{ $ligne->designation }}</td>
                                        <td class="text-end">{{ number_format($ligne->quantite, 2, ',', ' ') }}</td>
                                        <td class="text-end">{{ number_format($ligne->prix_unitaire, 0, ',', ' ') }} FCFA</td>
                                        <td class="text-end fw-medium">{{ number_format($ligne->total, 0, ',', ' ') }} FCFA</td>
                                    </tr>
                                @endforeach
                                <tr>
                                    <td colspan="3"></td>
                                    <td class="fw-semibold text-end">Sous-total HT</td>
                                    <td class="fw-semibold text-end">{{ number_format($facture->sous_total_ht, 0, ',', ' ') }} FCFA</td>
                                </tr>
                                @if ($facture->appliquer_tva)
                                    <tr>
                                        <td colspan="3"></td>
                                        <td class="fw-semibold text-end">TVA <span class="text-muted fw-normal fs-12">({{ (float) $facture->taux_tva }} %)</span></td>
                                        <td class="fw-semibold text-end">{{ number_format($facture->montant_tva, 0, ',', ' ') }} FCFA</td>
                                    </tr>
                                @endif
                                <tr class="border-top">
                                    <td colspan="3"></td>
                                    <td class="fw-semibold text-end fs-14">Total TTC</td>
                                    <td class="fw-semibold text-end fs-14 text-primary">{{ number_format($facture->total_ttc, 0, ',', ' ') }} FCFA</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                @if ($facture->notes)
                    <div class="pt-2">
                        <h6 class="mb-2 text-muted text-uppercase fs-11">Notes</h6>
                        <p class="mb-0">{{ $facture->notes }}</p>
                    </div>
                @endif

                <div class="pt-6">
                    <div class="p-4 bg-light-subtle rounded text-center">
                        <p class="mb-1">Merci pour votre confiance. Pour toute question relative à cette facture, contactez-nous{{ $reglage->email ? ' à '.$reglage->email : '' }}.</p>
                        <p class="mb-0 text-muted fs-12">Facture générée par {{ config('app.name') }} — {{ now()->format('d/m/Y') }}</p>
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
