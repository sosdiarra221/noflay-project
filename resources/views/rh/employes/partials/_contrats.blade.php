<div class="card">
    <div class="card-body p-0">
        <div class="table-box table-responsive">
            <table class="table text-nowrap align-middle mb-0">
                <thead><tr><th>Numéro</th><th>Type</th><th>Début</th><th>Fin prévue</th><th>Fin réelle</th><th>Montant</th><th>État</th><th>Actions</th></tr></thead>
                <tbody>
                    @forelse ($employe->contrats as $contrat)
                        <tr>
                            <td class="fw-medium">{{ $contrat->numero }}</td>
                            <td>{{ $contrat->libelleType() }}</td>
                            <td>{{ $contrat->date_debut->format('d/m/Y') }}</td>
                            <td>{{ $contrat->date_prevu_fin?->format('d/m/Y') ?: '—' }}</td>
                            <td>{{ $contrat->date_fin?->format('d/m/Y') ?: '—' }}</td>
                            <td>{{ $contrat->montant ? number_format($contrat->montant, 0, ',', ' ').' FCFA' : '—' }}</td>
                            <td>
                                @if ($contrat->etat === 'actif')
                                    <span class="badge bg-success-subtle text-success">Actif</span>
                                @else
                                    <span class="badge bg-secondary-subtle text-secondary">Clôturé</span>
                                @endif
                            </td>
                            <td>
                                @can('rh.gerer')
                                    @if ($contrat->etat === 'actif')
                                        <div class="hstack gap-2">
                                            <button type="button" class="btn btn-light-primary btn-sm" data-bs-toggle="modal" data-bs-target="#renouvelerContratModal{{ $contrat->id }}">Renouveler</button>
                                            <button type="button" class="btn btn-light-danger btn-sm" data-bs-toggle="modal" data-bs-target="#cloturerContratModal{{ $contrat->id }}">Clôturer</button>
                                        </div>
                                    @endif
                                @endcan
                                <div class="hstack gap-2">
                                    @if ($contrat->document)
                                        <button type="button" class="btn btn-light-info icon-btn-sm" title="Voir le document signé" onclick="ouvrirApercuDocument({{ Js::from(route('rh.contrats.document.apercu', $contrat)) }}, {{ Js::from('Contrat '.$contrat->numero) }}, true, {{ Js::from(route('rh.contrats.document.apercu', $contrat)) }})"><i class="bi bi-file-earmark-check"></i></button>
                                    @endif
                                    <button type="button" class="btn btn-light-secondary icon-btn-sm" title="Contrat en PDF" onclick="ouvrirApercuDocument({{ Js::from(route('rh.contrats.pdf.apercu', $contrat)) }}, {{ Js::from('Contrat '.$contrat->numero) }}, true, {{ Js::from(route('rh.contrats.pdf', $contrat)) }})"><i class="bi bi-file-earmark-pdf"></i></button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="8" class="text-center text-muted py-5">Aucun contrat enregistré pour cet employé.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
