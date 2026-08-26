@php $classesStatutAbsence = ['en_attente' => 'warning', 'validee' => 'success', 'refusee' => 'danger', 'annulee' => 'secondary']; @endphp

<div class="row g-4">
    <div class="col-lg-4">
        <div class="card border h-100">
            <div class="card-body d-flex flex-column justify-content-center text-center">
                <div class="h-64px w-64px bg-info text-white d-flex align-items-center justify-content-center rounded-circle fs-2 mx-auto mb-3"><i class="bi bi-calendar-check"></i></div>
                <h2 class="mb-1">{{ number_format($employe->solde_conges, 1) }} <span class="fs-16 text-muted">j</span></h2>
                <p class="text-muted mb-0">Solde de congé disponible</p>
            </div>
        </div>
    </div>
    <div class="col-lg-4">
        <div class="card border">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <span class="text-muted">Jours consommés (validés)</span>
                    <span class="fw-semibold fs-16">{{ rtrim(rtrim(number_format($congesStats['jours_consommes'], 2, ',', ' '), '0'), ',') }} j</span>
                </div>
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <span class="text-muted">En attente de validation</span>
                    <span class="fw-semibold fs-16">{{ rtrim(rtrim(number_format($congesStats['jours_en_attente'], 2, ',', ' '), '0'), ',') }} j</span>
                </div>
                <div class="d-flex justify-content-between align-items-center">
                    <span class="text-muted">Demandes en attente</span>
                    <span class="badge bg-warning-subtle text-warning fs-13">{{ $congesStats['demandes_en_attente'] }}</span>
                </div>
            </div>
        </div>
    </div>
    <div class="col-lg-4">
        <div class="card border">
            <div class="card-body">
                @if ($employe->solde_conges > 0 || $congesStats['jours_consommes'] > 0)
                    <div id="chartSoldeConges" style="min-height: 180px;"></div>
                @else
                    <p class="text-muted fs-12 text-center py-5 mb-0">Aucune donnée de congé à représenter pour le moment.</p>
                @endif
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
                            <td>{{ rtrim(rtrim(number_format($absence->nombre_jours, 2, ',', ' '), '0'), ',') }}</td>
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
