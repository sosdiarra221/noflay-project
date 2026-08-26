@php $classesStatutAbsence = ['en_attente' => 'warning', 'validee' => 'success', 'refusee' => 'danger', 'annulee' => 'secondary']; @endphp
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
