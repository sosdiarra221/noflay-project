<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h6 class="card-action-title mb-0">Historique des transferts</h6>
        @can('rh.gerer')
            <a href="{{ route('rh.affectations.index') }}" class="btn btn-light-primary btn-sm"><i class="bi bi-geo-alt me-1"></i>Affecter à un site</a>
        @endcan
    </div>
    <div class="card-body p-0">
        <div class="table-box table-responsive">
            <table class="table text-nowrap align-middle mb-0">
                <thead><tr><th>Date</th><th>Ancien département</th><th>Nouveau département</th><th>Anciens sites</th><th>Nouveaux sites</th><th>Motif</th></tr></thead>
                <tbody>
                    @forelse ($employe->affectations as $affectation)
                        <tr>
                            <td>{{ $affectation->date_affectation->format('d/m/Y') }}</td>
                            <td>{{ $affectation->ancienDepartement->nom ?? '—' }}</td>
                            <td class="fw-medium">{{ $affectation->nouveauDepartement->nom ?? '—' }}</td>
                            <td class="text-muted fs-12">{{ $affectation->anciens_sites ?: '—' }}</td>
                            <td class="fw-medium">{{ $affectation->nouveaux_sites ?: '—' }}</td>
                            <td class="text-muted fs-12">{{ $affectation->motif ?: '—' }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="6" class="text-center text-muted py-5">Aucun transfert enregistré — l'employé n'a jamais changé de département ou de site.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
