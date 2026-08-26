@php $classesStatutAbsence = ['en_attente' => 'warning', 'validee' => 'success', 'refusee' => 'danger', 'annulee' => 'secondary']; @endphp

<div class="row g-4">
    <div class="col-md-4">
        <div class="card border">
            <div class="card-body">
                <div class="d-flex gap-4 border-bottom pb-5 mb-5">
                    <div class="h-50px w-50px bg-success text-white d-flex align-items-center justify-content-center rounded fs-3"><i class="bi bi-check-circle"></i></div>
                    <div><h5 class="mb-2">{{ \App\Models\Rh\Employe::formaterJours($congesStats['jours_consommes']) }}</h5><p class="text-muted mb-0 fs-12">Jours consommés (validés)</p></div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card border">
            <div class="card-body">
                <div class="d-flex gap-4 border-bottom pb-5 mb-5">
                    <div class="h-50px w-50px bg-warning text-white d-flex align-items-center justify-content-center rounded fs-3"><i class="bi bi-hourglass-split"></i></div>
                    <div><h5 class="mb-2">{{ \App\Models\Rh\Employe::formaterJours($congesStats['jours_en_attente']) }}</h5><p class="text-muted mb-0 fs-12">Jours en attente de validation</p></div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card border">
            <div class="card-body">
                <div class="d-flex gap-4 border-bottom pb-5 mb-5">
                    <div class="h-50px w-50px bg-secondary text-white d-flex align-items-center justify-content-center rounded fs-3"><i class="bi bi-card-list"></i></div>
                    <div><h5 class="mb-2">{{ $congesStats['demandes_en_attente'] }}</h5><p class="text-muted mb-0 fs-12">Demandes en attente</p></div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h6 class="card-action-title mb-0">Historique des congés &amp; absences</h6>
        @can('rh.gerer')
            <a href="{{ route('rh.absences.index') }}" class="btn btn-light-primary btn-sm"><i class="bi bi-calendar2-week me-1"></i>Nouvelle demande</a>
        @endcan
    </div>
    <div class="card-body p-0">
        <div class="table-box table-responsive">
            <table class="table text-nowrap align-middle mb-0">
                <thead><tr><th>Numéro</th><th>Type</th><th>Début</th><th>Retour</th><th>Jours</th><th>Statut</th><th>Justificatif</th></tr></thead>
                <tbody>
                    @forelse ($employe->absences as $absence)
                        <tr>
                            <td class="fw-medium">{{ $absence->numero }}</td>
                            <td>{{ $absence->typeAbsence->nom ?? '—' }}</td>
                            <td>{{ $absence->date_debut->format('d/m/Y') }}</td>
                            <td>{{ $absence->date_retour->format('d/m/Y') }}</td>
                            <td>{{ \App\Models\Rh\Employe::formaterJours($absence->nombre_jours) }}</td>
                            <td><span class="badge bg-{{ $classesStatutAbsence[$absence->statut] ?? 'secondary' }}-subtle text-{{ $classesStatutAbsence[$absence->statut] ?? 'secondary' }}">{{ $absence->libelleStatut() }}</span></td>
                            <td>
                                @if ($absence->document)
                                    <button type="button" class="btn btn-light-info icon-btn-sm" title="Voir le justificatif" onclick="ouvrirApercuDocument({{ Js::from(route('rh.absences.document.apercu', $absence)) }}, {{ Js::from('Justificatif '.$absence->numero) }}, true, {{ Js::from(route('rh.absences.document.apercu', $absence)) }})"><i class="bi bi-file-earmark-text"></i></button>
                                @else
                                    —
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="7" class="text-center text-muted py-5">Aucune demande de congé ou d'absence enregistrée pour cet employé.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
