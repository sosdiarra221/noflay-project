@extends('partials.layouts.master-facturation')

@section('title', $devis->numero.' | Facturation')
@section('title-sub', 'Devis')
@section('pagetitle', $devis->numero)

@section('content')
    <div id="layout-wrapper">

        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        @php $classesStatut = ['nouveau' => 'secondary', 'en_negociation' => 'warning', 'gagne' => 'success', 'perdu' => 'danger', 'annule' => 'dark']; @endphp

        <div class="card">
            <div class="card-body">
                <div class="d-flex flex-wrap justify-content-between align-items-start gap-3">
                    <div>
                        <h5 class="mb-1">{{ $devis->numero }} <span class="badge bg-{{ $classesStatut[$devis->statut] ?? 'secondary' }}-subtle text-{{ $classesStatut[$devis->statut] ?? 'secondary' }} ms-1">{{ $devis->libelleStatut() }}</span></h5>
                        <p class="text-muted mb-0">
                            {{ $devis->client->nom_complet }} — {{ $devis->date_devis->format('d/m/Y') }} — <strong>{{ number_format($devis->total_ttc, 0, ',', ' ') }} FCFA</strong>
                        </p>
                    </div>
                    <div class="d-flex flex-wrap gap-2">
                        <button type="button" class="btn btn-light-info" data-bs-toggle="modal" data-bs-target="#devisPdfModal"><i class="bi bi-file-earmark-pdf me-1"></i>Aperçu / PDF</button>
                        <button type="button" class="btn btn-light-primary" data-bs-toggle="modal" data-bs-target="#statutDevisModal"><i class="bi bi-arrow-repeat me-1"></i>Changer le statut</button>
                        <button type="button" class="btn btn-light-danger" data-bs-toggle="modal" data-bs-target="#supprimerDevisModal"><i class="bi bi-trash3 me-1"></i>Supprimer</button>
                    </div>
                </div>
            </div>
        </div>

        <div class="row g-4">
            <div class="col-lg-4">
                <div class="card">
                    <div class="card-header"><h6 class="card-action-title mb-0">Client / Prospect</h6></div>
                    <div class="card-body">
                        <div class="row mb-3"><div class="col-5 text-muted">Nom complet</div><div class="col-7 fw-medium">{{ $devis->client->nom_complet }}</div></div>
                        <div class="row mb-3"><div class="col-5 text-muted">Téléphone</div><div class="col-7 fw-medium">{{ $devis->client->telephone ?: '—' }}</div></div>
                        <div class="row"><div class="col-5 text-muted">Email</div><div class="col-7 fw-medium">{{ $devis->client->email ?: '—' }}</div></div>
                    </div>
                </div>
                <div class="card">
                    <div class="card-header"><h6 class="card-action-title mb-0">Informations</h6></div>
                    <div class="card-body">
                        <div class="row mb-3"><div class="col-5 text-muted">Date</div><div class="col-7 fw-medium">{{ $devis->date_devis->format('d/m/Y') }}</div></div>
                        <div class="row mb-3"><div class="col-5 text-muted">TVA</div><div class="col-7 fw-medium">{{ $devis->appliquer_tva ? number_format((float) $devis->taux_tva, 2, ',', ' ').' %' : 'Non activée' }}</div></div>
                        <div class="row"><div class="col-5 text-muted">Créé par</div><div class="col-7 fw-medium">{{ $devis->creePar->name ?? '—' }}</div></div>
                    </div>
                </div>
                @if ($devis->notes)
                    <div class="card">
                        <div class="card-header"><h6 class="card-action-title mb-0">Notes</h6></div>
                        <div class="card-body"><p class="mb-0">{{ $devis->notes }}</p></div>
                    </div>
                @endif
            </div>
            <div class="col-lg-8">
                <div class="card">
                    <div class="card-header"><h6 class="mb-0">Lignes du devis <span class="badge bg-secondary-subtle text-secondary ms-1">{{ $devis->lignes->count() }}</span></h6></div>
                    <div class="card-body p-0">
                        <div class="table-box table-responsive">
                            <table class="table align-middle mb-0">
                                <thead><tr><th>Désignation</th><th class="text-end">Quantité</th><th class="text-end">Prix unitaire</th><th class="text-end">Total</th></tr></thead>
                                <tbody>
                                    @foreach ($devis->lignes as $ligne)
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
                                <div class="d-flex justify-content-between py-1"><span class="text-muted">Sous-total HT</span><span class="fw-medium">{{ number_format($devis->sous_total_ht, 0, ',', ' ') }} FCFA</span></div>
                                @if ($devis->appliquer_tva)
                                    <div class="d-flex justify-content-between py-1"><span class="text-muted">TVA ({{ (float) $devis->taux_tva }} %)</span><span class="fw-medium">{{ number_format($devis->montant_tva, 0, ',', ' ') }} FCFA</span></div>
                                @endif
                                <div class="d-flex justify-content-between py-2 border-top mt-1"><span class="fw-semibold">Total TTC</span><span class="fw-semibold fs-15 text-primary">{{ number_format($devis->total_ttc, 0, ',', ' ') }} FCFA</span></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>

    {{-- Aperçu / PDF --}}
    <div class="modal fade" id="devisPdfModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Devis — {{ $devis->numero }}</h5>
                    <button type="button" class="btn-close icon-btn-sm" data-bs-dismiss="modal" aria-label="Close"><i class="ri-close-large-line fw-semibold"></i></button>
                </div>
                <div class="modal-body p-0">
                    <iframe src="{{ route('facturation.devis.apercu', $devis) }}" style="width: 100%; height: 70vh; border: 0;"></iframe>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Fermer</button>
                    <a href="{{ route('facturation.devis.pdf', $devis) }}" class="btn btn-primary"><i class="bi bi-download me-1"></i>Télécharger en PDF</a>
                </div>
            </div>
        </div>
    </div>

    {{-- Changer le statut --}}
    <div class="modal fade" id="statutDevisModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <form action="{{ route('facturation.devis.update', $devis) }}" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="modal-header">
                        <h5 class="modal-title">Changer le statut du devis</h5>
                        <button type="button" class="btn-close icon-btn-sm" data-bs-dismiss="modal" aria-label="Close"><i class="ri-close-large-line fw-semibold"></i></button>
                    </div>
                    <div class="modal-body">
                        <label class="form-label">Statut<span class="text-danger ms-1">*</span></label>
                        <select class="form-select" name="statut" required>
                            @foreach (\App\Models\Facturation\Devis::STATUTS as $valeur => $libelle)
                                <option value="{{ $valeur }}" @selected($devis->statut === $valeur)>{{ $libelle }}</option>
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

    {{-- Supprimer --}}
    <div class="modal fade" id="supprimerDevisModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <form action="{{ route('facturation.devis.destroy', $devis) }}" method="POST">
                    @csrf
                    @method('DELETE')
                    <div class="modal-header">
                        <h5 class="modal-title">Supprimer le devis {{ $devis->numero }}</h5>
                        <button type="button" class="btn-close icon-btn-sm" data-bs-dismiss="modal" aria-label="Close"><i class="ri-close-large-line fw-semibold"></i></button>
                    </div>
                    <div class="modal-body">
                        <label class="form-label">Motif de suppression<span class="text-danger ms-1">*</span></label>
                        <textarea class="form-control" name="motif_suppression" rows="2" required></textarea>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Annuler</button>
                        <button type="submit" class="btn btn-danger">Confirmer la suppression</button>
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
